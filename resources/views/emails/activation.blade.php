<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Activate Your Account - FlippDeal</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }
        .content {
            background: #f9f9f9;
            padding: 30px;
            border-radius: 0 0 10px 10px;
        }
        .activation-button {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            margin: 20px 0;
            text-align: center;
        }
        .activation-button:hover {
            background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
        }
        .backup-link {
            background: #fff;
            border: 2px solid #667eea;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            word-break: break-all;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            color: #666;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>FlippDeal</h1>
        <p>Domain Marketplace</p>
    </div>
    
    <div class="content">
        <h2>Welcome to FlippDeal!</h2>
        <p>Hello!</p>
        <p>Thank you for registering with FlippDeal. To complete your registration and activate your account, please click the button below:</p>
        
        <div style="text-align: center;">
            <a href="{{ $activationUrl }}" class="activation-button">Activate My Account</a>
        </div>
        
        <p>If the button doesn't work, you can copy and paste this link into your browser:</p>
        
        <div class="backup-link">
            {{ $activationUrl }}
        </div>
        
        <p><strong>Important:</strong> This activation link will expire in 24 hours for security reasons.</p>
        <p>If you didn't create an account with FlippDeal, please ignore this email.</p>
        
        <div class="footer">
            <p>Best regards,<br>The FlippDeal Team</p>
        </div>
    </div>
</body>
</html>
