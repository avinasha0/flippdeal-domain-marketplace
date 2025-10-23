<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Your Password - FlippDeal</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f8f9fa;
        }
        .email-container {
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: bold;
        }
        .header p {
            margin: 10px 0 0 0;
            opacity: 0.9;
        }
        .content {
            padding: 40px 30px;
        }
        .greeting {
            font-size: 18px;
            margin-bottom: 20px;
            color: #2c3e50;
        }
        .message {
            font-size: 16px;
            margin-bottom: 30px;
            color: #555;
        }
        .reset-button {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            font-size: 16px;
            margin: 20px 0;
            transition: transform 0.2s;
        }
        .reset-button:hover {
            transform: translateY(-2px);
        }
        .security-info {
            background-color: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 20px;
            margin: 30px 0;
            border-radius: 0 8px 8px 0;
        }
        .security-info h3 {
            margin: 0 0 10px 0;
            color: #2c3e50;
            font-size: 16px;
        }
        .security-info ul {
            margin: 10px 0;
            padding-left: 20px;
        }
        .security-info li {
            margin: 5px 0;
            color: #555;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 30px;
            text-align: center;
            border-top: 1px solid #e9ecef;
        }
        .footer p {
            margin: 5px 0;
            color: #666;
            font-size: 14px;
        }
        .footer a {
            color: #667eea;
            text-decoration: none;
        }
        .footer a:hover {
            text-decoration: underline;
        }
        .divider {
            height: 1px;
            background-color: #e9ecef;
            margin: 20px 0;
        }
        .warning {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
            color: #856404;
        }
        .warning strong {
            display: block;
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>🔐 FlippDeal</h1>
            <p>Domain Marketplace</p>
        </div>
        
        <div class="content">
            <div class="greeting">
                Hello{{ $user ? ' ' . $user->name : '' }},
            </div>
            
            <div class="message">
                We received a request to reset the password for your FlippDeal account associated with <strong>{{ $email }}</strong>. If you made this request, click the button below to reset your password.
            </div>
            
            <div style="text-align: center;">
                <a href="{{ $resetUrl }}" class="reset-button">
                    Reset My Password
                </a>
            </div>
            
            <div class="security-info">
                <h3>🔒 Security Information</h3>
                <ul>
                    <li>This password reset link will expire in <strong>60 minutes</strong></li>
                    <li>The link can only be used <strong>once</strong></li>
                    <li>If you didn't request this reset, please ignore this email</li>
                    <li>Your password will remain unchanged until you create a new one</li>
                </ul>
            </div>
            
            <div class="warning">
                <strong>⚠️ Important Security Notice:</strong>
                If you didn't request a password reset, please contact our support team immediately. Your account security is our top priority.
            </div>
            
            <div class="divider"></div>
            
            <div style="font-size: 14px; color: #666;">
                <p><strong>Having trouble with the button?</strong></p>
                <p>Copy and paste this link into your browser:</p>
                <p style="word-break: break-all; background-color: #f8f9fa; padding: 10px; border-radius: 4px; font-family: monospace;">
                    {{ $resetUrl }}
                </p>
            </div>
        </div>
        
        <div class="footer">
            <p><strong>FlippDeal - Domain Marketplace</strong></p>
            <p>The premier marketplace for buying and selling domains with secure escrow, instant transfers, and verified listings.</p>
            <div class="divider"></div>
            <p>
                <a href="{{ url('/') }}">Visit FlippDeal</a> | 
                <a href="{{ url('/help') }}">Help Center</a> | 
                <a href="{{ url('/support/contact') }}">Contact Support</a>
            </p>
            <p style="margin-top: 20px; font-size: 12px; color: #999;">
                © {{ date('Y') }} FlippDeal. All rights reserved.<br>
                This email was sent to {{ $email }}. If you have any questions, please contact our support team.
            </p>
        </div>
    </div>
</body>
</html>
