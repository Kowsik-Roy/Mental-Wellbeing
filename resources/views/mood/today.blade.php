@extends('layouts.app')

@php
use App\Models\Journal;
@endphp

@section('title', 'Mood & Day Tracker')

@push('styles')
<style>
    /* Mood-based color themes using CSS variables */
    .mood-theme-default {
        --accent: #6366f1;
        --accent-soft: #eef2ff;
        --accent-gradient-from: #6366f1;
        --accent-gradient-to: #8b5cf6;
    }

    .mood-theme-happy {
        --accent: #f59e0b;
        --accent-soft: #fef3c7;
        --accent-gradient-from: #fbbf24;
        --accent-gradient-to: #f59e0b;
    }

    .mood-theme-calm {
        --accent: #3b82f6;
        --accent-soft: #dbeafe;
        --accent-gradient-from: #60a5fa;
        --accent-gradient-to: #3b82f6;
    }

    .mood-theme-neutral {
        --accent: #6b7280;
        --accent-soft: #f3f4f6;
        --accent-gradient-from: #9ca3af;
        --accent-gradient-to: #6b7280;
    }

    .mood-theme-sad {
        --accent: #a855f7;
        --accent-soft: #f3e8ff;
        --accent-gradient-from: #c084fc;
        --accent-gradient-to: #a855f7;
    }

    .mood-theme-anxious {
        --accent: #14b8a6;
        --accent-soft: #ccfbf1;
        --accent-gradient-from: #5eead4;
        --accent-gradient-to: #14b8a6;
    }

    .mood-theme-angry {
        --accent: #f43f5e;
        --accent-soft: #ffe4e6;
        --accent-gradient-from: #fb7185;
        --accent-gradient-to: #f43f5e;
    }

    /* Apply accent colors to UI elements */
    .mood-theme-default .page-header-gradient,
    .mood-theme-happy .page-header-gradient,
    .mood-theme-calm .page-header-gradient,
    .mood-theme-neutral .page-header-gradient,
    .mood-theme-sad .page-header-gradient,
    .mood-theme-anxious .page-header-gradient,
    .mood-theme-angry .page-header-gradient {
        background: linear-gradient(135deg, var(--accent-gradient-from), var(--accent-gradient-to));
        -webkit-background-clip: text;
        background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .mood-theme-default .btn-primary,
    .mood-theme-happy .btn-primary,
    .mood-theme-calm .btn-primary,
    .mood-theme-neutral .btn-primary,
    .mood-theme-sad .btn-primary,
    .mood-theme-anxious .btn-primary,
    .mood-theme-angry .btn-primary {
        background: linear-gradient(135deg, var(--accent-gradient-from), var(--accent-gradient-to));
    }

    .mood-theme-default .card-border-accent,
    .mood-theme-happy .card-border-accent,
    .mood-theme-calm .card-border-accent,
    .mood-theme-neutral .card-border-accent,
    .mood-theme-sad .card-border-accent,
    .mood-theme-anxious .card-border-accent,
    .mood-theme-angry .card-border-accent {
        border-color: var(--accent-soft);
    }

    .mood-card {
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
        border: 1px solid rgba(99, 102, 241, 0.1);
        transition: all 0.3s ease;
    }

    .mood-theme-default .mood-card,
    .mood-theme-happy .mood-card,
    .mood-theme-calm .mood-card,
    .mood-theme-neutral .mood-card,
    .mood-theme-sad .mood-card,
    .mood-theme-anxious .mood-card,
    .mood-theme-angry .mood-card {
        border-color: var(--accent-soft);
    }

    .mood-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px -5px rgba(99, 102, 241, 0.2);
    }

    .mood-theme-default .mood-card:hover,
    .mood-theme-happy .mood-card:hover,
    .mood-theme-calm .mood-card:hover,
    .mood-theme-neutral .mood-card:hover,
    .mood-theme-sad .mood-card:hover,
    .mood-theme-anxious .mood-card:hover,
    .mood-theme-angry .mood-card:hover {
        box-shadow: 0 10px 25px -5px color-mix(in srgb, var(--accent) 20%, transparent);
    }

    .mood-option {
        transition: all 0.2s ease;
        border: 2px solid transparent;
        cursor: pointer;
    }

    .mood-option:hover {
        transform: scale(1.05);
        border-color: var(--accent);
        background-color: var(--accent-soft);
    }

    .mood-option input:checked + .mood-content {
        border-color: var(--accent);
        background: linear-gradient(135deg, var(--accent-soft) 0%, color-mix(in srgb, var(--accent-soft) 80%, white) 100%);
        transform: scale(1.05);
    }

    .time-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        border-radius: 9999px;
        font-size: 0.875rem;
        font-weight: 500;
    }

    .time-badge.available {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
    }

    .time-badge.unavailable {
        background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);
        color: white;
    }

    /* Weather chip styling - cozy and comforting */
    .weather-chip {
        display: inline-flex;
        align-items: center;
        gap: 0.625rem;
        padding: 1rem 1.75rem;
        border-radius: 9999px;
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.95) 0%, rgba(250, 245, 255, 0.9) 100%);
        backdrop-filter: blur(10px);
        border: 2px solid rgba(196, 181, 253, 0.3);
        box-shadow: 0 4px 12px -2px rgba(139, 92, 246, 0.15);
        font-size: 1rem;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .weather-chip:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px -2px rgba(139, 92, 246, 0.2);
    }

    /* Weather emoji animation - enhanced */
    #weather-emoji {
        display: inline-block;
        font-size: 1.75rem;
        line-height: 1;
        animation: floatPulse 3s ease-in-out infinite;
        filter: drop-shadow(0 2px 6px rgba(0, 0, 0, 0.15));
        transition: transform 0.3s ease;
        margin-right: 0.25rem;
    }

    #weather-emoji:hover {
        transform: scale(1.15);
        filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.2));
    }

    @keyframes floatPulse {
        0%, 100% {
            transform: translateY(0px) scale(1);
        }
        25% {
            transform: translateY(-6px) scale(1.05);
        }
        50% {
            transform: translateY(-8px) scale(1.08);
        }
        75% {
            transform: translateY(-6px) scale(1.05);
        }
    }
</style>
@endpush

@section('content')
<div id="mood-page-container" class="max-w-6xl mx-auto space-y-8 mood-theme-{{ isset($moodTheme) ? $moodTheme : 'default' }}">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-8">
        <a href="{{ route('dashboard') }}" 
           class="inline-flex items-center gap-2 px-4 py-2 rounded-full font-medium btn-primary shadow-lg text-white hover:scale-105 transition transform">
            <span>🏠</span>
            <span>Dashboard</span>
        </a>

        <div class="text-center">
            <h1 class="text-4xl md:text-5xl font-bold page-header-gradient">
                🌤️ Mood & Day Tracker
            </h1>
            <p class="text-gray-600 mt-2">{{ now()->format('l, F jS, Y') }}</p>
        </div>

        <div class="w-24"></div> {{-- Spacer for alignment --}}
    </div>

    {{-- Success / Error Messages --}}
    @if (session('status'))
        <div class="rounded-2xl bg-gradient-to-r from-emerald-50 to-teal-50 border-2 border-emerald-200 text-emerald-800 px-6 py-4 text-sm font-medium shadow-sm">
            {{ session('status') }}
        </div>
    @elseif (session('error'))
        <div class="rounded-2xl bg-gradient-to-r from-rose-50 to-pink-50 border-2 border-rose-200 text-rose-800 px-6 py-4 text-sm font-medium shadow-sm">
            {{ session('error') }}
        </div>
    @endif

    {{-- Feature B: Gentle Check-in Reminder Banner --}}
    @if(isset($reminderData) && $reminderData && $reminderData['show'])
    <div id="reminder-banner" class="rounded-2xl bg-gradient-to-r from-orange-50 via-pink-50 to-purple-50 border-2 border-orange-200 p-6 shadow-sm">
        <div class="flex items-start justify-between gap-4">
            <div class="flex-1">
                <h3 class="text-lg font-semibold text-gray-800 mb-2 flex items-center gap-2">
                    <span class="text-2xl">💝</span>
                    Quick Check-in?
                </h3>
                <p class="text-gray-700 mb-4">{{ $reminderData['message'] }}</p>
                <button onclick="document.getElementById('morning-checkin').scrollIntoView({behavior: 'smooth', block: 'center'})"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-gradient-to-r from-orange-500 to-pink-500 text-white text-sm font-medium hover:shadow-lg transition transform hover:scale-105">
                    Check in now →
                </button>
            </div>
            <button onclick="document.getElementById('reminder-banner').classList.add('hidden')"
                class="text-gray-400 hover:text-gray-600 transition">
                <span class="text-xl">×</span>
            </button>
        </div>
    </div>
    @endif

    {{-- Location Settings Button --}}
    <div class="flex justify-center mb-4">
        <button id="location-settings-btn" class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/80 backdrop-blur-sm border border-purple-200 text-sm font-medium text-gray-700 hover:bg-white hover:shadow-md transition-all">
            <span>📍</span>
            <span id="location-display">{{ $userCity }}, {{ $userCountry }}</span>
            <span>✏️</span>
        </button>
    </div>

    {{-- Location Settings Modal --}}
    <div id="location-modal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-xl max-w-md w-full p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xl font-bold text-gray-800">📍 Change Location</h3>
                <button id="close-location-modal" class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
            </div>
            <form id="location-form" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">City</label>
                    <input type="text" id="location-city" name="city" value="{{ $userCity }}" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent" 
                           placeholder="e.g., Dhaka" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Country</label>
                    <input type="text" id="location-country" name="country" value="{{ $userCountry }}" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent" 
                           placeholder="e.g., Bangladesh" required>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="submit" class="flex-1 px-4 py-2 bg-gradient-to-r from-purple-500 to-pink-500 text-white rounded-lg font-medium hover:shadow-lg transition transform hover:scale-105">
                        Save Location
                    </button>
                    <button type="button" id="cancel-location" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg font-medium hover:bg-gray-200 transition">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Weather Chip (under header) --}}
    <div id="weather-chip-container" class="hidden flex flex-col items-center gap-4 mb-6">
        <div id="weather-chip" class="weather-chip">
            <span id="weather-emoji">🌦</span>
            <span id="weather-chip-text" class="text-gray-800"></span>
        </div>
        
        {{-- Today's Tip (cozy and comforting) --}}
        <div id="today-tip-container" class="hidden max-w-2xl w-full">
            <div class="rounded-2xl bg-gradient-to-r from-purple-50 via-pink-50 to-orange-50 border-2 border-purple-200 p-5 shadow-sm">
                <div class="flex items-start gap-3">
                    <span class="text-3xl">💝</span>
                    <div class="flex-1">
                        <p class="text-base font-semibold text-purple-800 mb-2">Today's Tip</p>
                        <p id="today-tip-text" class="text-base text-gray-700 leading-relaxed"></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Health Mode Banner (if mood + AQI combination detected) --}}
    <div id="health-mode-banner" class="hidden health-mode-banner">
        <div class="flex items-center gap-3">
            <span class="text-2xl">🌬️</span>
            <div>
                <p class="font-semibold text-gray-800">Air Quality Notice</p>
                <p class="text-sm text-gray-700" id="health-mode-message"></p>
            </div>
        </div>
    </div>

    {{-- Today Summary Card --}}
    <div class="bg-white rounded-3xl p-8 shadow-lg border border-indigo-100">
        <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center gap-2">
            <span class="text-3xl">📊</span>
            Today's Summary
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="rounded-2xl bg-gradient-to-br from-indigo-50 to-purple-50 p-6 border border-indigo-100">
                <div class="text-sm font-medium text-[var(--accent)] mb-2">🌅 Morning Mood</div>
                <div class="text-xl font-bold text-gray-900">
                    @if($log->morning_mood)
                        {{ Journal::MOODS[$log->morning_mood] ?? $log->morning_mood }}
                    @else
                        <span class="text-gray-400">Not set yet</span>
                    @endif
                </div>
            </div>
            <div class="rounded-2xl bg-gradient-to-br from-purple-50 to-pink-50 p-6 border border-purple-100">
                <div class="text-sm font-medium text-purple-600 mb-2">🌙 Evening Mood</div>
                <div class="text-xl font-bold text-gray-900">
                    @if($log->evening_mood)
                        {{ Journal::MOODS[$log->evening_mood] ?? $log->evening_mood }}
                    @else
                        <span class="text-gray-400">Not set yet</span>
                    @endif
                </div>
            </div>
            <div class="rounded-2xl bg-gradient-to-br from-emerald-50 to-teal-50 p-6 border border-emerald-100">
                <div class="text-sm font-medium text-emerald-600 mb-2">🏃 Active Today?</div>
                <div class="text-xl font-bold text-gray-900">
                    @if(is_null($log->was_active))
                        <span class="text-gray-400">Not answered</span>
                    @else
                        {{ $log->was_active ? 'Yes ✅' : 'No ❌' }}
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Two columns: Morning / Evening --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

        {{-- Morning Check-in --}}
        <div id="morning-checkin" class="mood-card rounded-3xl p-8 shadow-lg">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800 mb-2 flex items-center gap-2">
                        <span class="text-3xl">🌅</span>
                        Morning Check-in
                    </h2>
                    <p class="text-sm text-gray-600">Set your intention for the day</p>
                </div>
                <div>
                    @if($canCheckInMorning)
                        <span class="time-badge available">
                            <span>✓</span>
                            <span>Available</span>
                        </span>
                    @else
                        <span class="time-badge unavailable">
                            <span>⏰</span>
                            <span>6 AM - 12 PM</span>
                        </span>
                    @endif
                </div>
            </div>

            <form method="POST" action="{{ route('mood.morning') }}" class="space-y-6">
                @csrf

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-3">
                        How do you feel this morning?
                    </label>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        @foreach($journalMoods as $key => $mood)
                            <label class="mood-option">
                                <input type="radio" name="morning_mood" value="{{ $key }}" 
                                       class="hidden"
                                       @checked(old('morning_mood', $log->morning_mood) === $key)
                                       @disabled(!$canCheckInMorning)>
                                <div class="mood-content rounded-xl p-4 text-center border-2 transition-all">
                                    <div class="text-3xl mb-2">{{ explode(' ', $mood)[0] }}</div>
                                    <div class="text-xs font-medium text-gray-700">{{ explode(' ', $mood)[1] ?? '' }}</div>
                                </div>
                            </label>
                        @endforeach
                    </div>
                    @error('morning_mood')
                        <div class="text-sm text-rose-600 mt-2">{{ $message }}</div>
                    @enderror
                    @if(!$canCheckInMorning)
                        <p class="text-xs text-gray-500 mt-2">Morning check-in is only available between 6:00 AM and 12:00 PM.</p>
                    @endif
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-3">
                        What activities are coming up today?
                    </label>
                    <textarea name="planned_activities" rows="4"
                        class="w-full rounded-2xl border-2 border-gray-200 focus:border-[var(--accent)] focus:ring-2 focus:ring-[var(--accent-soft)] p-4 transition"
                        placeholder="Example: class at 10am, study, short walk, call a friend..."
                        @disabled(!$canCheckInMorning)>{{ old('planned_activities', $log->planned_activities) }}</textarea>
                    @error('planned_activities')
                        <div class="text-sm text-rose-600 mt-2">{{ $message }}</div>
                    @enderror
                </div>

                <div class="flex gap-3">
                    <button type="submit"
                        class="flex-1 inline-flex items-center justify-center gap-2 px-6 py-4 rounded-full btn-primary text-white font-semibold hover:shadow-xl transition transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed"
                        @disabled(!$canCheckInMorning)>
                        @if($log->morning_mood)
                            Update morning check-in →
                        @else
                            Save morning check-in →
                        @endif
                    </button>
                    @if($log->morning_mood)
                    <form method="POST" action="{{ route('mood.morning.clear') }}" class="inline-block" onsubmit="return confirm('Are you sure you want to clear your morning check-in?');">
                        @csrf
                        <button type="submit"
                            class="px-4 py-4 rounded-full bg-gray-200 text-gray-700 font-semibold hover:bg-gray-300 transition transform hover:scale-105"
                            title="Clear morning check-in">
                            🗑️
                        </button>
                    </form>
                    @endif
                </div>
            </form>
            
            {{-- Impact Confirmation (after morning check-in) --}}
            @if($log->morning_mood)
            <div class="mt-6 p-5 rounded-2xl bg-gradient-to-r from-blue-50 to-indigo-50 border-2 border-blue-200">
                <p class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
                    <span class="text-lg">💭</span>
                    <span>Did weather affect your mood today?</span>
                </p>
                <div class="flex gap-3">
                    <button type="button" class="impact-toggle px-4 py-2 rounded-full text-sm font-medium bg-white border-2 border-blue-300 text-blue-700 hover:bg-blue-50 transition" data-impact="weather" data-value="yes">
                        Yes 💙
                    </button>
                    <button type="button" class="impact-toggle px-4 py-2 rounded-full text-sm font-medium bg-white border-2 border-blue-300 text-blue-700 hover:bg-blue-50 transition" data-impact="weather" data-value="not-sure">
                        Not sure 🤔
                    </button>
                    <button type="button" class="impact-toggle px-4 py-2 rounded-full text-sm font-medium bg-white border-2 border-blue-300 text-blue-700 hover:bg-blue-50 transition" data-impact="weather" data-value="no">
                        No ✨
                    </button>
                </div>
            </div>
            @endif
        </div>

        {{-- Evening Check-out --}}
        <div class="mood-card rounded-3xl p-8 shadow-lg">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800 mb-2 flex items-center gap-2">
                        <span class="text-3xl">🌙</span>
                        Evening Check-out
                    </h2>
                    <p class="text-sm text-gray-600">Reflect on how the day went</p>
                </div>
                <div>
                    @if($canCheckInEvening)
                        <span class="time-badge available">
                            <span>✓</span>
                            <span>Available</span>
                        </span>
                    @else
                        <span class="time-badge unavailable">
                            <span>⏰</span>
                            <span>12 PM - 6 PM</span>
                        </span>
                    @endif
                </div>
            </div>

            <form method="POST" action="{{ route('mood.evening') }}" class="space-y-6">
                @csrf

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-3">
                        How do you feel now?
                    </label>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        @foreach($journalMoods as $key => $mood)
                            <label class="mood-option">
                                <input type="radio" name="evening_mood" value="{{ $key }}" 
                                       class="hidden"
                                       @checked(old('evening_mood', $log->evening_mood) === $key)
                                       @disabled(!$canCheckInEvening)>
                                <div class="mood-content rounded-xl p-4 text-center border-2 transition-all">
                                    <div class="text-3xl mb-2">{{ explode(' ', $mood)[0] }}</div>
                                    <div class="text-xs font-medium text-gray-700">{{ explode(' ', $mood)[1] ?? '' }}</div>
                                </div>
                            </label>
                        @endforeach
                    </div>
                    @error('evening_mood')
                        <div class="text-sm text-rose-600 mt-2">{{ $message }}</div>
                    @enderror
                    @if(!$canCheckInEvening)
                        <p class="text-xs text-gray-500 mt-2">Evening check-in is only available between 12:00 PM and 6:00 PM.</p>
                    @endif
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-3">
                        How did the day go?
                    </label>
                    <textarea name="day_summary" rows="4"
                        class="w-full rounded-2xl border-2 border-gray-200 focus:border-[var(--accent)] focus:ring-2 focus:ring-[var(--accent-soft)] p-4 transition"
                        placeholder="A quick reflection on your day..."
                        @disabled(!$canCheckInEvening)>{{ old('day_summary', $log->day_summary) }}</textarea>
                    @error('day_summary')
                        <div class="text-sm text-rose-600 mt-2">{{ $message }}</div>
                    @enderror
                </div>

                <div class="rounded-2xl bg-gradient-to-br from-gray-50 to-slate-50 p-5 border border-gray-200">
                    <label class="block text-sm font-semibold text-gray-700 mb-3">Were you active today?</label>
                    <div class="flex items-center gap-6">
                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="was_active" value="1"
                                class="w-5 h-5 text-[var(--accent)] focus:ring-[var(--accent)]"
                                @checked(old('was_active', is_null($log->was_active) ? null : ($log->was_active ? '1' : '0')) === '1')
                                @disabled(!$canCheckInEvening)>
                            <span class="text-sm font-medium text-gray-700">Yes ✅</span>
                        </label>
                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="was_active" value="0"
                                class="w-5 h-5 text-[var(--accent)] focus:ring-[var(--accent)]"
                                @checked(old('was_active', is_null($log->was_active) ? null : ($log->was_active ? '1' : '0')) === '0')
                                @disabled(!$canCheckInEvening)>
                            <span class="text-sm font-medium text-gray-700">No ❌</span>
                        </label>
                    </div>
                    @error('was_active')
                        <div class="text-sm text-rose-600 mt-2">{{ $message }}</div>
                    @enderror
                </div>

                <div class="flex gap-3">
                    <button type="submit"
                        class="flex-1 inline-flex items-center justify-center gap-2 px-6 py-4 rounded-full btn-primary text-white font-semibold hover:shadow-xl transition transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed"
                        @disabled(!$canCheckInEvening)>
                        @if($log->evening_mood)
                            Update evening check-out →
                        @else
                            Save evening check-out →
                        @endif
                    </button>
                    @if($log->evening_mood)
                    <form method="POST" action="{{ route('mood.evening.clear') }}" class="inline-block" onsubmit="return confirm('Are you sure you want to clear your evening check-out?');">
                        @csrf
                        <button type="submit"
                            class="px-4 py-4 rounded-full bg-gray-200 text-gray-700 font-semibold hover:bg-gray-300 transition transform hover:scale-105"
                            title="Clear evening check-out">
                            🗑️
                        </button>
                    </form>
                    @endif
                </div>
            </form>
            
            {{-- Impact Confirmation (after evening check-in) --}}
            @if($log->evening_mood)
            <div class="mt-6 p-5 rounded-2xl bg-gradient-to-r from-purple-50 to-pink-50 border-2 border-purple-200">
                <p class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
                    <span class="text-lg">💭</span>
                    <span>Did air quality affect your energy?</span>
                </p>
                <div class="flex gap-3">
                    <button type="button" class="impact-toggle px-4 py-2 rounded-full text-sm font-medium bg-white border-2 border-purple-300 text-purple-700 hover:bg-purple-50 transition" data-impact="air" data-value="yes">
                        Yes 💜
                    </button>
                    <button type="button" class="impact-toggle px-4 py-2 rounded-full text-sm font-medium bg-white border-2 border-purple-300 text-purple-700 hover:bg-purple-50 transition" data-impact="air" data-value="no">
                        No ✨
                    </button>
                </div>
            </div>
            @endif
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    // Add visual feedback for mood selection
    document.querySelectorAll('.mood-option input[type="radio"]').forEach(radio => {
        radio.addEventListener('change', function() {
            // Remove selected class from all
            document.querySelectorAll('.mood-option').forEach(opt => {
                opt.querySelector('.mood-content').classList.remove('selected');
            });
            // Add selected class to checked one
            if (this.checked) {
                this.closest('.mood-option').querySelector('.mood-content').classList.add('selected');
            }
        });
        
        // Initialize selected state
        if (radio.checked) {
            radio.closest('.mood-option').querySelector('.mood-content').classList.add('selected');
        }
    });

    // Load today's context (weather and air quality)
    document.addEventListener('DOMContentLoaded', function() {
        loadContext();
    });

    function loadContext() {
        const apiUrl = '{{ route("api.context.today") }}';
        
        fetch(apiUrl, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
            credentials: 'same-origin'
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            const pageContainer = document.getElementById('mood-page-container');
            const weatherChipContainer = document.getElementById('weather-chip-container');
            const weatherChipText = document.getElementById('weather-chip-text');
            const weatherEmoji = document.getElementById('weather-emoji');
            const healthModeBanner = document.getElementById('health-mode-banner');
            const healthModeMessage = document.getElementById('health-mode-message');

            if (!pageContainer) {
                console.error('Page container not found');
                return;
            }

            // Apply temperature-based background tint
            if (data && data.weather && data.weather.temp_c !== undefined) {
                const temp = data.weather.temp_c;
                pageContainer.classList.remove('weather-hot', 'weather-cool', 'weather-normal');
                if (temp >= 32) {
                    pageContainer.classList.add('weather-hot');
                } else if (temp <= 20) {
                    pageContainer.classList.add('weather-cool');
                } else {
                    pageContainer.classList.add('weather-normal');
                }
            }

            // Show weather chip with AQI
            if (data && data.weather && data.weather.temp_c !== undefined) {
                const city = data.city || 'Dhaka';
                const temp = data.weather.temp_c;
                const condition = data.weather.condition || '';
                
                // Set weather emoji based on condition (with CSS float animation)
                if (data.weather.is_rainy || condition.toLowerCase().includes('rain')) {
                    weatherEmoji.textContent = '🌧️';
                } else if (temp >= 32 || condition.toLowerCase().includes('clear') || condition.toLowerCase().includes('sunny')) {
                    weatherEmoji.textContent = '☀️';
                } else if (condition.toLowerCase().includes('cloud') || condition.toLowerCase().includes('fog')) {
                    weatherEmoji.textContent = '☁️';
                } else if (condition.toLowerCase().includes('haze') || condition.toLowerCase().includes('mist')) {
                    weatherEmoji.textContent = '🌫️';
                } else if (temp <= 20) {
                    weatherEmoji.textContent = '❄️';
                } else {
                    weatherEmoji.textContent = '🌦';
                }

                // Build chip text: "Dhaka: Cloudy, 17°C • AQI 162 (Unhealthy)"
                let chipText = `${city}: ${condition}, ${temp}°C`;
                
                // Add AQI if available (but show emotional context separately)
                if (data.air && data.air.aqi !== undefined && data.air.aqi !== null) {
                    chipText += ` • AQI ${data.air.aqi} (${data.air.level})`;
                }
                
                weatherChipText.textContent = chipText;
                weatherChipContainer.classList.remove('hidden');
            }

            // Show Today's Tip
            const todayTipContainer = document.getElementById('today-tip-container');
            const todayTipText = document.getElementById('today-tip-text');
            if (data && data.tips && data.tips.today_tip && todayTipContainer && todayTipText) {
                todayTipText.textContent = data.tips.today_tip;
                todayTipContainer.classList.remove('hidden');
            }

            // Show health mode banner if mood + AQI combination detected
            if (data && data.health_mode && data.air) {
                healthModeMessage.textContent = 'Air quality is high today; consider indoor activity';
                healthModeBanner.classList.remove('hidden');
                
                // Apply soft caution theme
                pageContainer.classList.add('mood-theme-health-caution');
            } else {
                healthModeBanner.classList.add('hidden');
            }
        })
        .catch(error => {
            console.error('Error loading context:', error);
            // Show error message in context bar for debugging
            const contextBar = document.getElementById('context-bar');
            if (contextBar) {
                contextBar.classList.remove('hidden');
                const cityEl = document.getElementById('context-city');
                const weatherEl = document.getElementById('context-weather');
                const airEl = document.getElementById('context-air');
                if (cityEl) cityEl.textContent = 'Dhaka';
                if (weatherEl) weatherEl.textContent = 'Weather unavailable';
                if (airEl) airEl.textContent = 'Air quality unavailable';
            }
        });
    }

    // Location settings modal
    document.getElementById('location-settings-btn')?.addEventListener('click', function() {
        document.getElementById('location-modal').classList.remove('hidden');
    });

    document.getElementById('close-location-modal')?.addEventListener('click', function() {
        document.getElementById('location-modal').classList.add('hidden');
    });

    document.getElementById('cancel-location')?.addEventListener('click', function() {
        document.getElementById('location-modal').classList.add('hidden');
    });

    // Close modal on outside click
    document.getElementById('location-modal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            this.classList.add('hidden');
        }
    });

    // Handle location form submission
    document.getElementById('location-form')?.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const city = document.getElementById('location-city').value.trim();
        const country = document.getElementById('location-country').value.trim();
        
        if (!city || !country) {
            alert('Please enter both city and country');
            return;
        }
        
        // Get CSRF token
        const csrfToken = document.querySelector('input[name="_token"]')?.value || 
                         document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        
        fetch('{{ route("api.location.update") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
            credentials: 'same-origin',
            body: JSON.stringify({ city, country })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('location-display').textContent = `${data.city}, ${data.country}`;
                document.getElementById('location-modal').classList.add('hidden');
                
                // Reload weather data with new location
                setTimeout(() => loadContext(), 500);
                
                // Show success message
                const successMsg = document.createElement('div');
                successMsg.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50';
                successMsg.textContent = '✓ Location updated! Refreshing weather...';
                document.body.appendChild(successMsg);
                setTimeout(() => successMsg.remove(), 3000);
            } else {
                alert(data.message || 'Failed to update location');
            }
        })
        .catch(error => {
            console.error('Error updating location:', error);
            alert('Failed to update location. Please try again.');
        });
    });

    // Impact toggle buttons (cute and comforting)
    document.querySelectorAll('.impact-toggle').forEach(button => {
        button.addEventListener('click', function() {
            // Remove active state from siblings
            const siblings = this.parentElement.querySelectorAll('.impact-toggle');
            siblings.forEach(btn => {
                btn.classList.remove('bg-gradient-to-r', 'from-purple-500', 'to-pink-500', 'text-white', 'border-purple-500');
                btn.classList.add('bg-white', 'border-2');
            });
            
            // Add active state to clicked button
            this.classList.remove('bg-white', 'border-2');
            this.classList.add('bg-gradient-to-r', 'from-purple-500', 'to-pink-500', 'text-white', 'border-purple-500');
            
            // Store the selection (you can save this to backend later if needed)
            const impact = this.dataset.impact;
            const value = this.dataset.value;
            console.log(`${impact} impact: ${value}`);
            
            // Show a gentle confirmation
            const originalText = this.textContent;
            this.textContent = '✓ Saved';
            setTimeout(() => {
                this.textContent = originalText;
            }, 1500);
        });
    });

</script>
@endpush
