<?php
/**
 * Production Error Debug - Get Full Error Details
 */

require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Production Error Debug ===\n\n";

// Test the exact registration process
echo "Testing registration process step by step...\n\n";

try {
    // 1. Test token generation
    echo "1. Testing token generation...\n";
    $testEmail = 'test@example.com';
    $token = App\Models\EmailActivationToken::generateToken($testEmail);
    echo "✅ Token generated: " . $token->id . "\n";
    
    // 2. Test email creation
    echo "2. Testing email creation...\n";
    $mail = new App\Mail\EmailActivationMail($testEmail, $token->token);
    echo "✅ Email created successfully\n";
    echo "Activation URL: " . $mail->activationUrl . "\n";
    
    // 3. Test email sending
    echo "3. Testing email sending...\n";
    Mail::to($testEmail)->send($mail);
    echo "✅ Email sent successfully\n";
    
    // Clean up
    $token->delete();
    echo "✅ Test completed successfully\n";
    
} catch (Exception $e) {
    echo "❌ ERROR OCCURRED:\n";
    echo "Message: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== Debug Complete ===\n";
