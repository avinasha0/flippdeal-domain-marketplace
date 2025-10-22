<?php
/**
 * Debug Email Activation Token Issues
 */

require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Email Activation Token Debug ===\n\n";

// 1. Check if table exists
echo "1. CHECKING DATABASE TABLE:\n";
echo "----------------------------------------\n";

try {
    if (Schema::hasTable('email_activation_tokens')) {
        echo "✅ email_activation_tokens table: EXISTS\n";
        
        // Check table structure
        $columns = Schema::getColumnListing('email_activation_tokens');
        echo "Columns: " . implode(', ', $columns) . "\n";
        
        // Check if table is empty
        $count = DB::table('email_activation_tokens')->count();
        echo "Total tokens: " . $count . "\n";
        
        // Check recent tokens
        $recent = DB::table('email_activation_tokens')
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();
            
        echo "Recent tokens:\n";
        foreach ($recent as $token) {
            echo "  - Email: " . $token->email . ", Created: " . $token->created_at . ", Used: " . ($token->used ? 'Yes' : 'No') . "\n";
        }
        
    } else {
        echo "❌ email_activation_tokens table: MISSING\n";
        echo "Run: php artisan migrate\n";
    }
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "\n";
}

echo "\n";

// 2. Test token generation
echo "2. TESTING TOKEN GENERATION:\n";
echo "----------------------------------------\n";

try {
    $testEmail = 'test@example.com';
    echo "Generating token for: " . $testEmail . "\n";
    
    $token = App\Models\EmailActivationToken::generateToken($testEmail);
    echo "✅ Token generated successfully\n";
    echo "Token ID: " . $token->id . "\n";
    echo "Token: " . substr($token->token, 0, 10) . "...\n";
    echo "Expires: " . $token->expires_at . "\n";
    
    // Clean up test token
    $token->delete();
    echo "✅ Test token cleaned up\n";
    
} catch (Exception $e) {
    echo "❌ Token generation failed: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n";

// 3. Test email template
echo "3. TESTING EMAIL TEMPLATE:\n";
echo "----------------------------------------\n";

try {
    $testEmail = 'test@example.com';
    $testToken = 'test-token-123';
    
    echo "Creating EmailActivationMail instance...\n";
    $mail = new App\Mail\EmailActivationMail($testEmail, $testToken);
    echo "✅ EmailActivationMail created successfully\n";
    
    echo "Activation URL: " . $mail->activationUrl . "\n";
    
} catch (Exception $e) {
    echo "❌ Email template error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n";

// 4. Check routes
echo "4. CHECKING ROUTES:\n";
echo "----------------------------------------\n";

try {
    $routes = Route::getRoutes();
    $registerRoutes = [];
    
    foreach ($routes as $route) {
        if (strpos($route->uri(), 'register') !== false || strpos($route->uri(), 'activate') !== false) {
            $registerRoutes[] = $route->uri() . ' -> ' . $route->getName();
        }
    }
    
    if (!empty($registerRoutes)) {
        echo "Registration routes found:\n";
        foreach ($registerRoutes as $route) {
            echo "  - " . $route . "\n";
        }
    } else {
        echo "❌ No registration routes found\n";
    }
    
} catch (Exception $e) {
    echo "❌ Route check failed: " . $e->getMessage() . "\n";
}

echo "\n=== Debug Complete ===\n";
