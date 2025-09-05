# PayPal OAuth Integration Setup Guide

## Overview
This implementation provides secure PayPal account connection using OAuth 2.0, redirecting users to PayPal's official login page for authentication.

## Features
- ✅ **Secure OAuth Flow**: Users are redirected to PayPal's official website
- ✅ **Automatic Verification**: No admin approval required
- ✅ **Account Management**: Users can update or disconnect their PayPal account
- ✅ **Professional UI**: Modern, responsive design with PayPal branding
- ✅ **Audit Logging**: Complete activity tracking

## Setup Instructions

### 1. PayPal Developer Account Setup

1. **Create PayPal Developer Account**:
   - Go to [PayPal Developer Portal](https://developer.paypal.com/)
   - Sign in with your PayPal account or create one
   - Navigate to "My Apps & Credentials"

2. **Create New App**:
   - Click "Create App"
   - Choose "Default Application" or "Web Application"
   - Fill in the required details:
     - **App Name**: FlippDeal Domain Marketplace
     - **Merchant**: Your business information
     - **Features**: Enable "Log in with PayPal"

3. **Configure OAuth Settings**:
   - **Return URL**: `http://127.0.0.1:8000/paypal/callback` (for development)
   - **Live Return URL**: `https://yourdomain.com/paypal/callback` (for production)
   - **Scopes**: `openid profile email`

### 2. Environment Configuration

Update your `.env` file with PayPal credentials:

```env
# PayPal Configuration
PAYPAL_CLIENT_ID=your_paypal_client_id_here
PAYPAL_CLIENT_SECRET=your_paypal_client_secret_here
PAYPAL_MODE=sandbox
PAYPAL_REDIRECT_URI=http://127.0.0.1:8000/paypal/callback
```

**For Production**:
```env
PAYPAL_MODE=live
PAYPAL_REDIRECT_URI=https://yourdomain.com/paypal/callback
```

### 3. Database Requirements

The following tables and fields are required (already implemented):

**Users Table**:
- `paypal_email` (string, nullable)
- `paypal_verified` (boolean, default: false)
- `paypal_verified_at` (timestamp, nullable)

**Verifications Table**:
- `user_id` (foreign key)
- `type` (string: 'paypal_email')
- `status` (string: 'verified')
- `identifier` (string: PayPal email)
- `data` (json: OAuth response data)

### 4. Routes

The following routes are automatically configured:

```php
// PayPal OAuth routes
Route::get('/paypal/connect', [PayPalOAuthController::class, 'redirect'])->name('paypal.connect');
Route::get('/paypal/callback', [PayPalOAuthController::class, 'callback'])->name('paypal.callback');
Route::post('/paypal/disconnect', [PayPalOAuthController::class, 'disconnect'])->name('paypal.disconnect');
```

### 5. User Experience Flow

1. **User clicks "Connect PayPal"** in profile page
2. **Redirected to PayPal** official login page
3. **User logs in** with PayPal credentials
4. **PayPal redirects back** to your site with authorization code
5. **System exchanges code** for access token
6. **System fetches user info** from PayPal
7. **Account automatically verified** and ready to use

### 6. Testing

**Sandbox Testing**:
1. Use PayPal sandbox credentials
2. Create test accounts in PayPal Developer Portal
3. Test the complete OAuth flow

**Production Testing**:
1. Switch to live mode in `.env`
2. Use real PayPal accounts
3. Verify all functionality works

### 7. Security Features

- ✅ **CSRF Protection**: State parameter validation
- ✅ **Secure Token Exchange**: Server-side token handling
- ✅ **No Credential Storage**: Only verified email stored
- ✅ **Audit Logging**: Complete activity tracking
- ✅ **Error Handling**: Graceful failure management

### 8. Troubleshooting

**Common Issues**:

1. **"Invalid redirect URI"**:
   - Check PayPal app configuration
   - Ensure exact URL match (including http/https)

2. **"Invalid client credentials"**:
   - Verify CLIENT_ID and CLIENT_SECRET
   - Check if app is in correct mode (sandbox/live)

3. **"State parameter mismatch"**:
   - Clear browser cookies/session
   - Check CSRF token generation

4. **"Failed to get user info"**:
   - Verify OAuth scopes include 'openid profile email'
   - Check access token validity

### 9. Production Checklist

- [ ] PayPal app configured for live mode
- [ ] Production redirect URI set
- [ ] SSL certificate installed
- [ ] Environment variables updated
- [ ] Error logging configured
- [ ] User testing completed

## Support

For PayPal OAuth issues:
- [PayPal Developer Documentation](https://developer.paypal.com/docs/api/overview/)
- [PayPal OAuth 2.0 Guide](https://developer.paypal.com/docs/api/overview/#oauth-20)

For implementation issues:
- Check Laravel logs: `storage/logs/laravel.log`
- Verify database migrations
- Test with PayPal sandbox first
