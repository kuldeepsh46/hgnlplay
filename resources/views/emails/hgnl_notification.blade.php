<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $mailData['subject'] ?? 'HGNL Pay Notification' }}</title>

    <style>
        body {
            margin: 0;
            padding: 0;
            background: #eef4fb;
            font-family: Arial, Helvetica, sans-serif;
            color: #111827;
        }

        table {
            border-spacing: 0;
        }

        img {
            border: 0;
            display: block;
        }

        @media only screen and (max-width: 640px) {
            .email-wrapper {
                width: 100% !important;
                max-width: 100% !important;
            }

            .email-container {
                width: 100% !important;
                border-radius: 0 !important;
            }

            .mobile-padding {
                padding-left: 18px !important;
                padding-right: 18px !important;
            }

            .mobile-center {
                text-align: center !important;
            }

            .brand-name {
                font-size: 22px !important;
            }

            .main-title {
                font-size: 22px !important;
                line-height: 1.35 !important;
            }

            .info-table td {
                display: block !important;
                width: 100% !important;
                box-sizing: border-box !important;
            }

            .info-label {
                border-bottom: 0 !important;
                padding-bottom: 6px !important;
            }

            .info-value {
                padding-top: 6px !important;
            }

            .button-link {
                width: 100% !important;
                display: block !important;
                box-sizing: border-box !important;
                text-align: center !important;
            }
        }
    </style>
</head>

<body style="margin:0; padding:0; background:#eef4fb; font-family:Arial, Helvetica, sans-serif; color:#111827;">

@php
    $supportEmail = config('mail.support.address') ?: env('MAIL_SUPPORT_ADDRESS') ?: 'support@hgnlpay.com';

    $subject = $mailData['subject'] ?? 'HGNL Pay Notification';
    $title = $mailData['title'] ?? 'Notification';
    $badge = $mailData['badge'] ?? 'Notification';

    $actionUrl = $mailData['action_url'] ?? null;
    $actionText = $mailData['action_text'] ?? 'View Details';

    $preheader = $mailData['message'] ?? $subject;
@endphp

{{-- Hidden preheader --}}
<div style="display:none; max-height:0; overflow:hidden; opacity:0; color:transparent; font-size:1px; line-height:1px;">
    {{ $preheader }}
</div>

<table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="width:100%; background:#eef4fb; padding:28px 12px;">
    <tr>
        <td align="center">

            <table class="email-wrapper" width="640" cellpadding="0" cellspacing="0" role="presentation" style="width:640px; max-width:640px;">

                {{-- Top brand strip --}}
                <tr>
                    <td style="padding:0 0 14px 0;">
                        <table width="100%" cellpadding="0" cellspacing="0" role="presentation">
                            <tr>
                                <td align="center" style="font-size:13px; color:#64748b;">
                                    Secure message from <strong style="color:#0b5ed7;">HGNL Pay</strong>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                {{-- Main card --}}
                <tr>
                    <td>
                        <table class="email-container" width="100%" cellpadding="0" cellspacing="0" role="presentation" style="width:100%; background:#ffffff; border-radius:20px; overflow:hidden; border:1px solid #dbe7f3; box-shadow:0 14px 35px rgba(15,23,42,0.12);">

                            {{-- Header --}}
                            <tr>
                                <td style="background:#073b8e; padding:0;">
                                    <table width="100%" cellpadding="0" cellspacing="0" role="presentation">
                                        <tr>
                                            <td class="mobile-padding" style="padding:30px 34px; background:#073b8e;">

                                                <table width="100%" cellpadding="0" cellspacing="0" role="presentation">
                                                    <tr>
                                                        <td style="vertical-align:middle;">

                                                            <table cellpadding="0" cellspacing="0" role="presentation">
                                                                <tr>
                                                                    {{-- Logo --}}
                                                                    <td style="vertical-align:middle;">
                                                                        <table width="58" height="58" cellpadding="0" cellspacing="0" role="presentation" style="width:58px; height:58px; background:#ffffff; border-radius:16px;">
                                                                            <tr>
                                                                                <td align="center" valign="middle" style="font-size:28px; font-weight:800; color:#073b8e; line-height:58px;">
                                                                                    H
                                                                                </td>
                                                                            </tr>
                                                                        </table>
                                                                    </td>

                                                                    <td style="padding-left:16px; vertical-align:middle;">
                                                                        <div class="brand-name" style="font-size:27px; line-height:1.15; font-weight:800; color:#ffffff; letter-spacing:-0.3px;">
                                                                            HGNL Pay
                                                                        </div>
                                                                        <div style="font-size:13px; color:#dbeafe; margin-top:5px; font-weight:500;">
                                                                            Secure Transaction Notification
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            </table>

                                                        </td>

                                                        <td align="right" style="vertical-align:middle;">
                                                            <span style="display:inline-block; padding:8px 13px; border-radius:999px; background:#e0f2fe; color:#075985; font-size:12px; line-height:1; font-weight:700;">
                                                                {{ $badge }}
                                                            </span>
                                                        </td>
                                                    </tr>
                                                </table>

                                            </td>
                                        </tr>

                                        {{-- Accent line --}}
                                        <tr>
                                            <td style="height:5px; background:#22c55e; font-size:0; line-height:0;">
                                                &nbsp;
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>

                            {{-- Body --}}
                            <tr>
                                <td class="mobile-padding" style="padding:36px 38px 30px 38px;">

                                    <h1 class="main-title" style="margin:0 0 16px 0; color:#0f172a; font-size:28px; line-height:1.3; font-weight:800; letter-spacing:-0.4px;">
                                        {{ $title }}
                                    </h1>

                                    @if(!empty($mailData['greeting']))
                                        <p style="margin:0 0 14px 0; font-size:16px; line-height:1.7; color:#334155;">
                                            {{ $mailData['greeting'] }}
                                        </p>
                                    @endif

                                    @if(!empty($mailData['message']))
                                        <p style="margin:0 0 24px 0; font-size:16px; line-height:1.7; color:#334155;">
                                            {{ $mailData['message'] }}
                                        </p>
                                    @endif

                                    @if(!empty($mailData['rows']) && is_array($mailData['rows']))
                                        <table class="info-table" width="100%" cellpadding="0" cellspacing="0" role="presentation" style="width:100%; border-collapse:separate; border-spacing:0; border:1px solid #dbe4ef; border-radius:14px; overflow:hidden; margin:24px 0;">
                                            @foreach($mailData['rows'] as $label => $value)
                                                <tr>
                                                    <td class="info-label" width="38%" style="width:38%; padding:14px 16px; background:#f8fafc; border-bottom:1px solid #e5e7eb; color:#475569; font-size:14px; line-height:1.5; font-weight:700;">
                                                        {{ $label }}
                                                    </td>
                                                    <td class="info-value" width="62%" style="width:62%; padding:14px 16px; background:#ffffff; border-bottom:1px solid #e5e7eb; color:#0f172a; font-size:15px; line-height:1.5; font-weight:600;">
                                                        {{ $value }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </table>
                                    @endif

                                    @if(!empty($actionUrl))
                                        <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="margin:28px 0;">
                                            <tr>
                                                <td align="center">
                                                    <a class="button-link" href="{{ $actionUrl }}" style="display:inline-block; background:#0b5ed7; color:#ffffff; text-decoration:none; padding:14px 28px; border-radius:12px; font-size:15px; line-height:1.2; font-weight:800;">
                                                        {{ $actionText }}
                                                    </a>
                                                </td>
                                            </tr>
                                        </table>
                                    @endif

                                    @if(!empty($mailData['note']))
                                        <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="margin-top:26px;">
                                            <tr>
                                                <td style="background:#f8fafc; border-left:5px solid #0b5ed7; border-radius:10px; padding:16px 18px; color:#475569; font-size:14px; line-height:1.7;">
                                                    {{ $mailData['note'] }}
                                                </td>
                                            </tr>
                                        </table>
                                    @endif

                                    <p style="margin:30px 0 0 0; font-size:15px; line-height:1.7; color:#334155;">
                                        Regards,<br>
                                        <strong style="color:#0f172a;">HGNL Pay Team</strong>
                                    </p>

                                </td>
                            </tr>

                            {{-- Help section --}}
                            <tr>
                                <td class="mobile-padding" style="padding:0 38px 32px 38px;">
                                    <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="background:#eff6ff; border-radius:14px; border:1px solid #bfdbfe;">
                                        <tr>
                                            <td style="padding:18px 20px;">
                                                <p style="margin:0; color:#1e3a8a; font-size:14px; line-height:1.7;">
                                                    <strong>Need help?</strong><br>
                                                    Contact HGNL Pay Support at
                                                    <a href="mailto:{{ $supportEmail }}" style="color:#0b5ed7; text-decoration:none; font-weight:700;">
                                                        {{ $supportEmail }}
                                                    </a>
                                                </p>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>

                            {{-- Footer --}}
                            <tr>
                                <td style="background:#f8fafc; padding:22px 34px; border-top:1px solid #e5e7eb;">
                                    <p style="margin:0; font-size:12px; line-height:1.7; color:#64748b; text-align:center;">
                                        This is an automated email from HGNL Pay. Please do not share your account, wallet, OTP, or transaction details with anyone.
                                    </p>

                                    <p style="margin:10px 0 0 0; font-size:12px; line-height:1.7; color:#94a3b8; text-align:center;">
                                        © {{ date('Y') }} HGNL Pay. All rights reserved.
                                    </p>
                                </td>
                            </tr>

                        </table>
                    </td>
                </tr>

            </table>

        </td>
    </tr>
</table>

</body>
</html>