@extends('layouts.app')

@section('content')

<!-- DAILY QUOTE SECTION -->
<div class="mb-20">
    <div class="relative mx-auto max-w-3xl">
        <!-- Outer rainbow frame -->
        <div class="rounded-[3.5rem] p-2
                    bg-gradient-to-br from-pink-300 via-purple-300 to-sky-300
                    shadow-[0_25px_80px_-20px_rgba(180,120,255,0.7)]">
            <!-- Inner card -->
            <div class="relative rounded-[3.2rem]
                        bg-gradient-to-br from-pink-200 via-purple-200 to-sky-200
                        px-14 py-14 text-center overflow-hidden">
                <!-- Sticker blobs -->
                <div class="absolute -top-16 -left-16 w-64 h-64 bg-pink-200 rounded-full opacity-40 blur-2xl"></div>
                <div class="absolute -bottom-20 -right-20 w-72 h-72 bg-purple-200 rounded-full opacity-40 blur-2xl"></div>
                
                <!-- Stars & hearts -->
                <span class="absolute top-8 left-10 text-pink-400 text-2xl">★</span>
                <span class="absolute bottom-14 left-16 text-purple-400 text-xl">♡</span>
                <span class="absolute top-16 right-12 text-pink-400 text-2xl">♡</span>
                <span class="absolute bottom-10 right-20 text-sky-400 text-xl">✦</span>
                
                <!-- Mascot -->
                <div class="absolute -top-3 left-1/2 -translate-x-1/2
                            w-20 h-20 bg-yellow-200 rounded-full
                            flex items-center justify-center text-4xl
                            shadow-md">
                    😊
                </div>
                
                <!-- Quote -->
                <blockquote
                    class="mt-6 text-[2.1rem] leading-snug text-gray-800
                            font-medium"
                    style="font-family: 'Patrick Hand', cursive;"
                    >
                    {{ $dailyQuote['text'] }}
                </blockquote>
                
                <!-- Author -->
                <div class="mt-6 text-base font-semibold text-purple-500">
                    — {{ $dailyQuote['author'] }}
                </div>
            </div>
        </div>
    </div>
</div>

<section class="mb-10 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div class="text-center md:text-left">
        <h1 class="text-4xl md:text-5xl font-semibold mb-2 text-indigo-900 whitespace-nowrap">
            Welcome {{ Auth::user()->name }}!
        </h1>
        <p class="text-gray-700 text-lg md:text-xl">
            A gentle space designed for reflection, healing, and growth.
        </p>
    </div>
</section>


<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-14">

    {{-- Daily Journal --}}
    <div class="bg-white rounded-3xl p-6 shadow-sm card-hover">
        <h3 class="font-semibold mb-2">Daily Journal</h3>
        <p class="text-sm text-gray-600 mb-6">Write your thoughts freely.</p>
        <a href="{{ route('journal.today') }}" class="text-indigo-600 text-sm font-medium hover:underline">
            Write today →
        </a>
    </div>

    {{-- Daily Habits --}}
    <div class="bg-white rounded-3xl p-6 shadow-sm card-hover">
        <h3 class="font-semibold mb-2">Daily Habits</h3>
        <p class="text-sm text-gray-600 mb-6">Build gentle routines.</p>
        <a href="{{ route('habits.index') }}" class="text-indigo-600 text-sm font-medium hover:underline">
            View habits →
        </a>
    </div>

    {{-- Journal History --}}
    <div class="bg-white rounded-3xl p-6 shadow-sm card-hover">
        <h3 class="font-semibold mb-2">Journal History</h3>
        <p class="text-sm text-gray-600 mb-6">Reflect on your journey.</p>
        <a href="{{ route('journal.history') }}" class="text-indigo-600 text-sm font-medium hover:underline">
            View history →
        </a>
    </div>

    {{-- Meditation --}}
    <div class="bg-white rounded-3xl p-6 shadow-sm card-hover">
        <h3 class="font-semibold mb-2">Meditation Timer</h3>
        <p class="text-sm text-gray-600 mb-6">
            Relax, breathe, and focus with a guided timer.
        </p>
        <a href="{{ route('meditation') }}" class="text-indigo-600 text-sm font-medium hover:underline">
            Start meditation →
        </a>
    </div>

    {{-- Emotion Dashboard --}}
    <div class="bg-white rounded-3xl p-6 shadow-sm card-hover">
        <h3 class="font-semibold mb-2">Weekly Summary</h3>
        <p class="text-sm text-gray-600 mb-6">
            See your weekly mood trends and habit completion.
        </p>
        <a href="{{ route('dashboard.weekly-summary') }}" class="text-indigo-600 text-sm font-medium hover:underline">
            View summary →
        </a>
    </div>

    {{-- Mood & Day Tracker --}}
    <div class="bg-white rounded-3xl p-6 shadow-sm card-hover">
        <h3 class="font-semibold mb-2">Mood & Day Tracker</h3>
        <p class="text-sm text-gray-600 mb-6">
            Morning mood + plans, evening reflection & activity.
        </p>
        <a href="{{ route('mood.today') }}" class="text-indigo-600 text-sm font-medium hover:underline">
            Open tracker →
        </a>
    </div>

    {{-- Emergency Contact --}}
    <div class="bg-white rounded-3xl p-6 shadow-sm card-hover">
        <h3 class="font-semibold mb-2">Emergency Contact</h3>
        <p class="text-sm text-gray-600 mb-6">
            Manage who gets notified if you confirm an alert.
        </p>
        <a href="{{ route('emergency.edit') }}" class="text-indigo-600 text-sm font-medium hover:underline">
            Update contact →
        </a>
    </div>

    {{-- AI Support Chat (NEW) --}}
    <div class="bg-white rounded-3xl p-6 shadow-sm card-hover">
        <h3 class="font-semibold mb-2">AI Support Chat</h3>
        <p class="text-sm text-gray-600 mb-6">
            Talk about your feelings and receive gentle, cozy support.
        </p>
        <a href="{{ route('ai.chat') }}" class="text-indigo-600 text-sm font-medium hover:underline">
            Open chat →
        </a>
    </div>

</div>

<div class="bg-indigo-100 rounded-3xl p-10 card-hover">
    <h2 class="text-xl font-semibold mb-3">Create a new habit</h2>
    <p class="text-gray-700 mb-6 max-w-lg">
        Small steps, taken consistently, create meaningful change.
    </p>
    <a href="{{ route('habits.create') }}"
       class="inline-block px-5 py-3 rounded-full bg-indigo-700 text-white text-sm font-medium button-hover hover:bg-indigo-800">
        Create habit
    </a>
</div>

@endsection
