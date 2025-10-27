<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Your Password - Bandmate</title>
    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            line-height: 1.6;
            color: #374151;
            background-color: #f9fafb;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 40px 30px;
            text-align: center;
            color: white;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 700;
        }
        .header p {
            margin: 10px 0 0 0;
            opacity: 0.9;
            font-size: 16px;
        }
        .content {
            padding: 40px 30px;
        }
        .content h2 {
            color: #1f2937;
            font-size: 24px;
            margin: 0 0 20px 0;
            font-weight: 600;
        }
        .content p {
            margin: 0 0 20px 0;
            font-size: 16px;
            line-height: 1.6;
        }
        .button {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            padding: 16px 32px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            margin: 20px 0;
            transition: transform 0.2s ease;
        }
        .button:hover {
            transform: translateY(-2px);
        }
        .footer {
            background-color: #f9fafb;
            padding: 30px;
            text-align: center;
            border-top: 1px solid #e5e7eb;
        }
        .footer p {
            margin: 0;
            font-size: 14px;
            color: #6b7280;
        }
        .logo {
            width: 60px;
            height: 60px;
            margin: 0 auto 20px;
            display: block;
        }
        .security-note {
            background-color: #fef3c7;
            border: 1px solid #f59e0b;
            border-radius: 8px;
            padding: 16px;
            margin: 20px 0;
        }
        .security-note p {
            margin: 0;
            font-size: 14px;
            color: #92400e;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="{{ asset('assets/logo_both.png') }}" alt="Bandmate Logo" class="logo">
            <h1>Reset Your Password</h1>
            <p>Don't worry, it happens to the best of us!</p>
        </div>
        
        <div class="content">
            <h2>Hello {{ $user->name ?? 'there' }}!</h2>
            
            <p>We received a request to reset your password for your Bandmate account. If you didn't make this request, you can safely ignore this email.</p>
            
            <p>To reset your password, click the button below:</p>
            
            <div style="text-align: center;">
                <a href="{{ $resetUrl }}" class="button">Reset My Password</a>
            </div>
            
            <p>Or copy and paste this link into your browser:</p>
            <p style="word-break: break-all; background-color: #f3f4f6; padding: 12px; border-radius: 6px; font-family: monospace; font-size: 14px;">{{ $resetUrl }}</p>
            
            <div class="security-note">
                <p><strong>Security Note:</strong> This link will expire in 1 hour for your security. If you need a new link, please request another password reset.</p>
            </div>
            
            <p>If you have any questions or need help, feel free to reach out to our support team.</p>
            
            <p>Happy music making!<br>
            The Bandmate Team</p>
        </div>
        
        <div class="footer">
            <p>This email was sent to {{ $user->email ?? 'your email address' }}. If you didn't request this, please ignore this email.</p>
            <p>&copy; {{ date('Y') }} Bandmate. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
