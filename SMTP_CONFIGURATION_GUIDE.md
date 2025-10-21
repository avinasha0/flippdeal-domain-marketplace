# SMTP Configuration Guide for FlippDeal

## Email Activation System Setup

The registration system has been updated to use email activation instead of OTP. Here's how to configure SMTP:

### 1. Environment Configuration

Add these settings to your `.env` file:

```env
# Mail Configuration
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="FlippDeal"
```

### 2. Gmail SMTP Setup

1. Enable 2-Factor Authentication on your Gmail account
2. Generate an App Password:
   - Go to Google Account settings
   - Security → 2-Step Verification → App passwords
   - Generate a password for "Mail"
   - Use this password in `MAIL_PASSWORD`

### 3. Alternative SMTP Providers

#### SendGrid
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=your-sendgrid-api-key
MAIL_ENCRYPTION=tls
```

#### Mailgun
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailgun.org
MAIL_PORT=587
MAIL_USERNAME=your-mailgun-smtp-username
MAIL_PASSWORD=your-mailgun-smtp-password
MAIL_ENCRYPTION=tls
```

### 4. Testing Email Configuration

Run this command to test your email configuration:

```bash
php artisan tinker
```

Then in tinker:
```php
Mail::raw('Test email', function ($message) {
    $message->to('test@example.com')->subject('Test Email');
});
```

### 5. Registration Flow

1. User fills registration form
2. System generates activation token (24-hour expiry)
3. Activation email sent with secure link
4. User clicks link to activate account
5. Account is created and user is logged in

### 6. Security Features

- Tokens expire after 24 hours
- Tokens are single-use (marked as used after activation)
- Secure random token generation (64 characters)
- Email validation before sending activation

### 7. Troubleshooting

- Check mail logs: `storage/logs/laravel.log`
- Verify SMTP credentials
- Ensure firewall allows SMTP ports (587, 465)
- Check spam folder for activation emails
