<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title', 'Mental Wellness Companion')</title>
<link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
<link rel="alternate icon" type="image/png" href="{{ asset('favicon.png') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="https://cdn.tailwindcss.com"></script>

<style>
body {
 font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
 background: linear-gradient(135deg, #f8d0eeff, #edf1f3ff);
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

 <!-- Butterflies in vacant spots -->
 <!-- Butterfly 1 - Middle left area -->
 <svg class="absolute left-20 top-[39%] w-32 animate-[floatUpDown_5s_ease-in-out_infinite]" viewBox="0 0 120 120">
 <circle cx="40" cy="60" r="30" fill="#C4B5FD"/>
 <circle cx="80" cy="60" r="30" fill="#C4B5FD"/>
 <rect x="56" y="40" width="8" height="40" rx="4" fill="#6B7280"/>
 </svg>

 <!-- Butterfly 2 - Middle right area -->
 <svg class="absolute right-24 top-[44%] w-36 animate-[floatUpDown_6s_ease-in-out_infinite]" style="animation-delay: 1s;" viewBox="0 0 120 120">
 <circle cx="40" cy="60" r="32" fill="#F9A8D4"/>
 <circle cx="80" cy="60" r="32" fill="#F9A8D4"/>
 <rect x="56" y="40" width="8" height="40" rx="4" fill="#6B7280"/>
 </svg>

 <!-- Butterfly 3 - Upper middle area -->
 <svg class="absolute left-1/2 top-[24%] w-28 animate-[floatUpDown_7s_ease-in-out_infinite]" style="transform: translateX(-50%); animation-delay: 2s;" viewBox="0 0 120 120">
 <circle cx="40" cy="60" r="28" fill="#FBCFE8"/>
 <circle cx="80" cy="60" r="28" fill="#DDD6FE"/>
 <rect x="56" y="40" width="8" height="40" rx="4" fill="#6B7280"/>
 </svg>

 <!-- Grass (stable) -->
 <div class="absolute bottom-0 left-0 right-0 h-44 bg-emerald-300 rounded-t-[40px]"></div>

 <!-- Flowers on grass -->
 @for ($i = 0; $i < 15; $i++)
 <div class="absolute bottom-44" style="left: {{ rand(5, 95) }}%; width: {{ rand(6, 10) }}px; height: {{ rand(15, 25) }}px; background: #F472B6; border-radius: 4px; z-index: 1;"></div>
 @endfor

 <!-- Trees on grass -->
 @for ($i = 0; $i < 5; $i++)
 <div class="absolute bottom-44" style="left: {{ rand(8, 92) }}%; width: {{ rand(14, 20) }}px; height: {{ rand(50, 70) }}px; background:rgb(82, 196, 164); border-radius: 4px; z-index: 1;"></div>
 @endfor

 <!-- Humans on top of grass - positioned on far left and right to avoid cards -->
 <!-- First Human (far left) -->
 <svg class="absolute bottom-44 left-8 w-40 h-40 animate-[floatUpDown_5s_ease-in-out_infinite]" viewBox="0 0 120 200">
 <!-- Head (with face) -->
 <circle cx="60" cy="30" r="26" fill="#FED7AA" stroke="#FBCFE8" stroke-width="2.5"/>
 <!-- Eyes -->
 <circle cx="52" cy="28" r="3.5" fill="#1E293B"/>
 <circle cx="68" cy="28" r="3.5" fill="#1E293B"/>
 <!-- Smile -->
 <path d="M 50 36 Q 60 42 70 36" stroke="#1E293B" stroke-width="2.5" fill="none" stroke-linecap="round"/>
 <!-- Body -->
 <rect x="44" y="50" width="32" height="90" rx="14" fill="#C4B5FD"/>
 <!-- Left arm -->
 <rect x="36" y="60" width="14" height="45" rx="7" fill="#C4B5FD" transform="rotate(-25 43 82.5)"/>
 <!-- Right arm -->
 <rect x="70" y="60" width="14" height="45" rx="7" fill="#C4B5FD" transform="rotate(25 77 82.5)"/>
 <!-- Left hand -->
 <circle cx="30" cy="98" r="7" fill="#FED7AA"/>
 <!-- Right hand -->
 <circle cx="90" cy="98" r="7" fill="#FED7AA"/>
 <!-- Left leg -->
 <rect x="48" y="140" width="12" height="55" rx="6" fill="#C4B5FD"/>
 <!-- Right leg -->
 <rect x="60" y="140" width="12" height="55" rx="6" fill="#C4B5FD"/>
 </svg>

 <!-- Second Human (far right) -->
 <svg class="absolute bottom-44 right-1 w-40 h-44 animate-[floatUpDown_5s_ease-in-out_infinite]" style="animation-delay: 0.5s;" viewBox="0 0 120 200">
 <!-- Head (with face) -->
 <circle cx="60" cy="30" r="26" fill="#FBCFE8" stroke="#F9A8D4" stroke-width="2.5"/>
 <!-- Eyes -->
 <circle cx="52" cy="28" r="3.5" fill="#1E293B"/>
 <circle cx="68" cy="28" r="3.5" fill="#1E293B"/>
 <!-- Smile -->
 <path d="M 50 36 Q 60 42 70 36" stroke="#1E293B" stroke-width="2.5" fill="none" stroke-linecap="round"/>
 <!-- Body -->
 <rect x="44" y="50" width="32" height="90" rx="14" fill="#DDD6FE"/>
 <!-- Left arm -->
 <rect x="36" y="60" width="14" height="45" rx="7" fill="#DDD6FE" transform="rotate(-25 43 82.5)"/>
 <!-- Right arm -->
 <rect x="70" y="60" width="14" height="45" rx="7" fill="#DDD6FE" transform="rotate(25 77 82.5)"/>
 <!-- Left hand -->
 <circle cx="30" cy="98" r="7" fill="#FBCFE8"/>
 <!-- Right hand -->
 <circle cx="90" cy="98" r="7" fill="#FBCFE8"/>
 <!-- Left leg -->
 <rect x="48" y="140" width="12" height="55" rx="6" fill="#DDD6FE"/>
 <!-- Right leg -->
 <rect x="60" y="140" width="12" height="55" rx="6" fill="#DDD6FE"/>
 </svg>



 <!-- Stars -->
 @for ($i = 0; $i < 50; $i++)
 <div class="star" style="top: {{ rand(5, 90) }}%; left: {{ rand(5, 95) }}%; width: {{ rand(1,3) }}px; height: {{ rand(1,3) }}px; animation-duration: {{ rand(2,5) }}s;"></div>
 @endfor

</div>

<!-- NAVBAR -->
<header class="sticky top-0 z-50">

    <!-- TOP BAR -->
    <div class="bg-gradient-to-r from-indigo-200 via-purple-200 to-pink-200 text-white">

        <div class="max-w-7xl mx-auto px-6 h-18 flex items-center justify-between">

            <!-- Logo -->
            <a href="{{ auth()->check() ? route('dashboard') : url('/') }}"
               class="flex items-center gap-3 hover:opacity-90 transition">
                <img src="{{ asset('favicon.svg') }}" class="w-10 h-10">
                <div>
                    <div class="font-semibold text-lg text-indigo-900">
                        Mental Wellbeing
                    </div>
                    <div class="text-xs text-indigo-600">
                        Mental Wellness Companion
                    </div>
                </div>
            </a>

            <!-- Right side -->
            @guest
            <div class="flex items-center gap-5 text-sm">
                <a href="{{ route('login') }}"
                   class="text-pink-500 hover:text-pink-600 transition font-medium">
                    Log in
                </a>
                @if (Route::has('register'))
                <a href="{{ route('register') }}"
                   class="px-4 py-2 rounded-full bg-gradient-to-r from-pink-400 to-rose-400
                          text-white shadow-md hover:shadow-lg hover:scale-105 transition">
                    Sign up
                </a>
                @endif
            </div>
            @endguest

            @auth
            <div class="relative">
                <!-- User Menu -->
                <button id="userMenuButton"
                        class="flex items-center gap-2 text-indigo-600 hover:text-pink-500 transition">
                    <div class="w-9 h-9 rounded-full bg-gradient-to-tr from-pink-400 to-indigo-400
                                text-white flex items-center justify-center font-semibold shadow-sm">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <span class="text-sm font-medium">
                        {{ Auth::user()->name }}
                    </span>
                    <i class="fas fa-chevron-down text-xs"></i>
                </button>

                <!-- Dropdown -->
                <div id="userDropdown"
                     class="dropdown-menu absolute right-0 mt-2 w-48 bg-pink-50 rounded-lg shadow-lg border border-pink-200 py-1">

                    <a href="{{ route('profile.edit') }}"
                       class="block px-4 py-2 text-sm text-black hover:bg-pink-100">
                        Edit Profile
                    </a>

                    <a href="{{ route('profile.password.edit') }}"
                       class="block px-4 py-2 text-sm text-black hover:bg-pink-100">
                        Change Password
                    </a>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                                class="w-full text-left px-4 py-2 text-sm text-black hover:bg-pink-100">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
            @endauth
        </div>
    </div>

    <!-- BOTTOM NAV -->
    <nav class="bg-gradient-to-r from-pink-200 via-purple-200 to-indigo-200 text-black">
        <div class="max-w-7xl mx-auto px-6">
            <ul class="flex items-center gap-10 h-12 text-sm font-medium">

                <li>
                    <a href="{{ auth()->check() ? route('dashboard') : url('/') }}"
                       class="hover:text-yellow-200 transition">
                        <i class="fas fa-home text-lg"></i>
                    </a>
                </li>

                @auth
                <li>
                    <a href="{{ route('journal.today') }}"
                       class="relative group">
                        Journal
                        <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-black
                                     group-hover:w-full transition-all"></span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('habits.index') }}" class="relative group">
                        Habits
                        <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-black
                                     group-hover:w-full transition-all"></span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('wellness.index') }}" class="relative group">
                        Wellness
                        <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-black
                                     group-hover:w-full transition-all"></span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('dashboard.weekly-summary') }}" class="relative group">
                        Weekly Summary
                        <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-black
                                     group-hover:w-full transition-all"></span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('meditation') }}" class="relative group">
                        Meditation
                        <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-black
                                     group-hover:w-full transition-all"></span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('ai.chat') }}" class="relative group">
                        AI Chat
                        <span class="absolute -bottom-1 left-0 w-0 h-0.5 bg-black
                                     group-hover:w-full transition-all"></span>
                    </a>
                </li>
                @endauth

            </ul>
        </div>
    </nav>

</header>



<main class="max-w-5xl mx-auto px-6 py-16">
  @yield('content')
</main>

<!-- FOOTER -->
<footer class="relative z-10 mt-10">

    <!-- Soft grass overlay -->
    <div class="max-w-12xl mx-auto px-12">
        <div
            class="bg-gradient-to-r from-emerald-300 via-green-200 to-emerald-300
                   rounded-t-[40px] shadow-lg border border-emerald-300
                   px-10 py-10">

            <div class="flex flex-col md:flex-row justify-between items-center gap-4 text-sm">

                <!-- Left -->
                <div class="text-emerald-900 text-center md:text-left">
                    <p class="font-medium">
                        © {{ date('Y') }} Mental Wellbeing. All rights reserved.
                    </p>
                    <p class="text-xs text-emerald-700">
                        Growing healthier minds 🌱
                    </p>
                </div>

                <!-- Center links -->
                <div class="flex flex-wrap items-center justify-center gap-6">

                    <a href="{{ route('about.index') }}"
                       class="group flex items-center gap-2 text-emerald-800
                              hover:text-emerald-900 transition">
                        <i class="fas fa-info-circle text-pink-400
                                  group-hover:text-pink-500"></i>
                        <span class="font-medium group-hover:underline">
                            About Us
                        </span>
                    </a>

                    <button onclick="openContactModal()"
                            class="group flex items-center gap-2 text-emerald-800
                                   hover:text-emerald-900 transition">
                        <i class="fas fa-envelope text-indigo-400
                                  group-hover:text-indigo-500"></i>
                        <span class="font-medium group-hover:underline">
                            Contact Us
                        </span>
                    </button>

                </div>

                <!-- Right -->
                <div class="text-emerald-800 text-center md:text-right">
                    <p class="text-xs flex items-center justify-center md:justify-end gap-1.5">
                        <i class="fas fa-map-marker-alt text-rose-400"></i>
                        Dhaka, Bangladesh
                    </p>
                </div>

            </div>
        </div>
    </div>
</footer>


<!-- Custom Confirmation Modal -->
<div id="confirmModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 relative shadow-2xl">
        <!-- Close Button -->
        <button onclick="closeConfirmModal()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 transition">
            <i class="fas fa-times text-xl"></i>
        </button>

        <!-- Icon -->
        <div class="text-center mb-4">
            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-exclamation-triangle text-red-600 text-2xl"></i>
            </div>
            <h3 id="confirmModalTitle" class="text-2xl font-bold text-gray-900 mb-2">Confirm Action</h3>
            <p id="confirmModalMessage" class="text-gray-600 mb-6"></p>
        </div>

        <!-- Buttons -->
        <div class="flex gap-3">
            <button onclick="closeConfirmModal()" 
                    class="flex-1 px-4 py-3 bg-gray-200 text-gray-700 rounded-xl font-medium hover:bg-gray-300 transition">
                Cancel
            </button>
            <button id="confirmModalButton" 
                    class="flex-1 px-4 py-3 bg-red-600 text-white rounded-xl font-medium hover:bg-red-700 transition">
                Confirm
            </button>
        </div>
    </div>
</div>

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
                    Mental Wellbeing<br>
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
// Custom Confirmation Modal Functions
let confirmModalCallback = null;

function showConfirmModal(title, message, onConfirm) {
    document.getElementById('confirmModalTitle').textContent = title;
    document.getElementById('confirmModalMessage').textContent = message;
    confirmModalCallback = onConfirm;
    document.getElementById('confirmModal').classList.remove('hidden');
    document.getElementById('confirmModal').classList.add('flex');
    document.body.style.overflow = 'hidden';
}

function closeConfirmModal() {
    document.getElementById('confirmModal').classList.add('hidden');
    document.getElementById('confirmModal').classList.remove('flex');
    document.body.style.overflow = 'auto';
    confirmModalCallback = null;
}

// Handle confirm button click (will be set up in DOMContentLoaded)

// Close modal when clicking outside
document.getElementById('confirmModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeConfirmModal();
    }
});

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

// Close contact modal with Escape key (handled above for confirm modal)
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        if (!document.getElementById('confirmModal').classList.contains('hidden')) {
            closeConfirmModal();
        } else {
            closeContactModal();
        }
    }
});
</script>

<script>
document.addEventListener('DOMContentLoaded', () => {
 // Handle confirm button click
 const confirmButton = document.getElementById('confirmModalButton');
 if (confirmButton) {
     confirmButton.addEventListener('click', function() {
         if (confirmModalCallback) {
             confirmModalCallback();
         }
         closeConfirmModal();
     });
 }

 // User menu dropdown (only if user is authenticated)
 const btn = document.getElementById('userMenuButton');
 const menu = document.getElementById('userDropdown');
 if (btn && menu) {
 btn.addEventListener('click', e => {
 e.stopPropagation();
 menu.classList.toggle('show');
 });
 document.addEventListener('click', (e) => {
  // Don't close if clicking inside the dropdown menu
  if (!menu.contains(e.target) && e.target !== btn) {
   menu.classList.remove('show');
  }
 });
 }
 
 // Ensure logout form submits properly with fresh CSRF token
 const logoutForm = document.getElementById('logoutForm');
 if (logoutForm) {
  logoutForm.addEventListener('submit', function(e) {
   e.preventDefault();
   
   // Get fresh CSRF token from meta tag
   const token = document.querySelector('meta[name="csrf-token"]');
   if (token) {
    // Update or add CSRF token
    let csrfInput = this.querySelector('input[name="_token"]');
    if (csrfInput) {
     csrfInput.value = token.getAttribute('content');
    } else {
     csrfInput = document.createElement('input');
     csrfInput.type = 'hidden';
     csrfInput.name = '_token';
     csrfInput.value = token.getAttribute('content');
     this.appendChild(csrfInput);
    }
   }
   
   // Submit the form
   this.submit();
  });
 }

 // Initialize push notifications (only if user is authenticated)
 @auth
 initPushNotifications();
 @endauth
});

@auth
// Push Notification System
// Key used to avoid infinite reloads when a reminder is detected
const REMINDER_REFRESH_KEY = 'habit-reminder-refreshed-' + new Date().toDateString();
async function initPushNotifications() {
 if (!('Notification' in window) || !('serviceWorker' in navigator)) {
 console.log('Push notifications not supported');
 return;
 }

 // Request permission
 if (Notification.permission === 'default') {
 await Notification.requestPermission();
 }

 if (Notification.permission === 'granted') {
 // Register service worker
 try {
 const registration = await navigator.serviceWorker.register('{{ asset('sw.js') }}');
 await subscribeToPush(registration);
 } catch (error) {
 console.error('Service Worker registration failed:', error);
 }

 // Start checking for reminders immediately, then every 30 seconds
 checkHabitReminders();
 setInterval(checkHabitReminders, 30000);
 }
}

async function subscribeToPush(registration) {
 // Service worker ready for notifications
}

async function checkHabitReminders() {
 try {
 const response = await fetch('{{ route("push.check-reminders") }}', {
 headers: {
 'X-CSRF-TOKEN': '{{ csrf_token() }}'
 }
 });
 const data = await response.json();

 // If there are reminders and we haven't auto‑refreshed yet today, refresh once
 if (data.has_reminders && !sessionStorage.getItem(REMINDER_REFRESH_KEY)) {
 sessionStorage.setItem(REMINDER_REFRESH_KEY, '1');
 window.location.reload();
 return;
 }

 if (data.has_reminders && data.reminders.length > 0) {
 // Show notifications with a small delay between each to avoid browser throttling
 data.reminders.forEach((reminder, index) => {
 setTimeout(() => {
 showNotification(reminder);
 }, index * 500); // 500ms delay between each notification
 });
 }
 } catch (error) {
 console.error('Error checking reminders:', error);
 }
}

// Track which habits have already shown a reminder in this page session (avoid spam)
const shownHabitReminders = new Set();

function showNotification(reminder) {
 if (Notification.permission !== 'granted') {
 return;
 }

 // Only show once per page session per habit (to prevent duplicates on refresh)
 if (shownHabitReminders.has(reminder.id)) {
 return;
 }

 try {
 const iconUrl = '{{ asset("favicon.svg") }}';
 
 // Create unique tag for each notification to allow multiple simultaneous notifications
 const uniqueTag = `habit-reminder-${reminder.id}-${Date.now()}-${Math.random().toString(36).substr(2, 9)}`;
 
 const notification = new Notification('MentalWellbeing', {
 body: `Don't Forget to do the Habit: ${reminder.title}`,
 icon: iconUrl,
 badge: iconUrl,
 tag: uniqueTag,
 requireInteraction: false,
 });

 // Mark as shown for this session
 shownHabitReminders.add(reminder.id);

 notification.onclick = function() {
 window.focus();
 window.location.href = '{{ route("habits.index") }}';
 notification.close();
 };

 notification.onerror = function(error) {
 console.error('Notification error:', error);
 shownHabitReminders.delete(reminder.id);
 };

 // Auto-close after 10 seconds
 setTimeout(() => {
 notification.close();
 }, 10000);
 } catch (error) {
 console.error('Error creating notification:', error);
 shownHabitReminders.delete(reminder.id);
 }
}
@endauth

</script>

@stack('scripts')

</body>
</html>
