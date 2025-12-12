# SMTP2Go Laravel Transport - Test Application

This directory contains a minimal test script to verify the SMTP2Go Laravel Mail Transport package works correctly with the real SMTP2Go API.

## Prerequisites

1. **SMTP2Go Account** - Sign up at https://www.smtp2go.com/pricing/
2. **API Key** - Generate one in your SMTP2Go dashboard
3. **Verified Sender** - The "from" email address must be verified in SMTP2Go

## Quick Start

### 1. Configure Environment

```bash
# Copy the example environment file
cp .env.example .env

# Edit .env and add your credentials
nano .env
```

Required variables:
```env
SMTP2GO_API_KEY=your-actual-api-key-here
TEST_EMAIL_TO=your-email@example.com
```

Optional variables (with defaults):
```env
TEST_EMAIL_FROM=noreply@example.com      # Must be verified in SMTP2Go!
TEST_EMAIL_FROM_NAME=SMTP2Go Test
SMTP2GO_ENDPOINT=https://api.smtp2go.com/v3
```

### 2. Run the Test

```bash
# From the package root directory
php test-app/send-test-email.php
```

### 3. Check Results

You should see output like:
```
🚀 SMTP2Go Laravel Transport - Test Email
==========================================

Configuration:
  From: SMTP2Go Test <noreply@example.com>
  To: your-email@example.com
  API Key: api-ABC123...

📧 Sending test email...

✅ SUCCESS! Email sent successfully!

📬 Check your inbox at: your-email@example.com
   (Don't forget to check spam folder)

🎉 The SMTP2Go Laravel Transport package is working correctly!
```

Then check your email inbox for the test message.

## What Does This Test?

The test script verifies:

- ✅ SMTP2Go API authentication with your API key
- ✅ Email payload formatting (sender, recipient, subject)
- ✅ HTML email content rendering
- ✅ Plain text email fallback
- ✅ Proper email address formatting (Name <email@example.com>)
- ✅ End-to-end email delivery through SMTP2Go

## Troubleshooting

### Error: "SMTP2GO_API_KEY not set"
- Make sure you copied `.env.example` to `.env`
- Add your actual API key from SMTP2Go dashboard

### Error: "Invalid API key" or 401 Response
- Check your API key is correct in `.env`
- Verify the API key has "Send Email" permissions in SMTP2Go dashboard

### Error: "Sender not verified" or 403 Response
- The `TEST_EMAIL_FROM` address must be verified in your SMTP2Go account
- Go to SMTP2Go → Settings → Sender Domains/Emails
- Add and verify your sender email address

### Email Not Received
- Check your spam/junk folder
- Check SMTP2Go dashboard for delivery status
- Verify recipient email address in `.env` is correct

### Network/Connection Errors
- Check firewall isn't blocking `api.smtp2go.com`
- Verify you have internet connection
- Try accessing https://api.smtp2go.com/v3 in browser

## Using in Your Laravel Application

Once the test passes, you can use the package in your Laravel app:

### 1. Install the Package

```bash
composer require clinically-au/laravel-smtp2go
```

### 2. Configure Laravel

Add to your `.env`:
```env
SMTP2GO_API_KEY=your-api-key-here
MAIL_MAILER=smtp2go
```

Add to `config/mail.php`:
```php
'mailers' => [
    'smtp2go' => [
        'transport' => 'smtp2go',
    ],
],
```

### 3. Send Emails

```php
use Illuminate\Support\Facades\Mail;

Mail::to('user@example.com')
    ->send(new WelcomeEmail($user));
```

## Need Help?

- 📚 Package Documentation: [README.md](../README.md)
- 🐛 Report Issues: https://github.com/clinically-au/laravel-smtp2go/issues
- 📖 SMTP2Go API Docs: https://developers.smtp2go.com/
