<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $emailSubject }}</title>
</head>
<body style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #0f172a; margin: 0; padding: 40px 16px;">
    <table role="presentation" width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" style="max-width: 600px; background-color: #ffffff; border-radius: 24px; overflow: hidden; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.3);" border="0" cellspacing="0" cellpadding="0">
                    <!-- Brand Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #022c22 0%, #064e3b 50%, #0f172a 100%); padding: 36px 32px; text-align: center;">
                            <div style="display: inline-block; width: 44px; height: 44px; line-height: 44px; background-color: #10b981; border-radius: 12px; font-weight: 900; font-size: 22px; color: #ffffff; text-align: center; margin-bottom: 12px;">
                                ⚡
                            </div>
                            <h1 style="color: #ffffff; margin: 0; font-size: 22px; font-weight: 800; letter-spacing: -0.5px;">WorkForge</h1>
                            <p style="color: #6ee7b7; margin: 4px 0 0 0; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px;">Freelance Marketplace & Escrow</p>
                        </td>
                    </tr>

                    <!-- Body Content -->
                    <tr>
                        <td style="padding: 36px 32px 28px 32px;">
                            <h2 style="color: #0f172a; font-size: 18px; font-weight: 700; margin: 0 0 16px 0;">
                                {{ $greeting }}
                            </h2>

                            <p style="color: #334155; font-size: 14px; line-height: 1.6; margin: 0 0 24px 0;">
                                {!! nl2br(e($mainMessage)) !!}
                            </p>

                            @if(!empty($details))
                                <div style="background-color: #f8fafc; border-radius: 16px; border: 1px solid #e2e8f0; padding: 20px; margin-bottom: 24px;">
                                    <table role="presentation" width="100%" border="0" cellspacing="0" cellpadding="0">
                                        @foreach($details as $label => $val)
                                            <tr>
                                                <td style="padding: 6px 0; font-size: 12px; color: #64748b; font-weight: 600; text-transform: uppercase; width: 40%;">
                                                    {{ $label }}
                                                </td>
                                                <td style="padding: 6px 0; font-size: 13px; color: #0f172a; font-weight: 700; text-align: right;">
                                                    {{ $val }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </table>
                                </div>
                            @endif

                            @if(!empty($actionUrl) && !empty($actionText))
                                <div style="text-align: center; margin: 32px 0 16px 0;">
                                    <a href="{{ $actionUrl }}" style="display: inline-block; background: linear-gradient(135deg, #059669 0%, #0d9488 100%); color: #ffffff; text-decoration: none; font-size: 13px; font-weight: 800; padding: 14px 32px; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.3);">
                                        {{ $actionText }} &rarr;
                                    </a>
                                </div>
                            @endif
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f8fafc; border-top: 1px solid #f1f5f9; padding: 24px 32px; text-align: center;">
                            <p style="color: #94a3b8; font-size: 11px; margin: 0; line-height: 1.5;">
                                This is an automated notification from your WorkForge account.<br>
                                Secured by 256-bit SSL & Dodo Payments Escrow Protection.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
