<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
</head>

<body style="margin:0;padding:0;background:#f4f3f0;font-family:'Segoe UI',Tahoma,Geneva,Verdana,sans-serif">
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f3f0;padding:32px 16px">
        <tr>
            <td align="center">
                <table width="580" cellpadding="0" cellspacing="0"
                    style="background:#fff;border-radius:8px;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,.06)">

                    <tr>
                        <td style="background:#03558C;padding:28px 32px;text-align:center">
                            <h1 style="margin:0;font-size:22px;font-weight:700;color:#fff;letter-spacing:.02em">ASOG TBI
                            </h1>
                            <p
                                style="margin:6px 0 0;font-size:12px;color:rgba(255,255,255,.7);letter-spacing:.08em;text-transform:uppercase">
                                Password Reset Request
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:32px 32px 24px">
                            <p style="margin:0 0 20px;font-size:15px;color:#1e293b;line-height:1.6">
                                Hi <strong><?= esc($adminName) ?></strong>,
                            </p>

                            <p style="margin:0 0 20px;font-size:15px;color:#1e293b;line-height:1.6">
                                We received a request to reset the password for your ASOG TBI admin account.
                            </p>

                            <p style="margin:0 0 24px;font-size:15px;color:#1e293b;line-height:1.6">
                                Click the button below to set a new password. This link will expire in 1 hour.
                            </p>

                            <table cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="border-radius:4px;background:#03558C">
                                        <a href="<?= esc($resetUrl) ?>"
                                            style="display:inline-block;padding:12px 28px;font-size:14px;font-weight:600;color:#fff;text-decoration:none;border-radius:4px;letter-spacing:.02em">
                                            Reset your password
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin:24px 0 12px;font-size:14px;color:#64748b;line-height:1.6">
                                If the button doesn't work, copy and paste this link into your browser:
                            </p>
                            <p style="margin:0 0 24px;font-size:13px;color:#03558C;word-break:break-all">
                                <?= esc($resetUrl) ?>
                            </p>

                            <p style="margin:0;font-size:14px;color:#64748b;line-height:1.6">
                                If you did not request this change, you can safely ignore this email.
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td style="background:#fafaf9;border-top:1px solid #eceae6;padding:20px 32px;text-align:center">
                            <p style="margin:0;font-size:11px;color:#94a3b8;line-height:1.5">
                                This is an automated notification from the ASOG TBI website.<br>
                                Please do not reply directly to this email.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>

</html>
