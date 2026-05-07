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

                    <!-- Header -->
                    <tr>
                        <td style="background:#03558C;padding:28px 32px;text-align:center">
                            <h1 style="margin:0;font-size:22px;font-weight:700;color:#fff;letter-spacing:.02em">ASOG TBI
                            </h1>
                            <p
                                style="margin:6px 0 0;font-size:12px;color:rgba(255,255,255,.7);letter-spacing:.08em;text-transform:uppercase">
                                New Contact Message
                            </p>
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="padding:32px 32px 24px">
                            <p style="margin:0 0 20px;font-size:15px;color:#1e293b;line-height:1.6">
                                You received a new message from the <strong>Contact Us</strong> form on the ASOG TBI
                                website.
                            </p>

                            <table width="100%" cellpadding="0" cellspacing="0"
                                style="border:1px solid #eceae6;border-radius:6px;overflow:hidden;margin-bottom:24px">
                                <?= view('emails/_row', ['label' => 'Name',    'value' => esc($name)]) ?>
                                <?= view('emails/_row', ['label' => 'Email',   'value' => '<a href="mailto:' . esc($email) . '" style="color:#03558C;text-decoration:none">' . esc($email) . '</a>']) ?>
                                <?= view('emails/_row', ['label' => 'Message', 'value' => nl2br(esc($message))]) ?>
                                <?= view('emails/_row', ['label' => 'Sent At', 'value' => esc($sentAt)]) ?>
                            </table>

                            <p style="margin:0;font-size:14px;color:#64748b;line-height:1.6">
                                You can reply directly to <strong style="color:#1e293b"><?= esc($email) ?></strong> or
                                view all messages in the admin dashboard.
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
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