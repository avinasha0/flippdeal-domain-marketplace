<?php
/**
 * Quick Email Fix for Hostinger Production
 * This script provides alternative SMTP configurations for Hostinger
 */

echo "=== Hostinger Email Configuration Fix ===\n\n";

echo "Your current configuration:\n";
echo "MAIL_HOST=smtp.hostinger.com\n";
echo "MAIL_PORT=587\n";
echo "MAIL_USERNAME=hell0@digitalxbrand.com\n";
echo "MAIL_PASSWORD=Avinash!08!08\n";
echo "MAIL_ENCRYPTION=tls\n\n";

echo "=== ALTERNATIVE CONFIGURATIONS ===\n\n";

echo "1. GMAIL SMTP (Recommended):\n";
echo "----------------------------------------\n";
echo "MAIL_MAILER=smtp\n";
echo "MAIL_HOST=smtp.gmail.com\n";
echo "MAIL_PORT=587\n";
echo "MAIL_USERNAME=your-gmail@gmail.com\n";
echo "MAIL_PASSWORD=your-app-password\n";
echo "MAIL_ENCRYPTION=tls\n";
echo "MAIL_FROM_ADDRESS=your-gmail@gmail.com\n";
echo "MAIL_FROM_NAME=\"FlippDeal By DigitalXBrand\"\n\n";

echo "2. SENDGRID SMTP:\n";
echo "----------------------------------------\n";
echo "MAIL_MAILER=smtp\n";
echo "MAIL_HOST=smtp.sendgrid.net\n";
echo "MAIL_PORT=587\n";
echo "MAIL_USERNAME=apikey\n";
echo "MAIL_PASSWORD=your-sendgrid-api-key\n";
echo "MAIL_ENCRYPTION=tls\n";
echo "MAIL_FROM_ADDRESS=hell0@digitalxbrand.com\n";
echo "MAIL_FROM_NAME=\"FlippDeal By DigitalXBrand\"\n\n";

echo "3. MAILGUN SMTP:\n";
echo "----------------------------------------\n";
echo "MAIL_MAILER=smtp\n";
echo "MAIL_HOST=smtp.mailgun.org\n";
echo "MAIL_PORT=587\n";
echo "MAIL_USERNAME=your-mailgun-smtp-username\n";
echo "MAIL_PASSWORD=your-mailgun-smtp-password\n";
echo "MAIL_ENCRYPTION=tls\n";
echo "MAIL_FROM_ADDRESS=hell0@digitalxbrand.com\n";
echo "MAIL_FROM_NAME=\"FlippDeal By DigitalXBrand\"\n\n";

echo "4. HOSTINGER ALTERNATIVE PORTS:\n";
echo "----------------------------------------\n";
echo "MAIL_MAILER=smtp\n";
echo "MAIL_HOST=smtp.hostinger.com\n";
echo "MAIL_PORT=465\n";
echo "MAIL_USERNAME=hell0@digitalxbrand.com\n";
echo "MAIL_PASSWORD=Avinash!08!08\n";
echo "MAIL_ENCRYPTION=ssl\n";
echo "MAIL_FROM_ADDRESS=hell0@digitalxbrand.com\n";
echo "MAIL_FROM_NAME=\"FlippDeal By DigitalXBrand\"\n\n";

echo "=== QUICK FIX STEPS ===\n\n";
echo "1. Try Gmail SMTP first (most reliable):\n";
echo "   - Create a Gmail account\n";
echo "   - Enable 2FA\n";
echo "   - Generate App Password\n";
echo "   - Use the Gmail configuration above\n\n";

echo "2. If Gmail doesn't work, try SendGrid:\n";
echo "   - Sign up for SendGrid (free tier available)\n";
echo "   - Get API key\n";
echo "   - Use SendGrid configuration above\n\n";

echo "3. Test the configuration:\n";
echo "   - Run: php production_email_debug.php\n";
echo "   - Check logs: tail -f storage/logs/laravel.log\n\n";

echo "4. Common Hostinger Issues:\n";
echo "   - SMTP ports 587/465 may be blocked\n";
echo "   - Email account may not have SMTP access\n";
echo "   - Firewall may block outbound connections\n";
echo "   - Rate limiting on email sending\n\n";

echo "=== IMMEDIATE ACTION ===\n";
echo "Update your .env file with Gmail SMTP and test:\n";
echo "1. Copy Gmail configuration above\n";
echo "2. Replace in your .env file\n";
echo "3. Run: php production_email_debug.php\n";
echo "4. Test registration\n\n";

echo "This should resolve the 'Failed to send activation email' error.\n";
