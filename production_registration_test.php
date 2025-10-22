<?php
/**
 * Production Registration Test - Simulate Exact Registration Process
 */

require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Production Registration Test ===\n";
echo "APP_URL: " . config('app.url') . "\n";
echo "APP_ENV: " . config('app.env') . "\n\n";

// Simulate the exact registration process
try {
    echo "1. Testing validation...\n";
    $request = new \Illuminate\Http\Request();
    $request->merge([
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'phone' => '1234567890',
        'company_name' => 'Test Company',
        'website' => 'https://test.com',
        'location' => 'Test City'
    ]);
    
    echo "✅ Request data prepared\n";
    
    echo "2. Testing token generation...\n";
    $activationToken = App\Models\EmailActivationToken::generateToken($request->email);
    echo "✅ Token generated: " . $activationToken->id . "\n";
    
    echo "3. Testing email creation...\n";
    $mail = new App\Mail\EmailActivationMail($request->email, $activationToken->token);
    echo "✅ Email created\n";
    echo "Activation URL: " . $mail->activationUrl . "\n";
    
    echo "4. Testing email sending...\n";
    Mail::to($request->email)->send($mail);
    echo "✅ Email sent successfully\n";
    
    echo "5. Testing session storage...\n";
    session([
        'pending_user' => [
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'company_name' => $request->company_name,
            'website' => $request->website,
            'location' => $request->location,
        ]
    ]);
    echo "✅ Session stored\n";
    
    // Clean up
    $activationToken->delete();
    session()->forget('pending_user');
    
    echo "\n✅ ALL TESTS PASSED - Registration should work!\n";
    
} catch (Exception $e) {
    echo "\n❌ ERROR FOUND:\n";
    echo "Message: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "Code: " . $e->getCode() . "\n";
    
    if (strpos($e->getMessage(), 'Connection') !== false) {
        echo "\n🔍 DIAGNOSIS: SMTP Connection Issue\n";
        echo "Solution: Check Hostinger SMTP settings or try Gmail SMTP\n";
    } elseif (strpos($e->getMessage(), 'route') !== false) {
        echo "\n🔍 DIAGNOSIS: Route/URL Generation Issue\n";
        echo "Solution: Check APP_URL and run 'php artisan route:clear'\n";
    } elseif (strpos($e->getMessage(), 'database') !== false) {
        echo "\n🔍 DIAGNOSIS: Database Issue\n";
        echo "Solution: Check database connection and table exists\n";
    } elseif (strpos($e->getMessage(), 'view') !== false) {
        echo "\n🔍 DIAGNOSIS: Email Template Issue\n";
        echo "Solution: Check if emails/activation.blade.php exists\n";
    } else {
        echo "\n🔍 DIAGNOSIS: Unknown Issue\n";
        echo "Check the full stack trace below:\n";
        echo $e->getTraceAsString() . "\n";
    }
}

echo "\n=== Test Complete ===\n";
