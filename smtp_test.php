<?php
// Final SMTP Test with Encryption
require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Final SMTP Test ===\n";
echo "Testing with proper encryption settings...\n\n";

echo "Current Configuration:\n";
echo "- Host: " . config('mail.mailers.smtp.host') . "\n";
echo "- Port: " . config('mail.mailers.smtp.port') . "\n";
echo "- Username: " . config('mail.mailers.smtp.username') . "\n";
echo "- Encryption: " . (config('mail.mailers.smtp.encryption') ?: 'None') . "\n\n";

try {
    // Test sending email
    $testEmail = new App\Mail\EmailActivationMail('test@example.com', 'test-token-123');
    Mail::to('test@example.com')->send($testEmail);
    
    echo "✅ SUCCESS: Email sent successfully!\n";
    echo "✅ Registration emails should now work!\n";
    echo "✅ The 'Failed to send activation email' issue is FIXED!\n";
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n\n";
    echo "The issue might be:\n";
    echo "1. SMTP credentials are incorrect\n";
    echo "2. Email account doesn't have SMTP access enabled\n";
    echo "3. Hostinger SMTP server is blocking the connection\n";
    echo "4. Need to use different SMTP settings\n";
}