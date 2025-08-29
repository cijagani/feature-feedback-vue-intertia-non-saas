# WhatsApp Marketing SaaS Project - Complete Development Guide

## Project Overview
This is a **WhatsApp marketing SaaS application** built with **Laravel 12 (TALL Stack)** featuring multi-tenancy, subscription management, and custom package architecture. The project follows a **path-based tenancy approach** with **database isolation** and **settings-based configuration**.

## Tech Stack & Architecture

### Core Framework
- **Laravel 12** (Latest version)
- **TALL Stack**: Tailwind CSS, Alpine.js, Laravel, Livewire
- **PHP 8.3+**
- **Livewire 3.6+** for reactive components
- **Alpine.js** for frontend interactivity
- **Tailwind CSS** for styling
- **Vite** for asset compilation

### Multi-Tenancy Architecture
- **Spatie Laravel Multitenancy 4.0** for tenant management
- **Single Database approach** with tenant isolation using `tenant_id` columns
- **Path-based tenancy**: `domain.com/tenant1/dashboard` (current implementation)
- **Subdomain-based tenancy**: `tenant1.domain.com` (future capability)
- **Database isolation**: Each tenant has isolated data using `tenant_id` columns
- **Custom Path Tenant Finder**: `App\Multitenancy\PathTenantFinder`
- **Tenant Model**: `App\Models\Tenant` with subscription management
- **Automatic URL rewriting**: URLs automatically stripped of tenant prefix

### Spatie Multitenancy Implementation Details

#### **Single Database with Tenant Isolation**
- **Implementation**: Single database with `tenant_id` column isolation
- **Global Scopes**: Automatic tenant filtering on all queries
- **Data Isolation**: Complete separation between tenants
- **Performance**: Efficient single-database queries with proper indexing
- **Reference**: [Single Database](https://spatie.be/docs/laravel-multitenancy/v4/installation/using-a-single-database)

#### **Tenant Resolution Methods**
1. **Path-based (Current)**: `/tenant1/dashboard`
   - Custom `PathTenantFinder` implementation
   - URL rewriting to strip tenant prefix
   - Automatic tenant context switching

2. **Subdomain-based (Future)**: `tenant1.domain.com`
   - DNS wildcard configuration required
   - Automatic subdomain detection
   - Seamless tenant switching

**Reference**: [Determining Current Tenant](https://spatie.be/docs/laravel-multitenancy/v4/installation/determining-current-tenant) | [Automatic Determination](https://spatie.be/docs/laravel-multitenancy/v4/basic-usage/automatically-determining-the-current-tenant)

#### **Multi-Tenancy Tasks System**
Tasks are executed when switching between tenants to prepare the environment:

1. **SwitchTenantDatabaseTask**: Database connection switching
2. **SwitchTenantSettingsTask**: Tenant-specific settings loading
3. **SwitchTenantEventTask**: Event system preparation
4. **SwitchTenantCacheTask**: Cache prefixing and isolation
5. **SwitchTenantRouteTask**: Route cache path switching

**Task Execution Flow**:
```php
// When tenant is resolved, tasks run in order:
'switch_tenant_tasks' => [
    \App\Multitenancy\Tasks\SwitchTenantDatabaseTask::class,
    \App\Multitenancy\Tasks\SwitchTenantSettingsTask::class,
    \App\Multitenancy\Tasks\SwitchTenantEventTask::class,
    \App\Multitenancy\Tasks\SwitchTenantCacheTask::class,
]
```

**Reference**: [Creating Custom Tasks](https://spatie.be/docs/laravel-multitenancy/v4/using-tasks-to-prepare-the-environment/creating-your-own-task) | [Switching Databases](https://spatie.be/docs/laravel-multitenancy/v4/using-tasks-to-prepare-the-environment/switching-databases) | [Cache Prefixing](https://spatie.be/docs/laravel-multitenancy/v4/using-tasks-to-prepare-the-environment/prefixing-cache)

#### **Working with Current Tenant**
```php
// Check if tenant context exists
if (Tenant::checkCurrent()) {
    $tenant = Tenant::current();
    $tenantId = $tenant->id;
}

// Get tenant-specific data
$settings = tenant_settings_by_group('system');
$users = User::where('tenant_id', tenant_id())->get();

// Execute code for specific tenant
$tenant->execute(function () {
    // This code runs in tenant context
});
```

**Reference**: [Working with Current Tenant](https://spatie.be/docs/laravel-multitenancy/v4/basic-usage/working-with-the-current-tenant) | [Ensuring Tenant Context](https://spatie.be/docs/laravel-multitenancy/v4/advanced-usage/ensuring-a-current-tenant-has-been-set)

#### **Queue System Integration**
- **Tenant-aware jobs**: Jobs remember their tenant context
- **Automatic tenant switching**: Jobs execute in correct tenant context
- **Queue configuration**: Tenant-specific queue prefixes

```php
// Job automatically runs in tenant context
class ProcessTenantData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle()
    {
        // This runs in the same tenant context as when dispatched
        $tenant = Tenant::current();
    }
}
```

**Reference**: [Making Queues Tenant Aware](https://spatie.be/docs/laravel-multitenancy/v4/basic-usage/making-queues-tenant-aware)

#### **Artisan Commands**
- **Tenant-aware commands**: Run commands for specific tenants
- **Bulk operations**: Execute commands across all tenants
- **Tenant filtering**: Target specific tenants

```bash
# Run command for specific tenant
php artisan tenant:artisan "migrate" --tenant=1

# Run command for all tenants
php artisan tenant:artisan "cache:clear"
```

**Reference**: [Making Artisan Commands Tenant Aware](https://spatie.be/docs/laravel-multitenancy/v4/advanced-usage/making-artisan-commands-tenant-aware) | [Looping Over Tenants](https://spatie.be/docs/laravel-multitenancy/v4/advanced-usage/looping-over-a-collection-of-tenants)

#### **Event System**
- **Tenant-specific events**: Events fired within tenant context
- **Event listeners**: Automatically scoped to tenant
- **Global events**: System-wide events for tenant management

```php
// Tenant-specific events
event(new TenantCreated($tenant));
event(new TenantUpdated($tenant));
event(new TenantDeleted($tenant));
```

**Reference**: [Listening for Events](https://spatie.be/docs/laravel-multitenancy/v4/advanced-usage/listening-for-events)

#### **Custom Tenant Model**
```php
// App\Models\Tenant extends Spatie\Multitenancy\Models\Tenant
class Tenant extends BaseTenant implements Team
{
    use HasSubscription;

    protected $fillable = [
        'name', 'domain', 'subdomain', 'database', 'status'
    ];

    // Custom tenant methods
    public function users() { /* ... */ }
    public function subscription() { /* ... */ }
    public function settings() { /* ... */ }
}
```

**Reference**: [Using Custom Tenant Model](https://spatie.be/docs/laravel-multitenancy/v4/advanced-usage/using-a-custom-tenant-model)

### Key Multi-Tenancy Components
- **Tenant Resolution**: First URL segment identifies tenant
- **Database Switching**: `SwitchTenantDatabaseTask`
- **Settings Switching**: `SwitchTenantSettingsTask`
- **Event Switching**: `SwitchTenantEventTask`
- **Cache Prefixing**: `SwitchTenantCacheTask`
- **Global Scopes**: Models automatically scope to current tenant

### Custom Packages (Platform Directory)
Located in `platform/packages/corbital/`:

1. **corbital/laravel-settings** - Advanced settings management
2. **corbital/module-manager** - Module system management
3. **corbital/laravel-emails** - Email service management
4. **corbital/installer** - Application installer

### Third-Party Packages
- **nwidart/laravel-modules** - Module architecture
- **spatie/laravel-permission** - Role & permission management
- **livewire/livewire** - Frontend reactivity
- **power-components/livewire-powergrid** - Data tables
- **razorpay/razorpay** - Payment gateway
- **stripe/stripe-php** - Payment gateway
- **netflie/whatsapp-cloud-api** - WhatsApp API integration
- **endroid/qr-code** - QR code generation
- **dompdf/dompdf** - PDF generation

## SaaS Features & Multi-Tenancy Capabilities

### **Path-Based Tenancy (Current Implementation)**
- **URL Structure**: `domain.com/tenant1/dashboard`
- **Advantages**:
  - No DNS configuration required
  - Easy to implement and maintain
  - Works with any domain setup
  - Simple tenant identification
- **Implementation**: Custom `PathTenantFinder` class
- **URL Rewriting**: Automatic stripping of tenant prefix
- **Routing**: Tenant-specific route groups

### **Subdomain-Based Tenancy (Future Capability)**
- **URL Structure**: `tenant1.domain.com`
- **Advantages**:
  - Clean URLs without tenant prefix
  - Better SEO for tenant-specific content
  - More professional appearance
  - Easier branding per tenant
- **Requirements**:
  - Wildcard DNS configuration
  - SSL certificate for subdomains
  - Subdomain tenant finder implementation
- **Migration Path**: Can switch from path-based to subdomain-based

### **Tenant Isolation Strategies**

#### **Single Database with Tenant ID**
```php
// All models automatically scoped to tenant
class Contact extends Model
{
    use BelongsToTenant;

    protected $fillable = ['name', 'phone', 'tenant_id'];

    // Automatically filtered by tenant_id
    public function scopeForTenant($query, $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }
}

// Global scope ensures tenant isolation
protected static function booted()
{
    static::addGlobalScope(new TenantScope);
}
```

#### **Database Schema Design**
```sql
-- All tenant-specific tables include tenant_id
CREATE TABLE contacts (
    id BIGINT PRIMARY KEY,
    tenant_id BIGINT NOT NULL,
    name VARCHAR(255),
    phone VARCHAR(20),
    created_at TIMESTAMP,
    updated_at TIMESTAMP,

    INDEX idx_tenant_id (tenant_id),
    FOREIGN KEY (tenant_id) REFERENCES tenants(id)
);
```

### **Multi-Tenancy Task System Deep Dive**

#### **Task Execution Order**
Tasks run sequentially when tenant context changes:

1. **Database Task**: Switch database connection
2. **Settings Task**: Load tenant-specific settings
3. **Cache Task**: Prefix cache keys with tenant ID
4. **Event Task**: Setup tenant-specific event listeners
5. **Route Task**: Switch route cache paths

#### **Custom Task Implementation**
```php
use Spatie\Multitenancy\Tasks\SwitchTenantTask;

class SwitchTenantSettingsTask implements SwitchTenantTask
{
    public function makeCurrent(Tenant $tenant): void
    {
        // Load tenant-specific settings
        $settings = TenantSetting::where('tenant_id', $tenant->id)->get();

        // Register settings in container
        app()->instance('tenant.settings', $settings);
    }

    public function forgetCurrent(): void
    {
        // Clear tenant settings
        app()->forgetInstance('tenant.settings');
    }
}
```

#### **Available Task Types**
- **SwitchTenantDatabaseTask**: Database connection switching
- **SwitchTenantSettingsTask**: Settings management
- **SwitchTenantCacheTask**: Cache isolation
- **SwitchTenantEventTask**: Event system setup
- **SwitchTenantRouteTask**: Route cache management
- **SwitchTenantFilesystemTask**: File storage isolation

### **Tenant-Aware Components**

#### **Models with Tenant Scoping**
```php
// Automatic tenant scoping
class User extends Model
{
    use BelongsToTenant;

    // Global scope automatically applied
    protected static function booted()
    {
        static::addGlobalScope(new TenantScope);
    }
}

// Usage - automatically scoped to current tenant
$users = User::all(); // Only current tenant's users
$contacts = Contact::where('status', 'active')->get(); // Tenant-scoped
```

#### **Queue Jobs**
```php
// Tenant context preserved in jobs
class SendWhatsAppMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle()
    {
        // Automatically runs in correct tenant context
        $tenant = Tenant::current();
        $settings = tenant_settings_by_group('whatsapp');

        // Send message using tenant-specific credentials
    }
}
```

#### **Artisan Commands**
```php
// Tenant-aware commands
php artisan tenant:artisan "migrate" --tenant=1
php artisan tenant:artisan "queue:work" --tenant=1
php artisan tenant:artisan "cache:clear" --tenant=all
```

#### **Event System**
```php
// Tenant-specific events
class TenantCreated
{
    public function __construct(public Tenant $tenant) {}
}

// Event listener
class SetupTenantDefaults
{
    public function handle(TenantCreated $event)
    {
        $event->tenant->execute(function () {
            // Setup default settings, users, etc.
        });
    }
}
```

### **Advanced Multi-Tenancy Features**

#### **Tenant Facades**
```php
// Tenant-specific facades
use Spatie\Multitenancy\Facades\Tenant;

// Get current tenant
$tenant = Tenant::current();

// Check tenant context
if (Tenant::checkCurrent()) {
    // Tenant-specific logic
}

// Execute in tenant context
$tenant->execute(function () {
    // Code runs in tenant context
});
```

#### **Cross-Tenant Operations**
```php
// Execute code for landlord (system-wide)
Tenant::forgetCurrent();

// Execute code for all tenants
Tenant::all()->each(function (Tenant $tenant) {
    $tenant->execute(function () {
        // Code runs for each tenant
    });
});
```

#### **Tenant-Specific Configuration**
```php
// Runtime configuration per tenant
$tenant->execute(function () {
    config(['mail.default' => 'tenant_smtp']);
    config(['cache.prefix' => "tenant_{$tenant->id}"]);
});
```

### **SaaS Subscription Integration**

#### **Tenant Subscription Management**
```php
class Tenant extends BaseTenant
{
    use HasSubscription;

    public function subscription()
    {
        return $this->hasOne(Subscription::class);
    }

    public function isSubscribed(): bool
    {
        return $this->subscription && $this->subscription->active();
    }

    public function canAccess(string $feature): bool
    {
        return $this->subscription->plan->hasFeature($feature);
    }
}
```

#### **Feature Gating**
```php
// Check tenant capabilities
if (tenant()->canAccess('advanced_reporting')) {
    // Show advanced features
}

// Usage limits
if (tenant()->subscription->hasReachedLimit('contacts')) {
    // Block creation of new contacts
}
```

## Directory Structure

```
whatsmark-saas/
├── app/
│   ├── Console/           # Artisan commands
│   ├── Contracts/         # Interface contracts
│   ├── DTOs/              # Data Transfer Objects
│   ├── Enum/              # Enumeration classes
│   ├── Events/            # Event classes
│   ├── Exceptions/        # Custom exceptions
│   ├── Facades/           # Service facades
│   ├── Helpers/           # Helper functions
│   ├── Http/              # Controllers, middleware, requests
│   ├── Jobs/              # Queue jobs
│   ├── Listeners/         # Event listeners
│   ├── Livewire/          # Livewire components
│   ├── Models/            # Eloquent models
│   ├── Multitenancy/      # Multi-tenancy components
│   ├── Notifications/     # Notification classes
│   ├── Observers/         # Model observers
│   ├── Providers/         # Service providers
│   ├── Repositories/      # Repository pattern
│   ├── Rules/             # Validation rules
│   ├── Services/          # Business logic services
│   ├── Settings/          # Settings classes
│   ├── Traits/            # Reusable traits
│   └── View/              # View composers
├── Modules/               # Modular components
│   ├── CacheManager/      # Cache management module
│   ├── EmbeddedSignup/    # Embedded signup module
│   ├── LogViewer/         # Log viewer module
│   ├── SystemInfo/        # System information module
│   └── Tickets/           # Ticket system module
├── platform/packages/    # Custom packages
│   └── corbital/
│       ├── installer/
│       ├── laravel-emails/
│       ├── laravel-settings/
│       └── module-manager/
├── routes/
│   ├── admin/             # Admin routes
│   ├── tenant/            # Tenant routes
│   ├── auth.php           # Authentication routes
│   ├── console.php        # Console routes
│   └── web.php            # Web routes
├── config/
│   ├── custom-saas.php    # SaaS configuration
│   ├── multitenancy.php   # Multi-tenancy settings
│   ├── modules.php        # Module configuration
│   └── settings.php       # Settings configuration
└── database/
    ├── migrations/        # Database migrations
    ├── seeders/           # Database seeders
    └── settings/          # Settings migrations
```

## Settings Management System

### NO .env Dependencies
- **All configuration stored in database settings table**
- **Tenant-specific settings** using `Corbital\Settings` package
- **Admin settings** vs **tenant settings** separation
- **Settings classes** in `app/Settings/`

### Settings Helper Functions
```php
// Get tenant settings by group
tenant_settings_by_group('system')

// Get batch settings
get_batch_settings(['system.timezone', 'system.date_format'])

// Check if tenant exists
Tenant::checkCurrent()
```

### Default Settings Configuration
- Defined in `config/custom-saas.php`
- Includes tenant status, source, and default settings
- Automatic tenant setup with predefined configurations

## Routing Architecture

### Route Structure
- **Admin routes**: `/admin/*` - Super admin functionality
- **Tenant routes**: `/{tenant}/*` - Tenant-specific functionality
- **Authentication routes**: Standard Laravel auth
- **API routes**: For webhooks and integrations

### Middleware Stack
- **SanitizeInputs**: Input sanitization
- **Multi-tenancy middleware**: Automatic tenant resolution
- **Permission middleware**: Role-based access control

## Database Architecture

### Tenant Isolation
- **Single database** with tenant_id columns
- **Automatic scoping** using global scopes
- **Tenant-specific data** completely isolated
- **Shared tables** for system-wide data

### Key Models
- **Tenant**: Main tenant model with subscription management
- **User**: User model with tenant relationships
- **Subscription**: Subscription management
- **PaymentMethod**: Payment method storage

## Module System

### Module Architecture
- **Modular approach** using `nwidart/laravel-modules`
- **Hot-swappable modules** for features
- **Module-specific routes, views, and controllers**
- **Module helper functions** for asset and config management

### Available Modules
- **CacheManager**: Advanced cache management
- **EmbeddedSignup**: Embedded signup forms
- **LogViewer**: System log management
- **SystemInfo**: System information display
- **Tickets**: Support ticket system

## Payment Gateway Integration

### Supported Gateways
- **Stripe**: Full integration with webhooks
- **Razorpay**: Complete payment processing
- **PaymentGatewayInterface**: Contract for new gateways

### Payment Features
- **Subscription billing**
- **Auto-charging**
- **Invoice generation**
- **Webhook processing**
- **Transaction logging**

## WhatsApp Integration

### WhatsApp Cloud API
- **netflie/whatsapp-cloud-api** package
- **Webhook handling** for incoming messages
- **Message sending** capabilities
- **Template management**
- **Media handling**

## Frontend Architecture

### TALL Stack Implementation
- **Livewire components** for dynamic interfaces
- **Alpine.js** for frontend interactivity
- **Tailwind CSS** for styling
- **PowerGrid** for data tables

### Asset Management
- **Vite** for build process
- **Module assets** support
- **Theme system** with JSON configuration
- **CDN support** for production

## Caching Strategy

### Cache Management
- **Tenant-specific caching**
- **Cache invalidation** hooks
- **Redis support** for session and cache
- **Admin cache management** module

### Cache Keys
- **Tenant-scoped** cache keys
- **Settings caching**
- **Query result caching**
- **File-based fallback**

## Development Guidelines

### Code Standards
- **PSR-12** coding standards
- **Laravel best practices**
- **PHPDoc documentation**
- **Type hints** for all methods

### Testing
- **PHPUnit** for unit tests
- **Pest** for feature tests
- **Laravel Pint** for code formatting
- **PHPStan** for static analysis

### Security
- **Input sanitization** middleware
- **CSRF protection**
- **XSS prevention**
- **SQL injection protection**
- **Webhook signature verification**

## Environment Setup

### Requirements
- **PHP 8.3+**
- **MySQL 8.0+**
- **Redis** (optional, for caching)
- **Node.js 18+** (for asset compilation)

### Installation Process
1. **License validation** through installer
2. **Database setup** and migrations
3. **Settings initialization**
4. **Module activation**
5. **Asset compilation**

## API Integration

### WhatsApp Webhooks
- **GET/POST support** for webhook verification
- **Message processing**
- **Status updates**
- **Error handling**

### Payment Webhooks
- **Stripe webhooks** for payment events
- **Razorpay webhooks** for transaction updates
- **Signature verification**
- **Idempotency handling**

## Queue System

### Job Processing
- **Queue jobs** for heavy operations
- **Tenant-aware jobs**
- **Failed job handling**
- **Job retry logic**

### Background Tasks
- **Email sending**
- **WhatsApp message processing**
- **Report generation**
- **Cache warming**

## Notification System

### Multi-Channel Notifications
- **Email notifications**
- **WhatsApp notifications**
- **In-app notifications**
- **Push notifications** (optional)

### Notification Channels
- **Tenant-specific** templates
- **Admin notifications**
- **System alerts**
- **Payment confirmations**

## File Management

### Storage Strategy
- **Tenant-isolated** file storage
- **Multiple disk** support
- **Image optimization**
- **CDN integration**

### File Types
- **Profile images**
- **WhatsApp media**
- **Generated reports**
- **Template files**

## Subscription Management

### Subscription Features
- **Plan management**
- **Billing cycles**
- **Usage tracking**
- **Overage handling**
- **Cancellation management**

### Billing Integration
- **Automatic billing**
- **Invoice generation**
- **Payment retry logic**
- **Dunning management**

## Monitoring & Logging

### Logging Strategy
- **Tenant-specific** logs
- **Payment transaction** logs
- **WhatsApp API** logs
- **System error** logs

### Monitoring
- **Performance monitoring**
- **Error tracking**
- **Usage analytics**
- **System health** checks

## Development Workflow

### Local Development
```bash
# Setup development environment
composer install
npm install
php artisan migrate
php artisan db:seed
npm run dev
```

### Production Deployment
```bash
# Production build
composer install --no-dev --optimize-autoloader
npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Common Development Patterns

### Service Pattern
```php
// Service registration in AppServiceProvider
$this->app->singleton(PusherService::class);
$this->app->singleton(LanguageService::class);
```

### Repository Pattern
```php
// Repository usage for data access
$userRepository = app(UserRepository::class);
$users = $userRepository->findByTenant($tenantId);
```

### Event-Driven Architecture
```php
// Event dispatching
event(new TenantCreated($tenant));
event(new TenantUpdated($tenant));
```

## Helper Functions

### Tenant Helpers
```php
tenant_id()                    // Get current tenant ID
tenant_settings_by_group()     // Get tenant settings
Tenant::checkCurrent()         // Check if tenant context exists
```

### Module Helpers
```php
module_asset()                 // Get module asset URL
module_config()                // Get module configuration
module_exists()                // Check if module exists
module_enabled()               // Check if module is enabled
```

## Configuration Management

### Settings Classes
- **PaymentSettings**: Payment gateway configuration
- **SystemSettings**: System-wide settings
- **TenantSettings**: Tenant-specific settings
- **EmailSettings**: Email configuration

### Configuration Files
- **custom-saas.php**: SaaS-specific configuration
- **multitenancy.php**: Multi-tenancy settings
- **modules.php**: Module system configuration
- **settings.php**: Settings repository configuration

This comprehensive guide covers all aspects of the WhatsApp Marketing SaaS project. Use this as a reference for understanding the architecture, development patterns, and implementation details when working on any feature or requirement.

## Spatie Multi-Tenancy Documentation References

### **Installation & Setup**
- [Using Single Database](https://spatie.be/docs/laravel-multitenancy/v4/installation/using-a-single-database)
- [Determining Current Tenant](https://spatie.be/docs/laravel-multitenancy/v4/installation/determining-current-tenant)

### **Basic Usage**
- [Automatically Determining Current Tenant](https://spatie.be/docs/laravel-multitenancy/v4/basic-usage/automatically-determining-the-current-tenant)
- [Working with Current Tenant](https://spatie.be/docs/laravel-multitenancy/v4/basic-usage/working-with-the-current-tenant)
- [Making Queues Tenant Aware](https://spatie.be/docs/laravel-multitenancy/v4/basic-usage/making-queues-tenant-aware)

### **Advanced Usage**
- [Ensuring Current Tenant Has Been Set](https://spatie.be/docs/laravel-multitenancy/v4/advanced-usage/ensuring-a-current-tenant-has-been-set)
- [Looping Over Collection of Tenants](https://spatie.be/docs/laravel-multitenancy/v4/advanced-usage/looping-over-a-collection-of-tenants)
- [Making Artisan Commands Tenant Aware](https://spatie.be/docs/laravel-multitenancy/v4/advanced-usage/making-artisan-commands-tenant-aware)
- [Using Custom Tenant Model](https://spatie.be/docs/laravel-multitenancy/v4/advanced-usage/using-a-custom-tenant-model)
- [Listening for Events](https://spatie.be/docs/laravel-multitenancy/v4/advanced-usage/listening-for-events)
- [Using Tenant Specific Facades](https://spatie.be/docs/laravel-multitenancy/v4/advanced-usage/using-tenant-specific-facades)
- [Executing Code for Tenants and Landlords](https://spatie.be/docs/laravel-multitenancy/v4/advanced-usage/executing-code-for-tenants-and-landlords)

### **Task System**
- [Creating Your Own Task](https://spatie.be/docs/laravel-multitenancy/v4/using-tasks-to-prepare-the-environment/creating-your-own-task)
- [Switching Databases](https://spatie.be/docs/laravel-multitenancy/v4/using-tasks-to-prepare-the-environment/switching-databases)
- [Switching Route Cache Paths](https://spatie.be/docs/laravel-multitenancy/v4/using-tasks-to-prepare-the-environment/switching-route-cache-paths)
- [Prefixing Cache](https://spatie.be/docs/laravel-multitenancy/v4/using-tasks-to-prepare-the-environment/prefixing-cache)

### **Implementation Notes**
- **Current Implementation**: Single database with path-based tenancy
- **Tenant Isolation**: Using `tenant_id` column with global scopes
- **Task System**: Custom tasks for database, settings, cache, and events
- **Tenant Model**: Extended with subscription management
- **Queue Integration**: Tenant-aware job processing
- **Event System**: Tenant-scoped event handling
- **Artisan Commands**: Tenant-aware command execution

Use these references when implementing or modifying multi-tenancy features in the WhatsApp Marketing SaaS application.
