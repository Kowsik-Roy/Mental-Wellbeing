@php
    $title = $type === 'registration'
        ? 'Verify your email address'
        : 'Confirm your password change';
@endphp

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ $title }} - WellBeing</title>
</head>
<body style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background:#f5f5f7; padding:24px;">
    <div style="max-width: 480px; margin: 0 auto; background:#ffffff; border-radius:16px; padding:24px; box-shadow:0 10px 30px rgba(15,23,42,0.08);">
        <h1 style="font-size:20px; margin-bottom:8px; color:#111827;">
            {{ $title }}
        </h1>

        <p style="font-size:14px; color:#4b5563; margin-bottom:16px;">
            Hi {{ $user->name }}, 
        </p>

        @if($type === 'registration')
            <p style="font-size:14px; color:#4b5563; margin-bottom:16px;">
                Use the verification code below to complete your registration for the WellBeing app.
            </p>
        @else
            <p style="font-size:14px; color:#4b5563; margin-bottom:16px;">
                Use the verification code below to confirm your password change request in the WellBeing app.
            </p>
        @endif

        <div style="text-align:center; margin:24px 0;">
            <div style="display:inline-block; padding:12px 24px; border-radius:999px; background:#4f46e5; color:#ffffff; font-size:24px; letter-spacing:6px; font-weight:600;">
                {{ $code }}
            </div>
        </div>

        <p style="font-size:13px; color:#6b7280; margin-bottom:12px;">
            This code will expire in 3 minutes. If you did not request this, you can safely ignore this email.
        </p>

        <p style="font-size:12px; color:#9ca3af; margin-top:24px;">
            With care,<br>
            The WellBeing Team
        </p>
    </div>
</body>
</html>

