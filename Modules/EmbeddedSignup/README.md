# WhatsApp Embedded Signup Module

This module provides WhatsApp Embedded Signup functionality for seamless WABA (WhatsApp Business Account) onboarding through Facebook's authentication flow.

## Features

- **One-Click Setup**: Connect WhatsApp Business Account through Facebook authentication
- **Automatic Configuration**: Automatically sets up WABA, phone numbers, webhooks, and templates
- **Real-time Progress**: Shows setup progress with modern UI/UX
- **Error Handling**: Comprehensive error handling and retry mechanisms
- **Admin Configuration**: Easy admin panel for Facebook app configuration
- **Pluggable Module**: Can be enabled/disabled without affecting the main application

## Installation

1. Copy this folder to `Modules/EmbeddedSignup`
2. Activate the module using `php artisan module:activate EmbeddedSignup`
3. Configure Facebook app credentials in admin panel

## Configuration

### Admin Setup

1. Go to **Admin Panel** > **WhatsApp Settings** > **Embedded Signup**
2. Enable embedded signup
3. Configure Facebook App settings:
   - Facebook App ID
   - Facebook App Secret
   - Facebook Config ID (optional)
   - Webhook Verify Token

### Facebook App Requirements

1. **Create Facebook App**:
   - Go to [Facebook Developers](https://developers.facebook.com/apps/)
   - Create new app with "Business" type
   - Add WhatsApp Business API product

2. **Configure Embedded Signup**:
   - Add Facebook Login for Business product
   - Configure redirect URIs in Facebook Login settings
   - Set up webhook URLs

3. **App Review**:
   - Submit app for review to get advanced access
   - Required permissions: `whatsapp_business_management`, `whatsapp_business_messaging`

## Usage

### For Tenants

1. Navigate to **Settings** > **WhatsApp Setup**
2. If embedded signup is available, click "Connect with Facebook"
3. Complete Facebook authentication flow
4. System automatically configures everything

### For Developers

#### Using the Component

```blade
<x-embedded-signup-button
    :enabled="true"
    :app-id="$facebookAppId"
    :config-id="$facebookConfigId"
    size="default"
/>
```

#### Using the Livewire Component

```blade
@livewire('embedded-signup.tenant.flow')
```

#### JavaScript Integration

```javascript
// Initialize SDK
const sdk = new EmbeddedSignupSDK({
    appId: 'your-app-id',
    configId: 'your-config-id',
    onSuccess: (data) => console.log('Success:', data),
    onError: (error) => console.log('Error:', error)
});

// Launch signup
sdk.launchSignup();
```

## Hooks Integration

The module integrates with the application's hook system:

### Filters

- `tenant_whatsapp_connection_methods`: Adds embedded signup to connection options
- `tenant_waba_setup_options`: Extends WABA setup options
- `admin_whatsapp_settings_tabs`: Adds admin settings tab

### Actions

- `embedded_signup_completed`: Fired when signup completes successfully
- `embedded_signup_failed`: Fired when signup fails
- `waba_assets_configured`: Fired when WABA assets are configured

### Example Hook Usage

```php
// Add custom processing after signup completion
add_action('embedded_signup_completed', function($wabaData, $tokenData, $tenantId) {
    // Custom post-signup processing
    CustomService::processNewWaba($wabaData, $tenantId);
});

// Modify connection methods
add_filter('tenant_whatsapp_connection_methods', function($methods) {
    $methods['custom_method'] = [
        'name' => 'Custom Connection',
        'description' => 'Custom connection method',
        'component' => 'custom.component'
    ];
    return $methods;
});
```

## API Endpoints

### Tenant Endpoints

- `POST /embedded-signup/process` - Process signup completion
- `GET /embedded-signup/availability` - Check availability
- `GET /embedded-signup/configuration` - Get configuration

### Response Format

```json
{
    "success": true,
    "message": "Success message",
    "data": {
        "waba_id": "123456789",
        "phone_number_id": "987654321",
        "display_phone_number": "+1234567890",
        "verified_name": "Business Name"
    }
}
```

## Security Features

- **Encrypted Storage**: App secrets and tokens are encrypted
- **Permission Validation**: Proper permission checks
- **Input Validation**: Comprehensive input validation
- **CSRF Protection**: CSRF token validation
- **Rate Limiting**: Built-in rate limiting

## Troubleshooting

### Common Issues

1. **Facebook App Not Configured**
   - Verify app settings in Facebook Developer Console
   - Check webhook URLs and permissions

2. **Embedded Signup Not Available**
   - Ensure module is activated
   - Check admin configuration
   - Verify Facebook app credentials

3. **Authentication Failures**
   - Check Facebook app status (development vs live)
   - Verify redirect URIs
   - Check app permissions

### Debug Information

Enable debug logging in your `.env`:

```env
LOG_LEVEL=debug
WHATSAPP_LOG_ENABLED=true
```

Check logs in `storage/logs/laravel.log` for detailed error information.

## Development

### File Structure

```
Modules/EmbeddedSignup/
├── Config/config.php              # Module configuration
├── Http/Controllers/              # Controllers
├── Livewire/                      # Livewire components
├── Resources/                     # Views and assets
│   ├── views/                     # Blade templates
│   └── assets/js/                 # JavaScript files
├── Routes/                        # Route definitions
├── Services/                      # Business logic services
└── Providers/                     # Service providers
```

### Testing

```bash
# Test embedded signup availability
curl -X GET /embedded-signup/availability

# Test configuration
curl -X GET /embedded-signup/configuration
```

## Requirements

- Laravel 9+
- Livewire 3+
- Alpine.js
- Valid Facebook App with WhatsApp Business API
- HTTPS enabled domain
- Active WhatsApp Business Account

## License

This module follows the same license as the main application.

## Support

For support and documentation:

- Check the main application documentation
- Review Facebook's Embedded Signup documentation
- Contact support team for assistance

## Changelog

### Version 1.0.0

- Initial release
- Facebook SDK integration
- Livewire components
- Admin configuration panel
- Hooks integration
- Comprehensive error handling
