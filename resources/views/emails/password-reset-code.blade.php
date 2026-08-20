<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QuickWash password reset code</title>
</head>
<body style="margin:0;background:#f1f5f9;font-family:Arial,sans-serif;color:#0f172a">
    <div style="max-width:560px;margin:32px auto;padding:32px;background:#ffffff;border-radius:16px;text-align:center">
        <img src="{{ asset('images/quickwash-logo.png') }}" alt="QuickWash" width="110" height="110" style="object-fit:contain">
        <h1 style="font-size:24px;color:#0c4a6e">Reset your password</h1>
        <p>Use this verification code for the {{ ucfirst($portal) }} Portal:</p>
        <div style="margin:24px 0;font-size:34px;font-weight:700;letter-spacing:8px;color:#0284c7">{{ $code }}</div>
        <p style="color:#475569">This code expires in 10 minutes. If you did not request a password reset, you can ignore this email.</p>
    </div>
</body>
</html>
