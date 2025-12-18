<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>@yield('title', 'Mental Wellness Companion')</title>
<link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
<link rel="alternate icon" type="image/png" href="{{ asset('favicon.png') }}">

<script src="https://cdn.tailwindcss.com"></script>

<style>
body {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    background: linear-gradient(135deg, #D9C7FF, #F0EEFF);
    overflow-x: hidden;
    color: #1f2937;
}
main { position: relative; z-index: 10; }

.dropdown-menu { display: none; }
.dropdown-menu.show { display: block; }

@keyframes floatUpDown {0%,100%{transform:translateY(0);}50%{transform:translateY(-12px);}}
@keyframes floatSideways {0%,100%{transform:translateX(0);}50%{transform:translateX(20px);}}
@keyframes twinkle {0%,100%{opacity:1;}50%{opacity:0.4;}}

/* Cards hover */
.card-hover:hover { transform: translateY(-6px); box-shadow: 0 16px 28px rgba(0,0,0,0.15); transition: 0.3s; }
.button-hover:hover { transform: translateY(-2px); transition: 0.2s; }

/* Sun/Moon glow */
.sun, .moon { filter: drop-shadow(0 0 16px rgba(255,255,255,0.6)); }

/* Stars */
.star { position: absolute; width: 2px; height: 2px; background:white; border-radius:50%; opacity:0.8; animation: twinkle 2s infinite; }
</style>

@stack('styles')
</head>
<body>

<!-- BACKGROUND ART -->
<div class="fixed inset-0 -z-10 pointer-events-none overflow-hidden">

    <!-- Sun -->
    <div class="absolute top-16 left-16 w-36 h-36 bg-yellow-300 rounded-full opacity-95 sun animate-[floatUpDown_6s_ease-in-out_infinite]"></div>

    <!-- Moon -->
    <div class="absolute top-24 right-24 w-28 h-28 bg-indigo-400 rounded-full opacity-90 moon animate-[floatUpDown_8s_ease-in-out_infinite]"></div>

    <!-- Clouds -->
    <svg class="absolute top-8 left-1/3 w-[560px] animate-[floatSideways_18s_linear_infinite]" viewBox="0 0 300 140">
        <ellipse cx="90" cy="80" rx="90" ry="50" fill="#E0E7FF"/>
        <ellipse cx="150" cy="65" rx="80" ry="45" fill="#E0E7FF"/>
        <ellipse cx="210" cy="80" rx="90" ry="50" fill="#E0E7FF"/>
        <ellipse cx="150" cy="100" rx="130" ry="50" fill="#E0E7FF"/>
    </svg>

    <!-- Birds -->
    <svg class="absolute top-40 left-24 w-24 animate-[floatSideways_12s_linear_infinite]" viewBox="0 0 100 100">
        <circle cx="50" cy="50" r="30" fill="#FBB6CE"/>
        <circle cx="60" cy="45" r="6" fill="#111"/>
        <polygon points="78,50 92,55 78,60" fill="#FDE68A"/>
    </svg>

    <svg class="absolute top-56 right-56 w-28 animate-[floatSideways_14s_linear_infinite]" viewBox="0 0 100 100">
        <circle cx="50" cy="50" r="34" fill="#BBF7D0"/>
        <circle cx="60" cy="45" r="6" fill="#111"/>
        <polygon points="78,50 92,55 78,60" fill="#86EFAC"/>
    </svg>

    <!-- Butterflies -->
    <svg class="absolute left-16 top-[55%] w-32 animate-[floatUpDown_5s_ease-in-out_infinite]" viewBox="0 0 120 120">
        <circle cx="40" cy="60" r="30" fill="#C4B5FD"/>
        <circle cx="80" cy="60" r="30" fill="#C4B5FD"/>
        <rect x="56" y="40" width="8" height="40" rx="4" fill="#6B7280"/>
    </svg>

    <svg class="absolute right-20 bottom-56 w-36 animate-[floatUpDown_6s_ease-in-out_infinite]" viewBox="0 0 120 120">
        <circle cx="40" cy="60" r="32" fill="#F9A8D4"/>
        <circle cx="80" cy="60" r="32" fill="#F9A8D4"/>
        <rect x="56" y="40" width="8" height="40" rx="4" fill="#6B7280"/>
    </svg>

    <!-- Grass (stable) -->
    <div class="absolute bottom-0 left-0 right-0 h-44 bg-emerald-300 rounded-t-[60px]"></div>

    <!-- Flowers -->
    @for ($i = 0; $i < 8; $i++)
        <div style="position:absolute; bottom:44px; left:{{ rand(5,90) }}%; width:8px; height:20px; background:#F472B6; border-radius:4px;"></div>
    @endfor

    <!-- Trees -->
    @for ($i = 0; $i < 3; $i++)
        <div style="position:absolute; bottom:44px; left:{{ rand(5,90) }}%; width:16px; height:60px; background:#065F46; border-radius:4px;"></div>
    @endfor

    <!-- Playful Animals -->
    <svg class="absolute bottom-24 left-28 w-36 animate-[floatUpDown_5s_ease-in-out_infinite]" viewBox="0 0 120 200">
        <circle cx="60" cy="30" r="22" fill="#FBCFE8"/>
        <rect x="44" y="50" width="32" height="90" rx="14" fill="#C4B5FD"/>
    </svg>

    <svg class="absolute bottom-24 right-40 w-36 animate-[floatUpDown_7s_ease-in-out_infinite]" viewBox="0 0 120 120">
        <circle cx="60" cy="70" r="34" fill="#FED7AA"/>
        <polygon points="40,40 30,20 50,35" fill="#FED7AA"/>
        <polygon points="80,40 90,20 70,35" fill="#FED7AA"/>
    </svg>

    <!-- Stars -->
    @for ($i = 0; $i < 50; $i++)
        <div class="star" style="top: {{ rand(5, 90) }}%; left: {{ rand(5, 95) }}%; width: {{ rand(1,3) }}px; height: {{ rand(1,3) }}px; animation-duration: {{ rand(2,5) }}s;"></div>
    @endfor

</div>

<!-- NAVBAR -->
<nav class="sticky top-0 z-50 bg-indigo-900/95 backdrop-blur border-b border-indigo-700 text-white shadow-lg">
    <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">

        <!-- Logo with cute cloud & star -->
        <div class="flex items-center gap-3 relative">
            <div class="w-16 h-16 rounded-full bg-indigo-600 flex items-center justify-center font-bold text-white text-lg shadow-lg relative overflow-hidden">
                MWC
                <!-- tiny star -->
                <div class="absolute top-1 right-1 w-3 h-3 bg-yellow-300 rounded-full animate-[floatUpDown_2s_ease-in-out_infinite]"></div>
                <!-- tiny cloud -->
                <div class="absolute bottom-1 left-1 w-5 h-2 bg-white rounded-full animate-[floatSideways_3s_linear_infinite]"></div>
            </div>
            <div class="leading-tight">
                <div class="font-semibold text-lg">Mental Wellness Companion</div>
                <div class="text-xs text-indigo-200">Your peaceful space</div>
            </div>
        </div>

        <!-- Navigation links with soft hover -->
        <div class="flex gap-3 text-sm">
            <a href="{{ route('dashboard') }}"
               class="px-5 py-2 rounded-full font-medium bg-gradient-to-r from-purple-400 to-indigo-500 shadow-lg text-white hover:scale-105 hover:from-purple-300 hover:to-indigo-400 transition transform">
               Home
            </a>
            <a href="{{ route('journal.today') }}"
               class="px-5 py-2 rounded-full font-medium bg-gradient-to-r from-pink-400 to-rose-500 shadow-lg text-white hover:scale-105 hover:from-pink-300 hover:to-rose-400 transition transform">
               Journal
            </a>
            <a href="{{ route('habits.index') }}"
               class="px-5 py-2 rounded-full font-medium bg-gradient-to-r from-green-400 to-emerald-500 shadow-lg text-white hover:scale-105 hover:from-green-300 hover:to-emerald-400 transition transform">
               Habits
            </a>
            <a href="{{ route('wellness.index') }}"
               class="px-5 py-2 rounded-full font-medium bg-gradient-to-r from-amber-400 to-orange-500 shadow-lg text-white hover:scale-105 hover:from-amber-300 hover:to-orange-400 transition transform">
               Wellness
            </a>
        </div>

        <!-- Cute User Profile -->
        <div class="relative">
            <button id="userMenuButton" class="flex items-center gap-2 group">
                <!-- Floating avatar with gradient and sparkle -->
                <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-pink-400 via-purple-500 to-indigo-500 flex items-center justify-center text-white font-bold shadow-lg relative animate-bounce-slow">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    <!-- Tiny floating sparkle -->
                    <span class="absolute top-0 left-0 w-2 h-2 bg-yellow-300 rounded-full animate-pulse-slow"></span>
                    <span class="absolute bottom-0 right-1 w-2 h-2 bg-white rounded-full animate-pulse-slow"></span>
                </div>
                <span class="text-sm group-hover:text-yellow-200 transition-colors font-medium">
                    {{ Auth::user()->name }}
                </span>
            </button>

            <!-- Dropdown menu -->
            <div id="userDropdown" 
                class="dropdown-menu absolute right-0 mt-3 w-64 bg-gradient-to-br from-purple-50 via-indigo-50 to-pink-50 text-gray-700 rounded-3xl shadow-2xl border border-indigo-200 overflow-hidden transition-all scale-95 opacity-0 transform origin-top-right">

                <!-- Header -->
                <div class="p-4 border-b border-indigo-200 relative rounded-t-3xl">
                    <div class="font-bold text-indigo-900">{{ Auth::user()->name }}</div>
                    <div class="font-semibold text-gray-500 truncate">{{ Auth::user()->email }}</div>
                    <div class="text-xs text-gray-500 truncate">
                                Member since {{ Auth::user()->created_at->format('F j, Y') }}</div>

                    <!-- Tiny floating sparkles -->
                    <span class="absolute top-1 left-2 w-2 h-2 bg-yellow-300 rounded-full animate-pulse-slow"></span>
                    <span class="absolute top-3 right-3 w-1.5 h-1.5 bg-white rounded-full animate-pulse-slow"></span>
                    <span class="absolute bottom-2 left-1.5 w-1.5 h-1.5 bg-pink-300 rounded-full animate-pulse-slow"></span>
                </div>

                <!-- Actions -->
                <div class="p-2 text-sm space-y-2">
                    <a href="{{ route('profile.edit') }}" 
                    class="flex items-center gap-2 px-4 py-2 rounded-lg bg-purple-100 hover:bg-purple-200 hover:shadow-md transition-all">
                     Edit Profile
                    </a>
                    <a href="{{ route('profile.password.edit') }}" 
                    class="flex items-center gap-2 px-4 py-2 rounded-lg bg-purple-100 hover:bg-purple-200 hover:shadow-md transition-all">
                     Change Password
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="flex items-center gap-2 w-full text-left px-4 py-2 rounded-lg bg-purple-100 hover:bg-purple-200 hover:shadow-md transition-all">
                             Logout
                        </button>
                    </form>
                </div>
            </div>

            <!-- Dropdown Animations -->
            <style>
            /* Dropdown scale & fade animation */
            .dropdown-menu.show {
                opacity: 1;
                transform: scale(1);
                transition: transform 0.25s ease-out, opacity 0.25s ease-out;
            }

            /* Slow pulsing sparkles */
            @keyframes pulse-slow {
                0%, 100% { opacity: 0.5; transform: scale(1); }
                50% { opacity: 1; transform: scale(1.3); }
            }
            .animate-pulse-slow {
                animation: pulse-slow 2s infinite ease-in-out;
            }
            </style>


        </div>

        <!-- Add animations -->
        <style>
        /* Slow bounce for avatar */
        @keyframes bounce-slow {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-3px); }
        }
        .animate-bounce-slow {
            animation: bounce-slow 2.5s infinite ease-in-out;
        }

        /* Slow pulsing for sparkles */
        @keyframes pulse-slow {
            0%, 100% { opacity: 0.5; transform: scale(1); }
            50% { opacity: 1; transform: scale(1.3); }
        }
        .animate-pulse-slow {
            animation: pulse-slow 2s infinite ease-in-out;
        }
        </style>


    </div>
</nav>


<main class="max-w-5xl mx-auto px-6 py-12">
    <!-- Daily Motivation Quote -->
    <div class="mb-10">
        <div class="bg-white/80 backdrop-blur rounded-3xl shadow-xl px-8 py-6 border border-indigo-200 text-center">
            <div class="text-indigo-900 text-lg font-semibold mb-2">
                Motivation for today 😄
            </div>

            <blockquote class="text-gray-700 italic text-xl leading-relaxed">
                “{{ $dailyQuote['text'] }}”
            </blockquote>

            <div class="mt-4 text-sm text-indigo-600 font-medium">
                — {{ $dailyQuote['author'] }}
            </div>
        </div>
    </div>

    @yield('content')
</main>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const btn = document.getElementById('userMenuButton');
    const menu = document.getElementById('userDropdown');
    btn.addEventListener('click', e => {
        e.stopPropagation();
        menu.classList.toggle('show');
    });
    document.addEventListener('click', () => menu.classList.remove('show'));
});
</script>

@stack('scripts')

</body>
</html>
