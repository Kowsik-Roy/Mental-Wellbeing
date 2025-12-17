<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Weekly Wellness Summary</title>
</head>
<body style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background-color: #f3f4f6; padding: 24px;">
    <table width="100%" cellpadding="0" cellspacing="0" style="max-width: 640px; margin: 0 auto; background-color: #ffffff; border-radius: 16px; overflow: hidden;">
        <tr>
            <td style="background: linear-gradient(135deg,#4f46e5,#10b981); padding: 20px 24px; color: #ffffff;">
                <h1 style="margin: 0; font-size: 22px;">Your Weekly Wellness Summary</h1>
                <p style="margin: 4px 0 0; font-size: 14px;">{{ $periodLabel }}</p>
            </td>
        </tr>
        <tr>
            <td style="padding: 20px 24px; color: #111827; font-size: 14px;">
                <p>Hi {{ $user->name }},</p>
                <p>Here's a quick overview of your mood trends and habit progress from the past week.</p>

                @if($moodStats->count() > 0)
                    <h2 style="font-size: 16px; margin-top: 20px; margin-bottom: 8px;">Mood Trends</h2>
                    <ul style="padding-left: 20px; margin: 0;">
                        @foreach($moodStats as $row)
                            @php
                                $label = \App\Models\Journal::MOODS[$row->mood] ?? $row->mood;
                            @endphp
                            <li style="margin-bottom: 4px;">
                                <strong>{{ $label }}</strong>: {{ $row->count }} day(s)
                            </li>
                        @endforeach
                    </ul>
                @else
                    <h2 style="font-size: 16px; margin-top: 20px; margin-bottom: 8px;">Mood Trends</h2>
                    <p style="margin: 0;">No moods were logged this week. Try logging your mood with your journal entries to see trends here.</p>
                @endif

                <h2 style="font-size: 16px; margin-top: 24px; margin-bottom: 8px;">Habit Progress</h2>
                @if(count($habitStats) > 0)
                    <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse: collapse; font-size: 13px;">
                        <thead>
                            <tr>
                                <th align="left" style="padding: 6px 4px; border-bottom: 1px solid #e5e7eb;">Habit</th>
                                <th align="left" style="padding: 6px 4px; border-bottom: 1px solid #e5e7eb;">Weekly %</th>
                                <th align="left" style="padding: 6px 4px; border-bottom: 1px solid #e5e7eb;">Current Streak</th>
                                <th align="left" style="padding: 6px 4px; border-bottom: 1px solid #e5e7eb;">Best Streak</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($habitStats as $habit)
                                <tr>
                                    <td style="padding: 6px 4px; border-bottom: 1px solid #f3f4f6;">{{ $habit['title'] }}</td>
                                    <td style="padding: 6px 4px; border-bottom: 1px solid #f3f4f6;">{{ $habit['weekly_completion'] }}%</td>
                                    <td style="padding: 6px 4px; border-bottom: 1px solid #f3f4f6;">{{ $habit['current_streak'] }} day(s)</td>
                                    <td style="padding: 6px 4px; border-bottom: 1px solid #f3f4f6;">{{ $habit['best_streak'] }} day(s)</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p style="margin: 0;">You don't have any active habits yet. Create some habits to start tracking your progress.</p>
                @endif

                <p style="margin-top: 24px;">Keep going – small steps add up over time. 💚</p>
                <p style="margin-top: 8px; font-size: 12px; color: #6b7280;">You are receiving this email because you have an account on Mental Wellness Companion.</p>
            </td>
        </tr>
    </table>
</body>
</html>


