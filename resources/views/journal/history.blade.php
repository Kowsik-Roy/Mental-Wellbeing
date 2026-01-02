@extends('layouts.app')

@section('title', 'Journal History')

@push('styles')
<style>
    /* Calendar grid styling aligned with app aesthetic */
    .calendar-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 20px;
    }

    .calendar-card {
        background: white;
        border-radius: 16px;
        padding: 20px;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.08);
        border: 1px solid #e5e7eb;
        transition: all 0.3s ease;
        cursor: pointer;
        height: 100%;
    }

    .calendar-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 16px 32px rgba(0, 0, 0, 0.15);
        border-color: #3b82f6;
    }

    .month-header {
        background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
        color: white;
        padding: 12px 20px;
        border-radius: 14px 14px 0 0;
        margin-bottom: 15px;
        font-weight: 600;
        font-size: 1.1rem;
    }

    .day-circle {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        margin-right: 12px;
        flex-shrink: 0;
    }

    .day-sun { background-color: #fee2e2; color: #dc2626; }
    .day-mon { background-color: #fef3c7; color: #d97706; }
    .day-tue { background-color: #d1fae5; color: #059669; }
    .day-wed { background-color: #e0e7ff; color: #4f46e5; }
    .day-thu { background-color: #fce7f3; color: #db2777; }
    .day-fri { background-color: #fef9c3; color: #ca8a04; }
    .day-sat { background-color: #f3e8ff; color: #7c3aed; }

    .empty-state {
        background: linear-gradient(135deg, #f3e8ff 0%, #e0e7ff 100%);
        border: 2px dashed #a78bfa;
        border-radius: 20px;
        padding: 60px 40px;
        text-align: center;
    }
</style>
@endpush

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header with Today button -->
    <div class="flex items-center justify-between mb-8">
        <!-- Page Title -->
        <h1 class="text-3xl font-bold text-gray-800">
            Journal History
        </h1>
        
        <!-- Today Button -->
        <a 
            href="{{ route('journal.today') }}" 
            class="flex items-center gap-2 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white px-6 py-3 rounded-full font-semibold text-lg shadow-md transition duration-200"
        >
            <span> Today's Journal</span>
        </a>
    </div>

    <!-- Stats Summary -->
    <div class="bg-white rounded-2xl shadow-md p-6 mb-8 border border-indigo-100 card-shadow">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="text-center p-4 bg-blue-50 rounded-xl">
                <div class="text-3xl font-bold text-blue-700">{{ $totalEntries }}</div>
                <div class="text-gray-600 mt-2">Total Entries</div>
            </div>
            <div class="text-center p-4 bg-green-50 rounded-xl">
                <div class="text-3xl font-bold text-green-700">
                    @if($entries->count() > 0)
                        @php
                            $currentMonth = now()->format('F Y');
                            $currentMonthEntries = 0;
                            foreach($entries as $month => $monthEntries) {
                                if($month === $currentMonth) {
                                    $currentMonthEntries = count($monthEntries);
                                    break;
                                }
                            }
                        @endphp
                        {{ $currentMonthEntries }}
                    @else
                        0
                    @endif
                </div>
                <div class="text-gray-600 mt-2">Entries This Month</div>
            </div>
            <div class="text-center p-4 bg-purple-50 rounded-xl">
                <div class="text-2xl font-bold text-purple-700">
                    @if($entries->count() > 0)
                        @php
                            $firstGroup = $entries->first();
                            $latestEntry = $firstGroup->first();
                        @endphp
                        {{ $latestEntry->created_at->format('M d, Y') }}
                    @else
                        N/A
                    @endif
                </div>
                <div class="text-gray-600 mt-2">Latest Entry Date</div>
            </div>
        </div>
    </div>

    @php
    use Carbon\Carbon;

    // 1) Get all journal entry dates (from your grouped $entries)
    $allDates = collect();
    foreach ($entries as $month => $monthEntries) {
        foreach ($monthEntries as $entry) {
            $allDates->push(Carbon::parse($entry->created_at)->toDateString());
        }
    }

    $uniqueDates = $allDates->unique()->values();

    // 2) Calculate current streak (today → backwards)
    $streak = 0;

    // allow streak start from today; if no today entry, allow yesterday
    $expected = Carbon::today()->toDateString();

    // Sort dates descending
    $sorted = $uniqueDates->sortDesc()->values();

    foreach ($sorted as $d) {
        if ($d === $expected) {
            $streak++;
            $expected = Carbon::parse($expected)->subDay()->toDateString();
            continue;
        }

        // allow streak to start from yesterday if no entry today
        if ($streak === 0 && $d === Carbon::yesterday()->toDateString()) {
            $streak++;
            $expected = Carbon::yesterday()->subDay()->toDateString();
            continue;
        }

        break;
    }

    // 3) Badge rules
    $badgeList = [
        ['days' => 3,  'key' => 'streak_3',  'label' => '🌱 Seedling (3-day streak)'],
        ['days' => 7,  'key' => 'streak_7',  'label' => '🔥 Flame (7-day streak)'],
        ['days' => 14, 'key' => 'streak_14', 'label' => '🌼 Bloom (14-day streak)'],
        ['days' => 30, 'key' => 'streak_30', 'label' => '🏆 Champion (30-day streak)'],
    ];

    // Use earned badges from database (passed from controller)
    $earnedBadgeKeys = $earnedBadges ?? [];
@endphp

<!-- ✅ Achievement Badges -->
<div class="bg-white rounded-2xl shadow-md p-6 mb-8 border border-indigo-100">
    <div class="flex items-start justify-between gap-4 flex-wrap">
        <div>
            <h2 class="text-xl font-bold text-gray-800">
                🏅 Achievement Badges
            </h2>
            <p class="text-gray-600 mt-1">
                Current streak: <span class="font-semibold text-indigo-700">{{ $streak }}</span> day(s)
            </p>
        </div>

        <div class="text-sm text-gray-500">
            Keep journaling daily to unlock more badges ✨
        </div>
    </div>

    <div class="mt-5 grid grid-cols-1 md:grid-cols-2 gap-4">
        @foreach($badgeList as $b)
            @php 
                $unlocked = $streak >= $b['days'] && in_array($b['key'], $earnedBadgeKeys);
            @endphp

            <div class="rounded-xl p-4 border {{ $unlocked ? 'bg-indigo-50 border-indigo-200' : 'bg-gray-50 border-gray-200' }}">
                <div class="flex items-center justify-between">
                    <div class="font-semibold {{ $unlocked ? 'text-indigo-800' : 'text-gray-600' }}">
                        {{ $b['label'] }}
                    </div>
                    <div class="text-sm {{ $unlocked ? 'text-emerald-700' : 'text-gray-500' }}">
                        {{ $unlocked ? 'Unlocked ✅' : 'Locked 🔒' }}
                    </div>
                </div>

                <div class="mt-2 text-xs {{ $unlocked ? 'text-indigo-700' : 'text-gray-500' }}">
                    Requirement: {{ $b['days'] }} consecutive days
                </div>
            </div>
        @endforeach
    </div>
</div> 

    @if($entries->count() === 0)
        <!-- Empty State -->
        <div class="empty-state">
            <div class="text-6xl mb-4">📭</div>
            <h3 class="text-2xl font-bold text-gray-800 mb-2">No Journal Entries Yet</h3>
            <p class="text-gray-600 mb-6">Start your journaling journey by writing your first entry!</p>
            <a 
                href="{{ route('journal.today') }}" 
                class="inline-flex items-center gap-2 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white px-6 py-3 rounded-full font-semibold text-lg shadow-md transition duration-200"
            >
                <span>✏️ Write First Entry</span>
            </a>
        </div>
    @else
        <div class="mb-8">
            <!-- Month Navigation / header -->
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold text-gray-800">Recent Entries</h2>
                <div class="text-gray-600">
                    Showing {{ $totalEntries }} entries
                </div>
            </div>

            <!-- Calendar Grid -->
            <div class="calendar-grid">
                @foreach($entries as $month => $monthEntries)
                    
                    <div class="bg-white rounded-2xl shadow-md overflow-hidden border border-gray-100">
                        <!-- Month Header -->
                        <div class="month-header">
                            {{ $month }}
                        </div>
                        
                        <!-- Month Entries -->
                        <div class="p-4 space-y-3">
                            @foreach($monthEntries as $entry)
                                <a 
                                    href="{{ route('journal.edit', $entry->id) }}"
                                    class="block hover:no-underline group"
                                >
                                    <div class="calendar-card">
                                        <div class="flex items-center">
                                            <!-- Day Circle -->
                                            @php
                                                $dayOfWeek = strtolower($entry->created_at->format('D'));
                                                $dayClass = 'day-' . $dayOfWeek;
                                            @endphp
                                            <div class="day-circle {{ $dayClass }}">
                                                {{ $entry->created_at->format('d') }}
                                            </div>
                                            
                                            <!-- Entry Info -->
                                            <div class="flex-1">
                                                <div class="font-semibold text-gray-800 text-lg">
                                                    {{ $entry->created_at->format('l') }}
                                                </div>
                                                <div class="text-gray-600 text-sm">
                                                    {{ $entry->created_at->format('F d, Y') }}
                                                </div>
                                                <div class="text-xs text-gray-400 mt-1">
                                                    Created at {{ $entry->created_at->format('h:i A') }}
                                                </div>
                                            </div>
                                            
                                            <!-- Mood Display -->
                                            @if($entry->mood)
                                                @php
                                                    $moodLabel = App\Models\Journal::MOODS[$entry->mood] ?? $entry->mood;
                                                    $moodEmoji = explode(' ', $moodLabel)[0] ?? '';
                                                    $moodText = explode(' ', $moodLabel)[1] ?? $moodLabel;
                                                @endphp
                                                <div class="mr-3 flex flex-col items-center">
                                                    <div class="text-2xl">{{ $moodEmoji }}</div>
                                                    <div class="text-xs text-gray-600">{{ $moodText }}</div>
                                                </div>
                                            @endif
                                            
                                            <!-- Edit Icon -->
                                            <div class="text-blue-600 opacity-0 group-hover:opacity-100 transition-opacity">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M12.586 4.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                        </div>
                                        
                                        <!-- Entry Preview -->
                                        @if($entry->content)
                                            <div class="mt-4 pt-4 border-t border-gray-100">
                                                <p class="text-gray-600 text-sm line-clamp-2">
                                                    {{ Str::limit(strip_tags($entry->content), 80) }}
                                                </p>
                                            </div>
                                        @endif

                                        <!-- Emotional Reflection Preview -->
                                        @if($entry->emotional_reflection)
                                            <div class="mt-3 pt-3 border-t border-indigo-100">
                                                <div class="flex items-start gap-2">
                                                    <span class="text-lg">💭</span>
                                                    <p class="text-xs text-indigo-700 italic line-clamp-1">
                                                        {{ Str::limit($entry->emotional_reflection, 60) }}
                                                    </p>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- View Today Button -->
        <div class="text-center mt-8">
            <a 
                href="{{ route('journal.today') }}" 
                class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white px-8 py-3 rounded-full font-semibold text-lg shadow-lg transition duration-200"
            >
                <span>📝 Continue Journaling Today</span>
            </a>
        </div>
    @endif
</div>
@endsection