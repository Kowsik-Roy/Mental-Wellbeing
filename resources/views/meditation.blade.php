@extends('layouts.app')

@section('title', 'Meditation Timer')

@push('styles')
<style>
    /* Cozy gradient background */
    .meditation-container {
        background: linear-gradient(135deg, #fef3f2 0%, #fdf2f8 25%, #f3e8ff 50%, #e0e7ff 75%, #dbeafe 100%);
        min-height: calc(100vh - 200px);
        padding: 2rem 0;
    }

    /* Cozy timer card */
    .timer-card {
        background: linear-gradient(135deg, #ffffff 0%, #fef7ff 100%);
        border: 2px solid rgba(251, 146, 60, 0.2);
        box-shadow: 0 20px 60px -15px rgba(251, 146, 60, 0.3),
                    0 10px 25px -5px rgba(139, 92, 246, 0.2);
        transition: all 0.3s ease;
    }

    .timer-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 25px 70px -15px rgba(251, 146, 60, 0.4),
                    0 15px 30px -5px rgba(139, 92, 246, 0.3);
    }

    /* Breathing circle with cozy colors */
    .breathing-circle {
        background: linear-gradient(135deg, 
            #fbbf24 0%, 
            #f59e0b 25%, 
            #fb7185 50%, 
            #ec4899 75%, 
            #a855f7 100%);
        box-shadow: 
            0 0 40px rgba(251, 146, 60, 0.4),
            0 0 80px rgba(236, 72, 153, 0.3),
            inset 0 0 30px rgba(255, 255, 255, 0.3);
    }

    /* Breathing glow animation */
    .breathing-glow {
        background: radial-gradient(
            circle at 35% 30%,
            rgba(251, 191, 36, 0.8),
            rgba(251, 113, 133, 0.6),
            rgba(236, 72, 153, 0.4),
            rgba(168, 85, 247, 0.2),
            rgba(255, 255, 255, 0)
        );
        transform: scale(0.85);
        opacity: 0.9;
        transition: opacity 0.3s ease;
    }

    .breathing-on .breathing-glow {
        animation: breatheCozy 6s ease-in-out infinite;
    }

    .breathing-paused .breathing-glow {
        animation: none;
        opacity: 0.5;
    }

    @keyframes breatheCozy {
        0%   { 
            transform: scale(0.85); 
            opacity: 0.7; 
        }
        50%  { 
            transform: scale(1.1); 
            opacity: 1; 
        }
        100% { 
            transform: scale(0.85); 
            opacity: 0.7; 
        }
    }

    /* Floating particles */
    .floating-particles {
        position: absolute;
        width: 100%;
        height: 100%;
        overflow: hidden;
        pointer-events: none;
    }

    .particle {
        position: absolute;
        width: 8px;
        height: 8px;
        background: radial-gradient(circle, rgba(251, 191, 36, 0.6), rgba(236, 72, 153, 0.3));
        border-radius: 50%;
        animation: float 15s infinite ease-in-out;
    }

    @keyframes float {
        0%, 100% {
            transform: translateY(0) translateX(0) rotate(0deg);
            opacity: 0.3;
        }
        25% {
            transform: translateY(-20px) translateX(10px) rotate(90deg);
            opacity: 0.6;
        }
        50% {
            transform: translateY(-40px) translateX(-10px) rotate(180deg);
            opacity: 0.8;
        }
        75% {
            transform: translateY(-20px) translateX(5px) rotate(270deg);
            opacity: 0.6;
        }
    }

    /* Cozy buttons */
    .btn-cozy {
        background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
        color: white;
        font-weight: 600;
        box-shadow: 0 4px 15px rgba(251, 191, 36, 0.4);
        transition: all 0.3s ease;
    }

    .btn-cozy:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(251, 191, 36, 0.5);
        background: linear-gradient(135deg, #fcd34d 0%, #fbbf24 100%);
    }

    .btn-pause {
        background: linear-gradient(135deg, #fb7185 0%, #ec4899 100%);
        color: white;
        font-weight: 600;
        box-shadow: 0 4px 15px rgba(251, 113, 133, 0.4);
    }

    .btn-pause:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(251, 113, 133, 0.5);
        background: linear-gradient(135deg, #fda4af 0%, #fb7185 100%);
    }

    .btn-reset {
        background: linear-gradient(135deg, #a855f7 0%, #9333ea 100%);
        color: white;
        font-weight: 600;
        box-shadow: 0 4px 15px rgba(168, 85, 247, 0.4);
    }

    .btn-reset:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(168, 85, 247, 0.5);
        background: linear-gradient(135deg, #c084fc 0%, #a855f7 100%);
    }

    /* Quote styling */
    .quote-text {
        background: linear-gradient(135deg, #fef3f2 0%, #fdf2f8 100%);
        border-left: 4px solid #fb7185;
        padding: 1.5rem;
        border-radius: 1rem;
        font-style: italic;
        color: #7c3aed;
        font-size: 1.1rem;
        line-height: 1.8;
    }

    /* Timer display */
    .timer-display {
        font-family: 'Courier New', monospace;
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.9), rgba(255, 255, 255, 0.7));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        text-shadow: 0 2px 10px rgba(251, 191, 36, 0.3);
    }
</style>
@endpush

@section('content')
<div class="meditation-container">
    <div class="max-w-6xl mx-auto px-4">
        
        {{-- Header --}}
        <div class="text-center mb-10">
            <h1 class="text-5xl md:text-6xl font-bold mb-4 bg-gradient-to-r from-orange-400 via-pink-500 to-purple-600 bg-clip-text text-transparent">
                🧘 Meditation Timer
            </h1>
            <p class="text-lg text-gray-700 max-w-2xl mx-auto">
                Create a peaceful moment for yourself. Breathe slowly. Let your thoughts pass like clouds.
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-start">
            
            {{-- Timer Card --}}
            <div class="timer-card rounded-3xl p-10 text-center relative overflow-hidden">
                
                {{-- Floating particles background --}}
                <div class="floating-particles">
                    <div class="particle" style="left: 10%; animation-delay: 0s;"></div>
                    <div class="particle" style="left: 30%; animation-delay: 2s;"></div>
                    <div class="particle" style="left: 50%; animation-delay: 4s;"></div>
                    <div class="particle" style="left: 70%; animation-delay: 6s;"></div>
                    <div class="particle" style="left: 90%; animation-delay: 8s;"></div>
                </div>

                {{-- Quote --}}
                <p class="quote-text mb-8" id="quote">
                    "Breathe in peace, breathe out tension."
                </p>

                {{-- Breathing Circle --}}
                <div id="breathingWrap"
                     class="mx-auto relative w-[280px] h-[280px] grid place-items-center breathing-paused mb-8">
                    
                    <div class="breathing-glow absolute inset-0 rounded-full"></div>
                    
                    <div class="breathing-circle w-[240px] h-[240px] rounded-full flex flex-col items-center justify-center relative z-10">
                        <div id="timer" class="timer-display text-6xl font-bold text-white mb-3">
                            05:00
                        </div>
                        <div id="breathText" class="text-lg font-semibold text-white/90">
                            Ready 🌿
                        </div>
                    </div>
                </div>

                {{-- Minutes Input --}}
                <div class="mb-8 flex flex-wrap items-center justify-center gap-4">
                    <label class="text-base font-semibold text-gray-700">Duration (minutes)</label>
                    <input type="number" id="minutes" value="5" min="1" max="180"
                           class="w-28 px-4 py-3 rounded-full border-2 border-orange-200 text-center bg-white text-lg font-semibold focus:border-orange-400 focus:ring-2 focus:ring-orange-200 transition" />
                </div>

                {{-- Control Buttons --}}
                <div class="flex flex-wrap justify-center gap-4">
                    <button id="startBtn"
                            class="btn-cozy px-8 py-4 rounded-full text-base font-semibold"
                            onclick="startTimer()">
                        ▶ Start
                    </button>
                    
                    <button id="pauseBtn"
                            class="btn-pause px-8 py-4 rounded-full text-base font-semibold"
                            onclick="pauseTimer()">
                        ⏸ Pause
                    </button>
                    
                    <button id="resetBtn"
                            class="btn-reset px-8 py-4 rounded-full text-base font-semibold"
                            onclick="resetTimer()">
                        ↻ Reset
                    </button>
                </div>

                <p class="text-sm text-gray-600 mt-6 italic">
                    💡 Tip: Inhale as the glow expands, exhale as it shrinks.
                </p>
            </div>

            {{-- Instructions & Info Card --}}
            <div class="bg-white rounded-3xl p-8 shadow-lg border-2 border-pink-100">
                <h3 class="text-2xl font-bold text-gray-800 mb-6 flex items-center gap-2">
                    <span class="text-3xl">✨</span>
                    How to Meditate
                </h3>
                
                <ul class="text-base text-gray-700 space-y-4 mb-8">
                    <li class="flex items-start gap-3">
                        <span class="text-2xl">🌱</span>
                        <span>Choose your duration and press <strong class="text-orange-600">Start</strong>.</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="text-2xl">🌊</span>
                        <span>Follow the breathing cue: <strong class="text-pink-600">Inhale</strong> → <strong class="text-purple-600">Exhale</strong>.</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="text-2xl">⏸️</span>
                        <span>You can <strong class="text-purple-600">Pause</strong> or <strong class="text-pink-600">Reset</strong> anytime.</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="text-2xl">💫</span>
                        <span>Your progress is saved automatically!</span>
                    </li>
                </ul>

                <div class="rounded-2xl bg-gradient-to-br from-pink-50 to-purple-50 border-2 border-pink-200 p-6 mb-6">
                    <p class="text-base text-pink-900 font-semibold mb-2 flex items-center gap-2">
                        <span class="text-2xl">💝</span>
                        Gentle Reminder
                    </p>
                    <p class="text-sm text-gray-700 leading-relaxed">
                        You don't have to control your thoughts. Simply notice them like clouds passing in the sky, and gently return to your breath. This is your safe space.
                    </p>
                </div>

                <div class="rounded-2xl bg-gradient-to-br from-orange-50 to-amber-50 border-2 border-orange-200 p-6">
                    <p class="text-sm font-semibold text-orange-900 mb-2">🎯 Current Session</p>
                    <p class="text-xs text-gray-700" id="sessionInfo">
                        Ready to begin your meditation journey
                    </p>
                </div>

                <div class="mt-6">
                    <a href="{{ route('dashboard') }}"
                       class="inline-flex items-center gap-2 text-indigo-600 hover:text-indigo-800 font-medium transition">
                        <span>←</span>
                        <span>Back to dashboard</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Timer state management with localStorage
    const STORAGE_KEY = 'meditation_timer_state';
    
    let totalSeconds = 300;
    let interval = null;
    let paused = false;
    let startTime = null;
    let elapsedWhenPaused = 0;

    let breathInterval = null;
    let inhale = true;

    const timerEl = document.getElementById("timer");
    const minutesInput = document.getElementById("minutes");
    const quoteEl = document.getElementById("quote");
    const breathText = document.getElementById("breathText");
    const breathingWrap = document.getElementById("breathingWrap");
    const sessionInfo = document.getElementById("sessionInfo");

    const quotes = [
        "Breathe in peace, breathe out tension.",
        "You are safe in this moment.",
        "Let your thoughts pass like clouds.",
        "Nothing to do. Nowhere to go.",
        "Your breath is your anchor.",
        "Stillness is where healing begins.",
        "This moment is perfect, just as it is.",
        "You are exactly where you need to be.",
        "Peace begins with a single breath.",
        "Allow yourself to simply be."
    ];

    // Load state from localStorage
    function loadState() {
        try {
            const saved = localStorage.getItem(STORAGE_KEY);
            if (saved) {
                const state = JSON.parse(saved);
                const now = Date.now();
                
                // Check if session is still valid (within 24 hours)
                if (now - state.timestamp < 24 * 60 * 60 * 1000) {
                    totalSeconds = state.totalSeconds;
                    paused = state.paused;
                    startTime = state.startTime ? new Date(state.startTime) : null;
                    elapsedWhenPaused = state.elapsedWhenPaused || 0;
                    
                    // If timer was running, calculate remaining time
                    if (startTime && !paused) {
                        const elapsed = Math.floor((now - startTime.getTime()) / 1000) + elapsedWhenPaused;
                        const initialSeconds = state.initialSeconds || totalSeconds;
                        totalSeconds = Math.max(0, initialSeconds - elapsed);
                        
                        if (totalSeconds > 0) {
                            // Resume timer
                            updateDisplay();
                            startBreathing();
                            resumeTimer();
                            sessionInfo.textContent = `Session resumed - ${formatTime(totalSeconds)} remaining`;
                        } else {
                            // Timer completed
                            totalSeconds = 0;
                            updateDisplay();
                            completeBreathing();
                            sessionInfo.textContent = "Previous session completed ✨";
                            clearState();
                        }
                    } else if (paused && totalSeconds > 0) {
                        // Timer was paused
                        updateDisplay();
                        pauseBreathing();
                        sessionInfo.textContent = `Session paused - ${formatTime(totalSeconds)} remaining`;
                    } else {
                        updateDisplay();
                    }
                    
                    minutesInput.value = Math.ceil((state.initialSeconds || totalSeconds) / 60);
                    return true;
                } else {
                    clearState();
                }
            }
        } catch (e) {
            console.error('Error loading state:', e);
            clearState();
        }
        return false;
    }

    // Save state to localStorage
    function saveState() {
        try {
            const state = {
                totalSeconds: totalSeconds,
                paused: paused,
                startTime: startTime ? startTime.toISOString() : null,
                elapsedWhenPaused: elapsedWhenPaused,
                initialSeconds: parseInt(minutesInput.value || "5", 10) * 60,
                timestamp: Date.now()
            };
            localStorage.setItem(STORAGE_KEY, JSON.stringify(state));
        } catch (e) {
            console.error('Error saving state:', e);
        }
    }

    // Clear state
    function clearState() {
        localStorage.removeItem(STORAGE_KEY);
        startTime = null;
        elapsedWhenPaused = 0;
    }

    // Format time helper
    function formatTime(seconds) {
        const m = Math.floor(seconds / 60);
        const s = seconds % 60;
        return `${String(m).padStart(2, '0')}:${String(s).padStart(2, '0')}`;
    }

    function updateDisplay() {
        timerEl.textContent = formatTime(totalSeconds);
        saveState();
    }

    function startBreathing() {
        breathingWrap.classList.remove("breathing-paused");
        breathingWrap.classList.add("breathing-on");

        inhale = true;
        breathText.textContent = "Inhale…";

        clearInterval(breathInterval);
        breathInterval = setInterval(() => {
            inhale = !inhale;
            breathText.textContent = inhale ? "Inhale…" : "Exhale…";
        }, 3000);
    }

    function pauseBreathing() {
        breathingWrap.classList.remove("breathing-on");
        breathingWrap.classList.add("breathing-paused");
        breathText.textContent = "Paused ⏸";
        clearInterval(breathInterval);
        breathInterval = null;
    }

    function completeBreathing() {
        breathingWrap.classList.remove("breathing-on");
        breathingWrap.classList.add("breathing-paused");
        breathText.textContent = "Complete ✨";
        clearInterval(breathInterval);
        breathInterval = null;
    }

    function startTimer() {
        if (interval && !paused) return;

        if (!interval) {
            // Starting fresh
            totalSeconds = parseInt(minutesInput.value || "5", 10) * 60;
            startTime = new Date();
            elapsedWhenPaused = 0;
        } else {
            // Resuming
            startTime = new Date();
        }

        updateDisplay();
        paused = false;
        quoteEl.textContent = `"${quotes[Math.floor(Math.random() * quotes.length)]}"`;

        startBreathing();
        sessionInfo.textContent = `Session active - ${formatTime(totalSeconds)} remaining`;

        interval = setInterval(() => {
            if (!paused && totalSeconds > 0) {
                totalSeconds--;
                updateDisplay();
                
                if (totalSeconds % 60 === 0 && totalSeconds > 0) {
                    sessionInfo.textContent = `Session active - ${formatTime(totalSeconds)} remaining`;
                }
            }

            if (totalSeconds === 0) {
                clearInterval(interval);
                interval = null;
                quoteEl.textContent = '"Meditation complete 🌿"';
                completeBreathing();
                sessionInfo.textContent = "Session completed! Great job! ✨";
                clearState();
            }
        }, 1000);
        
        saveState();
    }

    function pauseTimer() {
        if (!interval) return;

        paused = !paused;
        
        if (paused) {
            // Calculate elapsed time
            if (startTime) {
                elapsedWhenPaused += Math.floor((Date.now() - startTime.getTime()) / 1000);
            }
            pauseBreathing();
            sessionInfo.textContent = `Session paused - ${formatTime(totalSeconds)} remaining`;
        } else {
            // Resume
            startTime = new Date();
            startBreathing();
            sessionInfo.textContent = `Session active - ${formatTime(totalSeconds)} remaining`;
        }
        
        saveState();
    }

    function resetTimer() {
        clearInterval(interval);
        interval = null;

        paused = false;
        totalSeconds = parseInt(minutesInput.value || "5", 10) * 60;
        updateDisplay();

        quoteEl.textContent = '"Breathe in peace, breathe out tension."';
        breathingWrap.classList.remove("breathing-on");
        breathingWrap.classList.add("breathing-paused");
        breathText.textContent = "Ready 🌿";
        sessionInfo.textContent = "Ready to begin your meditation journey";

        clearInterval(breathInterval);
        breathInterval = null;
        startTime = null;
        elapsedWhenPaused = 0;
        
        clearState();
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', () => {
        const loaded = loadState();
        if (!loaded) {
            updateDisplay();
        }
        
        // Auto-save every 5 seconds while timer is running
        setInterval(() => {
            if (interval && !paused) {
                saveState();
            }
        }, 5000);
    });

    // Save state before page unload
    window.addEventListener('beforeunload', () => {
        saveState();
    });
</script>
@endsection
