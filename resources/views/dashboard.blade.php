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
                <div class="absolute -top-10 left-1/2 -translate-x-1/2
                            w-20 h-20 bg-yellow-200 rounded-full
                            flex items-center justify-center text-3xl
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

<section class="mb-12 relative overflow-hidden">
    <div class="relative z-10">
        <div class="text-center md:text-left mb-6">
            <h1 class="text-4xl md:text-6xl font-bold mb-4 bg-gradient-to-r from-indigo-900 to-purple-800 bg-clip-text text-transparent leading-tight">
                Hello, {{ Auth::user()->name }}!
            </h1>
            <p class="text-gray-600 text-lg max-w-2xl">
                A gentle space designed for reflection, healing, and growth.
                <span class="block text-sm text-indigo-400 mt-2">Today is {{ now()->format('l, F jS') }}</span>
            </p>
        </div>
       
        <!-- Decorative elements -->
        <div class="absolute top-0 right-10 w-64 h-64 bg-gradient-to-br from-indigo-100/30 to-purple-100/20 rounded-full blur-2xl"></div>
        <div class="absolute bottom-0 left-10 w-48 h-48 bg-gradient-to-tr from-indigo-50/40 to-transparent rounded-full blur-xl"></div>
    </div>
</section>






<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-16">
    <div class="group relative bg-gradient-to-br from-white to-indigo-50 rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1 border border-indigo-100/50 overflow-hidden">
        <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-br from-indigo-50 to-transparent rounded-full -translate-y-8 translate-x-8"></div>
        <div class="relative z-10">
            <div class="w-12 h-12 rounded-xl bg-gradient-to-r from-indigo-100 to-purple-100 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
            </div>
            <h3 class="font-bold text-lg mb-2 text-gray-800">Daily Journal</h3>
            <p class="text-sm text-gray-500 mb-6">Write your thoughts freely in a safe space.</p>
            <a href="{{ route('journal.today') }}" class="inline-flex items-center gap-2 text-indigo-600 text-sm font-semibold group/link">
                Write today
                <span class="group-hover/link:translate-x-1 transition-transform">→</span>
            </a>
        </div>
    </div>


    <div class="group relative bg-gradient-to-br from-white to-emerald-50 rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1 border border-emerald-100/50 overflow-hidden">
        <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-br from-emerald-50 to-transparent rounded-full -translate-y-8 translate-x-8"></div>
        <div class="relative z-10">
            <div class="w-12 h-12 rounded-xl bg-gradient-to-r from-emerald-100 to-teal-100 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h3 class="font-bold text-lg mb-2 text-gray-800">Daily Habits</h3>
            <p class="text-sm text-gray-500 mb-6">Build gentle, sustainable routines.</p>
            <a href="{{ route('habits.index') }}" class="inline-flex items-center gap-2 text-emerald-600 text-sm font-semibold group/link">
                View habits
                <span class="group-hover/link:translate-x-1 transition-transform">→</span>
            </a>
        </div>
    </div>


    <div class="group relative bg-gradient-to-br from-white to-amber-50 rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1 border border-amber-100/50 overflow-hidden">
        <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-br from-amber-50 to-transparent rounded-full -translate-y-8 translate-x-8"></div>
        <div class="relative z-10">
            <div class="w-12 h-12 rounded-xl bg-gradient-to-r from-amber-100 to-orange-100 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h3 class="font-bold text-lg mb-2 text-gray-800">Journal History</h3>
            <p class="text-sm text-gray-500 mb-6">Reflect on your journey over time.</p>
            <a href="{{ route('journal.history') }}" class="inline-flex items-center gap-2 text-amber-600 text-sm font-semibold group/link">
                View history
                <span class="group-hover/link:translate-x-1 transition-transform">→</span>
            </a>
        </div>
    </div>


    <div class="group relative bg-gradient-to-br from-white to-rose-50 rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-1 border border-rose-100/50 overflow-hidden">
        <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-br from-rose-50 to-transparent rounded-full -translate-y-8 translate-x-8"></div>
        <div class="relative z-10">
            <div class="w-12 h-12 rounded-xl bg-gradient-to-r from-rose-100 to-pink-100 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                <svg class="w-6 h-6 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
            </div>
            <h3 class="font-bold text-lg mb-2 text-gray-800">Weekly Summary</h3>
            <p class="text-sm text-gray-500 mb-6">See your mood trends and habit progress.</p>
            <a href="{{ route('dashboard.weekly-summary') }}" class="inline-flex items-center gap-2 text-rose-600 text-sm font-semibold group/link">
                View summary
                <span class="group-hover/link:translate-x-1 transition-transform">→</span>
            </a>
        </div>
    </div>
</div>


<div class="relative bg-gradient-to-r from-indigo-600 to-purple-600 rounded-3xl p-10 overflow-hidden group transition-all duration-500 hover:shadow-2xl hover:scale-[1.02]">
    <!-- Background pattern -->
    <div class="absolute inset-0 opacity-10">
        <div class="absolute top-0 left-0 w-full h-full bg-[url('data:image/svg+xml,%3Csvg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="none" fill-rule="evenodd"%3E%3Cg fill="%23ffffff" fill-opacity="0.4"%3E%3Cpath d="M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')]"></div>
    </div>
   
    <!-- Floating elements -->
    <div class="absolute top-4 right-4 w-24 h-24 bg-white/10 rounded-full blur-xl"></div>
    <div class="absolute bottom-4 left-4 w-32 h-32 bg-purple-400/20 rounded-full blur-xl"></div>
   
    <div class="relative z-10">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
            <div class="max-w-xl">
                <h2 class="text-2xl font-bold text-white mb-4 flex items-center gap-3">
                    <span class="p-2 bg-white/20 rounded-xl">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </span>
                    Start a new healing habit
                </h2>
                <p class="text-indigo-100 text-lg mb-6 leading-relaxed">
                    "Small steps, taken consistently, create meaningful change."
                    <span class="block text-indigo-200/80 text-sm mt-2 italic">— One day at a time</span>
                </p>
            </div>
           
            <a href="{{ route('habits.create') }}"
               class="group/button inline-flex items-center gap-3 px-8 py-4 rounded-full bg-white text-indigo-700 text-base font-bold hover:shadow-2xl transition-all duration-300 hover:gap-4 hover:scale-105 active:scale-95 whitespace-nowrap">
                <span>Create habit</span>
                <svg class="w-5 h-5 group-hover/button:rotate-90 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                </svg>
            </a>
        </div>
    </div>
</div>


@endsection



<style>
    @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-10px); }
    }
   
    .group:hover .group-hover\:animate-float {
        animation: float 3s ease-in-out infinite;
    }
</style>
