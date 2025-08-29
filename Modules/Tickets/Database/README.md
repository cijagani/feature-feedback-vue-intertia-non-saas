# Tickets Module - Factories & Seeders

This document describes the comprehensive factories and seeders created for the Tickets module database structure.

## Overview

The Tickets module includes the following database tables and their corresponding factories/seeders:
- `departments` → `DepartmentFactory` & `DepartmentSeeder`
- `department_translations` → `DepartmentTranslationFactory`
- `tickets` → `TicketFactory` & `TicketSeeder`
- `ticket_replies` → `TicketReplyFactory`

## Database Structure

### Tables
1. **departments**: Core department data
2. **department_translations**: Multi-language support for departments
3. **tickets**: Main ticket records with relationships to users and departments
4. **ticket_replies**: Reply messages for tickets with user associations

### Relationships
- `Ticket` belongs to `Department` and `User` (client)
- `TicketReply` belongs to `Ticket` and `User`
- `DepartmentTranslation` belongs to `Department`

## Factories

### DepartmentFactory
Located: `database/factories/DepartmentFactory.php`

**Features:**
- Creates realistic department names (Technical Support, Billing, Sales, etc.)
- 85% chance of being active by default
- Predefined states: `active()`, `inactive()`, `technical()`, `billing()`, `sales()`, `general()`

**Usage:**
```php
// Basic department
$department = Department::factory()->create();

// Active technical department
$department = Department::factory()->technical()->active()->create();

// Multiple departments
$departments = Department::factory(5)->create();
```

### DepartmentTranslationFactory
Located: `Modules/Tickets/Database/Factories/DepartmentTranslationFactory.php`

**Features:**
- Supports multiple languages (en, es, fr, de, it, pt)
- Realistic translations for common department names
- Can create translations for specific departments

**Usage:**
```php
// Random translation
$translation = DepartmentTranslation::factory()->create();

// Spanish translation for specific department
$translation = DepartmentTranslation::factory()
    ->forDepartment($department, 'es')
    ->create();

// Multiple translations for one department
$translations = DepartmentTranslation::factory(3)
    ->forDepartment($department)
    ->create();
```

### TicketFactory
Located: `Modules/Tickets/Database/Factories/TicketFactory.php`

**Features:**
- Generates unique ticket IDs (TKT-XXXXXX format)
- Realistic subjects and content
- Proper priority and status distributions
- Optional attachments with realistic file data
- Multiple states for different ticket conditions

**States:**
- `open()` - New, unresolved tickets
- `closed()` - Resolved tickets
- `pending()` - Awaiting client response
- `answered()` - Admin replied, awaiting client
- `onHold()` - Temporarily paused
- `highPriority()` / `lowPriority()` - Priority levels
- `withAttachments()` / `withoutAttachments()` - File attachments

**Usage:**
```php
// Basic ticket
$ticket = Ticket::factory()->create();

// Open high-priority ticket with attachments
$ticket = Ticket::factory()
    ->open()
    ->highPriority()
    ->withAttachments()
    ->create();

// Closed ticket for specific department
$ticket = Ticket::factory()
    ->closed()
    ->create(['department_id' => $department->id]);
```

### TicketReplyFactory
Located: `Modules/Tickets/Database/Factories/TicketReplyFactory.php`

**Features:**
- Creates replies from admin, client, or system users
- Realistic content based on user type
- Optional attachments
- Viewed/unviewed states

**States:**
- `fromAdmin()` - Administrative responses
- `fromClient()` - Client responses
- `fromSystem()` - Automated system messages
- `withAttachments()` - Include file attachments
- `viewed()` / `unviewed()` - Read status

**Usage:**
```php
// Basic reply
$reply = TicketReply::factory()->create();

// Admin reply with attachments
$reply = TicketReply::factory()
    ->fromAdmin()
    ->withAttachments()
    ->viewed()
    ->create(['ticket_id' => $ticket->id]);

// Multiple replies for conversation
$replies = TicketReply::factory(5)
    ->sequence(
        ['user_type' => 'client'],
        ['user_type' => 'admin'],
    )
    ->create(['ticket_id' => $ticket->id]);
```

## Seeders

### DepartmentSeeder
Located: `Modules/Tickets/Database/Seeders/DepartmentSeeder.php`

**Features:**
- Creates 10 predefined departments with realistic names
- Includes translations in 6 languages (en, es, fr, de, it, pt)
- All departments active by default
- Uses `updateOrCreate()` to prevent duplicates

**Departments Created:**
- Technical Support
- Billing & Payments
- General Inquiries
- Sales
- Account Management
- Bug Reports
- Feature Requests
- Customer Success
- Pre-Sales
- Product Support

### TicketUserSeeder
Located: `Modules/Tickets/Database/Seeders/TicketUserSeeder.php`

**Features:**
- Creates admin users for ticket support
- Creates client users for testing
- Ensures minimum user requirements are met
- Uses realistic email addresses and names

### TicketSeeder
Located: `Modules/Tickets/Database/Seeders/TicketSeeder.php`

**Features:**
- Creates 100 tickets with realistic distribution:
  - 30 open tickets (0-3 replies each)
  - 15 pending tickets (1-5 replies each)
  - 20 answered tickets (1-4 replies each)
  - 25 closed tickets (2-8 replies each)
  - 10 on-hold tickets (1-3 replies each)
- Generates department-specific subjects and content
- Creates realistic conversation flows
- Includes system-generated replies
- Proper priority distribution (10% high, 25% medium, 65% low)

### TicketsTestSeeder
Located: `Modules/Tickets/Database/Seeders/TicketsTestSeeder.php`

**Features:**
- Tests all factories to ensure they work correctly
- Creates sample data for each factory type
- Provides detailed output of what was created
- Useful for development and debugging

### TicketsDatabaseSeeder
Located: `Modules/Tickets/Database/Seeders/TicketsDatabaseSeeder.php`

**Features:**
- Main seeder that orchestrates all other seeders
- Runs seeders in correct dependency order
- Provides progress feedback

## Usage Instructions

### Running All Seeders
```bash
# Run the complete Tickets module seeding
php artisan db:seed --class="Modules\Tickets\Database\Seeders\TicketsDatabaseSeeder"
```

### Running Individual Seeders
```bash
# Just departments and translations
php artisan db:seed --class="Modules\Tickets\Database\Seeders\DepartmentSeeder"

# Just users
php artisan db:seed --class="Modules\Tickets\Database\Seeders\TicketUserSeeder"

# Just tickets and replies
php artisan db:seed --class="Modules\Tickets\Database\Seeders\TicketSeeder"

# Test factories
php artisan db:seed --class="Modules\Tickets\Database\Seeders\TicketsTestSeeder"
```

### Using Factories in Tests
```php
// In your test files
use Modules\Tickets\Models\Ticket;
use App\Models\Department;

public function test_ticket_creation()
{
    $department = Department::factory()->technical()->create();

    $ticket = Ticket::factory()
        ->open()
        ->highPriority()
        ->create(['department_id' => $department->id]);

    $this->assertEquals('open', $ticket->status);
    $this->assertEquals('high', $ticket->priority);
}
```

### Creating Realistic Test Scenarios
```php
// Complete ticket conversation
$ticket = Ticket::factory()->open()->create();

// Client creates ticket
$clientReply = TicketReply::factory()
    ->fromClient()
    ->create(['ticket_id' => $ticket->id]);

// Admin responds
$adminReply = TicketReply::factory()
    ->fromAdmin()
    ->create(['ticket_id' => $ticket->id]);

// Client confirms resolution
$resolution = TicketReply::factory()
    ->fromClient()
    ->create([
        'ticket_id' => $ticket->id,
        'content' => 'Thank you, this resolves my issue!'
    ]);

// Close ticket
$ticket->update(['status' => 'closed']);
```

## Sample Data Generated

After running the complete seeder, you will have:
- **10 departments** with full translations
- **50+ users** (admin and client accounts)
- **100 tickets** with realistic distribution
- **200+ ticket replies** creating conversation flows
- **Realistic attachments** metadata
- **Proper relationships** between all entities

## File Structure
```
Modules/Tickets/Database/
├── Factories/
│   ├── DepartmentTranslationFactory.php
│   ├── TicketFactory.php (updated)
│   └── TicketReplyFactory.php (updated)
├── Seeders/
│   ├── DepartmentSeeder.php
│   ├── TicketUserSeeder.php
│   ├── TicketSeeder.php
│   ├── TicketsTestSeeder.php
│   └── TicketsDatabaseSeeder.php
└── Migrations/ (existing)

database/factories/
└── DepartmentFactory.php
```

## Notes

1. **Dependencies**: The seeders handle user creation automatically, but ensure migrations are run first
2. **Relationships**: All foreign key relationships are properly maintained
3. **Realistic Data**: Content is generated based on department types for authenticity
4. **Multilingual**: Department translations support international deployments
5. **Testing**: Use `TicketsTestSeeder` to verify all factories work correctly
6. **Customization**: All factories support custom states and parameters for specific testing needs

## Troubleshooting

If you encounter issues:

1. **Missing Users**: Run `TicketUserSeeder` first or ensure users exist
2. **Missing Departments**: Run `DepartmentSeeder` before `TicketSeeder`
3. **Foreign Key Errors**: Ensure migrations are up to date
4. **Factory Errors**: Run `TicketsTestSeeder` to identify issues

## Integration with Main Application

To integrate with your main `DatabaseSeeder`:

```php
// In database/seeders/DatabaseSeeder.php
public function run(): void
{
    // ... existing seeders ...

    $this->call([
        \Modules\Tickets\Database\Seeders\TicketsDatabaseSeeder::class,
    ]);
}
```
