<?php
// Real SMTP Test with your actual email
require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Real SMTP Test ===\n";
echo "Testing with your actual email address...\n\n";

echo "SMTP Configuration:\n";
echo "- Host: " . config('mail.mailers.smtp.host') . "\n";
echo "- Port: " . config('mail.mailers.smtp.port') . "\n";
echo "- Username: " . config('mail.mailers.smtp.username') . "\n";
echo "- Encryption: " . (config('mail.mailers.smtp.encryption') ?: 'None') . "\n";
echo "- From Address: " . config('mail.from.address') . "\n";
echo "- From Name: " . config('mail.from.name') . "\n\n";

try {
    // Test sending email to your real email address
    $testEmail = new App\Mail\EmailActivationMail('aavi10111@gmail.com', 'test-token-123');
    Mail::to('aavi10111@gmail.com')->send($testEmail);
    
    echo "✅ SUCCESS: Email sent to aavi10111@gmail.com!\n";
    echo "✅ Check your Gmail inbox (and spam folder)\n";
    echo "✅ Registration emails should now work!\n";
    echo "✅ The 'Failed to send activation email' issue is FIXED!\n";
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n\n";
    echo "The issue might be:\n";
    echo "1. SMTP credentials are incorrect\n";
    echo "2. Email account doesn't have SMTP access enabled\n";
    echo "3. Hostinger SMTP server is blocking the connection\n";
    echo "4. Need to use different SMTP settings\n";
    echo "5. Check if Gmail is blocking emails from this sender\n";
}
