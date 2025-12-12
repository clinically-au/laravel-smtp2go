#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * SMTP2Go Laravel Transport - Test Script
 *
 * This script sends a real test email through the SMTP2Go API
 * to verify the package is working correctly.
 *
 * Usage:
 *   1. Copy .env.example to .env in this directory
 *   2. Add your SMTP2GO_API_KEY
 *   3. Set TEST_EMAIL_TO to your email address
 *   4. Run: php send-test-email.php
 */

require __DIR__.'/../vendor/autoload.php';

use Clinically\Smtp2GoTransport\Client\Smtp2GoApiClient;
use Clinically\Smtp2GoTransport\Mail\Smtp2GoTransport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

// Load environment variables
if (file_exists(__DIR__.'/.env')) {
    $env = parse_ini_file(__DIR__.'/.env');
    foreach ($env as $key => $value) {
        $_ENV[$key] = $value;
    }
}

// Validate required environment variables
if (empty($_ENV['SMTP2GO_API_KEY'])) {
    echo "❌ Error: SMTP2GO_API_KEY not set in .env file\n";
    echo "   Please copy .env.example to .env and add your API key\n";
    exit(1);
}

if (empty($_ENV['TEST_EMAIL_TO'])) {
    echo "❌ Error: TEST_EMAIL_TO not set in .env file\n";
    echo "   Please add your email address to receive the test email\n";
    exit(1);
}

$fromEmail = $_ENV['TEST_EMAIL_FROM'] ?? 'test@example.com';
$fromName = $_ENV['TEST_EMAIL_FROM_NAME'] ?? 'SMTP2Go Test';
$toEmail = $_ENV['TEST_EMAIL_TO'];

echo "🚀 SMTP2Go Laravel Transport - Test Email\n";
echo "==========================================\n\n";
echo "Configuration:\n";
echo "  From: {$fromName} <{$fromEmail}>\n";
echo "  To: {$toEmail}\n";
echo '  API Key: '.substr($_ENV['SMTP2GO_API_KEY'], 0, 10)."...\n\n";

try {
    // Create SMTP2Go client and transport
    $client = new Smtp2GoApiClient([
        'endpoint' => $_ENV['SMTP2GO_ENDPOINT'] ?? 'https://api.smtp2go.com/v3',
        'api_key' => $_ENV['SMTP2GO_API_KEY'],
    ]);

    $transport = new Smtp2GoTransport($client);
    $mailer = new Mailer($transport);

    $sentTime = date('Y-m-d H:i:s');

    // Create test email
    $email = (new Email)
        ->from(new Address($fromEmail, $fromName))
        ->to(new Address($toEmail))
        ->subject('SMTP2Go Laravel Transport - Test Email')
        ->text('This is a plain text test email sent via SMTP2Go API.')
        ->html(<<<HTML
<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #4CAF50; color: white; padding: 20px; border-radius: 5px; }
        .content { padding: 20px; background: #f9f9f9; margin-top: 20px; border-radius: 5px; }
        .success { color: #4CAF50; font-weight: bold; }
        .code { background: #e8e8e8; padding: 10px; border-radius: 3px; font-family: monospace; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>✅ SMTP2Go Laravel Transport</h1>
            <p>Test Email Successful!</p>
        </div>
        <div class="content">
            <h2>Congratulations!</h2>
            <p>If you're reading this, the <strong>SMTP2Go Laravel Mail Transport</strong> package is working correctly!</p>

            <h3>Test Details:</h3>
            <ul>
                <li><strong>From:</strong> {$fromName} &lt;{$fromEmail}&gt;</li>
                <li><strong>To:</strong> {$toEmail}</li>
                <li><strong>Sent:</strong> {$sentTime}</li>
                <li><strong>Package:</strong> clinically-au/laravel-smtp2go</li>
            </ul>

            <h3>What was tested:</h3>
            <ul>
                <li>✅ SMTP2Go API authentication</li>
                <li>✅ Email payload formatting</li>
                <li>✅ HTML email content</li>
                <li>✅ Plain text fallback</li>
                <li>✅ Address formatting (Name &lt;email@example.com&gt;)</li>
            </ul>

            <p class="success">🎉 The package is ready for production use!</p>
        </div>
    </div>
</body>
</html>
HTML);

    echo "📧 Sending test email...\n\n";

    // Send the email
    $mailer->send($email);

    echo "✅ SUCCESS! Email sent successfully!\n\n";
    echo "📬 Check your inbox at: {$toEmail}\n";
    echo "   (Don't forget to check spam folder)\n\n";
    echo "🎉 The SMTP2Go Laravel Transport package is working correctly!\n";

} catch (Exception $e) {
    echo "❌ ERROR: Failed to send email\n\n";
    echo "Error Message:\n";
    echo '  '.$e->getMessage()."\n\n";
    echo "Stack Trace:\n";
    echo '  '.$e->getTraceAsString()."\n\n";

    echo "Common Issues:\n";
    echo "  1. Invalid API key - check SMTP2GO_API_KEY in .env\n";
    echo "  2. API key doesn't have send permissions\n";
    echo "  3. 'From' email not verified in SMTP2Go dashboard\n";
    echo "  4. Network/firewall blocking SMTP2Go API\n";
    exit(1);
}
