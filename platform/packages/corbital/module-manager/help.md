# Corbital Module Manager

A powerful Laravel module management system that extends nwidart/laravel-modules with additional features like module dependencies, core/addon module types, and a beautiful TALL stack admin interface.

## Features

- **Extended Module Types**: Distinguish between core modules (cannot be deactivated or removed) and addon modules
- **Dependency Management**: Enforce module dependencies with semantic versioning support
- **Conflict Resolution**: Define conflicts between modules to prevent incompatible modules from running together
- **Web Interface**: Beautiful TALL stack (Tailwind, Alpine.js, Laravel, Livewire) admin interface for module management
- **Event System**: Powerful event hooks system to extend functionality
- **Console Commands**: Comprehensive set of artisan commands for module management
- **Module Creation**: Easy scaffolding for new modules

## Requirements

- PHP 8.1+
- Laravel 12+
- Livewire 3+

## Installation

### 1. Install via Composer

```bash
composer require corbital/module-manager
```

### 2. Publish Configuration

```bash
php artisan vendor:publish --provider="Corbital\ModuleManager\Providers\ModuleManagerServiceProvider" --tag="config"
```

### 3. Run Migrations

```bash
php artisan migrate
```

### 4. Add Service Provider (Optional - Laravel 12+ auto-discovers packages)

If you're using Laravel < 12, add the service provider to your `config/app.php`:

```php
'providers' => [
    // ...
    Corbital\ModuleManager\Providers\ModuleManagerServiceProvider::class,
],
```

### 5. Publish Assets (Optional)

```bash
php artisan vendor:publish --provider="Corbital\ModuleManager\Providers\ModuleManagerServiceProvider" --tag="assets"
```

### 6. Publish Views (Optional)

```bash
php artisan vendor:publish --provider="Corbital\ModuleManager\Providers\ModuleManagerServiceProvider" --tag="views"
```

## Basic Usage

### Creating Modules

Use the provided artisan command to create a new module:

```bash
php artisan module:make MyModule
```

To create a core module:

```bash
php artisan module:make MyModule --type=core
```

### Managing Modules

Activate a module:

```bash
php artisan module:activate MyModule
```

Deactivate a module:

```bash
php artisan module:deactivate MyModule
```

List all modules:

```bash
php artisan module:list
```

Remove a module:

```bash
php artisan module:remove MyModule
```

### Web Interface

The package provides a web interface to manage modules. Access it at `/modules` route (make sure to protect this route with appropriate middleware in your application).

## Module Structure

Each module follows this structure:

```
ModuleName/
├── module.json             # Module manifest file
├── Config/                 # Module configuration
├── Console/                # Console commands
├── Database/               # Migrations, seeders, and factories
├── Http/                   # Controllers, middleware, requests
├── Models/                 # Eloquent models
├── Providers/              # Service providers
├── resources/              # Views, language files, and assets
├── Routes/                 # Route definitions
└── Tests/                  # Module tests
```

## Module Manifest

The `module.json` file contains metadata about your module:

```json
{
    "name": "MyModule",
    "alias": "my-module",
    "namespace": "App\\Modules\\MyModule\\",
    "provider": "App\\Modules\\MyModule\\Providers\\MyModuleServiceProvider",
    "author": "Your Name",
    "url": "https://example.com",
    "version": "1.0.0",
    "description": "Description of what the module does",
    "require": {
        "CoreModule": ">=1.0.0",
        "AnotherModule": "^2.0.0"
    },
    "conflicts": [
        "IncompatibleModule >=2.0.0"
    ],
    "type": "addon"
}
```

## Dependency Management

### Specifying Dependencies

In your `module.json` file, specify dependencies using the `require` property:

```json
"require": {
    "CoreModule": ">=1.0.0",
    "AnotherModule": "^2.0.0"
}
```

Supported version constraints:

- Exact version: `1.2.3`
- Greater than: `>1.2.3`
- Greater than or equal: `>=1.2.3`
- Less than: `<1.2.3`
- Less than or equal: `<=1.2.3`
- Range: `>=1.2.3 <2.0.0` (space-separated)
- Caret range: `^1.2.3` (>= 1.2.3 < 2.0.0)
- Tilde range: `~1.2.3` (>= 1.2.3 < 1.3.0)
- Wildcard: `1.2.*` (>= 1.2.0 < 1.3.0)

### Specifying Conflicts

You can also specify modules that conflict with yours:

```json
"conflicts": [
    "IncompatibleModule >=2.0.0"
]
```

## Events System

Register event listeners in your module's `registerHooks` method:

```php
public function registerHooks()
{
    // Simple event listener
    ModuleEvents::listen('content.rendered', function ($content) {
        return $content . ' [Modified by MyModule]';
    });

    // Event listener with priority (higher numbers run first)
    ModuleEvents::listen('content.rendered', function ($content) {
        return '[MyModule prefix] ' . $content;
    }, 20);
}
```

Fire events from your module or application:

```php
use Corbital\ModuleManager\Facades\ModuleEvents;

// Fire an event with a single parameter
$modifiedContent = ModuleEvents::fire('content.rendered', $content);

// Fire an event with multiple parameters (returned as array)
list($modifiedContent, $metadata) = ModuleEvents::fire('content.rendered', [$content, $metadata]);
```

## Module Lifecycle Methods

Each module can implement these lifecycle methods:

```php
// Called when the module is being activated
public function activate()
{
    // Setup code here
}

// Called after the module has been activated
public function activated()
{
    // Post-activation code here
}

// Called when the module is being deactivated
public function deactivate()
{
    // Cleanup code here
}

// Called after the module has been deactivated
public function deactivated()
{
    // Post-deactivation code here
}
```

## Extending the Package

### Custom Module Hooks

You can register custom hooks for your modules to interact with:

```php
// In your service provider
ModuleEvents::listen('my-app.init', function ($app) {
    // Do something when your app initializes
    return $app;
});

// Then in your application
$app = ModuleEvents::fire('my-app.init', $app);
```

### Storage and Settings

The package provides a migration for a `module_settings` table that you can use to store module-specific settings.

## Configuration

The published configuration file contains these options:

```php
return [
    // Directory where modules are stored
    'directory' => app_path('Modules'),

    // Namespace for modules
    'namespaces' => [
        'modules' => 'App\\Modules',
        'controllers' => 'App\\Modules\\{module}\\Http\\Controllers',
        // ...
    ],

    // List of active modules
    'active' => [
        // Modules to auto-load
    ],

    // Module type configurations
    'types' => [
        'core' => [
            'can_deactivate' => false,
            'can_remove' => false,
        ],
        'addon' => [
            'can_deactivate' => true,
            'can_remove' => true,
        ],
    ],

    // Additional configuration...
];
```

## Differences from nwidart/laravel-modules

This package extends nwidart/laravel-modules with:

1. **Module Types**: Added support for core and addon modules with different restrictions
2. **Dependencies & Conflicts**: Enhanced dependency management with semantic versioning
3. **Web Interface**: Beautiful TALL stack admin interface for module management
4. **Event System**: More flexible event hooks system
5. **Enhanced Commands**: Improved artisan commands with more functionality
6. **Module Settings**: Built-in support for module-specific settings storage

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
