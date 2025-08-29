# Simplified Hooks-Based License System Installation

## Quick Setup (Minimal Files Required)

### 1. Copy Core Files
Only 4 essential files needed:

```
app/
├── Services/ModuleLicenseManager.php        # Core license manager
├── Hooks/ModuleLicenseHooks.php            # WordPress-style hooks
├── Models/ModuleLicense.php                # Enhanced model with integrity
├── Http/Livewire/ModuleLicenseVerification.php  # UI component
└── Providers/ModuleLicenseServiceProvider.php   # Service registration

database/migrations/
└── 2025_01_01_000000_create_module_licenses_table.php
```

### 2. Register Service Provider
Add to `config/app.php`:

```php
'providers' => [
    // ... existing providers
    App\Providers\ModuleLicenseServiceProvider::class,
],
```

### 3. Run Migration
```bash
php artisan migrate
```

### 4. Add Helper Functions
Add the helper functions to your existing `app/Helpers/helpers.php` file.

### 5. Update Module JSON Files
For modules requiring licenses, add to their `module.json`:

```json
{
    "name": "WhatsAppModule",
    "version": "1.0.0",
    "license_required": true,
    "license_product_id": "12345678",
    "title": "WhatsApp Marketing Module",
    "type": "addon"
}
```

## How It Works

### 🔗 Hook Integration
The system uses WordPress-style hooks that integrate with your existing module manager:

```php
// Before module activation - automatically checks license
do_action('module.before_activate', $moduleName);

// After successful activation
do_action('module.after_activate', $moduleName);

// When license is verified
do_action('module.license.verified', $moduleName, $license, $data);

// When license verification fails
do_action('module.license.failed', $moduleName, $error);
```

### 🔍 Dynamic Module Detection
- Automatically scans `app/Modules/*/module.json` files
- Detects `"license_required": true` setting
- No manual configuration needed
- Works with any number of installed modules

### 🛡️ Security Features
- **Integrity Checking**: Detects database tampering automatically
- **Auto-Deactivation**: Modules are deactivated immediately if tampering detected
- **Encrypted Storage**: Purchase codes stored encrypted
- **Rate Limiting**: Prevents API abuse

### ⚙️ Default Settings (No .env Required)
- Activation enabled by default: `is_active = true`
- Revalidation frequency: 7 days (configurable per module)
- Grace period: 0 days (can be filtered per module)
- Automatic tampering detection and response

## Usage Examples

### Basic Module Activation
```php
// Your existing activation code - no changes needed!
app('module.manager')->activate('WhatsAppModule');

// The hooks system automatically:
// 1. Checks if license is required
// 2. Validates existing license
// 3. Throws exception if invalid
// 4. Proceeds with activation if valid
```

### Check License Status
```php
// Simple helper functions
if (module_has_valid_license('WhatsAppModule')) {
    // Module can be used
}

$status = get_module_license_status('WhatsAppModule');
// Returns: ['exists', 'enabled', 'requires_license', 'has_valid_license', etc.]
```

### Custom License Verification UI
```blade
{{-- In your module management view --}}
@if($module['requires_license'] && !$module['has_valid_license'])
    <livewire:module-license-verification :module-name="$module['name']" />
@endif
```

## Customization with Hooks & Filters

### Add Custom License Requirements
```php
add_filter('module.requires_license', function($requires, $moduleName, $config) {
    // Custom logic to determine license requirement
    if ($moduleName === 'FreeModule') {
        return false; // Never require license for this module
    }
    return $requires;
}, 10, 3);
```

### Custom Grace Periods
```php
add_filter('module.license.grace_period', function($days, $moduleName) {
    return match($moduleName) {
        'CriticalModule' => 0,    // No grace period
        'StandardModule' => 7,    // 7 days grace
        'PremiumModule' => 3,     // 3 days grace
        default => $days
    };
}, 10, 2);
```

### Override Activation Rules
```php
add_filter('module.activation.allowed', function($allowed, $moduleName) {
    // Allow activation in development
    if (app()->environment('local')) {
        return true;
    }

    // Allow for super admins
    if (auth()->user()?->hasRole('super-admin')) {
        return true;
    }

    return $allowed;
}, 10, 2);
```

### Custom Notifications
```php
add_action('module.license.verified', function($moduleName, $license, $data) {
    // Send Slack notification
    // Send email to admin
    // Update dashboard stats
}, 10, 3);

add_action('module.license.expired', function($moduleName, $license) {
    // Send urgent notification
    // Disable related features
}, 10, 2);
```

## Anti-Tampering Features

### Automatic Detection
- Database integrity checking on every license access
- Hash validation of critical license fields
- Automatic module deactivation on tampering detection

### Tampering Response
```php
add_action('module.license.tampered', function($license) {
    // Custom response to tampering
    Log::critical('SECURITY BREACH: License tampering detected', [
        'module' => $license->module_name,
        'ip' => request()->ip(),
        'user' => auth()->id(),
    ]);

    // Send immediate alert
    // Disable user account
    // Lock system if needed
});
```

## Module JSON Configuration

### Example Complete Configuration
```json
{
    "name": "WhatsAppModule",
    "alias": "whatsapp",
    "description": "WhatsApp Marketing and Automation",
    "version": "2.1.0",
    "license_required": true,
    "license_product_id": "12345678",
    "license_grace_period": 7,
    "title": "WhatsApp Marketing Pro",
    "type": "premium",
    "author": "YourCompany",
    "providers": [
        "Modules\\WhatsApp\\Providers\\WhatsAppServiceProvider"
    ]
}
```

## Benefits Over Previous Approach

### Reduced Complexity
- **Before**: 15+ files, complex service dependencies
- **After**: 4 core files, hook-driven behavior
- **Maintenance**: Much easier to extend and modify

### Dynamic Module Support
- No manual registration of modules needed
- Automatically discovers license requirements
- Works with third-party modules
- Self-configuring based on module.json

### Enhanced Security
- Built-in tampering detection
- Automatic integrity validation
- Immediate response to security threats
- Cannot be easily bypassed

### Developer Experience
- Simple hook registration for custom behavior
- Minimal configuration required
- Easy to test and debug
- Clean separation of concerns

## Production Notes

### Performance
- License checks are cached
- Minimal database queries
- Efficient integrity validation
- Background revalidation

### Monitoring
- All events are logged
- Dashboard statistics available
- Notification system for alerts
- Health monitoring included

### Scalability
- Works with unlimited modules
- Efficient batch operations
- Queue-friendly revalidation
- Database optimized with proper indexes

This hooks-based approach provides the same security and functionality as the previous implementation but with significantly reduced complexity and better maintainability.
