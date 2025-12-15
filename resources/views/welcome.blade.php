<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>WellBeing | Welcome</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="alternate icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
    @endif
</head>
<body class="bg-gradient-to-br from-slate-900 via-indigo-900 to-slate-950 text-white min-h-screen">
    <div class="max-w-6xl mx-auto px-6 py-12 flex flex-col gap-12">
        <header class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-xl bg-indigo-500/20 border border-indigo-400/30 flex items-center justify-center text-xl font-bold">WB</div>
                <div>
                    <p class="text-lg font-semibold">WellBeing</p>
                    <p class="text-sm text-indigo-100/70">Habits • Journal • Mood</p>
                </div>
            </div>
            @if (Route::has('login'))
                <nav class="flex items-center gap-3 text-sm">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="px-4 py-2 rounded-lg bg-white text-slate-900 font-semibold shadow hover:shadow-lg transition">Go to Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="px-4 py-2 rounded-lg border border-white/30 text-white hover:bg-white hover:text-slate-900 transition">Log in</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="px-4 py-2 rounded-lg bg-indigo-500 text-white font-semibold hover:bg-indigo-400 transition">Get Started</a>
                        @endif
                    @endauth
                </nav>
            @endif
        </header>

        <main class="grid grid-cols-1 lg:grid-cols-2 gap-10 items-center">
            <div class="space-y-6">
                <p class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-indigo-500/20 border border-indigo-400/30 text-indigo-100">Mental Wellbeing Toolkit</p>
                <h1 class="text-4xl lg:text-5xl font-bold leading-tight">
                    Track habits, journal moods and stay consistent every day.
                </h1>
                <p class="text-lg text-indigo-100/80 leading-relaxed">
                    WellBeing brings your daily habits, mood journaling and personal progress into a single, calming space. Build streaks, reflect on your day and celebrate the small wins.
                </p>
                <div class="flex flex-wrap gap-4">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="px-5 py-3 rounded-lg bg-white text-slate-900 font-semibold shadow hover:shadow-lg transition">Open Dashboard</a>
                    @else
                        <a href="{{ route('register') }}" class="px-5 py-3 rounded-lg bg-indigo-500 text-white font-semibold shadow hover:shadow-lg transition">Create your account</a>
                        <a href="{{ route('login') }}" class="px-5 py-3 rounded-lg border border-white/30 text-white hover:bg-white hover:text-slate-900 transition">Log in</a>
                    @endauth
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
                    <div class="p-4 rounded-lg bg_WHITE/5 border border-white/10">
                        <p class="text-sm font-semibold">Habit Tracking</p>
                        <p class="text-indigo-100/70 mt-1">Daily/weekly goals with streaks and completion rates.</p>
                    </div>
                    <div class="p-4 rounded-lg bg-white/5 border border-white/10">
                        <p class="text-sm font-semibold">Journal & Mood</p>
                        <p class="text-indigo-100/70 mt-1">Log entries, capture moods, and reflect on prompts.</p>
                    </div>
                    <div class="p-4 rounded-lg bg-white/5 border border-white/10">
                        <p class="text-sm font-semibold">Progress Insights</p>
                        <p class="text-indigo-100/70 mt-1">See consistency scores and long term trends.</p>
                    </div>
                </div>
        </main>
    </div>
</body>
</html>
