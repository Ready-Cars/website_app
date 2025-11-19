<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Your Account Credentials</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: #1173d4;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .content {
            background: #f9fafb;
            padding: 30px;
            border: 1px solid #e5e7eb;
            border-top: none;
            border-radius: 0 0 8px 8px;
        }
        .credentials-box {
            background: white;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            padding: 20px;
            margin: 20px 0;
        }
        .credential-item {
            margin: 10px 0;
            padding: 10px;
            background: #f3f4f6;
            border-radius: 4px;
        }
        .credential-label {
            font-weight: bold;
            color: #374151;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .credential-value {
            font-size: 16px;
            font-family: 'Courier New', monospace;
            color: #1f2937;
            margin-top: 5px;
        }
        .role-badge {
            display: inline-block;
            padding: 4px 12px;
            background: #10b981;
            color: white;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .admin-badge {
            background: #ef4444;
        }
        .login-button {
            display: inline-block;
            background: #1173d4;
            color: white;
            text-decoration: none;
            padding: 12px 24px;
            border-radius: 6px;
            font-weight: bold;
            margin: 20px 0;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            color: #6b7280;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Welcome to {{ config('app.name') }}</h1>
        <p>Your account has been created</p>
    </div>

    <div class="content">
        <p>Hello <strong>{{ $user->name }}</strong>,</p>

        <p>An account has been created for you on {{ config('app.name') }}. Below are your login credentials:</p>

        <div class="credentials-box">
            <div class="credential-item">
                <div class="credential-label">Email Address</div>
                <div class="credential-value">{{ $user->email }}</div>
            </div>

            <div class="credential-item">
                <div class="credential-label">Temporary Password</div>
                <div class="credential-value">{{ $password }}</div>
            </div>

            <div class="credential-item">
                <div class="credential-label">Account Type</div>
                <div>
                    <span class="role-badge {{ $role === 'admin' ? 'admin-badge' : '' }}">
                        {{ ucfirst($role) }}
                    </span>
                </div>
            </div>
        </div>

        <p><strong>Important:</strong> Please change your password immediately after your first login for security purposes.</p>

        <div style="text-align: center;">
            <a href="{{ route('login') }}" class="login-button">Login to Your Account</a>
        </div>

        @if($role === 'admin')
        <div style="background: #fef3c7; border: 1px solid #f59e0b; border-radius: 6px; padding: 15px; margin-top: 20px;">
            <p style="margin: 0; color: #92400e;"><strong>Admin Access:</strong> You have administrator privileges. Please use them responsibly and keep your credentials secure.</p>
        </div>
        @endif

        <div class="footer">
            <p>If you have any questions or need assistance, please contact our support team.</p>
            <p>Best regards,<br>The {{ config('app.name') }} Team</p>
        </div>
    </div>
</body>
</html>
