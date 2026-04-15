<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Account Credentials</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: Arial, sans-serif;
            background-color: #e8f5e9;
            padding: 40px 15px;
            color: #333;
        }

        .email-wrapper {
            max-width: 600px;
            margin: 0 auto;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(0,0,0,0.15);
        }

        /* ══ HEADER ══ */
        .header {
            background: linear-gradient(160deg, #1b5e20 0%, #2e7d32 40%, #43a047 75%, #66bb6a 100%);
            padding: 50px 30px 35px;
            text-align: center;
            position: relative;
        }

        /* decorative circles */
        .header .circle-tl {
            position: absolute; top: -30px; left: -30px;
            width: 130px; height: 130px;
            border-radius: 50%;
            background: rgba(255,255,255,0.06);
        }
        .header .circle-br {
            position: absolute; bottom: -40px; right: -20px;
            width: 170px; height: 170px;
            border-radius: 50%;
            background: rgba(255,255,255,0.05);
        }
        .header .circle-tr {
            position: absolute; top: 10px; right: 30px;
            width: 60px; height: 60px;
            border-radius: 50%;
            background: rgba(255,255,255,0.07);
        }

        .logo-outer {
            width: 110px; height: 110px;
            border-radius: 50%;
            background: rgba(255,255,255,0.25);
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 18px;
            position: relative;
            z-index: 1;
        }
        .logo-inner {
            width: 96px; height: 96px;
            border-radius: 50%;
            background: white;
            overflow: hidden;
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 4px 16px rgba(0,0,0,0.25);
        }
        .logo-inner img {
            width: 96px; height: 96px;
            border-radius: 50%;
            object-fit: cover;
            display: block;
        }

        .header h1 {
            color: #ffffff;
            font-size: 26px;
            font-weight: 800;
            letter-spacing: 2px;
            text-shadow: 0 2px 8px rgba(0,0,0,0.3);
            margin-bottom: 6px;
            position: relative; z-index: 1;
        }
        .header .tagline {
            color: rgba(255,255,255,0.80);
            font-size: 11px;
            letter-spacing: 2.5px;
            text-transform: uppercase;
            position: relative; z-index: 1;
        }

        /* ══ WELCOME STRIP ══ */
        .welcome-strip {
            background: #f1f8e9;
            border-top: 4px solid #8bc34a;
            border-bottom: 1px solid #dcedc8;
            padding: 16px 30px;
            display: flex;
            align-items: center;
            gap: 14px;
        }
        .welcome-strip .wave { font-size: 26px; flex-shrink: 0; }
        .welcome-strip .welcome-text strong {
            display: block;
            font-size: 15px;
            color: #2e7d32;
            margin-bottom: 2px;
        }
        .welcome-strip .welcome-text span {
            font-size: 12px;
            color: #666;
        }

        /* ══ BODY ══ */
        .body {
            background: #ffffff;
            padding: 32px 35px 28px;
        }

        .intro {
            font-size: 13.5px;
            color: #555;
            line-height: 1.7;
            margin-bottom: 24px;
            padding-bottom: 20px;
            border-bottom: 1px solid #f0f0f0;
        }

        /* ══ CREDENTIALS TABLE ══ */
        .cred-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            border-radius: 12px;
            overflow: hidden;
            border: 1.5px solid #c8e6c9;
            margin-bottom: 28px;
            box-shadow: 0 2px 12px rgba(46,125,50,0.08);
        }

        .cred-table thead tr {
            background: linear-gradient(135deg, #2e7d32, #4caf50);
        }
        .cred-table thead td {
            padding: 13px 20px;
            color: white;
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        .cred-table tbody tr { border-bottom: 1px solid #e8f5e9; }
        .cred-table tbody tr:last-child { border-bottom: none; }
        .cred-table tbody tr:nth-child(even) { background: #f9fef9; }
        .cred-table tbody tr:nth-child(odd)  { background: #ffffff; }

        .cred-table tbody td {
            padding: 13px 20px;
            vertical-align: middle;
        }

        .cred-label {
            font-size: 10.5px;
            font-weight: 700;
            color: #999;
            text-transform: uppercase;
            letter-spacing: 1px;
            width: 110px;
        }

        .cred-val {
            font-family: 'Courier New', monospace;
            font-size: 14px;
            font-weight: 600;
            color: #222;
            background: #fff;
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            padding: 5px 12px;
            display: inline-block;
        }

        .cred-val.password {
            color: #1b5e20;
            background: #f0fdf4;
            border-color: #a5d6a7;
            font-size: 17px;
            letter-spacing: 4px;
        }

        .role-badge {
            display: inline-block;
            padding: 4px 14px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .role-admin { background: #fff8e1; color: #e65100; border: 1.5px solid #ffb300; }
        .role-user  { background: #e3f2fd; color: #0d47a1; border: 1.5px solid #64b5f6; }

        /* ══ LOGIN BUTTON ══ */
        .btn-wrap { text-align: center; margin: 8px 0 28px; }
        .login-btn {
            display: inline-block;
            padding: 13px 42px;
            background: linear-gradient(135deg, #1b5e20, #4caf50);
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 50px;
            font-size: 14px;
            font-weight: 700;
            letter-spacing: 0.5px;
            box-shadow: 0 5px 18px rgba(46,125,50,0.40);
        }

        /* ══ INFO BOX ══ */
        .info-box {
            background: #fffde7;
            border: 1px solid #ffe082;
            border-left: 4px solid #ffc107;
            border-radius: 0 10px 10px 0;
            padding: 14px 18px;
            margin-bottom: 20px;
            font-size: 12.5px;
            color: #5d4037;
        }
        .info-box .info-title {
            font-weight: 700;
            margin-bottom: 8px;
            color: #6d4c41;
            font-size: 13px;
        }
        .info-box ul { padding-left: 16px; }
        .info-box ul li { margin: 4px 0; }

        /* ══ SECURITY ══ */
        .security-box {
            background: #fff5f5;
            border: 1px solid #ffcdd2;
            border-left: 4px solid #e53935;
            border-radius: 0 10px 10px 0;
            padding: 12px 18px;
            font-size: 12px;
            color: #b71c1c;
            margin-bottom: 24px;
        }

        .divider { border: none; border-top: 1px solid #f0f0f0; margin: 22px 0; }

        .closing { font-size: 13px; color: #555; line-height: 1.7; }
        .signature { margin-top: 14px; font-weight: 700; color: #2e7d32; font-size: 14px; }

        /* ══ FOOTER ══ */
        .footer {
            background: linear-gradient(135deg, #1b5e20, #2e7d32);
            padding: 22px 30px;
            text-align: center;
        }
        .footer .footer-name {
            color: #ffffff;
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 1px;
            margin-bottom: 6px;
        }
        .footer p {
            color: rgba(255,255,255,0.65);
            font-size: 11px;
            margin: 3px 0;
        }
    </style>
</head>
<body>
<div class="email-wrapper">

    <!-- HEADER -->
    <div class="header">

        <div class="logo-outer">
            <div class="logo-inner">
                <img src="https://i.imgur.com/LvgTuQn.jpg" alt="ATI-RTC1">
            </div>
        </div>

        <h1>{{ config('app.name') }}</h1>
        <div class="tagline">Agricultural Training Institute &nbsp;&middot;&nbsp; Regional Training Center I</div>
    </div>

    <!-- WELCOME STRIP -->
    <div class="welcome-strip">
        <div class="wave">👋</div>
        <div class="welcome-text">
            <strong>Welcome, {{ $user->name }}!</strong>
            <span>Your account has been successfully created by an administrator.</span>
        </div>
    </div>

    <!-- BODY -->
    <div class="body">

        <p class="intro">
            You now have access to <strong style="color:#2e7d32;">{{ config('app.name') }}</strong>.
            Your login credentials are listed below. Please keep them private and 
            <strong>change your password</strong> after your first login.
        </p>

        <!-- Credentials Table -->
        <table class="cred-table">
            <thead>
                <tr>
                    <td colspan="2">🔐 &nbsp; Your Login Credentials</td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="cred-label">Full Name</td>
                    <td><span class="cred-val">{{ $user->name }}</span></td>
                </tr>
                <tr>
                    <td class="cred-label">Email</td>
                    <td><span class="cred-val">{{ $user->email }}</span></td>
                </tr>
                <tr>
                    <td class="cred-label">Password</td>
                    <td><span class="cred-val password">{{ $plainPassword }}</span></td>
                </tr>
                <tr>
                    <td class="cred-label">Role</td>
                    <td>
                        <span class="role-badge {{ $user->role === 'admin' ? 'role-admin' : 'role-user' }}">
                            {{ ucfirst($user->role) }}
                        </span>
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- Login Button -->
        <div class="btn-wrap">
            <a href="{{ config('app.url') }}/login" class="login-btn">🚀 &nbsp; Login to {{ config('app.name') }}</a>
        </div>

        <!-- Important Info -->
        <div class="info-box">
            <div class="info-title">⚠️ Important — Please Read</div>
            <ul>
                <li>Keep your credentials <strong>private and secure</strong>.</li>
                <li>Change your password immediately after your first login.</li>
                <li>Do not share your password with anyone, including administrators.</li>
                <li>If you did not expect this email, contact your administrator right away.</li>
            </ul>
        </div>

        <!-- Security Notice -->
        <div class="security-box">
            <strong>🔒 Security Notice:</strong> This email contains sensitive account information.
            Please delete this email after saving your credentials securely.
        </div>

        <hr class="divider">

        <div class="closing">
            <p>If you have questions or need assistance, please contact your system administrator.</p>
            <p class="signature">
                Best regards,<br>
                {{ config('app.name') }} Team
            </p>
        </div>

    </div>

    <!-- FOOTER -->
    <div class="footer">
        <div class="footer-name">{{ config('app.name') }}</div>
        <p>This is an automated message — please do not reply to this email.</p>
        <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
    </div>

</div>
</body>
</html>