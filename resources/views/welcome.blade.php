<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>WellBeing | Welcome</title>
<link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
<link rel="alternate icon" type="image/png" href="{{ asset('favicon.png') }}">
<script src="https://cdn.tailwindcss.com"></script>
<style>
body {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    background: linear-gradient(135deg, #0f172a, #1e293b, #0f172a);
    color: #fff;
    overflow-x: hidden;
}
main { position: relative; z-index: 10; }

@keyframes floatUpDown {0%,100%{transform:translateY(0);}50%{transform:translateY(-12px);}}
@keyframes floatSideways {0%,100%{transform:translateX(0);}50%{transform:translateX(20px);}}
@keyframes twinkle {0%,100%{opacity:1;}50%{opacity:0.4;}}

/* Cards hover */
.card-hover:hover { transform: translateY(-6px); box-shadow: 0 16px 28px rgba(0,0,0,0.15); transition: 0.3s; }

/* Stars */
.star { position: absolute; border-radius:50%; background:white; opacity:0.8; animation: twinkle 2s infinite; }
</style>
</head>
<body class="relative min-h-screen overflow-hidden">

<!-- STARFIELD -->
@for ($i = 0; $i < 80; $i++)
<div class="star" style="top: {{ rand(5, 95) }}%; left: {{ rand(5, 95) }}%; width: {{ rand(1,3) }}px; height: {{ rand(1,3) }}px; animation-duration: {{ rand(2,5) }}s;"></div>
@endfor

<!-- HEADER -->
<header class="relative z-20 max-w-6xl mx-auto px-6 py-6 flex items-center justify-between">
    <div class="flex items-center gap-3">
        <div class="w-16 h-16 rounded-full bg-indigo-600 flex items-center justify-center font-bold text-white text-lg shadow-lg relative overflow-hidden">
            MWC
                <!-- tiny star -->
            <div class="absolute top-1 right-1 w-3 h-3 bg-yellow-300 rounded-full animate-[floatUpDown_2s_ease-in-out_infinite]"></div>
                <!-- tiny cloud -->
            <div class="absolute bottom-1 left-1 w-5 h-2 bg-white rounded-full animate-[floatSideways_3s_linear_infinite]"></div>
         </div>
        <div>
            <div class="font-semibold text-lg">Mental Wellness Companion</div>
            <p class="text-sm text-indigo-200/70">Habits • Journal • Mood</p>
        </div>
    </div>
    <nav class="flex items-center gap-3 text-sm">
        @auth
            <a href="{{ url('/dashboard') }}" class="px-4 py-2 rounded-full bg-white text-slate-900 font-semibold shadow hover:shadow-lg transition">Dashboard</a>
        @else
            <a href="{{ route('login') }}" class="px-4 py-2 rounded-full border border-white/30 text-white hover:bg-white hover:text-slate-900 transition">Log in</a>
            @if (Route::has('register'))
                <a href="{{ route('register') }}" class="px-4 py-2 rounded-full bg-indigo-500 hover:bg-indigo-400 text-white font-semibold transition">Get Started</a>
            @endif
        @endauth
    </nav>
</header>

<main class="max-w-6xl mx-auto px-6 py-12 relative z-10 space-y-12">

    <!-- HERO SECTION -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 items-center">
        <div class="space-y-6">
            <p class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-indigo-500/20 border border-indigo-400/30 text-indigo-100">Mental Wellbeing Toolkit</p>
            <h1 class="text-4xl lg:text-5xl font-bold leading-tight">
                Track habits, journal moods and stay consistent every day.
            </h1>
            <p class="text-lg text-indigo-200/80 leading-relaxed">
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
        </div>
    </div>

    <!-- FEATURE CARDS -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mt-6">
        <div class="p-6 rounded-3xl backdrop-blur-md bg-white/10 border border-white/20 hover:bg-indigo-900/20 hover:scale-105 transition transform duration-300 shadow-xl card-hover">
            <p class="text-sm font-semibold mb-2 text-indigo-200">Habit Tracking</p>
            <p class="text-indigo-200/70 text-sm">Daily/weekly goals with streaks and completion rates.</p>
        </div>
        <div class="p-6 rounded-3xl backdrop-blur-md bg-white/10 border border-white/20 hover:bg-indigo-900/20 hover:scale-105 transition transform duration-300 shadow-xl card-hover">
            <p class="text-sm font-semibold mb-2 text-indigo-200">Journal & Mood</p>
            <p class="text-indigo-200/70 text-sm">Log entries, capture moods, and reflect on prompts.</p>
        </div>
        <div class="p-6 rounded-3xl backdrop-blur-md bg-white/10 border border-white/20 hover:bg-indigo-900/20 hover:scale-105 transition transform duration-300 shadow-xl card-hover">
            <p class="text-sm font-semibold mb-2 text-indigo-200">Progress Insights</p>
            <p class="text-indigo-200/70 text-sm">See consistency scores and long term trends.</p>
        </div>
    </div>

</main>

</body>
</html>
