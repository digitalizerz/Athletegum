<?php

/**
 * Quick test script to verify email sending is working
 * Run: php test-email-sending.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

echo "Testing email configuration...\n";
echo "Mail driver: " . config('mail.default') . "\n";
echo "From address: " . config('mail.from.address') . "\n";
echo "From name: " . config('mail.from.name') . "\n";

// Test sending a simple email
try {
    $testEmail = env('TEST_EMAIL', 'test@example.com');
    echo "\nAttempting to send test email to: {$testEmail}\n";
    
    Mail::raw('This is a test email from AthleteGum. If you receive this, email sending is working!', function ($message) use ($testEmail) {
        $message->to($testEmail)
                ->subject('AthleteGum Email Test');
    });
    
    echo "✓ Email sent successfully!\n";
    echo "Check your inbox and SendGrid dashboard.\n";
    
} catch (\Exception $e) {
    echo "✗ Error sending email: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\nDone.\n";

