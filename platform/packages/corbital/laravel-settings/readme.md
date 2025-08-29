# Corbital Settings

An enterprise-grade settings management package for Laravel applications with multi-tenant support. Built on top of Spatie Laravel Settings with advanced caching, tenant isolation, and performance optimizations.

## Table of Contents

- [Features](#features)
- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
  - [Creating Settings Classes](#creating-settings-classes)
  - [Using Helper Functions](#using-helper-functions)
  - [Tenant Settings](#tenant-settings)
  - [Model Settings](#model-settings)
  - [Encrypted Settings](#encrypted-settings)
- [Performance Optimizations](#performance-optimizations)
- [Cache Management](#cache-management)
- [Backup and Restore](#backup-and-restore)
- [Export and Import](#export-and-import)
- [Events](#events)
- [Middleware](#middleware)
- [Testing](#testing)
- [Changelog](#changelog)
- [Contributing](#contributing)
- [Security Vulnerabilities](#security-vulnerabilities)
- [License](#license)

[![Latest Version on Packagist](https://img.shields.io/packagist/v/corbital/settings.svg?style=flat-square)](https://packagist.org/packages/corbital/settings)
[![Total Downloads](https://img.shields.io/packagist/dt/corbital/settings.svg?style=flat-square)](https://packagist.org/packages/corbital/settings)

## Features

- 🚀 **Built on Spatie Laravel Settings**: Extends the popular Spatie package with enterprise features
- ⚡ **Performance Optimized**: Advanced caching with xxh3 hashing, distributed locking, and cache stampede prevention
- 🏢 **Multi-tenant Architecture**: Complete tenant isolation with secure cache key generation
- 📦 **Batch Operations**: Load multiple settings efficiently with batch processing and lazy loading
- ✅ **Validation System**: Comprehensive validation for settings values with custom rules
- 🔒 **Encrypted Settings**: Built-in encryption support for sensitive configuration data
- 🛠️ **CLI Management**: Full command-line interface for backup, restore, and maintenance
- 🧩 **Helper Functions**: Simple, intuitive API for common operations
- 🔄 **Auto-invalidation**: Intelligent cache invalidation with TTL management
- 🌐 **Multi-tenant Ready**: Designed for multi-tenant applications with tenant isolation

## Installation

You can install the package via composer:

```bash
composer require corbital/settings
```

Publish the config file:

```bash
php artisan vendor:publish --tag="corbital-settings-config"
```

Run the migrations:

```bash
php artisan migrate
```

## Configuration

The package integrates seamlessly with your Laravel application. Settings are merged from both Spatie and Corbital configurations in `config/settings.php`:

```php
return [
    // === Spatie Laravel Settings Configuration ===
    'settings' => [
        // Your Spatie settings classes
        \App\Settings\GeneralSettings::class,
        \App\Settings\ApiSettings::class,
        \App\Settings\NotificationSettings::class,
    ],

    'auto_discover_settings' => [
        app_path('Settings'),
    ],

    'migrations' => [
        'table_name' => 'settings',
        'add_locked_at_column' => true,
    ],

    // === Corbital Settings Configuration ===
    'cache_ttl' => env('SETTINGS_CACHE_TTL', 3600),
    'cache_prefix' => env('SETTINGS_CACHE_PREFIX', 'app_settings_'),
    'enable_tenant_support' => env('SETTINGS_ENABLE_TENANT_SUPPORT', false),
    'debug_mode' => env('SETTINGS_DEBUG_MODE', false),

    // Tenant identification (if multi-tenant support is enabled)
    'tenant_header_name' => env('SETTINGS_TENANT_HEADER', 'X-Tenant-ID'),
    'tenant_param_name' => env('SETTINGS_TENANT_PARAM', 'tenant_id'),
    'tenant_subdomain_identification' => env('SETTINGS_TENANT_SUBDOMAIN', false),

    // Performance optimizations
    'distributed_locking' => env('SETTINGS_DISTRIBUTED_LOCKING', true),
    'use_xxh3_hashing' => env('SETTINGS_USE_XXH3_HASHING', true),
    'batch_loading' => env('SETTINGS_BATCH_LOADING', true),
    'lazy_loading' => env('SETTINGS_LAZY_LOADING', true),
];
```

## Usage

### Creating Settings Classes

Create settings classes in `app/Settings` directory:

```php
<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class ApiSettings extends Settings
{
    public string $api_key;
    public string $api_secret;
    public bool $debug_mode;
    public string $webhook_url;
    public int $rate_limit_per_minute;
    public bool $notifications_enabled;

    public static function group(): string
    {
        return 'api';
    }

    public static function validationRules(): array
    {
        return [
            'api_key' => 'required|string|max:255',
            'api_secret' => 'required|string|min:32',
            'debug_mode' => 'boolean',
            'webhook_url' => 'nullable|url',
            'rate_limit_per_minute' => 'integer|min:1|max:1000',
            'notifications_enabled' => 'boolean',
        ];
    }
}
}
```

### Using Helper Functions

The package includes tenant-aware helper functions:

```php
// Get a setting with automatic tenant detection (if enabled)
$apiKey = get_setting('api.api_key', 'default-key');

// Set a setting
set_setting('api.rate_limit_per_minute', 100);

// Get all settings for a specific group
$apiSettings = get_settings_by_group('api');

// Batch load multiple settings for performance
$settings = get_batch_settings([
    'api.api_key',
    'api.api_secret',
    'general.app_name',
    'notifications.email_enabled'
]);

// Get settings with caching benefits
$cachedSettings = get_cached_settings('api');
```

### Tenant Settings

For multi-tenant applications, the package provides advanced tenant isolation:

```php
// Get settings for a specific tenant
$tenantApiSettings = get_tenant_setting('tenant_123', 'api.api_key');

// Set settings for a specific tenant
set_tenant_setting('tenant_123', 'api.rate_limit_per_minute', 50);

// Get all settings for the current tenant (auto-detected from context)
$currentTenantSettings = tenant_settings();

// Get settings for multiple tenants at once (admin operation)
$multiTenantSettings = get_multi_tenant_settings([
    'tenant_123',
    'tenant_456'
], 'api.api_key');

// Using the TenantSettingsManager directly for advanced operations
$tenantManager = app(\Corbital\Settings\TenantSettingsManager::class);

// Load settings with distributed locking for consistency
$settings = $tenantManager->loadTenantSettingsWithLock('tenant_123', [
    'api.api_key',
    'api.api_secret'
]);

// Batch update multiple tenant settings
$tenantManager->batchUpdateTenantSettings('tenant_123', [
    'api.rate_limit_per_minute' => 100,
    'api.debug_mode' => true,
    'notifications.email_enabled' => false
]);
```

### Model Settings

Attach settings to any Laravel model using the `HasSettings` trait:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Corbital\Settings\Traits\HasSettings;

class Team extends Model
{
    use HasSettings;

    protected function getSettingsGroupName(): string
    {
        return 'team_' . $this->id;
    }
}

class Project extends Model
{
    use HasSettings;

    protected function getSettingsGroupName(): string
    {
        return 'project_' . $this->id;
    }
}
```

Usage in your application:

```php
$team = Team::find(1);

// Get settings for this team
$primaryColor = $team->getSettings('theme.primary_color', '#007bff');
$timezone = $team->getSettings('general.timezone', 'UTC');

// Set multiple settings for this team
$team->setSettings([
    'theme.primary_color' => '#28a745',
    'theme.secondary_color' => '#6c757d',
    'general.timezone' => 'America/New_York',
    'notifications.email_enabled' => true
]);

// For projects
$project = Project::find(1);
$project->setSettings([
    'visibility' => 'private',
    'auto_deploy' => true,
    'build_timeout' => 300
]);
]);
```

### Encrypted Settings

For sensitive data like API keys and secrets:

```php
<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;
use Corbital\Settings\Traits\EncryptsSettings;

class SecureApiSettings extends Settings
{
    use EncryptsSettings;

    public string $api_key;
    public string $webhook_secret;
    public string $database_password;
    public string $encryption_key;

    // Specify which properties should be encrypted
    protected array $encrypted = [
        'api_key',
        'webhook_secret',
        'database_password',
        'encryption_key'
    ];

    public static function group(): string
    {
        return 'secure_api';
    }

    public static function validationRules(): array
    {
        return [
            'api_key' => 'required|string|min:32',
            'webhook_secret' => 'required|string|min:16',
            'database_password' => 'required|string|min:8',
            'encryption_key' => 'required|string|min:32',
        ];
    }
}
```

## Performance Optimizations

The package includes enterprise-grade performance optimizations:

### Distributed Locking

Prevents cache stampede issues in high-traffic scenarios:

```php
// Automatic distributed locking when loading settings
$settings = app(\Corbital\Settings\TenantSettingsManager::class)
    ->loadTenantSettingsWithLock('tenant_123', ['api.api_key']);
```

### xxh3 Hashing

Ultra-fast cache key generation:

```php
// Cache keys are automatically generated using xxh3 for optimal performance
// Example: app_settings_xxh3_hash_of_tenant_and_setting_key
```

### Batch Loading

Load multiple settings in a single operation:

```php
// Load multiple settings efficiently
$settings = get_batch_settings([
    'api.api_key',
    'api.api_secret',
    'general.app_name',
    'notifications.email_enabled'
]);
```

### Lazy Loading

Settings are loaded only when accessed:

```php
// Settings are loaded on-demand, reducing memory usage
$lazySettings = lazy_load_settings(['api', 'notifications']);
```

### Memory Efficiency

Optimized for large applications with smart memory management and cache eviction policies.

## Cache Management

The package automatically manages cache with intelligent invalidation:

```bash
# Clear all settings cache
php artisan settings:clear

# Clear cache for specific tenant (if multi-tenant is enabled)
php artisan settings:clear --tenant=tenant_123

# Refresh cache for all settings
php artisan settings:refresh

# Refresh cache for specific settings groups
php artisan settings:refresh --groups=api,notifications

# Monitor cache performance
php artisan settings:cache-stats
```

## Backup and Restore

Enterprise backup and restore functionality:

```bash
# Backup all settings
php artisan settings:backup

# Backup specific tenant (if multi-tenant is enabled)
php artisan settings:backup --tenant=tenant_123

# Backup specific groups
php artisan settings:backup --groups=api,notifications

# Backup with custom filename
php artisan settings:backup --file=app_backup_$(date +%Y%m%d).json

# Backup with encryption (recommended for production)
php artisan settings:backup --encrypt
```

Restore from backups:

```bash
# Interactive restore (lists available backups)
php artisan settings:restore

# Restore specific backup
php artisan settings:restore app_backup_20250126.json

# Restore for specific tenant only
php artisan settings:restore --tenant=tenant_123

# Restore specific groups
php artisan settings:restore --groups=api,notifications

# Dry run (preview changes without applying)
php artisan settings:restore --dry-run

# Restore with automatic tenant migration
php artisan settings:restore --migrate-tenants
```

## Export and Import

Export and import settings:

```bash
# Export all settings with pretty formatting
php artisan settings:export --file=app_settings.json --pretty

# Export tenant-specific settings (if multi-tenant is enabled)
php artisan settings:export --file=tenant_123_settings.json --tenant=tenant_123

# Export only specific groups
php artisan settings:export --file=core_settings.json --groups=api,notifications

# Import settings from file
php artisan settings:import app_settings.json

# Import with tenant mapping (migrate from old tenant IDs)
php artisan settings:import settings.json --tenant-map=old_tenant_1:new_tenant_123

# Import with validation
php artisan settings:import settings.json --validate

# Import and merge (don't overwrite existing settings)
php artisan settings:import settings.json --merge
```

## Events

The package dispatches enhanced events for monitoring and integration:

```php
// Setting events with tenant context
use Corbital\Settings\Events\TenantSettingCreated;
use Corbital\Settings\Events\TenantSettingUpdated;
use Corbital\Settings\Events\TenantSettingDeleted;
use Corbital\Settings\Events\SettingsCacheCleared;
use Corbital\Settings\Events\SettingsBackupCreated;
use Corbital\Settings\Events\SettingsRestored;

// Listen for tenant-specific setting changes
Event::listen(TenantSettingUpdated::class, function ($event) {
    Log::info('Setting updated for tenant', [
        'tenant_id' => $event->tenantId,
        'setting_key' => $event->settingKey,
        'old_value' => $event->oldValue,
        'new_value' => $event->newValue,
    ]);
});
```

## Middleware

For multi-tenant applications, include middleware for automatic tenant context management:

```php
// In app/Http/Kernel.php
protected $middlewareGroups = [
    'web' => [
        // ...
        \Corbital\Settings\Http\Middleware\TenantSettingsMiddleware::class,
    ],
    'api' => [
        // ...
        \Corbital\Settings\Http\Middleware\TenantSettingsMiddleware::class,
    ],
];

// Or use as route middleware
Route::middleware(['tenant.settings'])->group(function () {
    Route::get('/api/settings', [SettingsController::class, 'index']);
    Route::post('/api/settings', [SettingsController::class, 'store']);
});
```

The middleware automatically:

- Detects tenant from headers, subdomains, or parameters
- Sets tenant context for all setting operations
- Provides cache isolation per tenant
- Handles tenant-specific rate limiting

## Testing

The package includes comprehensive test utilities:

```php
// Test tenant settings isolation (if multi-tenant is enabled)
public function test_tenant_settings_isolation()
{
    $this->withTenant('tenant_123', function () {
        set_setting('api.api_key', 'key_123');
    });

    $this->withTenant('tenant_456', function () {
        set_setting('api.api_key', 'key_456');
    });

    // Verify isolation
    $this->withTenant('tenant_123', function () {
        $this->assertEquals('key_123', get_setting('api.api_key'));
    });
}

// Test performance optimizations
public function test_batch_loading_performance()
{
    $startTime = microtime(true);

    $settings = get_batch_settings([
        'api.api_key',
        'api.api_secret',
        'general.app_name'
    ]);

    $endTime = microtime(true);

    $this->assertLessThan(0.1, $endTime - $startTime); // Should be fast
    $this->assertCount(3, $settings);
}
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please email <security@example.com> to report security vulnerabilities.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
