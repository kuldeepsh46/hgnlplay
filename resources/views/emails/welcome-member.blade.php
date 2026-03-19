<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to HGNL PAY</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f6f9fc; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;">
    <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td align="center" style="padding: 40px 0;">
                <table border="0" cellpadding="0" cellspacing="0" width="600" style="background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 10px rgba(0,0,0,0.05);">
                    
                    <tr>
                        <td align="center" style="padding: 40px 40px 20px 40px; background-color: #2563eb;">
                            <h1 style="color: #ffffff; margin: 0; font-size: 28px; font-weight: 700; letter-spacing: -1px;">HG NL PAY</h1>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding: 40px;">
                            <h2 style="color: #1f2937; margin-top: 0; font-size: 22px;">Welcome, {{ $userName }}!</h2>
                            <p style="color: #4b5563; font-size: 16px; line-height: 1.6; margin-bottom: 30px;">
                                Your account has been successfully created by your partner. You are now part of the HG NL PAY network. Below are your official login credentials.
                            </p>

                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; margin-bottom: 30px;">
                                <tr>
                                    <td style="padding: 25px;">
                                        <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                            <tr>
                                                <td style="padding-bottom: 10px; color: #64748b; font-size: 13px; text-transform: uppercase; font-weight: bold; letter-spacing: 1px;">Member ID</td>
                                            </tr>
                                            <tr>
                                                <td style="padding-bottom: 20px; color: #1e293b; font-size: 18px; font-weight: 600; font-family: monospace;">{{ $user->member_id }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding-bottom: 10px; color: #64748b; font-size: 13px; text-transform: uppercase; font-weight: bold; letter-spacing: 1px;">Temporary Password</td>
                                            </tr>
                                            <tr>
                                                <td style="color: #1e293b; font-size: 18px; font-weight: 600; font-family: monospace;">{{ $userPassword }}</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td align="center">
                                        <a href="{{ url('/login') }}" style="background-color: #2563eb; color: #ffffff; padding: 16px 32px; text-decoration: none; border-radius: 6px; font-weight: 600; font-size: 16px; display: inline-block;">
                                            Login to Your Dashboard
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <p style="color: #94a3b8; font-size: 14px; line-height: 1.5; margin-top: 40px; text-align: center;">
                                For security reasons, we recommend changing your password immediately after your first login.
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding: 20px 40px 40px 40px; background-color: #f1f5f9; text-align: center;">
                            <p style="color: #64748b; font-size: 12px; margin: 0;">&copy; 2026 HG NL PAY. All rights reserved.</p>
                            <p style="color: #64748b; font-size: 12px; margin: 5px 0 0 0;">You received this email because an account was created for you.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>