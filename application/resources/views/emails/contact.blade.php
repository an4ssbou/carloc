<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>New Contact Message - CarLoc</title>
</head>
<body style="margin: 0; padding: 0; background-color: #F1F5F9; font-family: Arial, sans-serif;">
    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #F1F5F9; padding: 32px 0;">
        <tr>
            <td align="center">
                <table border="0" cellpadding="0" cellspacing="0" width="600" style="background-color: #FFFFFF; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);">
                    <tr>
                        <td style="background-color: #0D2137; padding: 32px 40px;">
                            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td style="font-size: 24px; font-weight: bold; color: #FFFFFF;">
                                        CarLoc<span style="color: #E8A020; vertical-align: super; font-size: 12px; margin-left: 2px;">&reg;</span>
                                    </td>
                                    <td align="right">
                                        <span style="background-color: #E8A020; color: #0D2137; font-size: 11px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; padding: 6px 14px; border-radius: 20px;">New Contact Message</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2" style="padding-top: 20px;">
                                        <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                            <tr>
                                                <td style="height: 2px; background-color: #E8A020;"></td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2" align="center" style="padding-top: 24px;">
                                        <div style="color: #FFFFFF; font-size: 22px; font-weight: bold; margin-bottom: 4px;">You have received a new message</div>
                                        <div style="color: #94A3B8; font-size: 13px; font-style: italic;">Contact form submission details</div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td style="background-color: #E8A020; padding: 14px 40px; text-align: center; color: #0D2137; font-size: 14px; font-weight: bold;">
                            Contact request received
                        </td>
                    </tr>

                    <tr>
                        <td style="padding: 32px 40px;">
                            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td style="border-left: 4px solid #E8A020; padding-left: 12px; color: #0D2137; font-size: 16px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px;">
                                        Contact Details
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding-top: 20px;">
                                        <table border="0" cellpadding="0" cellspacing="12" width="100%">
                                            <tr>
                                                <td width="50%" style="background-color: #F1F5F9; border-radius: 6px; padding: 16px;">
                                                    <div style="color: #64748B; font-size: 10px; text-transform: uppercase; margin-bottom: 4px;">Subject</div>
                                                    <div style="color: #0D2137; font-size: 14px; font-weight: bold;">{{ $subject }}</div>
                                                </td>
                                                <td width="50%" style="background-color: #F1F5F9; border-radius: 6px; padding: 16px;">
                                                    <div style="color: #64748B; font-size: 10px; text-transform: uppercase; margin-bottom: 4px;">Email</div>
                                                    <div style="color: #0D2137; font-size: 14px; font-weight: bold;">
                                                        <a href="mailto:{{ $email }}" style="color: #E8A020; text-decoration: underline;">{{ $email }}</a>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="2" style="background-color: #F1F5F9; border-radius: 6px; padding: 16px;">
                                                    <div style="color: #64748B; font-size: 10px; text-transform: uppercase; margin-bottom: 4px;">Phone</div>
                                                    <div style="color: #0D2137; font-size: 14px; font-weight: bold;">{{ $phone ?: 'N/A' }}</div>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding: 0 40px 32px 40px;">
                            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td style="border-left: 4px solid #E8A020; padding-left: 12px; color: #0D2137; font-size: 16px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px;">
                                        Message
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding-top: 20px;">
                                        <table border="0" cellpadding="24" cellspacing="0" width="100%" style="background-color: #FDFAF5; border: 1px solid #E8D5B0; border-radius: 8px; border-left: 3px solid #E8A020;">
                                            <tr>
                                                <td style="color: #475569; font-size: 16px; line-height: 1.8; white-space: pre-line;">{{ $texte }}</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td style="background-color: #0D2137; padding: 28px 40px; text-align: center;">
                            <div style="margin-bottom: 16px;">
                                <span style="color: #FFFFFF; font-weight: bold;">CarLoc</span>
                                <span style="color: #E8A020; margin: 0 8px;">&bull;</span>
                                <span style="color: #E8A020; font-size: 12px;">Contact Notification</span>
                            </div>
                            <div style="color: #64748B; font-size: 12px; font-style: italic;">
                                This is an automated notification sent from the contact form.
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
