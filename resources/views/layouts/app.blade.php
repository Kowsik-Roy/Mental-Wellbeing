<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
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

<!-- Logo -->
<div class="flex items-center gap-3"><img src="{{ asset('favicon.svg') }}" alt="Mental Wellness Companion Logo" class="w-10 h-10">
  <div class="leading-tight">
    <div class="font-semibold text-lg">Mental Wellness Companion</div>
    <div class="text-xs text-indigo-200">Your peaceful space</div>
  </div>
</div>

 <!-- Navigation links with soft hover -->
 @auth
 <div class="flex flex-wrap gap-2 text-sm">
 <a href="{{ route('dashboard') }}"
 class="px-4 py-2 rounded-full font-medium bg-gradient-to-r from-purple-400 to-indigo-500 shadow-lg text-white hover:scale-105 hover:from-purple-300 hover:to-indigo-400 transition transform">
 <i class="fas fa-home mr-1"></i> Home
 </a>
 <a href="{{ route('journal.today') }}"
 class="px-4 py-2 rounded-full font-medium bg-gradient-to-r from-pink-400 to-rose-500 shadow-lg text-white hover:scale-105 hover:from-pink-300 hover:to-rose-400 transition transform">
 <i class="fas fa-book mr-1"></i> Journal
 </a>
 <a href="{{ route('habits.index') }}"
 class="px-4 py-2 rounded-full font-medium bg-gradient-to-r from-green-400 to-emerald-500 shadow-lg text-white hover:scale-105 hover:from-green-300 hover:to-emerald-400 transition transform">
 <i class="fas fa-tasks mr-1"></i> Habits
 </a>
 <a href="{{ route('wellness.index') }}"
 class="px-4 py-2 rounded-full font-medium bg-gradient-to-r from-amber-400 to-orange-500 shadow-lg text-white hover:scale-105 hover:from-amber-300 hover:to-orange-400 transition transform">
 <i class="fas fa-heart mr-1"></i> Wellness
 </a>
 <a href="{{ route('dashboard.weekly-summary') }}"
 class="px-4 py-2 rounded-full font-medium bg-gradient-to-r from-cyan-400 to-blue-500 shadow-lg text-white hover:scale-105 hover:from-cyan-300 hover:to-blue-400 transition transform">
 <i class="fas fa-chart-line mr-1"></i> <span class="hidden md:inline">Weekly</span> Summary
 </a>
 </div>
 @else
 <div class="flex flex-wrap gap-2 text-sm">
 <a href="{{ route('about.index') }}"
 class="px-4 py-2 rounded-full font-medium bg-gradient-to-r from-purple-400 to-indigo-500 shadow-lg text-white hover:scale-105 hover:from-purple-300 hover:to-indigo-400 transition transform">
 <i class="fas fa-info-circle mr-1"></i> About
 </a>
 <a href="{{ route('login') }}"
 class="px-4 py-2 rounded-full font-medium bg-gradient-to-r from-pink-400 to-rose-500 shadow-lg text-white hover:scale-105 hover:from-pink-300 hover:to-rose-400 transition transform">
 <i class="fas fa-sign-in-alt mr-1"></i> Log In
 </a>
 @if (Route::has('register'))
 <a href="{{ route('register') }}"
 class="px-4 py-2 rounded-full font-medium bg-gradient-to-r from-green-400 to-emerald-500 shadow-lg text-white hover:scale-105 hover:from-green-300 hover:to-emerald-400 transition transform">
 <i class="fas fa-user-plus mr-1"></i> Sign Up
 </a>
 @endif
 </div>
 @endauth

 <!-- Cute User Profile (only for authenticated users) -->
 @auth
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
 @endauth

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

<main class="max-w-5xl mx-auto px-6 py-16">
  @yield('content')
</main>

<!-- FOOTER -->
<footer class="mt-12 bg-indigo-900/95 backdrop-blur border-t border-indigo-700 text-white">
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

<script>
document.addEventListener('DOMContentLoaded', () => {
 // User menu dropdown (only if user is authenticated)
 const btn = document.getElementById('userMenuButton');
 const menu = document.getElementById('userDropdown');
 if (btn && menu) {
 btn.addEventListener('click', e => {
 e.stopPropagation();
 menu.classList.toggle('show');
 });
 document.addEventListener('click', () => menu.classList.remove('show'));
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
