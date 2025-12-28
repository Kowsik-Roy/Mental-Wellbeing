<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate, max-age=0">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Expires" content="0">
<title>WellBeing | Welcome</title>
<link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
<link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
<link rel="alternate icon" type="image/png" href="{{ asset('favicon.png') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="https://cdn.tailwindcss.com"></script>
<style>
body {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    background: linear-gradient(135deg, #0f172a, #1e293b, #0f172a);
    color: #fff;
    overflow-x: hidden;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    position: relative;
    padding-bottom: 0;
}
main { position: relative; z-index: 10; flex: 1; padding-bottom: 2rem; }

@keyframes floatUpDown {0%,100%{transform:translateY(0);}50%{transform:translateY(-12px);}}
@keyframes floatSideways {0%,100%{transform:translateX(0);}50%{transform:translateX(20px);}}
@keyframes twinkle {0%,100%{opacity:1;}50%{opacity:0.4;}}

/* Cards hover */
.card-hover:hover { transform: translateY(-6px); box-shadow: 0 16px 28px rgba(0,0,0,0.15); transition: 0.3s; }

/* Stars - fixed positioning to prevent layout shifts */
.star { 
    position: fixed; 
    border-radius:50%; 
    background:white; 
    opacity:0.8; 
    animation: twinkle 5s infinite; 
    will-change: opacity;
    pointer-events: none;
    z-index: 1;
}

/* Prevent layout shifts */
header { min-height: 80px; z-index: 20; position: relative; }
img { display: block; }
footer { 
    z-index: 20; 
    position: relative;
    margin-top: auto;
    width: 100%;
}
</style>
</head>
<body class="relative min-h-screen">

<!-- STARFIELD -->
@php
    // Use a fixed seed based on a constant to ensure stars are always in the same positions
    mt_srand(42); // Fixed seed for consistent star positions
@endphp
@for ($i = 0; $i < 80; $i++)
<div class="star" style="top: {{ mt_rand(5, 95) }}%; left: {{ mt_rand(5, 95) }}%; width: {{ mt_rand(1,3) }}px; height: {{ mt_rand(1,3) }}px; animation-duration: {{ mt_rand(2,5) }}s;"></div>
@endfor

<!-- HEADER -->
<header class="relative z-20 w-full px-6 py-6">
    <div class="max-w-6xl mx-auto flex items-center justify-between gap-6">
        <!-- Left: Logo and Name -->
        <a href="{{ url('/') }}" class="flex items-center gap-3 hover:opacity-80 transition-opacity cursor-pointer">
            <img src="{{ asset('favicon.svg') }}" alt="WellBeing Logo" class="w-10 h-10 object-contain" width="40" height="40" loading="eager">
            <div class="leading-tight">
                <div class="font-semibold text-lg">Mental Wellness Companion</div>
                <p class="text-sm text-indigo-200/70">Habits • Journal • Mood</p>
            </div>
        </a>

        <!-- Right: Navigation Buttons -->
        <nav class="flex items-center gap-3 text-sm flex-shrink-0">
            @auth
                <a href="{{ url('/dashboard') }}"
                class="px-5 py-2.5 rounded-full 
                        bg-gradient-to-r from-indigo-500 to-pink-600 
                        text-white font-semibold shadow-md 
                        hover:shadow-pink-300/50 hover:scale-110 
                        transition-all duration-300">
                    ✨ Dashboard
                </a>
            @else
                <a href="{{ route('login') }}"
                class="px-5 py-2.5 rounded-full 
                        border border-white/50 text-white 
                        hover:bg-white/70 hover:text-slate-900 
                        hover:scale-105 
                        transition-all duration-300">
                    Log in
                </a>

                @if (Route::has('register'))
                    <a href="{{ route('register') }}"
                    class="group px-5 py-2.5 rounded-full 
                            bg-gradient-to-r from-indigo-500 to-pink-40 
                            text-white font-semibold shadow-md 
                            hover:shadow-pink-300/50 hover:scale-110 
                            transition-all duration-300">
                        ✨ Get Started
                    </a>
                @endif
            @endauth
        </nav>

    </div>
</header>

<main class="max-w-6xl mx-auto px-6 py-12 relative z-10 space-y-12">

    <!-- HERO SECTION -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 items-center">
        <div class="space-y-6">
            <h1 class="text-4xl lg:text-5xl font-bold leading-tight">
                Track habits, journal moods and stay consistent every day.
            </h1>
            <p class="text-lg text-indigo-200/80 leading-relaxed">
                WellBeing brings your daily habits, mood journaling and personal progress into a single, calming space. Build streaks, reflect on your day and celebrate the small wins.
            </p>
            <div class="flex flex-wrap gap-4">
                @auth
                    <a href="{{ url('/dashboard') }}" class="px-5 py-3 rounded-full bg-gradient-to-r from-indigo-500 to-pink-600 text-white font-semibold shadow-md hover:shadow-pink-300/50 hover:scale-110 transition-all duration-300">✨ Open Dashboard</a>
                @else
                    <a href="{{ route('register') }}"
                    class="px-6 py-3 rounded-2xl 
                            bg-gradient-to-r from-indigo-500 to-pink-40 
                            text-white font-semibold 
                            shadow-lg shadow-pink-300/50 
                            hover:shadow-xl hover:-translate-y-0.5 
                            transition-all duration-300">
                        ✨ Create your account
                    </a>

                    <a href="{{ route('login') }}"
                    class="px-6 py-3 rounded-2xl 
                            bg-white/10 backdrop-blur-md 
                            border border-white/30 
                            text-white font-medium 
                            hover:bg-white/50 hover:-translate-y-0.5 
                            transition-all duration-300">
                        Log in
                    </a>

                @endauth
            </div>
        </div>
    </div>

    <!-- FEATURE CARDS -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mt-6">
        
        <!-- Card 1 -->
        <div class="group p-6 rounded-[2rem] bg-gradient-to-br from-indigo-500/20 to-pink-500/10 
                    border border-white/20 backdrop-blur-xl shadow-lg 
                    hover:shadow-indigo-500/40 hover:-translate-y-1 hover:scale-[1.03] 
                    transition-all duration-300">
            <div class="text-3xl mb-3">🌱</div>
            <p class="text-sm font-semibold mb-1 text-indigo-100">
                Habit Tracking
            </p>
            <p class="text-indigo-200/80 text-sm leading-relaxed">
                Daily & weekly goals with cute streaks and wins.
            </p>
        </div>

        <!-- Card 2 -->
        <div class="group p-6 rounded-[2rem] bg-gradient-to-br from-pink-500/20 to-purple-500/10 
                    border border-white/20 backdrop-blur-xl shadow-lg 
                    hover:shadow-pink-500/40 hover:-translate-y-1 hover:scale-[1.03] 
                    transition-all duration-300">
            <div class="text-3xl mb-3">📖</div>
            <p class="text-sm font-semibold mb-1 text-pink-100">
                Journal & Mood
            </p>
            <p class="text-pink-200/80 text-sm leading-relaxed">
                Capture feelings, thoughts, and daily reflections.
            </p>
        </div>

        <!-- Card 3 -->
        <div class="group p-6 rounded-[2rem] bg-gradient-to-br from-purple-500/20 to-indigo-500/10 
                    border border-white/20 backdrop-blur-xl shadow-lg 
                    hover:shadow-purple-500/40 hover:-translate-y-1 hover:scale-[1.03] 
                    transition-all duration-300">
            <div class="text-3xl mb-3">📊</div>
            <p class="text-sm font-semibold mb-1 text-purple-100">
                Progress Insights
            </p>
            <p class="text-purple-200/80 text-sm leading-relaxed">
                Track consistency and see your glow-up over time.
            </p>
        </div>

    </div>


</main>

<!-- FOOTER -->
<footer class="mt-auto bg-indigo-900/95 backdrop-blur border-t border-indigo-700 text-white relative z-20 w-full">
    <div class="max-w-7xl mx-auto px-6 py-6">
        <div class="flex flex-col md:flex-row justify-between items-center gap-4 text-sm">
            <!-- Left: Copyright -->
            <div class="text-indigo-200 text-center md:text-left">
                <p>&copy; {{ date('Y') }} Mental Wellness Companion. All rights reserved.</p>
            </div>

            <!-- Center: Links with hover effects -->
            <div class="flex flex-wrap items-center justify-center gap-6 text-indigo-200">
                <a href="{{ route('about.index') }}" 
                   class="group flex items-center gap-2 hover:text-white transition-colors duration-200 cursor-pointer">
                    <i class="fas fa-info-circle text-purple-400 group-hover:text-purple-300 transition-colors"></i>
                    <span class="font-medium group-hover:underline">About Us</span>
                </a>
                <button onclick="openContactModal()" 
                        class="group flex items-center gap-2 hover:text-white transition-colors duration-200 cursor-pointer">
                    <i class="fas fa-envelope text-green-400 group-hover:text-green-300 transition-colors"></i>
                    <span class="font-medium group-hover:underline">Contact Us</span>
                </button>
            </div>

            <!-- Right: Location -->
            <div class="text-indigo-200 text-center md:text-right">
                <p class="text-xs flex items-center justify-center md:justify-end gap-1.5">
                    <i class="fas fa-map-marker-alt text-green-400"></i>
                    <span>Dhaka, Bangladesh</span>
                </p>
            </div>
        </div>
    </div>
</footer>

<!-- Contact Us Modal -->
<div id="contactModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-2xl w-full p-8 relative max-h-[90vh] overflow-y-auto">
        <!-- Close Button -->
        <button onclick="closeContactModal()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 transition">
            <i class="fas fa-times text-2xl"></i>
        </button>

        <!-- Header -->
        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-envelope text-white text-2xl"></i>
            </div>
            <h2 class="text-3xl font-bold text-indigo-900 mb-2">Contact Us</h2>
            <p class="text-gray-600">We'd love to hear from you</p>
        </div>

        <!-- Contact Information -->
        <div class="space-y-6">
            <div class="bg-indigo-50 rounded-xl p-6">
                <h3 class="font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <i class="fas fa-envelope text-indigo-600"></i>
                    Email
                </h3>
                <p class="text-gray-700 mb-2">For support and inquiries:</p>
                <a href="mailto:support@mentalwellness.com" class="text-indigo-600 hover:text-indigo-700 font-medium">
                    kowsik.roy@g.bracu.ac.bd
                </a>
            </div>

            <div class="bg-green-50 rounded-xl p-6">
                <h3 class="font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <i class="fas fa-map-marker-alt text-green-600"></i>
                    Location
                </h3>
                <p class="text-gray-700 mb-2">We're based in:</p>
                <p class="text-gray-800 font-medium">
                    Mental Wellness Companion<br>
                    Dhaka, Bangladesh<br>
                </p>
            </div>

            <div class="bg-purple-50 rounded-xl p-6">
                <h3 class="font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <i class="fas fa-clock text-purple-600"></i>
                    Response Time
                </h3>
                <p class="text-gray-700">
                    We typically respond to emails within 24-48 hours. Your message is important to us, and we'll get back to you as soon as possible.
                </p>
            </div>

            <div class="bg-blue-50 rounded-xl p-6">
                <h3 class="font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <i class="fas fa-heart text-blue-600"></i>
                    We're Here to Help
                </h3>
                <p class="text-gray-700">
                    Whether you have questions about using the platform, suggestions for improvement, or just want to share your wellness journey, 
                    we're here to listen and support you.
                </p>
            </div>
        </div>

        <!-- Close Button -->
        <div class="mt-8 text-center">
            <button onclick="closeContactModal()" 
                    class="px-6 py-3 bg-indigo-600 text-white rounded-full hover:bg-indigo-700 transition">
                Close
            </button>
        </div>
    </div>
</div>

<script>
function openContactModal() {
    document.getElementById('contactModal').classList.remove('hidden');
    document.getElementById('contactModal').classList.add('flex');
    document.body.style.overflow = 'hidden';
}

function closeContactModal() {
    document.getElementById('contactModal').classList.add('hidden');
    document.getElementById('contactModal').classList.remove('flex');
    document.body.style.overflow = 'auto';
}

// Close modal when clicking outside
document.getElementById('contactModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeContactModal();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeContactModal();
    }
});
</script>

</body>
</html>
