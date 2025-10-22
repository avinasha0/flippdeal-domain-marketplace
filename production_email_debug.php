<?php
/**
 * Production Email Debug Script for FlippDeal
 * This script will help diagnose and fix email activation issues
 */

require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== FlippDeal Production Email Debug ===\n";
echo "Date: " . date('Y-m-d H:i:s') . "\n";
echo "Environment: " . config('app.env') . "\n\n";

// 1. Check Environment Configuration
echo "1. ENVIRONMENT CONFIGURATION:\n";
echo "----------------------------------------\n";
echo "APP_ENV: " . config('app.env') . "\n";
echo "APP_DEBUG: " . (config('app.debug') ? 'true' : 'false') . "\n";
echo "APP_URL: " . config('app.url') . "\n\n";

// 2. Check Mail Configuration
echo "2. MAIL CONFIGURATION:\n";
echo "----------------------------------------\n";
echo "MAIL_MAILER: " . config('mail.default') . "\n";
echo "MAIL_HOST: " . config('mail.mailers.smtp.host') . "\n";
echo "MAIL_PORT: " . config('mail.mailers.smtp.port') . "\n";
echo "MAIL_USERNAME: " . config('mail.mailers.smtp.username') . "\n";
echo "MAIL_PASSWORD: " . (config('mail.mailers.smtp.password') ? '[SET]' : '[NOT SET]') . "\n";
echo "MAIL_ENCRYPTION: " . (config('mail.mailers.smtp.encryption') ?: 'None') . "\n";
echo "MAIL_FROM_ADDRESS: " . config('mail.from.address') . "\n";
echo "MAIL_FROM_NAME: " . config('mail.from.name') . "\n\n";

// 3. Test Database Connection
echo "3. DATABASE CONNECTION:\n";
echo "----------------------------------------\n";
try {
    DB::connection()->getPdo();
    echo "✅ Database connection: SUCCESS\n";
    echo "Database: " . config('database.connections.mysql.database') . "\n";
    
    // Check if email_activation_tokens table exists
    if (Schema::hasTable('email_activation_tokens')) {
        echo "✅ email_activation_tokens table: EXISTS\n";
        $tokenCount = DB::table('email_activation_tokens')->count();
        echo "Active tokens: " . $tokenCount . "\n";
    } else {
        echo "❌ email_activation_tokens table: MISSING\n";
    }
} catch (Exception $e) {
    echo "❌ Database connection: FAILED\n";
    echo "Error: " . $e->getMessage() . "\n";
}
echo "\n";

// 4. Test SMTP Connection
echo "4. SMTP CONNECTION TEST:\n";
echo "----------------------------------------\n";
try {
    $transport = new \Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport(
        config('mail.mailers.smtp.host'),
        config('mail.mailers.smtp.port'),
        config('mail.mailers.smtp.encryption') === 'tls'
    );
    
    $transport->setUsername(config('mail.mailers.smtp.username'));
    $transport->setPassword(config('mail.mailers.smtp.password'));
    
    // Test connection
    $transport->start();
    echo "✅ SMTP connection: SUCCESS\n";
    $transport->stop();
} catch (Exception $e) {
    echo "❌ SMTP connection: FAILED\n";
    echo "Error: " . $e->getMessage() . "\n";
}
echo "\n";

// 5. Test Email Sending
echo "5. EMAIL SENDING TEST:\n";
echo "----------------------------------------\n";
try {
    // Create a test activation email
    $testEmail = 'aavi10111@gmail.com';
    $testToken = 'test-token-' . time();
    
    echo "Sending test email to: " . $testEmail . "\n";
    
    $activationMail = new App\Mail\EmailActivationMail($testEmail, $testToken);
    Mail::to($testEmail)->send($activationMail);
    
    echo "✅ Email sent: SUCCESS\n";
    echo "✅ Check your inbox at: " . $testEmail . "\n";
    echo "✅ Registration emails should now work!\n";
    
} catch (Exception $e) {
    echo "❌ Email sending: FAILED\n";
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
echo "\n";

// 6. Check Recent Logs
echo "6. RECENT EMAIL LOGS:\n";
echo "----------------------------------------\n";
$logFile = storage_path('logs/laravel.log');
if (file_exists($logFile)) {
    $logs = file_get_contents($logFile);
    $emailLogs = array_filter(explode("\n", $logs), function($line) {
        return strpos($line, 'mail') !== false || strpos($line, 'activation') !== false || strpos($line, 'SMTP') !== false;
    });
    
    $recentLogs = array_slice($emailLogs, -10);
    if (!empty($recentLogs)) {
        echo "Recent email-related logs:\n";
        foreach ($recentLogs as $log) {
            echo "  " . $log . "\n";
        }
    } else {
        echo "No recent email-related logs found.\n";
    }
} else {
    echo "Log file not found.\n";
}
echo "\n";

// 7. Recommendations
echo "7. RECOMMENDATIONS:\n";
echo "----------------------------------------\n";
echo "If email sending failed, try these solutions:\n\n";

echo "A. Hostinger SMTP Issues:\n";
echo "   - Hostinger may block SMTP connections\n";
echo "   - Try using Gmail SMTP instead:\n";
echo "     MAIL_HOST=smtp.gmail.com\n";
echo "     MAIL_PORT=587\n";
echo "     MAIL_USERNAME=your-gmail@gmail.com\n";
echo "     MAIL_PASSWORD=your-app-password\n\n";

echo "B. Alternative SMTP Providers:\n";
echo "   - SendGrid: smtp.sendgrid.net:587\n";
echo "   - Mailgun: smtp.mailgun.org:587\n";
echo "   - Amazon SES: email-smtp.us-east-1.amazonaws.com:587\n\n";

echo "C. Debugging Steps:\n";
echo "   1. Check Hostinger email settings\n";
echo "   2. Verify email account credentials\n";
echo "   3. Test with different SMTP provider\n";
echo "   4. Check firewall/security settings\n";
echo "   5. Enable detailed logging\n\n";

echo "=== Debug Complete ===\n";
echo "Run this script after making changes to test the fix.\n";
