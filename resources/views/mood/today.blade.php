@extends('layouts.app')

@section('title', 'Mood Tracker')

@section('content')
<div class="max-w-5xl mx-auto space-y-8">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-2 text-gray-700 hover:text-gray-900 font-medium">
            <span class="text-xl">🏠</span>
            <span>Dashboard</span>
        </a>

        <h1 class="text-2xl md:text-3xl font-bold text-gray-800">
            <span class="text-indigo-600">🌤️</span> Mood & Day Tracker
        </h1>

        <div class="text-sm text-gray-500">
            {{ now()->format('D, M d') }}
        </div>
    </div>

    {{-- Success / Error --}}
    @if (session('status'))
        <div class="rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 text-sm">
            {{ session('status') }}
        </div>
    @elseif (session('error'))
        <div class="rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 text-sm">
            {{ session('error') }}
        </div>
    @endif

    {{-- Alert Confirmation (shows only when streak triggers) --}}
    @if(!empty($needsAlert) && $needsAlert)

        <div class="rounded-3xl border border-amber-200 bg-amber-50 px-6 py-5 shadow-sm">
            <div class="flex items-start gap-3">
                <div class="text-2xl">⚠️</div>

                <div class="flex-1">
                    <h3 class="text-base font-semibold text-amber-900">
                        We noticed you’ve been feeling low for a few days.
                    </h3>

                    <p class="text-sm text-amber-800 mt-1">
                        Do you want us to notify your emergency contact for support?
                        We will only send it if you confirm.
                    </p>

                    <div class="mt-4 flex flex-wrap gap-3">
                        <form method="POST" action="{{ route('mood.alert.confirm') }}">
                            @csrf
                            <button type="submit"
                                class="inline-flex items-center gap-2 px-5 py-3 rounded-full bg-rose-600 text-white text-sm font-medium hover:bg-rose-700 shadow-md">
                                Yes, send alert
                            </button>
                        </form>

                        <form method="POST" action="{{ route('mood.alert.dismiss') }}">
                            @csrf
                            <button type="submit"
                                class="inline-flex items-center gap-2 px-5 py-3 rounded-full bg-gray-200 text-gray-800 text-sm font-medium hover:bg-gray-300">
                                No, not now
                            </button>
                        </form>
                    </div>

                    <p class="text-xs text-amber-700 mt-3">
                        If you’re in immediate danger, please contact local emergency services.
                    </p>
                </div>
            </div>
        </div>
    @endif

    {{-- Today Summary Card --}}
    <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100">
        <h2 class="text-lg font-semibold text-gray-800 mb-2">Today so far</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
            <div class="rounded-2xl bg-indigo-50 p-4">
                <div class="text-gray-500">Morning mood</div>
                <div class="text-gray-900 font-semibold mt-1">
                    {{ $log->morning_mood ?? 'Not set yet' }}
                </div>
            </div>
            <div class="rounded-2xl bg-purple-50 p-4">
                <div class="text-gray-500">Evening mood</div>
                <div class="text-gray-900 font-semibold mt-1">
                    {{ $log->evening_mood ?? 'Not set yet' }}
                </div>
            </div>
            <div class="rounded-2xl bg-emerald-50 p-4">
                <div class="text-gray-500">Active today?</div>
                <div class="text-gray-900 font-semibold mt-1">
                    @if(is_null($log->was_active))
                        Not answered
                    @else
                        {{ $log->was_active ? 'Yes ✅' : 'No ❌' }}
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Two columns: Morning / Evening --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Morning Check-in --}}
        <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100">
            <h2 class="text-xl font-semibold text-gray-800 mb-1">🌅 Morning check-in</h2>
            <p class="text-sm text-gray-600 mb-5">Set your intention for the day.</p>

            <form method="POST" action="{{ route('mood.morning') }}" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">How do you feel this morning?</label>
                    <select name="morning_mood"
                        class="w-full rounded-2xl border-gray-200 focus:border-indigo-400 focus:ring-indigo-400">
                        <option value="">Choose one…</option>
                        @php
                            $moods = ['Happy 😊','Calm 😌','Okay 🙂','Anxious 😟','Sad 😔','Angry 😠','Tired 😴'];
                        @endphp
                        @foreach($moods as $m)
                            <option value="{{ $m }}" @selected(old('morning_mood', $log->morning_mood) === $m)>
                                {{ $m }}
                            </option>
                        @endforeach
                    </select>
                    @error('morning_mood')
                        <div class="text-sm text-rose-600 mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">What activities are coming up today?</label>
                    <textarea name="planned_activities" rows="4"
                        class="w-full rounded-2xl border-gray-200 focus:border-indigo-400 focus:ring-indigo-400"
                        placeholder="Example: class at 10am, study, short walk, call a friend...">{{ old('planned_activities', $log->planned_activities) }}</textarea>
                    @error('planned_activities')
                        <div class="text-sm text-rose-600 mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit"
                    class="inline-flex items-center gap-2 px-5 py-3 rounded-full bg-indigo-700 text-white text-sm font-medium hover:bg-indigo-800 shadow-md">
                    Save morning check-in →
                </button>
            </form>
        </div>

        {{-- Evening Check-out --}}
        <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100">
            <h2 class="text-xl font-semibold text-gray-800 mb-1">🌙 Evening check-out</h2>
            <p class="text-sm text-gray-600 mb-5">Reflect on how the day actually went.</p>

            <form method="POST" action="{{ route('mood.evening') }}" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">How do you feel now?</label>
                    <select name="evening_mood"
                        class="w-full rounded-2xl border-gray-200 focus:border-indigo-400 focus:ring-indigo-400">
                        <option value="">Choose one…</option>
                        @foreach($moods as $m)
                            <option value="{{ $m }}" @selected(old('evening_mood', $log->evening_mood) === $m)>
                                {{ $m }}
                            </option>
                        @endforeach
                    </select>
                    @error('evening_mood')
                        <div class="text-sm text-rose-600 mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">How did the day go?</label>
                    <textarea name="day_summary" rows="4"
                        class="w-full rounded-2xl border-gray-200 focus:border-indigo-400 focus:ring-indigo-400"
                        placeholder="A quick reflection…">{{ old('day_summary', $log->day_summary) }}</textarea>
                    @error('day_summary')
                        <div class="text-sm text-rose-600 mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="rounded-2xl bg-gray-50 p-4 border border-gray-100">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Were you active today?</label>
                    <div class="flex items-center gap-4">
                        <label class="inline-flex items-center gap-2">
                            <input type="radio" name="was_active" value="1"
                                @checked(old('was_active', is_null($log->was_active) ? null : ($log->was_active ? '1' : '0')) === '1')>
                            <span class="text-sm text-gray-700">Yes</span>
                        </label>
                        <label class="inline-flex items-center gap-2">
                            <input type="radio" name="was_active" value="0"
                                @checked(old('was_active', is_null($log->was_active) ? null : ($log->was_active ? '1' : '0')) === '0')>
                            <span class="text-sm text-gray-700">No</span>
                        </label>
                    </div>
                    @error('was_active')
                        <div class="text-sm text-rose-600 mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit"
                    class="inline-flex items-center gap-2 px-5 py-3 rounded-full bg-purple-700 text-white text-sm font-medium hover:bg-purple-800 shadow-md">
                    Save evening check-out →
                </button>
            </form>
        </div>
    </div>

</div>
@endsection
