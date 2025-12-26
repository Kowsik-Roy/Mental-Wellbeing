@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto">

    <div class="mb-8 text-center md:text-left">
        <h1 class="text-3xl md:text-4xl font-semibold text-indigo-900">
            Meditation Timer
        </h1>
        <p class="text-gray-700 mt-2">
            Breathe slowly. Let your thoughts pass like clouds.
        </p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">

        {{-- Timer Card --}}
        <div class="bg-white rounded-3xl p-8 shadow-sm card-hover text-center">

            <p class="text-gray-600 italic mb-6" id="quote">
                “Breathe in peace, breathe out tension.”
            </p>

            <div id="breathingWrap"
                 class="mx-auto relative w-[260px] h-[260px] grid place-items-center breathing-paused">

                <div class="breathing-glow absolute inset-0 rounded-full"></div>

                <div
                    class="w-[230px] h-[230px] rounded-full flex flex-col items-center justify-center shadow-sm
                           bg-gradient-to-br from-indigo-200 to-emerald-200 relative z-10">
                    <div id="timer" class="text-5xl font-semibold text-gray-800">
                        05:00
                    </div>
                    <div id="breathText"
                         class="text-sm font-medium text-gray-700/80 mt-2">
                        Ready 🌿
                    </div>
                </div>
            </div>

            <div class="mt-7 flex flex-wrap items-center justify-center gap-3">
                <label class="text-sm text-gray-700">Minutes</label>
                <input type="number" id="minutes" value="5" min="1" max="180"
                       class="w-24 px-3 py-2 rounded-full border border-gray-200 text-center bg-white" />
            </div>

            <div class="mt-6 flex flex-wrap justify-center gap-3">
                <button
                    class="px-5 py-3 rounded-full bg-indigo-300 text-gray-900 text-sm font-medium hover:bg-indigo-400 transition"
                    onclick="startTimer()">
                    Start
                </button>

                <button
                    class="px-5 py-3 rounded-full bg-amber-200 text-amber-900 text-sm font-medium hover:bg-amber-300 transition"
                    onclick="pauseTimer()">
                    Pause
                </button>

                <button
                    class="px-5 py-3 rounded-full bg-rose-200 text-rose-900 text-sm font-medium hover:bg-rose-300 transition"
                    onclick="resetTimer()">
                    Reset
                </button>
            </div>

            <p class="text-xs text-gray-500 mt-5">
                Tip: inhale as the glow expands, exhale as it shrinks.
            </p>
        </div>

        {{-- Instructions / Quotes --}}
        <div class="bg-white rounded-3xl p-8 shadow-sm card-hover">
            <h3 class="font-semibold mb-2">How to use</h3>
            <ul class="text-sm text-gray-700 space-y-2">
                <li>• Choose minutes and press <b>Start</b>.</li>
                <li>• Follow the breathing cue: <b>Inhale</b> → <b>Exhale</b>.</li>
                <li>• Pause or reset anytime.</li>
            </ul>

            <div class="mt-6 rounded-2xl bg-indigo-50 border border-indigo-100 p-5">
                <p class="text-sm text-indigo-900 font-medium mb-1">
                    Soothing reminder
                </p>
                <p class="text-sm text-gray-700">
                    You don’t have to control your thoughts.
                    Simply notice them and return to your breath.
                </p>
            </div>

            <div class="mt-6">
                <a href="{{ route('dashboard') }}"
                   class="text-indigo-600 text-sm font-medium hover:underline">
                    ← Back to dashboard
                </a>
            </div>
        </div>

    </div>
</div>

<style>
    .breathing-glow {
        background: radial-gradient(
            circle at 35% 30%,
            rgba(199,210,254,0.9),
            rgba(187,247,208,0.65),
            rgba(255,255,255,0)
        );
        transform: scale(0.88);
        opacity: 0.85;
        transition: opacity .3s ease;
    }

    .breathing-on .breathing-glow {
        animation: breathe 6s ease-in-out infinite;
    }

    .breathing-paused .breathing-glow {
        animation: none;
        opacity: .55;
    }

    @keyframes breathe {
        0%   { transform: scale(0.88); opacity: 0.75; }
        50%  { transform: scale(1.04); opacity: 0.95; }
        100% { transform: scale(0.88); opacity: 0.75; }
    }
</style>

<script>
    let totalSeconds = 300;
    let interval = null;
    let paused = false;

    let breathInterval = null;
    let inhale = true;

    const timerEl = document.getElementById("timer");
    const minutesInput = document.getElementById("minutes");
    const quoteEl = document.getElementById("quote");
    const breathText = document.getElementById("breathText");
    const breathingWrap = document.getElementById("breathingWrap");

    const quotes = [
        "Breathe in peace, breathe out tension.",
        "You are safe in this moment.",
        "Let your thoughts pass like clouds.",
        "Nothing to do. Nowhere to go.",
        "Your breath is your anchor.",
        "Stillness is where healing begins."
    ];

    function updateDisplay() {
        const m = Math.floor(totalSeconds / 60);
        const s = totalSeconds % 60;
        timerEl.textContent =
            String(m).padStart(2, '0') + ':' + String(s).padStart(2, '0');
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
        if (interval) return;

        totalSeconds = parseInt(minutesInput.value || "5", 10) * 60;
        updateDisplay();

        paused = false;
        quoteEl.textContent = quotes[Math.floor(Math.random() * quotes.length)];

        startBreathing();

        interval = setInterval(() => {
            if (!paused && totalSeconds > 0) {
                totalSeconds--;
                updateDisplay();
            }

            if (totalSeconds === 0) {
                clearInterval(interval);
                interval = null;
                quoteEl.textContent = "Meditation complete 🌿";
                completeBreathing();
            }
        }, 1000);
    }

    function pauseTimer() {
        if (!interval) return;

        paused = !paused;
        paused ? pauseBreathing() : startBreathing();
    }

    function resetTimer() {
        clearInterval(interval);
        interval = null;

        paused = false;
        totalSeconds = parseInt(minutesInput.value || "5", 10) * 60;
        updateDisplay();

        quoteEl.textContent = "Breathe in peace, breathe out tension.";
        breathingWrap.classList.remove("breathing-on");
        breathingWrap.classList.add("breathing-paused");
        breathText.textContent = "Ready 🌿";

        clearInterval(breathInterval);
        breathInterval = null;
    }

    updateDisplay();
</script>
@endsection
