# WhatsMark Multi-tenancy Implementation Guide

[![Laravel Release Builder](https://github.com/corbitaltech-dev/whatsmark-saas/actions/workflows/release.yml/badge.svg?branch=release)](https://github.com/corbitaltech-dev/whatsmark-saas/actions/workflows/release.yml)

## Overview

This document explains the multi-tenancy implementation in the WhatsMark SaaS application. Our application uses a path-based tenancy approach (e.g., `whatsmark.com/tenant1/dashboard`) with the ability to migrate to domain-based tenancy in the future.

## Table of Contents

1. [Architecture](#architecture)
2. [How It Works](#how-it-works)
3. [Key Components](#key-components)
4. [Tenant Resolution Flow](#tenant-resolution-flow)
5. [Data Isolation](#data-isolation)
6. [URL Handling](#url-handling)
7. [Implementation Steps](#implementation-steps)
8. [Troubleshooting](#troubleshooting)
9. [Future Improvements](#future-improvements)

## Architecture

WhatsMark uses Spatie's Laravel Multitenancy package with a custom path-based tenant finder. The key architectural components are:

- **Path-based tenant detection**: Tenants are identified by the first URL segment (`whatsmark.com/tenant1/...`)
- **Database tenant isolation**: Each tenant has its own data isolated using a `tenant_id` column
- **Automatic request rewriting**: URLs are automatically rewritten to strip the tenant prefix
- **Global scopes**: Models use a trait to scope queries to the current tenant

## How It Works

1. A request comes in (e.g., `whatsmark.com/tenant1/dashboard`)
2. The `DetermineTenantFromPath` middleware extracts the first segment (`tenant1`)
3. The middleware looks up a tenant with that subdomain in the database
4. If found, it makes that tenant "current" and rewrites the URL to `whatsmark.com/dashboard`
5. The request continues through the normal Laravel routing system
6. All tenant models automatically apply a tenant scope to queries

## Key Components

### Tenant Model

```php
// app/Models/Tenant.php
namespace App\Models;

use Corbital\Settings\Models\TenantSetting;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Multitenancy\Models\Tenant as BaseTenant;

class Tenant extends BaseTenant
{
    protected $guarded = [];

    /**
     * Get the current tenant with proper type handling.
     */
    public static function current(): ?static
    {
        $tenant = parent::current();

        if ($tenant && get_class($tenant) === BaseTenant::class) {
            return static::find($tenant->getKey());
        }

        return $tenant;
    }

    // Relationships and additional methods...
}
```

### Tenant Middleware

```php
// app/Http/Middleware/DetermineTenantFromPath.php
namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class DetermineTenantFromPath
{
    public function handle(Request $request, Closure $next): mixed
    {
        $path = $request->path();
        $segments = explode('/', $path);

        // Skip for empty path or reserved paths
        $reservedPaths = ['admin', 'api', 'login', 'register', 'password', 'debug', 'assets'];
        if (empty($segments[0]) || in_array($segments[0], $reservedPaths)) {
            return $next($request);
        }

        $subdomain = $segments[0];

        // Find tenant using cache
        $tenant = Cache::remember("tenant:{$subdomain}", now()->addMinutes(10), function () use ($subdomain) {
            return Tenant::where('subdomain', $subdomain)->first();
        });

        if ($tenant) {
            // Make this tenant active
            $tenant->makeCurrent();
            app()->instance('currentTenant', $tenant);

            // Rewrite the URL
            $this->rewriteRequestPath($request, $subdomain);
        }

        return $next($request);
    }

    private function rewriteRequestPath(Request $request, string $prefix): void
    {
        // URL rewriting logic...
    }
}
```

### BelongsToTenant Trait

```php
// app/Traits/BelongsToTenant.php
namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use App\Models\Tenant;

trait BelongsToTenant
{
    public static function bootBelongsToTenant(): void
    {
        // Add global scope for tenant
        static::addGlobalScope('tenant', function (Builder $builder) {
            if (Tenant::checkCurrent()) {
                $builder->where('tenant_id', Tenant::current()->id);
            }
        });

        // Set tenant_id on creation
        static::creating(function ($model) {
            if (!$model->isDirty('tenant_id') && Tenant::checkCurrent()) {
                $model->tenant_id = Tenant::current()->id;
            }
        });
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}
```

### Application Configuration

```php
// bootstrap/app.php
return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        then: function () {
            // Admin routes
            Route::middleware('web')
                ->prefix('admin')
                ->as('admin.')
                ->group([/* admin routes */]);

            // Tenant routes
            Route::middleware(['web', 'tenant'])
                ->group(base_path('routes/tenant.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Register middleware
        $middleware->prepend(DetermineTenantFromPath::class);

        // Tenant middleware group
        $middleware->group('tenant', [
            NeedsTenant::class,
            EnsureValidTenantSession::class,
        ]);
    })
    ->withProviders([
        App\Providers\TenantServiceProvider::class,
    ])
    ->withBindings([
        'tenant.finder' => PathTenantFinder::class,
    ])
    ->create();
```

## Tenant Resolution Flow

1. **Request Start**:
   - The application receives a request to `whatsmark.com/tenant1/dashboard`

2. **Middleware Processing**:
   - `DetermineTenantFromPath` middleware runs
   - Extracts `tenant1` from the path
   - Looks up tenant with subdomain = `tenant1`

3. **Tenant Activation**:
   - If tenant found, call `$tenant->makeCurrent()`
   - Store tenant in container: `app()->instance('currentTenant', $tenant)`

4. **URL Rewriting**:
   - Rewrite URL from `/tenant1/dashboard` to `/dashboard`
   - Update both `REQUEST_URI` and path info

5. **Route Matching**:
   - Laravel matches the rewritten URL to routes in `routes/tenant.php`

6. **Controller/Action Execution**:
   - Route handler executes with tenant context already set

## Data Isolation

We use the `BelongsToTenant` trait on models to ensure data isolation:

```php
// Example model with tenant isolation
namespace App\Models;

use App\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use BelongsToTenant;

    // Rest of the model...
}
```

This automatically:

1. Filters queries to only include records for the current tenant
2. Sets the `tenant_id` when creating new records
3. Defines the relationship to the tenant

## URL Handling

### Path Structure

- **Admin routes**: `whatsmark.com/admin/...`
- **Tenant routes**: `whatsmark.com/{tenant}/...`
- **Auth routes**: `whatsmark.com/login`, `whatsmark.com/register`, etc.

### URL Generation

When generating URLs within tenant routes, use:

```php
// Generate a tenant-specific URL
$url = url($path); // This will automatically include the tenant prefix
```

For explicit control over tenant URLs:

```php
// Using the tenant model
$url = $tenant->getUrlAttribute() . '/dashboard';
```

## Implementation Steps

To add multi-tenancy to a Laravel application:

1. **Database Preparation**:
   - Add `tenant_id` to all tenant-specific tables
   - Create a `tenants` table with required fields

2. **Install Dependencies**:

   ```bash
   composer require spatie/laravel-multitenancy
   ```

3. **Create Models and Middleware**:
   - Create or extend `Tenant` model
   - Create `DetermineTenantFromPath` middleware
   - Create `BelongsToTenant` trait

4. **Configure Application**:
   - Update `bootstrap/app.php` with middleware and route configuration
   - Create service provider for tenant-specific bindings

5. **Organize Routes**:
   - Separate tenant routes into `routes/tenant.php`
   - Configure admin routes with appropriate prefixes

6. **Apply Trait to Models**:
   - Add `use BelongsToTenant;` to all tenant-specific models

7. **Test Implementation**:
   - Create test tenants
   - Verify URL rewriting works correctly
   - Confirm data isolation between tenants

## Troubleshooting

### Common Issues

1. **404 Errors**:
   - Check if URL rewriting is working correctly
   - Verify route definitions match the rewritten paths
   - Enable detailed logging in the middleware

2. **Type Mismatches**:
   - Ensure `Tenant::current()` returns your custom Tenant model
   - Check for incorrect typehints in method signatures

3. **Container Binding Issues**:
   - Verify `currentTenant` binding is properly registered
   - Ensure middleware correctly stores tenant in container

4. **Data Leaks**:
   - Make sure all models use the `BelongsToTenant` trait
   - Check for queries that bypass Eloquent

### Debugging Tips

Add debugging endpoints:

```php
Route::get('/debug-tenancy', function () {
    return response()->json([
        'current_tenant' => Tenant::checkCurrent() ? [
            'id' => Tenant::current()->id,
            'name' => Tenant::current()->name,
        ] : null,
        'request' => [
            'path' => request()->path(),
            'url' => request()->url(),
        ]
    ]);
});
```

## URL Structure and Examples

This section explains how URLs work in the multi-tenant application across different contexts (super admin, tenant admin, tenant users) with practical examples.

### URL Patterns

| Context | URL Pattern | Example | Description |
|---------|-------------|---------|-------------|
| Main Site | `whatsmark.com` | `whatsmark.com` | Main landing page for all users |
| Super Admin | `whatsmark.com/admin/...` | `whatsmark.com/admin/dashboard` | Central administration area |
| Tenant | `whatsmark.com/{tenant}/...` | `whatsmark.com/acme/dashboard` | Tenant-specific area |
| Tenant Admin | `whatsmark.com/{tenant}/admin/...` | `whatsmark.com/acme/admin/settings` | Tenant administration area |
| Auth Pages | `whatsmark.com/login` etc. | `whatsmark.com/login` | Authentication pages (global) |

### Example Scenarios

#### 1. Super Admin Navigation

When a super admin is logged in:

```
whatsmark.com/admin/dashboard            # Main super admin dashboard
whatsmark.com/admin/tenants              # Manage all tenants
whatsmark.com/admin/tenants/create       # Create a new tenant
whatsmark.com/admin/tenants/123/edit     # Edit tenant with ID 123
```

#### 2. Single Tenant Navigation

When accessing tenant "acme":

```
whatsmark.com/acme                       # Tenant home page
whatsmark.com/acme/dashboard             # Tenant dashboard
whatsmark.com/acme/contacts              # Tenant contacts
whatsmark.com/acme/settings              # Tenant settings
```

#### 3. Tenant Admin Navigation

When a tenant admin is logged in:

```
whatsmark.com/acme/admin/dashboard       # Tenant admin dashboard
whatsmark.com/acme/admin/users           # Manage tenant users
whatsmark.com/acme/admin/settings        # Tenant-specific settings
```

#### 4. Authentication Flow

```
whatsmark.com/login                      # Global login page
whatsmark.com/register                   # Global registration page
whatsmark.com/acme/login                 # Tenant-specific login (redirects to tenant dashboard)
```

### URL Generation in Code

#### 1. For Super Admin Routes

```php
// Generate super admin URL
$url = route('admin.dashboard');                  // whatsmark.com/admin/dashboard
$url = route('admin.tenants.edit', ['id' => 123]); // whatsmark.com/admin/tenants/123/edit
```

#### 2. For Tenant Routes

```php
// Current tenant URL - inside tenant context
$url = route('dashboard');                        // whatsmark.com/{current-tenant}/dashboard

// Specific tenant URL
$tenantSubdomain = 'acme';
$url = url($tenantSubdomain . '/dashboard');      // whatsmark.com/acme/dashboard

// Using the tenant model
$tenant = Tenant::find(123);
$url = $tenant->getUrlAttribute() . '/dashboard'; // whatsmark.com/acme/dashboard
```

#### 3. For Auth Routes

```php
// Global auth routes
$url = route('login');                            // whatsmark.com/login
$url = route('register');                         // whatsmark.com/register
```

### Middleware Rewrites

Remember that our middleware rewrites URLs internally. When a request comes to `whatsmark.com/acme/dashboard`, it's rewritten internally to `/dashboard` with the tenant context set. This means your route definitions should not include the tenant prefix:

```php
// routes/tenant.php
Route::get('/dashboard', 'DashboardController@index')->name('dashboard');
```

Not:

```php
// Don't do this
Route::get('/acme/dashboard', 'DashboardController@index');
```

## Required Database Schema

Here is the minimum SQL structure required for the multi-tenant system:

### 1. Tenants Table

```sql
CREATE TABLE `tenants` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT 'Tenant display name',
  `company_name` varchar(255) DEFAULT NULL COMMENT 'Tenant company name',
  `email` varchar(255) NOT NULL COMMENT 'Primary contact email',
  `phone` varchar(20) DEFAULT NULL COMMENT 'Primary contact phone',
  `domain` varchar(255) DEFAULT NULL COMMENT 'Custom domain if available',
  `subdomain` varchar(255) NOT NULL COMMENT 'Subdomain for tenant access',
  `status` enum('active','inactive','suspended','expired') NOT NULL DEFAULT 'active' COMMENT 'Current tenant status',
  `logo` varchar(255) DEFAULT NULL COMMENT 'Path to tenant logo',
  `timezone` varchar(100) DEFAULT 'UTC' COMMENT 'Tenant default timezone',
  `expires_at` timestamp NULL DEFAULT NULL COMMENT 'Subscription expiration date',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tenants_subdomain_unique` (`subdomain`),
  UNIQUE KEY `tenants_email_unique` (`email`),
  KEY `tenants_status_index` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 2. Users Table with Tenant Relationship

```sql
CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint UNSIGNED DEFAULT NULL COMMENT 'Reference to tenant (NULL for super admin)',
  `name` varchar(255) NOT NULL COMMENT 'User display name',
  `email` varchar(255) NOT NULL COMMENT 'User email address',
  `email_verified_at` timestamp NULL DEFAULT NULL COMMENT 'When email was verified',
  `password` varchar(255) NOT NULL COMMENT 'Hashed password',
  `remember_token` varchar(100) DEFAULT NULL,
  `is_super_admin` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Whether user is a super admin',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Whether user is active',
  `last_login_at` timestamp NULL DEFAULT NULL COMMENT 'Last successful login',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_tenant_id_index` (`tenant_id`),
  KEY `idx_users_role_status` (`tenant_id`,`is_super_admin`,`status`),
  CONSTRAINT `users_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 3. Example Tenant-Specific Table (Contacts)

```sql
CREATE TABLE `contacts` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint UNSIGNED NOT NULL COMMENT 'Reference to tenant',
  `firstname` varchar(191) NOT NULL,
  `lastname` varchar(191) NOT NULL,
  `email` varchar(191) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `contacts_tenant_id_index` (`tenant_id`),
  KEY `contacts_firstname_lastname_index` (`firstname`,`lastname`),
  CONSTRAINT `contacts_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 4. Tenant Settings Table

```sql
CREATE TABLE `tenant_settings` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint UNSIGNED NOT NULL COMMENT 'Reference to tenant',
  `group` varchar(255) NOT NULL COMMENT 'Settings group name',
  `key` varchar(255) NOT NULL COMMENT 'Setting key name',
  `value` longtext COMMENT 'Setting value',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tenant_settings_tenant_id_group_key_unique` (`tenant_id`,`group`,`key`),
  KEY `tenant_settings_tenant_id_index` (`tenant_id`),
  CONSTRAINT `tenant_settings_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### Initial Data Setup

To get started with the multi-tenant system, you need at least:

1. **Super Admin User**:

```sql
INSERT INTO `users` (`name`, `email`, `password`, `is_super_admin`, `created_at`, `updated_at`)
VALUES ('Super Admin', 'admin@whatsmark.com', '$2y$10$encrypted_password', 1, NOW(), NOW());
```

2. **Initial Tenant**:

```sql
INSERT INTO `tenants` (`name`, `company_name`, `email`, `subdomain`, `status`, `created_at`, `updated_at`)
VALUES ('Demo Tenant', 'Demo Company', 'admin@demo.com', 'demo', 'active', NOW(), NOW());
```

3. **Tenant Admin User**:

```sql
INSERT INTO `users` (`tenant_id`, `name`, `email`, `password`, `created_at`, `updated_at`)
VALUES (1, 'Tenant Admin', 'admin@demo.com', '$2y$10$encrypted_password', NOW(), NOW());
```

This minimal schema provides the foundation for the multi-tenant application. You can expand it with additional tables as needed, always including the `tenant_id` column on tenant-specific tables and applying the `BelongsToTenant` trait to the corresponding models.

## Following command is prepare your database

### info: this commands destroy your all data in db and migrate

php artisan db:wipe
php artisan migrate
php artisan migrate --path=database/migrations/Tenant
php artisan db:seed
