<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject }} - CarLoc</title>
</head>
<body style="margin: 0; padding: 0; background-color: #F1F5F9; font-family: Arial, sans-serif; -webkit-font-smoothing: antialiased;">
    <table width="100%" border="0" cellpadding="0" cellspacing="0" style="background-color: #F1F5F9; padding: 40px 10px;">
        <tr>
            <td align="center">
                <table width="600" border="0" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 12px rgba(13, 33, 55, 0.08);">
                    <tr>
                        <td bgcolor="#0D2137" style="padding: 28px 40px; border-bottom: 3px solid #E8A020;">
                            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td align="left" style="color: #ffffff; font-size: 22px; font-weight: bold; letter-spacing: -0.5px;">
                                        CarLoc
                                    </td>
                                    <td align="right">
                                        <table border="0" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td bgcolor="#FEF3C7" style="padding: 4px 12px; border-radius: 9999px;">
                                                    <span style="color: #92400E; font-size: 11px; font-weight: bold; text-transform: uppercase;">Notification</span>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td align="center" style="padding: 48px 40px 32px 40px;">
                            <table border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td align="center" bgcolor="#FEF3C7" style="width: 72px; height: 72px; border-radius: 36px; color: #92400E; font-size: 32px; line-height: 72px;">
                                        i
                                    </td>
                                </tr>
                            </table>
                            <h1 style="color: #0D2137; font-size: 22px; font-weight: bold; margin: 24px 0 8px 0; padding: 0;">{{ $subject }}</h1>
                            <p style="color: #64748B; font-size: 14px; font-style: italic; margin: 0; padding: 0;">Consultez les details de cette notification sur CarLoc.</p>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding: 0 40px 32px 40px;">
                            <p style="color: #0D2137; font-size: 16px; font-weight: bold; margin: 0 0 12px 0;">Bonjour {{ $recipientName }},</p>
                            <p style="color: #475569; font-size: 14px; line-height: 1.6; margin: 0 0 24px 0;">
                                {{ $body }}
                            </p>

                            <table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#F8FAFC" style="border: 1px solid #E2E8F0; border-radius: 8px; border-left: 4px solid #E8A020;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <div style="color: #64748B; font-size: 11px; text-transform: uppercase; margin-bottom: 8px;">Action</div>
                                        <div style="color: #0D2137; font-size: 14px; line-height: 1.6;">
                                            Cliquez sur le bouton ci-dessous pour acceder a la page liee a cette notification.
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td align="center" style="padding: 0 40px 48px 40px;">
                            <a href="{{ $actionUrl }}" style="background-color: #E8A020; color: #0D2137; padding: 16px 32px; border-radius: 4px; text-decoration: none; font-size: 14px; font-weight: bold; display: inline-block;">
                                {{ $actionText }} ->
                            </a>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding: 0 40px 40px 40px;">
                            <table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#F1F5F9" style="border-radius: 8px;">
                                <tr>
                                    <td align="center" style="padding: 20px 10px; border-right: 1px solid #E2E8F0;" width="33%">
                                        <div style="color: #64748B; font-size: 10px; text-transform: uppercase; margin-bottom: 4px;">Plateforme</div>
                                        <div style="color: #0D2137; font-size: 12px; font-weight: bold;">CarLoc</div>
                                    </td>
                                    <td align="center" style="padding: 20px 10px; border-right: 1px solid #E2E8F0;" width="33%">
                                        <div style="color: #64748B; font-size: 10px; text-transform: uppercase; margin-bottom: 4px;">Type</div>
                                        <div style="color: #0D2137; font-size: 12px; font-weight: bold;">Notification email</div>
                                    </td>
                                    <td align="center" style="padding: 20px 10px;" width="34%">
                                        <div style="color: #64748B; font-size: 10px; text-transform: uppercase; margin-bottom: 4px;">Destination</div>
                                        <div style="color: #0D2137; font-size: 12px; font-weight: bold;">Page de details</div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td align="center" bgcolor="#0D2137" style="padding: 40px;">
                            <table border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="padding: 0 10px;">
                                        <a href="{{ url('/contact') }}" style="color: #ffffff; font-size: 12px; text-decoration: none; font-weight: bold;">Support</a>
                                    </td>
                                    <td style="padding: 0 10px; color: #E8A020;">|</td>
                                    <td style="padding: 0 10px;">
                                        <a href="{{ url('/notifications') }}" style="color: #ffffff; font-size: 12px; text-decoration: none; font-weight: bold;">Notifications</a>
                                    </td>
                                    <td style="padding: 0 10px; color: #E8A020;">|</td>
                                    <td style="padding: 0 10px;">
                                        <a href="{{ url('/') }}" style="color: #ffffff; font-size: 12px; text-decoration: none; font-weight: bold;">Accueil</a>
                                    </td>
                                </tr>
                            </table>
                            <p style="color: #94A3B8; font-size: 11px; margin: 24px 0 0 0; line-height: 1.5;">
                                © {{ now()->year }} CarLoc. Tous droits reserves.<br>
                                Casablanca, Maroc.
                            </p>
                        </td>
                    </tr>
                </table>

                <table width="600" border="0" cellpadding="0" cellspacing="0">
                    <tr>
                        <td align="center" style="padding: 20px 0; color: #94A3B8; font-size: 11px;">
                            Vous recevez cet email car une notification a ete emise sur votre compte CarLoc.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
