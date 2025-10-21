<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Email Verification - FlippDeal</title>
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
        .verification-code {
            background: #fff;
            border: 2px solid #667eea;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            margin: 20px 0;
        }
        .code {
            font-size: 32px;
            font-weight: bold;
            color: #667eea;
            letter-spacing: 5px;
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
        <h2>Verify Your Email Address</h2>
        <p>Hello!</p>
        <p>Thank you for registering with FlippDeal. To complete your registration, please use the verification code below:</p>
        
        <div class="verification-code">
            <p>Your verification code is:</p>
            <div class="code">{{ $code }}</div>
        </div>
        
        <p>This code will expire in 10 minutes for security reasons.</p>
        <p>If you didn't create an account with FlippDeal, please ignore this email.</p>
        
        <div class="footer">
            <p>Best regards,<br>The FlippDeal Team</p>
        </div>
    </div>
</body>
</html>
