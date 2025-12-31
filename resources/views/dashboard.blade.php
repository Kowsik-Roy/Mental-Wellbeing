@extends('layouts.app')

@section('content')

{{-- Emergency Contact Alert --}}
@if(!$hasEmergencyContact)
<div class="fixed top-24 left-4 z-50 max-w-sm" id="emergency-contact-alert">
    <div class="bg-gradient-to-r from-red-600 to-red-700 text-white rounded-xl shadow-xl border-2 border-red-800 p-4">
        <div class="flex items-start gap-3">
            <div class="flex-shrink-0">
                <div class="w-10 h-10 bg-red-800 rounded-full flex items-center justify-center text-xl animate-pulse">
                    ⚠️
                </div>
            </div>
            <div class="flex-1">
                <h3 class="font-bold text-base mb-1">Emergency Contact Required</h3>
                <p class="text-xs text-red-100 mb-3">
                    Add a contact to be notified if you have 3 consecutive sad days.
                </p>
                <a href="{{ route('profile.edit') }}" 
                   class="inline-flex items-center gap-1 px-3 py-1.5 bg-white text-red-600 rounded-lg font-semibold text-xs hover:bg-red-50 transition shadow">
                    <span>Add Contact</span>
                    <i class="fas fa-arrow-right text-xs"></i>
                </a>
            </div>

        </div>
    </div>
</div>
@endif

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


<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-14">
    {{-- Daily Journal --}}
    <div class="relative rounded-3xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 bg-gradient-to-br from-blue-50 to-blue-100 border border-blue-200 group hover:bg-gradient-to-br hover:from-blue-100 hover:to-blue-50 hover:border-blue-300 hover:-translate-y-1">
        <!-- Decorative corner accent -->
        <div class="absolute top-4 right-4 w-12 h-12 rounded-full bg-blue-200/40 group-hover:bg-blue-300/40 transition-colors duration-300"></div>
        
        <div class="relative z-10">
            <div class="inline-flex items-center justify-center p-3 mb-5 rounded-2xl bg-gradient-to-br from-blue-500 to-blue-600 shadow-md group-hover:shadow-blue-300 group-hover:shadow-lg transition-all duration-300 group-hover:-translate-y-1">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            
            <h3 class="font-bold text-gray-800 mb-2 text-xl text-blue-700 group-hover:text-blue-800 transition-colors duration-300">
                Daily Journal
            </h3>
            <p class="text-gray-600 mb-6 leading-relaxed group-hover:text-gray-700 transition-colors duration-300">
                Capture your thoughts in a peaceful space
            </p>
            
            <a href="{{ route('journal.today') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white rounded-full text-blue-700 text-sm font-semibold hover:bg-blue-50 transition-all duration-300 border border-blue-200 shadow-sm hover:shadow group-hover:border-blue-300 group-hover:bg-blue-100">
                <span>Begin writing</span>
                <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                </svg>
            </a>
        </div>
    </div>

    {{-- Daily Habits --}}
    <div class="relative rounded-3xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 bg-gradient-to-br from-emerald-50 to-emerald-100 border border-emerald-200 group hover:bg-gradient-to-br hover:from-emerald-100 hover:to-emerald-50 hover:border-emerald-300 hover:-translate-y-1">
        <!-- Pattern dots -->
        <div class="absolute top-2 right-2 flex gap-1 opacity-30 group-hover:opacity-50 transition-opacity duration-300">
            <div class="w-2 h-2 rounded-full bg-emerald-400"></div>
            <div class="w-2 h-2 rounded-full bg-emerald-400"></div>
        </div>
        
        <div class="relative z-10">
            <div class="inline-flex items-center justify-center p-3 mb-5 rounded-2xl bg-gradient-to-br from-emerald-500 to-emerald-600 shadow-md group-hover:shadow-emerald-300 group-hover:shadow-lg transition-all duration-300 group-hover:-translate-y-1">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            
            <h3 class="font-bold text-gray-800 mb-2 text-xl text-emerald-700 group-hover:text-emerald-800 transition-colors duration-300">
                Daily Habits
            </h3>
            <p class="text-gray-600 mb-6 leading-relaxed group-hover:text-gray-700 transition-colors duration-300">
                Build meaningful routines gently
            </p>
            
            <a href="{{ route('habits.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white rounded-full text-emerald-700 text-sm font-semibold hover:bg-emerald-50 transition-all duration-300 border border-emerald-200 shadow-sm hover:shadow group-hover:border-emerald-300 group-hover:bg-emerald-100">
                <span>View habits</span>
                <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                </svg>
            </a>
        </div>
    </div>

    {{-- Journal History --}}
    <div class="relative rounded-3xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 bg-gradient-to-br from-purple-50 to-purple-100 border border-purple-200 group hover:bg-gradient-to-br hover:from-purple-100 hover:to-purple-50 hover:border-purple-300 hover:-translate-y-1">
        
        
        <div class="relative z-10">
            <div class="inline-flex items-center justify-center p-3 mb-5 rounded-2xl bg-gradient-to-br from-purple-500 to-purple-600 shadow-md group-hover:shadow-purple-300 group-hover:shadow-lg transition-all duration-300 group-hover:-translate-y-1">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            
            <h3 class="font-bold text-gray-800 mb-2 text-xl text-purple-700 group-hover:text-purple-800 transition-colors duration-300">
                Journal History
            </h3>
            <p class="text-gray-600 mb-6 leading-relaxed group-hover:text-gray-700 transition-colors duration-300">
                Reflect on your personal journey
            </p>
            
            <a href="{{ route('journal.history') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white rounded-full text-purple-700 text-sm font-semibold hover:bg-purple-50 transition-all duration-300 border border-purple-200 shadow-sm hover:shadow group-hover:border-purple-300 group-hover:bg-purple-100">
                <span>View history</span>
                <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                </svg>
            </a>
        </div>
    </div>

    {{-- Meditation --}}
    <div class="relative rounded-3xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 bg-gradient-to-br from-cyan-50 to-cyan-100 border border-cyan-200 group hover:bg-gradient-to-br hover:from-cyan-100 hover:to-cyan-50 hover:border-cyan-300 hover:-translate-y-1">
        <!-- Wave accent -->
        <div class="absolute bottom-0 left-0 right-0 h-4 overflow-hidden opacity-20 group-hover:opacity-30 transition-opacity duration-300">
            <div class="w-full h-full bg-gradient-to-r from-transparent via-cyan-400 to-transparent"></div>
        </div>
        
        <div class="relative z-10">
            <div class="inline-flex items-center justify-center p-3 mb-5 rounded-2xl bg-gradient-to-br from-cyan-500 to-cyan-600 shadow-md group-hover:shadow-cyan-300 group-hover:shadow-lg transition-all duration-300 group-hover:-translate-y-1">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            
            <h3 class="font-bold text-gray-800 mb-2 text-xl text-cyan-700 group-hover:text-cyan-800 transition-colors duration-300">
                Meditation Timer
            </h3>
            <p class="text-gray-600 mb-6 leading-relaxed group-hover:text-gray-700 transition-colors duration-300">
                Find peace in guided moments
            </p>
            
            <a href="{{ route('meditation') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white rounded-full text-cyan-700 text-sm font-semibold hover:bg-cyan-50 transition-all duration-300 border border-cyan-200 shadow-sm hover:shadow group-hover:border-cyan-300 group-hover:bg-cyan-100">
                <span>Start session</span>
                <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                </svg>
            </a>
        </div>
    </div>

    {{-- Weekly Summary --}}
    <div class="relative rounded-3xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 bg-gradient-to-br from-rose-50 to-rose-100 border border-rose-200 group hover:bg-gradient-to-br hover:from-rose-100 hover:to-rose-50 hover:border-rose-300 hover:-translate-y-1">
        <!-- Bar graph accent -->
        <div class="absolute bottom-3 left-4 right-4 h-8 flex items-end gap-1 opacity-30 group-hover:opacity-40 transition-opacity duration-300">
            <div class="w-3 bg-rose-400 rounded-t group-hover:bg-rose-500 transition-colors duration-300" style="height: 40%"></div>
            <div class="w-3 bg-rose-400 rounded-t group-hover:bg-rose-500 transition-colors duration-300" style="height: 70%"></div>
            <div class="w-3 bg-rose-400 rounded-t group-hover:bg-rose-500 transition-colors duration-300" style="height: 30%"></div>
            <div class="w-3 bg-rose-400 rounded-t group-hover:bg-rose-500 transition-colors duration-300" style="height: 90%"></div>
        </div>
        
        <div class="relative z-10">
            <div class="inline-flex items-center justify-center p-3 mb-5 rounded-2xl bg-gradient-to-br from-rose-500 to-rose-600 shadow-md group-hover:shadow-rose-300 group-hover:shadow-lg transition-all duration-300 group-hover:-translate-y-1">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
            </div>
            
            <h3 class="font-bold text-gray-800 mb-2 text-xl text-rose-700 group-hover:text-rose-800 transition-colors duration-300">
                Weekly Summary
            </h3>
            <p class="text-gray-600 mb-6 leading-relaxed group-hover:text-gray-700 transition-colors duration-300">
                Visualize your progress and patterns
            </p>
            
            <a href="{{ route('dashboard.weekly-summary') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white rounded-full text-rose-700 text-sm font-semibold hover:bg-rose-50 transition-all duration-300 border border-rose-200 shadow-sm hover:shadow group-hover:border-rose-300 group-hover:bg-rose-100">
                <span>View insights</span>
                <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                </svg>
            </a>
        </div>
    </div>

    {{-- AI Support Chat --}}
    <div class="relative rounded-3xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 bg-gradient-to-br from-violet-50 to-violet-100 border border-violet-200 group hover:bg-gradient-to-br hover:from-violet-100 hover:to-violet-50 hover:border-violet-300 hover:-translate-y-1">
        <!-- Sparkle accents -->
        <div class="absolute top-2 left-2 opacity-40 group-hover:opacity-60 transition-opacity duration-300">
            <div class="w-1 h-1 bg-violet-400 rounded-full"></div>
        </div>
        <div class="absolute bottom-2 right-2 opacity-40 group-hover:opacity-60 transition-opacity duration-300">
            <div class="w-1 h-1 bg-violet-400 rounded-full"></div>
        </div>
        
        <div class="relative z-10">
            <div class="inline-flex items-center justify-center p-3 mb-5 rounded-2xl bg-gradient-to-br from-violet-500 to-violet-600 shadow-md group-hover:shadow-violet-300 group-hover:shadow-lg transition-all duration-300 group-hover:-translate-y-1">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
            </div>
            
            <h3 class="font-bold text-gray-800 mb-2 text-xl text-violet-700 group-hover:text-violet-800 transition-colors duration-300">
                AI Support Chat
            </h3>
            <p class="text-gray-600 mb-6 leading-relaxed group-hover:text-gray-700 transition-colors duration-300">
                Gentle guidance and support
            </p>
            
            <a href="{{ route('ai.chat') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white rounded-full text-violet-700 text-sm font-semibold hover:bg-violet-50 transition-all duration-300 border border-violet-200 shadow-sm hover:shadow group-hover:border-violet-300 group-hover:bg-violet-100">
                <span>Start conversation</span>
                <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                </svg>
            </a>
        </div>
    </div>
</div>

<div class="relative rounded-3xl p-8 shadow-lg hover:shadow-xl transition-all duration-300 overflow-hidden bg-gradient-to-br from-green-50 to-emerald-100 border border-green-200 group hover:bg-gradient-to-br hover:from-green-100 hover:to-emerald-50 hover:border-green-300 hover:-translate-y-1">
    <!-- Decorative corner accents -->
    <div class="absolute top-0 right-0 w-24 h-24 group-hover:scale-110 transition-transform duration-300">
        <div class="absolute top-0 right-0 w-16 h-16 rounded-full bg-green-300/20 group-hover:bg-green-400/30 transition-colors duration-300"></div>
    </div>
    <div class="absolute bottom-0 left-0 w-16 h-16 rounded-full bg-emerald-300/20 group-hover:bg-emerald-400/30 transition-colors duration-300"></div>
    
    <div class="relative z-10">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div class="lg:w-2/3">
                <div class="inline-flex items-center gap-3 mb-4">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-green-400 to-emerald-500 flex items-center justify-center shadow-md group-hover:shadow-green-300 group-hover:shadow-lg transition-all duration-300 group-hover:-translate-y-1">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800 mb-2 group-hover:text-gray-900 transition-colors duration-300">Create New Habit</h2>
                        <div class="w-12 h-1 bg-gradient-to-r from-green-400 to-emerald-500 rounded-full group-hover:from-green-500 group-hover:to-emerald-600 transition-colors duration-300"></div>
                    </div>
                </div>
                
                <p class="text-gray-600 mb-6 max-w-xl leading-relaxed group-hover:text-gray-700 transition-colors duration-300">
                    Small, consistent steps create lasting change. Start your journey toward 
                    meaningful transformation today.
                </p>
            </div>
            
            <div class="lg:w-1/3 flex justify-center lg:justify-end">
                <a href="{{ route('habits.create') }}"
                   class="inline-flex items-center justify-center gap-3 px-6 py-3 rounded-full text-base font-semibold shadow-md hover:shadow-lg transition-all duration-300 bg-gradient-to-r from-green-400 to-emerald-500 text-white hover:from-green-500 hover:to-emerald-600 group-hover:scale-105 transform">
                    <span>Create Habit</span>
                    <svg class="w-5 h-5 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </a>
            </div>
        </div>
    </div>
</div>

@endsection
