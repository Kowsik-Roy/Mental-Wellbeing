@extends('layouts.app')

@section('title', 'Weekly Summary')

@push('styles')
<style>
    .chart-container {
        position: relative;
        height: 400px;
        margin: 20px 0;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .pie-chart {
        width: 350px;
        height: 350px;
        position: relative;
    }
    .pie-segment {
        transition: opacity 0.3s ease;
        cursor: pointer;
    }
    .pie-segment:hover {
        opacity: 0.8;
    }
    .pie-legend {
        display: flex;
        flex-wrap: wrap;
        gap: 16px;
        justify-content: center;
        margin-top: 20px;
    }
    .legend-item {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .legend-color {
        width: 20px;
        height: 20px;
        border-radius: 4px;
    }
</style>
@endpush

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Dashboard Button -->
    <div class="mb-4 flex justify-start">
        <a href="{{ route('dashboard') }}" 
           class="inline-flex items-center gap-2 px-4 py-2 rounded-full font-medium bg-gradient-to-r from-purple-400 to-indigo-500 shadow-lg text-white hover:scale-105 hover:from-purple-300 hover:to-indigo-400 transition transform text-sm">
            <i class="fas fa-home"></i>
            <span>Dashboard</span>
        </a>
    </div>

    <!-- Header -->
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-indigo-900 mb-2">Weekly Summary</h1>
            <p class="text-gray-600">
                {{ $periodStart->format('M d, Y') }} - {{ $periodEnd->format('M d, Y') }}
            </p>
        </div>
        <div class="flex gap-3">
            <form method="POST" action="{{ route('dashboard.send-summary') }}">
                @csrf
                <button
                    type="submit"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-full font-medium bg-gradient-to-r from-indigo-600 to-purple-600 shadow-lg text-white hover:scale-105 hover:from-indigo-500 hover:to-purple-500 transition transform text-sm">
                    <i class="fas fa-envelope"></i>
                    <span>Send Email Summary</span>
                </button>
            </form>
        </div>
    </div>

    @if (session('status'))
        <div class="mb-6 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 text-sm">
            {{ session('status') }}
        </div>
    @elseif (session('error'))
        <div class="mb-6 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 text-sm">
            {{ session('error') }}
        </div>
    @endif

    <!-- Mood Trends Chart -->
    <div class="bg-white rounded-3xl p-8 shadow-sm mb-6">
        <h2 class="text-2xl font-semibold text-indigo-900 mb-6">Weekly Mood Trends</h2>
        
        @php
            // Prepare data for 7 days
            $daysData = [];
            $dayColors = [
                '#6366f1', // indigo
                '#8b5cf6', // purple
                '#ec4899', // pink
                '#f59e0b', // amber
                '#10b981', // emerald
                '#06b6d4', // cyan
                '#3b82f6', // blue
            ];
            $dayNames = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
            
            $totalDaysWithData = 0;
            for ($i = 6; $i >= 0; $i--) {
                $date = now()->subDays($i)->format('Y-m-d');
                $dayOfWeek = now()->subDays($i)->dayOfWeek;
                $dayName = $dayNames[$dayOfWeek];
                $dayLabel = now()->subDays($i)->format('D');
                
                $dayMoods = $dailyMoods->get($date) ?? collect();
                $totalMoodsForDay = $dayMoods->sum('count');
                $hasData = $totalMoodsForDay > 0;
                
                if ($hasData) {
                    $totalDaysWithData++;
                }
                
                // Get most common mood for the day
                $mostCommonMood = null;
                $mostCommonMoodCount = 0;
                if ($hasData) {
                    foreach ($dayMoods as $moodData) {
                        if ($moodData->count > $mostCommonMoodCount) {
                            $mostCommonMoodCount = $moodData->count;
                            $mostCommonMood = $moodData->mood;
                        }
                    }
                }
                
                                // Get mood label for display
                                $moodEmoji = '';
                                $moodText = '';
                                if ($hasData && $mostCommonMood) {
                                    $moodLabel = \App\Models\Journal::MOODS[$mostCommonMood] ?? $mostCommonMood;
                                    $moodParts = explode(' ', $moodLabel, 2);
                                    $moodEmoji = $moodParts[0] ?? '';
                                    $moodText = $moodParts[1] ?? $moodLabel;
                                }
                                
                                $daysData[] = [
                    'date' => $date,
                    'dayName' => $dayName,
                    'dayLabel' => $dayLabel,
                    'hasData' => $hasData,
                    'totalMoods' => $totalMoodsForDay,
                    'mostCommonMood' => $mostCommonMood,
                    'moodEmoji' => $moodEmoji,
                    'moodText' => $moodText,
                    'color' => $dayColors[$i],
                ];
            }
            
            $currentAngle = -90;
            $radius = 150;
            $cx = 175;
            $cy = 175;
            $anglePerDay = 360 / 7; // Equal segments for 7 days
        @endphp
        
        <!-- Pie Chart -->
            <div class="chart-container">
                <div class="pie-chart">
                    <svg width="350" height="350" viewBox="0 0 350 350">
                        @foreach($daysData as $index => $day)
                            @php
                                $startAngle = $currentAngle;
                                $endAngle = $currentAngle + $anglePerDay;
                                $midAngle = ($startAngle + $endAngle) / 2;
                                
                                $x1 = $cx + $radius * cos(deg2rad($startAngle));
                                $y1 = $cy + $radius * sin(deg2rad($startAngle));
                                $x2 = $cx + $radius * cos(deg2rad($endAngle));
                                $y2 = $cy + $radius * sin(deg2rad($endAngle));
                                
                                $largeArcFlag = $anglePerDay > 180 ? 1 : 0;
                                
                                // For empty segments, alternate between lighter and darker shades
                                if ($day['hasData']) {
                                    $color = $day['color'];
                                } else {
                                    // Alternate pattern: lighter (#e5e7eb) for even indices, darker (#d1d5db) for odd indices
                                    $color = ($index % 2 == 0) ? '#e5e7eb' : '#d1d5db';
                                }
                                
                                $pathData = "M $cx $cy L $x1 $y1 A $radius $radius 0 $largeArcFlag 1 $x2 $y2 Z";
                                
                                // Calculate label position (middle of segment, pulled down a bit)
                                $labelRadius = $radius * 0.65;
                                $labelX = $cx + $labelRadius * cos(deg2rad($midAngle));
                                $labelY = $cy + $labelRadius * sin(deg2rad($midAngle)) + 8; // Pull down by 8px
                                
                                $currentAngle += $anglePerDay;
                            @endphp
                            <path 
                                d="{{ $pathData }}" 
                                fill="{{ $color }}" 
                                class="pie-segment"
                                data-day="{{ $day['dayName'] }}"
                                data-date="{{ $day['date'] }}"
                                data-has-data="{{ $day['hasData'] ? '1' : '0' }}"
                                data-mood="{{ $day['mostCommonMood'] ?? '' }}"
                                data-count="{{ $day['totalMoods'] }}"
                            />
                            <!-- Emoji and mood label on segment -->
                            @if($day['hasData'] && $day['moodEmoji'])
                                <text x="{{ $labelX }}" y="{{ $labelY - 1 }}" text-anchor="middle" font-size="24" fill="#1f2937">
                                    {{ $day['moodEmoji'] }}
                                </text>
                                <text x="{{ $labelX }}" y="{{ $labelY + 15 }}" text-anchor="middle" font-size="10" font-weight="bold" fill="#4b5563">
                                    {{ strlen($day['moodText']) > 8 ? substr($day['moodText'], 0, 8) . '...' : $day['moodText'] }}
                                </text>
                            @else
                                <text x="{{ $labelX }}" y="{{ $labelY }}" text-anchor="middle" font-size="10" fill="#9ca3af">
                                    No data
                                </text>
                            @endif
                        @endforeach
                        <!-- Center circle for donut effect -->
                        <circle cx="{{ $cx }}" cy="{{ $cy }}" r="65" fill="white"/>
                        <text x="{{ $cx }}" y="{{ $cy - 10 }}" text-anchor="middle" font-size="24" font-weight="bold" fill="#4f46e5">
                            {{ $totalDaysWithData }}/7
                        </text>
                        <text x="{{ $cx }}" y="{{ $cy + 15 }}" text-anchor="middle" font-size="14" fill="#6b7280">
                            Days with Data
                        </text>
                    </svg>
                </div>
            </div>

            <!-- Legend -->
            <div class="pie-legend">
                @foreach($daysData as $index => $day)
                    @php
                        $moodEmoji = '';
                        $moodText = '';
                        if ($day['hasData'] && $day['mostCommonMood']) {
                            $moodLabel = \App\Models\Journal::MOODS[$day['mostCommonMood']] ?? $day['mostCommonMood'];
                            $moodParts = explode(' ', $moodLabel, 2);
                            $moodEmoji = $moodParts[0] ?? '';
                            $moodText = $moodParts[1] ?? $moodLabel;
                        }
                        // Use same alternating pattern for empty segments
                        $legendColor = $day['hasData'] ? $day['color'] : (($index % 2 == 0) ? '#e5e7eb' : '#d1d5db');
                    @endphp
                    <div class="legend-item">
                        <div class="legend-color" style="background-color: {{ $legendColor }}"></div>
                        <span class="text-sm font-medium text-gray-700">
                            <strong>{{ $day['dayLabel'] }}</strong> ({{ $day['date'] }}):
                            @if($day['hasData'])
                                {{ $moodEmoji }} {{ $moodText }} - {{ $day['totalMoods'] }} entry/entries
                            @else
                                <span class="text-gray-400">No data</span>
                            @endif
                        </span>
                    </div>
                @endforeach
            </div>
    </div>

    <!-- Journal Completion Streak -->
    <div class="bg-white rounded-3xl p-8 shadow-sm mb-6">
        <h2 class="text-2xl font-semibold text-indigo-900 mb-6">Journal Completion</h2>
        
        <div class="mb-6">
            <div class="flex items-center gap-4 mb-4">
                <div class="bg-indigo-100 rounded-xl p-4">
                    <div class="text-sm text-gray-600">Current Streak</div>
                    <div class="text-3xl font-bold text-indigo-600">{{ $journalStreak }} days</div>
                </div>
            </div>
            
            <div class="mb-4">
                <h3 class="text-lg font-semibold text-gray-800 mb-3">Last 7 Days</h3>
                <div class="flex gap-2">
                    @php
                        $dayLabels = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
                    @endphp
                    @for($i = 6; $i >= 0; $i--)
                        @php
                            $date = now()->subDays($i)->format('Y-m-d');
                            $dayOfWeek = now()->subDays($i)->dayOfWeek;
                            $dayLabel = $dayLabels[$dayOfWeek];
                            $completed = $journalCompletionByDay[$date] ?? false;
                        @endphp
                        <div class="flex-1 text-center">
                            <div class="text-xs text-gray-600 mb-1">{{ $dayLabel }}</div>
                            <div class="w-full h-12 rounded-lg flex items-center justify-center {{ $completed ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-400' }}" title="{{ $date }}">
                                {{ $completed ? '✓' : '✗' }}
                            </div>
                            <div class="text-xs text-gray-500 mt-1">{{ now()->subDays($i)->format('M j') }}</div>
                        </div>
                    @endfor
                </div>
            </div>
        </div>
    </div>

    <!-- Habit Completion Chart -->
    <div class="bg-white rounded-3xl p-8 shadow-sm">
        <h2 class="text-2xl font-semibold text-indigo-900 mb-6">Habit Completion</h2>
        
        @if(count($habitStats) > 0)
            <div class="space-y-6">
                @foreach($habitStats as $habit)
                    <div class="border border-gray-200 rounded-xl p-6 mb-4">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-800">{{ $habit['title'] }}</h3>
                            <div class="flex gap-4 text-sm">
                                <span class="text-gray-600">
                                    <span class="font-semibold">Streak:</span> {{ $habit['current_streak'] }} days
                                </span>
                                <span class="text-gray-600">
                                    <span class="font-semibold">Best:</span> {{ $habit['best_streak'] }} days
                                </span>
                            </div>
                        </div>
                        
                        <!-- Progress Bar -->
                        <div class="mb-4">
                            <div class="flex justify-between text-sm text-gray-600 mb-1">
                                <span>Weekly Completion</span>
                                <span class="font-semibold">{{ $habit['weekly_completion'] }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-4">
                                <div class="bg-gradient-to-r from-indigo-500 to-purple-500 h-4 rounded-full transition-all duration-500" 
                                     style="width: {{ min($habit['weekly_completion'], 100) }}%"></div>
                            </div>
                        </div>
                        
                        <!-- Daily Completion -->
                        <div>
                            <h4 class="text-sm font-semibold text-gray-700 mb-2">Last 7 Days</h4>
                            <div class="flex gap-2">
                                @php
                                    $dayLabels = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
                                @endphp
                                @for($i = 6; $i >= 0; $i--)
                                    @php
                                        $date = now()->subDays($i)->format('Y-m-d');
                                        $dayOfWeek = now()->subDays($i)->dayOfWeek;
                                        $dayLabel = $dayLabels[$dayOfWeek];
                                        $completed = $habit['daily_completion'][$date] ?? false;
                                    @endphp
                                    <div class="flex-1 text-center">
                                        <div class="text-xs text-gray-600 mb-1">{{ $dayLabel }}</div>
                                        <div class="w-full h-10 rounded-lg flex items-center justify-center {{ $completed ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-400' }}" title="{{ $date }}">
                                            {{ $completed ? '✓' : '✗' }}
                                        </div>
                                    </div>
                                @endfor
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-12">
                <div class="text-6xl mb-4">✅</div>
                <h3 class="text-lg font-medium text-gray-800 mb-2">No active habits</h3>
                <p class="text-gray-600 mb-4">Create habits to start tracking your progress.</p>
                <a href="{{ route('habits.create') }}" class="inline-block px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                    Create Habit
                </a>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const segments = document.querySelectorAll('.pie-segment');
    const tooltip = document.createElement('div');
    tooltip.style.cssText = 'position: absolute; background: rgba(0,0,0,0.8); color: white; padding: 8px 12px; border-radius: 6px; pointer-events: none; opacity: 0; transition: opacity 0.3s; z-index: 1000; font-size: 14px;';
    document.body.appendChild(tooltip);

    const moodLabels = {
        'happy': '😊 Happy',
        'sad': '😢 Sad',
        'excited': '🎉 Excited',
        'angry': '😠 Angry',
        'anxious': '😰 Anxious',
        'calm': '😌 Calm',
        'tired': '😴 Tired',
        'neutral': '😐 Neutral'
    };

    segments.forEach(segment => {
        segment.addEventListener('mouseenter', function(e) {
            const day = this.getAttribute('data-day');
            const date = this.getAttribute('data-date');
            const hasData = this.getAttribute('data-has-data') === '1';
            const mood = this.getAttribute('data-mood');
            const count = this.getAttribute('data-count');
            
            if (hasData) {
                const moodLabel = moodLabels[mood] || mood;
                tooltip.textContent = `${day} (${date}): ${moodLabel} - ${count} entry/entries`;
            } else {
                tooltip.textContent = `${day} (${date}): No data`;
            }
            tooltip.style.opacity = '1';
        });

        segment.addEventListener('mousemove', function(e) {
            tooltip.style.left = (e.pageX + 10) + 'px';
            tooltip.style.top = (e.pageY - 10) + 'px';
        });

        segment.addEventListener('mouseleave', function() {
            tooltip.style.opacity = '0';
        });
    });
});
</script>
@endpush


