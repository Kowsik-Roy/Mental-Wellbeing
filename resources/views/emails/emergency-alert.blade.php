<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>WellBeing Emergency Alert</title>
</head>
<body style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background:#f5f5f7; padding:24px;">
    <div style="max-width: 600px; margin: 0 auto; background:#ffffff; border-radius:16px; padding:32px; box-shadow:0 10px 30px rgba(0,0,0,0.1); border-left: 4px solid #ef4444;">
        <h1 style="font-size:24px; margin-bottom:16px; color:#dc2626;">
            ⚠️ WellBeing Alert
        </h1>

        <p style="font-size:16px; color:#374151; margin-bottom:16px; line-height:1.6;">
            Hello {{ $emergencyContact->name }},
        </p>

        <p style="font-size:16px; color:#374151; margin-bottom:16px; line-height:1.6;">
            This is an automated alert from WellBeing. {{ $user->name }} has logged a sad mood for <strong>3 consecutive days</strong> in their journal entries.
        </p>

        <div style="background:#fef2f2; border:1px solid #fecaca; border-radius:12px; padding:20px; margin:24px 0;">
            <p style="font-size:15px; color:#991b1b; margin:0; line-height:1.6;">
                <strong>What this means:</strong><br>
                While this doesn't necessarily indicate a crisis, it's a signal that {{ $user->name }} may be going through a difficult time and could benefit from your support and care.
            </p>
        </div>

        <p style="font-size:16px; color:#374151; margin-bottom:16px; line-height:1.6;">
            <strong>Suggested actions:</strong>
        </p>
        <ul style="font-size:15px; color:#4b5563; margin-bottom:24px; padding-left:24px; line-height:1.8;">
            <li>Reach out to {{ $user->name }} with care and empathy</li>
            <li>Listen without judgment</li>
            <li>Offer your presence and support</li>
            <li>Encourage them to seek professional help if needed</li>
        </ul>

        <p style="font-size:14px; color:#6b7280; margin-top:24px; padding-top:24px; border-top:1px solid #e5e7eb;">
            This alert is sent automatically when a user has 3 consecutive days of sad mood entries. If you believe this is an error or have concerns, please contact {{ $user->name }} directly.
        </p>

        <p style="font-size:12px; color:#9ca3af; margin-top:24px;">
            With care,<br>
            The WellBeing Team
        </p>
    </div>
</body>
</html>
