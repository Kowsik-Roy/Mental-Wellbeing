@extends('layouts.app')

@section('content')

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
