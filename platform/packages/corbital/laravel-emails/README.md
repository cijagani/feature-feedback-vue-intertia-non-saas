# Laravel Emails

A comprehensive email sending and template management package for Laravel applications. This package provides a clean, easy-to-use interface for managing email templates, sending emails, tracking engagement, and monitoring email logs.

![Laravel Emails](https://via.placeholder.com/728x90.png?text=Laravel+Emails+Package)

## Features

- 📧 **Dynamic Email Templates**: Store and manage email templates with variable substitution
- 📊 **Email Logging**: Track all sent emails with detailed logs
- 📈 **Email Tracking**: Monitor email opens and clicks with integrated tracking pixels and link redirects
- 🖌️ **WYSIWYG Editor**: Create beautiful email templates with the integrated editor
- 🔄 **Queue Support**: Send emails using Laravel's queue system for better performance
- 📅 **Email Scheduling**: Schedule emails to be sent at specific times
- ⚙️ **Settings Management**: Manage email settings using the Spatie Settings package
- 🔒 **Rate Limiting**: Prevent email abuse with configurable rate limits
- 🧪 **Test Interface**: Test emails before sending them to users
- 📱 **Admin UI**: Manage everything from a clean Tailwind CSS & Livewire interface

## Requirements

- PHP 8.2+
- Laravel 11.x
- Spatie Settings package
- Queue driver configured (for queued emails)

## Installation

### 1. Install the package via Composer

```bash
composer require corbital/laravel-emails
```

### 2. Publish the configuration and migrations

```bash
php artisan vendor:publish --tag=laravel-emails-config
php artisan vendor:publish --tag=laravel-emails-migrations
```

### 3. Run the migrations

```bash
php artisan migrate
```

### 4. Create the email settings

```bash
php artisan settings:migrate
```

### 5. Seed default email templates (optional)

```bash
php artisan email:seed-templates
```

### 6. Configure your mail settings in .env

```
MAIL_MAILER=smtp
MAIL_HOST=smtp.example.com
MAIL_PORT=587
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=from@example.com
MAIL_FROM_NAME="${APP_NAME}"
```

## Email Layouts

Laravel Emails supports a dynamic template system with centralized header and footer management through layouts.

### How Layouts Work

1. **Email Layouts** store header, footer, and master template structure
2. **Email Templates** can use a specific layout or fall back to the default layout
3. The layout system uses placeholder variables to combine templates and layouts seamlessly

### Layout Structure

Email layouts consist of three main components:

- **Header**: Common content that appears at the top of every email (logos, navigation, etc.)
- **Footer**: Common content that appears at the bottom of every email (contact info, social links, etc.)
- **Master Template**: The structure that combines header, body content, and footer

The master template uses these placeholders:

- `{HEADER}`: Replaced with the header content
- `{CONTENT}`: Replaced with the email template's content
- `{FOOTER}`: Replaced with the footer content

Additionally, layouts can define their own variables that will be available in the header, footer, and master template.

### Managing Layouts

Layouts can be managed through the admin UI:

```
/admin/emails/layouts - Email layouts management
```

Or programmatically:

```php
use Corbital\LaravelEmails\Models\EmailLayout;

// Create a layout
$layout = EmailLayout::create([
    'name' => 'Responsive Layout',
    'slug' => 'responsive',
    'header' => '<div class="header"><img src="{{company_logo}}" alt="{{company_name}}"></div>',
    'footer' => '<div class="footer">© {{year}} {{company_name}}</div>',
    'master_template' => '<!DOCTYPE html><html><head><meta charset="utf-8"></head><body>{HEADER}<div class="content">{CONTENT}</div>{FOOTER}</body></html>',
    'variables' => ['company_name', 'company_logo', 'year'],
    'is_active' => true,
    'is_default' => true,
]);

// Find a layout
$layout = EmailLayout::where('slug', 'responsive')->first();

// Use a layout with template content
$html = $layout->render($templateContent, [
    'company_name' => 'ACME Inc',
    'company_logo' => 'https://example.com/logo.png',
    'year' => date('Y')
]);
```

## Basic Usage

### Send an email using a template

```php
use Corbital\LaravelEmails\Facades\Email;

// Using the facade
Email::to('recipient@example.com')
    ->subject('Welcome to Our App') // Optional, will use template subject if not provided
    ->template('welcome', [
        'user_name' => 'John Doe',
        'verification_url' => 'https://example.com/verify'
    ])
    ->send();

// Using helper function
send_email(
    'recipient@example.com',
    'Welcome to Our App',
    'welcome',
    [
        'user_name' => 'John Doe',
        'verification_url' => 'https://example.com/verify'
    ]
);
```

### Send direct content (without a template)

```php
Email::to('recipient@example.com')
    ->subject('Important Notice')
    ->content('<h1>Hello!</h1><p>This is a direct HTML email.</p>')
    ->send();
```

### Send with CC, BCC, and Reply-To

```php
Email::to('recipient@example.com')
    ->cc(['cc1@example.com', 'cc2@example.com'])
    ->bcc('bcc@example.com')
    ->replyTo('reply-to@example.com')
    ->template('notification', [
        'message' => 'This is a test message'
    ])
    ->send();
```

### Send with attachments

```php
Email::to('recipient@example.com')
    ->template('invoice', [
        'invoice_number' => 'INV-001'
    ])
    ->attach('/path/to/invoice.pdf', [
        'as' => 'invoice.pdf',
        'mime' => 'application/pdf'
    ])
    ->send();

// Multiple attachments
Email::to('recipient@example.com')
    ->template('documents')
    ->attachments([
        '/path/to/document1.pdf',
        [
            'path' => '/path/to/document2.docx',
            'options' => [
                'as' => 'report.docx',
                'mime' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
            ]
        ]
    ])
    ->send();
```

### Queue emails

```php
// Queue email for sending (using default queue)
Email::to('recipient@example.com')
    ->template('welcome', ['name' => 'John'])
    ->queue(true)
    ->send();

// Send immediately, bypassing the queue
Email::to('recipient@example.com')
    ->template('welcome', ['name' => 'John'])
    ->queue(false)
    ->send();
```

### Schedule emails

```php
// Schedule an email for a specific time
Email::to('recipient@example.com')
    ->template('reminder', ['event' => 'Meeting'])
    ->later(now()->addHours(2))
    ->send();
```

### Track email engagement

```php
// Enable tracking for opens and clicks
$settings = app(\Corbital\LaravelEmails\Settings\EmailSettings::class);
$settings->track_opens = true;
$settings->track_clicks = true;
$settings->save();

// Send email with tracking automatically applied
Email::to('recipient@example.com')
    ->template('marketing-campaign', [
        'product_name' => 'Amazing Product',
        'discount_code' => 'SALE20'
    ])
    ->send();
```

## Advanced Usage

### Using the HasEmailNotifications trait

Add the trait to your models to easily send emails to them:

```php
use Corbital\LaravelEmails\Traits\HasEmailNotifications;

class User extends Authenticatable
{
    use HasEmailNotifications;

    // ...
}
```

Then you can send emails directly from the model:

```php
$user->sendEmailNotification('welcome', [
    'name' => $user->name
]);

// Or schedule an email
$user->scheduleEmailNotification('reminder',
    now()->addDays(7),
    ['appointment_date' => '2025-03-18 10:00 AM']
);
```

### Using the WYSIWYG Editor

You can create and edit templates using the built-in Livewire-powered WYSIWYG editor:

```php
// Route to create a new template with the editor
Route::get('/admin/emails/templates/create/editor', \Corbital\LaravelEmails\Http\Livewire\TemplateEditor::class)
    ->name('laravel-emails.templates.create-editor');

// Route to edit an existing template with the editor
Route::get('/admin/emails/templates/{template}/editor', \Corbital\LaravelEmails\Http\Livewire\TemplateEditor::class)
    ->name('laravel-emails.templates.editor');
```

### Managing Email Templates

You can manage email templates through the admin UI or programmatically:

```php
use Corbital\LaravelEmails\Models\EmailTemplate;
use Corbital\LaravelEmails\Models\EmailLayout;

// Create a template with a specific layout
$layout = EmailLayout::where('slug', 'responsive')->first();

EmailTemplate::create([
    'name' => 'Password Reset',
    'slug' => 'password-reset',
    'subject' => 'Reset Your Password for {{app_name}}',
    'content' => '<h1>Hello {{name}},</h1><p>Click the link below to reset your password:</p><p><a href="{{reset_url}}">Reset Password</a></p>',
    'variables' => ['name', 'reset_url'],
    'is_active' => true,
    'layout_id' => $layout->id,
]);

// Create a template using the default layout
EmailTemplate::create([
    'name' => 'Account Verification',
    'slug' => 'account-verification',
    'subject' => 'Verify Your {{app_name}} Account',
    'content' => '<h1>Welcome, {{name}}!</h1><p>Please verify your account by clicking the link below:</p><p><a href="{{verification_url}}">Verify Account</a></p>',
    'variables' => ['name', 'verification_url'],
    'is_active' => true,
    // No layout_id specified - will use the default layout
]);

// Find a template
$template = EmailTemplate::where('slug', 'welcome')->first();

// Render a template preview (will use its associated layout)
$html = $template->renderContent([
    'name' => 'John Doe',
    'verification_url' => 'https://example.com/verify',
    'company_name' => 'ACME Inc', // Layout variable
    'company_logo' => 'https://example.com/logo.png', // Layout variable
]);

// Get the associated layout for a template
$layout = $template->layout;
```

## Configuration

### Email Settings

Email settings are managed through the Spatie Settings package. You can modify settings in your code:

```php
use Corbital\LaravelEmails\Settings\EmailSettings;

$settings = app(EmailSettings::class);

// Update sender info
$settings->sender_name = 'Your Company';
$settings->sender_email = 'no-reply@example.com';

// Configure rate limiting
$settings->enable_rate_limiting = true;
$settings->max_emails_per_minute = 20;
$settings->max_emails_per_hour = 100;
$settings->max_emails_per_day = 1000;

// Enable tracking
$settings->track_opens = true;
$settings->track_clicks = true;

// Save the settings
$settings->save();
```

### Configuration File

You can customize the package behavior in the `config/laravel-emails.php` file:

```php
return [
    'enable_logging' => env('EMAIL_LOGGING_ENABLED', true),
    'logs_retention_days' => env('EMAIL_LOGS_RETENTION_DAYS', 30),

    'rate_limiting' => [
        'enabled' => env('EMAIL_RATE_LIMITING_ENABLED', false),
        'max_per_minute' => env('EMAIL_MAX_PER_MINUTE', 10),
        'max_per_hour' => env('EMAIL_MAX_PER_HOUR', 100),
        'max_per_day' => env('EMAIL_MAX_PER_DAY', 1000),
    ],

    'throw_exceptions' => env('EMAIL_THROW_EXCEPTIONS', false),

    'admin_route' => [
        'enabled' => env('EMAIL_ADMIN_ROUTE_ENABLED', true),
        'prefix' => env('EMAIL_ADMIN_ROUTE_PREFIX', 'admin/emails'),
        'middleware' => ['web', 'auth'],
    ],

    'queue' => [
        'connection' => env('EMAIL_QUEUE_CONNECTION', env('QUEUE_CONNECTION', 'sync')),
        'default_queue' => env('EMAIL_QUEUE_NAME', 'emails'),
    ],

    // Default variables available in all templates
    'default_variables' => [
        'app_name' => config('app.name'),
        'app_url' => config('app.url'),
    ],

    // Email tracking options
    'tracking' => [
        'pixel_enabled' => env('EMAIL_TRACKING_PIXEL_ENABLED', true),
        'links_enabled' => env('EMAIL_TRACKING_LINKS_ENABLED', true),
    ],
];
```

## Admin UI

The package includes an admin UI for managing email templates, layouts, and logs. The UI is accessible at:

```
/admin/emails/templates - Email templates management
/admin/emails/layouts - Email layouts management
/admin/emails/logs - Email logs viewer
/admin/emails/test - Test email sending
/admin/emails/settings - Email settings configuration
```

You can customize the route prefix and middleware in the config file.

## Artisan Commands

The package provides several helpful Artisan commands:

```bash
# Send a test email
php artisan email:send-test --template=welcome --to=user@example.com

# Clear old email logs
php artisan email:clear-logs --days=30

# Process scheduled emails
php artisan email:process-scheduled

# Seed default email templates
php artisan email:seed-templates

# Initialize email settings
php artisan email:init-settings
```

For scheduled emails, add this to your `app/Console/Kernel.php`:

```php
protected function schedule(Schedule $schedule)
{
    // Process scheduled emails every minute
    $schedule->command('email:process-scheduled')->everyMinute();

    // Clear old logs weekly
    $schedule->command('email:clear-logs')->weekly();
}
```

## Events

The package dispatches events for various email actions:

- `EmailSent`: Dispatched when an email is successfully sent
- `EmailFailed`: Dispatched when an email fails to send
- `EmailScheduled`: Dispatched when an email is scheduled for later delivery
- `EmailTemplateCreated`, `EmailTemplateUpdated`, `EmailTemplateDeleted`: Dispatched for template actions
- `EmailOpened`: Dispatched when a tracking pixel is loaded (email opened)
- `EmailLinkClicked`: Dispatched when a tracked link is clicked

You can listen for these events in your application to perform additional actions.

## Troubleshooting

### Emails Not Sending

1. Check your SMTP settings
2. Make sure your queue worker is running: `php artisan queue:work`
3. Check the Laravel logs at `storage/logs/laravel.log`
4. Try using the `log` mail driver for testing: `MAIL_MAILER=log`

### Template Not Found

Make sure the template exists in your database:

```php
// Check if template exists
$template = \Corbital\LaravelEmails\Models\EmailTemplate::where('slug', 'welcome')->first();
dd($template);

// Create it if it doesn't
if (!$template) {
    \Corbital\LaravelEmails\Models\EmailTemplate::create([
        'name' => 'Welcome Email',
        'slug' => 'welcome',
        'subject' => 'Welcome to {{app_name}}',
        'content' => '<h1>Welcome {{name}}!</h1><p>Thank you for joining us.</p>',
        'is_active' => true,
    ]);
}
```

### Rate Limiting Issues

If you're testing many emails at once, you might hit rate limits:

```php
// Temporarily disable rate limiting
$settings = app(\Corbital\LaravelEmails\Settings\EmailSettings::class);
$settings->enable_rate_limiting = false;
$settings->save();
```

### Tracking Not Working

Make sure your tracking routes are registered:

```php
// Check the routes
php artisan route:list --name=tracking

// Make sure the settings are enabled
$settings = app(\Corbital\LaravelEmails\Settings\EmailSettings::class);
$settings->track_opens = true;
$settings->track_clicks = true;
$settings->save();
```

## Customization

### Custom Email Template

You can create your own email templates that extend the package's base templates:

1. Publish the package views:

```bash
php artisan vendor:publish --tag=laravel-emails-views
```

2. Modify the templates in `resources/views/vendor/laravel-emails/`

## License

This package is open-sourced software licensed under the MIT license.
