# SMTP2Go Laravel Mail Transport

[![Tests](https://img.shields.io/github/actions/workflow/status/clinically-au/laravel-smtp2go/run-tests.yml?label=tests)](https://github.com/clinically-au/laravel-smtp2go/actions)
[![Latest Version on Packagist](https://img.shields.io/packagist/v/clinically-au/laravel-smtp2go.svg)](https://packagist.org/packages/clinically-au/laravel-smtp2go)
[![Total Downloads](https://img.shields.io/packagist/dt/clinically-au/laravel-smtp2go.svg)](https://packagist.org/packages/clinically-au/laravel-smtp2go)
[![License](https://img.shields.io/packagist/l/clinically-au/laravel-smtp2go.svg)](https://packagist.org/packages/clinically-au/laravel-smtp2go)

A Laravel Mail transport driver for sending emails via the [SMTP2Go](https://www.smtp2go.com/) API. This package provides seamless integration with Laravel's built-in mail system.

## Features

- ✅ Full Laravel Mail API support
- ✅ Supports HTML and plain text emails
- ✅ File attachments with automatic Base64 encoding
- ✅ CC and BCC recipients
- ✅ Queue integration for background processing
- ✅ Automatic retries on failure
- ✅ Laravel 11 & 12 compatible
- ✅ PHP 8.4+ support
- ✅ Comprehensive test coverage

## Requirements

- PHP 8.4+
- Laravel 11.0+ or 12.0+
- SMTP2Go API key ([Get one here](https://www.smtp2go.com/pricing/))

## Installation

Install the package via Composer:

```bash
composer require clinically-au/laravel-smtp2go
```

The service provider will be automatically registered.

## Configuration

### 1. Add Environment Variables

Add your SMTP2Go API key to your `.env` file:

```env
SMTP2GO_API_KEY=your-api-key-here
```

### 2. Configure Mail Driver

Add the SMTP2Go mailer to your `config/mail.php`:

```php
'mailers' => [
    'smtp2go' => [
        'transport' => 'smtp2go',
    ],

    // ... other mailers
],
```

### 3. Set as Default (Optional)

To use SMTP2Go as your default mailer:

```env
MAIL_MAILER=smtp2go
```

Or in `config/mail.php`:

```php
'default' => env('MAIL_MAILER', 'smtp2go'),
```

### 4. Publish Config (Optional)

If you need to customize the configuration:

```bash
php artisan vendor:publish --tag="smtp2go-config"
```

This will create `config/smtp2go.php`:

```php
return [
    'endpoint' => env('SMTP2GO_ENDPOINT', 'https://api.smtp2go.com/v3'),
    'api_key' => env('SMTP2GO_API_KEY', ''),
];
```

## Usage

### Basic Email Sending

Once configured, use Laravel's Mail facade as normal:

```php
use Illuminate\Support\Facades\Mail;
use App\Mail\WelcomeEmail;

Mail::to('user@example.com')
    ->send(new WelcomeEmail($user));
```

### Using a Specific Mailer

If you have multiple mailers configured:

```php
Mail::mailer('smtp2go')
    ->to('user@example.com')
    ->send(new InvoicePaid($invoice));
```

### Queued Emails

Send emails in the background using Laravel's queue system:

```php
Mail::to($user)
    ->queue(new OrderShipped($order));
```

### Email with CC and BCC

```php
Mail::to('primary@example.com')
    ->cc('manager@example.com')
    ->bcc('archive@example.com')
    ->send(new MonthlyReport($data));
```

### Email with Attachments

```php
use Illuminate\Mail\Mailables\Attachment;

class InvoiceEmail extends Mailable
{
    public function content()
    {
        return new Content(
            view: 'emails.invoice',
        );
    }

    public function attachments()
    {
        return [
            Attachment::fromPath('/path/to/invoice.pdf'),
        ];
    }
}
```

### HTML and Plain Text

```php
class WelcomeEmail extends Mailable
{
    public function content()
    {
        return new Content(
            view: 'emails.welcome.html',
            text: 'emails.welcome.text',
        );
    }
}
```

## Testing

### Package Test Suite

The package includes a comprehensive test suite:

```bash
composer test
```

### Testing with Real SMTP2Go API

To verify the package works with your SMTP2Go account, use the included test script:

```bash
# 1. Configure your API credentials
cp test-app/.env.example test-app/.env
# Edit test-app/.env and add your SMTP2GO_API_KEY

# 2. Run the test
php test-app/send-test-email.php
```

See [test-app/README.md](test-app/README.md) for detailed instructions.

### Testing in Your Application

Use Laravel's mail faking in your tests:

```php
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderConfirmation;

public function test_order_sends_confirmation_email()
{
    Mail::fake();

    // Perform action that sends email
    $this->post('/orders', $orderData);

    // Assert email was sent
    Mail::assertSent(OrderConfirmation::class, function ($mail) {
        return $mail->hasTo('customer@example.com');
    });
}
```

## How It Works

This package implements Laravel's custom mail transport interface by:

1. Extending Symfony's `AbstractTransport` class
2. Converting Laravel mail messages to SMTP2Go API format
3. Sending via SMTP2Go's REST API with Guzzle HTTP client
4. Automatic Base64 encoding of attachments
5. Proper formatting of email addresses (Name <email@example.com>)

All error handling, retries, and queue integration are handled by Laravel's mail system automatically.

## API Documentation

For detailed SMTP2Go API documentation, see:
- [Send Email Endpoint](https://developers.smtp2go.com/reference/send-standard-email)
- [API Reference](https://developers.smtp2go.com/)

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Contributions are welcome! Please see our [contributing guidelines](CONTRIBUTING.md) for details.

## Security

If you discover any security-related issues, please email wojt@clinically.com.au instead of using the issue tracker.

## Credits

- [Wojt Janowski](https://github.com/clinically-au)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
