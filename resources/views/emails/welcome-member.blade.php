<!DOCTYPE html>
<html lang="en" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="x-apple-disable-message-reformatting">
    <title>Welcome to HGNL PAY</title>
    <style>
        /* Mobile Responsive Styles */
        @media screen and (max-width: 600px) {
            .container {
                width: 100% !important;
                margin: auto !important;
            }

            .stack-column {
                display: block !important;
                width: 100% !important;
                max-width: 100% !important;
                direction: ltr !important;
            }

            .padding-mobile {
                padding: 20px !important;
            }

            .button-full {
                width: 100% !important;
                display: block !important;
                text-align: center !important;
            }
        }
    </style>
</head>

<body
    style="margin: 0; padding: 0; width: 100%; background-color: #f6f9fc; -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%;">
    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="table-layout: fixed;">
        <tr>
            <td align="center" style="padding: 20px 10px; background-color: #f6f9fc;">
                <table border="0" cellpadding="0" cellspacing="0" width="600" class="container"
                    style="background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 10px rgba(0,0,0,0.05);">

                    <tr>
                        <td align="center" style="padding: 30px 20px; background-color: #2563eb;">
                            <h1
                                style="color: #ffffff; margin: 0; font-size: 24px; font-weight: 700; font-family: sans-serif; letter-spacing: -1px;">
                                HG NL PAY</h1>
                        </td>
                    </tr>

                    <tr>
                        <td class="padding-mobile" style="padding: 40px; font-family: sans-serif;">
                            <h2 style="color: #1f2937; margin-top: 0; font-size: 20px;">Welcome, {{ $userName }}!
                            </h2>
                            <p style="color: #4b5563; font-size: 16px; line-height: 1.6; margin-bottom: 30px;">
                                Your account has been successfully created. You are now part of the HG NL PAY network.
                                Below are your official login credentials.
                            </p>

                            <table border="0" cellpadding="0" cellspacing="0" width="100%"
                                style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; margin-bottom: 30px;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                            <tr>
                                                <td
                                                    style="color: #64748b; font-size: 11px; text-transform: uppercase; font-weight: bold; letter-spacing: 1px; padding-bottom: 5px;">
                                                    Member ID</td>
                                            </tr>
                                            <tr>
                                                <td
                                                    style="color: #1e293b; font-size: 17px; font-weight: 600; font-family: monospace; padding-bottom: 15px;">
                                                    {{ $user->member_id }}</td>
                                            </tr>
                                            <tr>
                                                <td
                                                    style="color: #64748b; font-size: 11px; text-transform: uppercase; font-weight: bold; letter-spacing: 1px; padding-bottom: 5px;">
                                                    Temporary Password</td>
                                            </tr>
                                            <tr>
                                                <td
                                                    style="color: #1e293b; font-size: 17px; font-weight: 600; font-family: monospace;">
                                                    {{ $userPassword }}</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td align="center">
                                        <a href="{{ url('/login') }}" class="button-full"
                                            style="background-color: #2563eb; color: #ffffff; padding: 16px 32px; text-decoration: none; border-radius: 6px; font-weight: 600; font-size: 16px; display: inline-block;">
                                            Login to Your Dashboard
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <p
                                style="color: #94a3b8; font-size: 13px; line-height: 1.5; margin-top: 30px; text-align: center;">
                                For security reasons, we recommend changing your password immediately after your first
                                login.
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td class="padding-mobile"
                            style="padding: 20px 40px 30px 40px; background-color: #f1f5f9; text-align: center; font-family: sans-serif;">
                            <p style="color: #64748b; font-size: 11px; margin: 0;">&copy; 2026 HG NL PAY. All rights
                                reserved.</p>
                            <p style="color: #64748b; font-size: 11px; margin: 5px 0 0 0;">You received this email
                                because an account was created for you.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>
