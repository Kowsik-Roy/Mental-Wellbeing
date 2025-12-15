@extends('layouts.app')

@section('content')
    
    <!DOCTYPE html>
    <html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Create New Habit - Wellness Companion</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        <script src="https://cdn.tailwindcss.com"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        
        <style>
            body {
                font-family: 'Inter', sans-serif;
                background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
                min-height: 100vh;
            }
            
            .glass-card {
                background: rgba(255, 255, 255, 0.95);
                backdrop-filter: blur(10px);
                border-radius: 20px;
                border: 1px solid rgba(255, 255, 255, 0.2);
            }
            
            .frequency-option {
                transition: all 0.3s ease;
                cursor: pointer;
                border: 2px solid transparent;
            }
            
            .frequency-option:hover {
                transform: translateY(-2px);
                border-color: #10b981;
                box-shadow: 0 10px 25px rgba(16, 185, 129, 0.1);
            }
            
            .frequency-option.active {
                border-color: #10b981;
                background-color: #f0fdf4;
            }
            
            .goal-card {
                transition: all 0.3s ease;
                cursor: pointer;
                border: 2px solid transparent;
            }
            
            .goal-card:hover {
                transform: translateY(-2px);
                box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            }
            
            .goal-card.active {
                border-color: #3b82f6;
                background-color: #eff6ff;
            }
            
            .btn-glow {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                transition: all 0.3s ease;
            }
            
            .btn-glow:hover {
                transform: translateY(-2px);
                box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
            }
            
            .pulse-animation {
                animation: pulse 2s infinite;
            }
            
            @keyframes pulse {
                0% { transform: scale(1); }
                50% { transform: scale(1.05); }
                100% { transform: scale(1); }
            }
            
            .floating-icon {
                animation: float 3s ease-in-out infinite;
            }
            
            @keyframes float {
                0%, 100% { transform: translateY(0px); }
                50% { transform: translateY(-10px); }
            }
        </style>
    </head>
    <body class="min-h-screen flex items-center justify-center p-4">
        <div class="max-w-2xl w-full">
            <!-- Floating decorative elements -->
            <div class="absolute top-10 left-10 w-24 h-24 bg-gradient-to-r from-purple-200 to-pink-200 rounded-full opacity-20 floating-icon"></div>
            <div class="absolute bottom-10 right-10 w-32 h-32 bg-gradient-to-r from-blue-200 to-green-200 rounded-full opacity-20 floating-icon" style="animation-delay: 1s;"></div>
            
            <!-- Back button -->
            <div class="mb-6">
                <a href="{{ route('habits.index') }}" 
                class="inline-flex items-center text-gray-700 hover:text-gray-900 font-medium transition">
                    <i class="fas fa-arrow-left mr-2"></i> Back to Habits
                </a>
            </div>
            
            <!-- Main card -->
            <div class="glass-card shadow-2xl p-8">
                <!-- Header with icon -->
                <div class="text-center mb-8">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-r from-green-400 to-blue-500 rounded-full mb-4">
                        <i class="fas fa-plus text-white text-2xl"></i>
                    </div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Create New Habit</h1>
                    <p class="text-gray-600">Build a consistent routine for better mental wellness</p>
                </div>
                
                <!-- Form -->
                <form method="POST" action="{{ route('habits.store') }}" id="habitForm">
                    @csrf
                    
                    <!-- Title -->
                    <div class="mb-8">
                        <label class="block text-sm font-semibold text-gray-700 mb-3">
                            <i class="fas fa-heading mr-2 text-blue-500"></i>
                            Habit Name *
                        </label>
                        <input type="text" 
                            name="title" 
                            value="{{ old('title') }}"
                            class="w-full px-6 py-4 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition text-lg"
                            placeholder="What's your new habit?"
                            required>
                        @error('title')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Description -->
                    <div class="mb-8">
                        <label class="block text-sm font-semibold text-gray-700 mb-3">
                            <i class="fas fa-align-left mr-2 text-green-500"></i>
                            Why is this important?
                        </label>
                        <textarea name="description" 
                                rows="3"
                                class="w-full px-6 py-4 border-2 border-gray-200 rounded-xl focus:border-green-500 focus:ring-2 focus:ring-green-200 transition"
                                placeholder="Describe why this habit matters to you...">{{ old('description') }}</textarea>
                    </div>
                    
                    <!-- Frequency -->
                    <div class="mb-10">
                        <label class="block text-sm font-semibold text-gray-700 mb-4">
                            <i class="fas fa-calendar-alt mr-2 text-purple-500"></i>
                            How often? *
                        </label>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <label class="frequency-option p-6 rounded-xl border-2 bg-white text-center cursor-pointer">
                                <input type="radio" name="frequency" value="daily" class="hidden" 
                                    {{ old('frequency', 'daily') == 'daily' ? 'checked' : '' }}>
                                <div class="text-4xl mb-3 text-blue-500">
                                    <i class="fas fa-sun"></i>
                                </div>
                                <h3 class="font-bold text-gray-900 mb-1">Daily</h3>
                                <p class="text-sm text-gray-600">Every single day</p>
                            </label>
                            
                            <label class="frequency-option p-6 rounded-xl border-2 bg-white text-center cursor-pointer">
                                <input type="radio" name="frequency" value="weekdays" class="hidden"
                                    {{ old('frequency') == 'weekdays' ? 'checked' : '' }}>
                                <div class="text-4xl mb-3 text-green-500">
                                    <i class="fas fa-briefcase"></i>
                                </div>
                                <h3 class="font-bold text-gray-900 mb-1">Weekdays</h3>
                                <p class="text-sm text-gray-600">Mon - Fri only</p>
                            </label>
                            
                            <label class="frequency-option p-6 rounded-xl border-2 bg-white text-center cursor-pointer">
                                <input type="radio" name="frequency" value="weekend" class="hidden"
                                    {{ old('frequency') == 'weekend' ? 'checked' : '' }}>
                                <div class="text-4xl mb-3 text-yellow-500">
                                    <i class="fas fa-umbrella-beach"></i>
                                </div>
                                <h3 class="font-bold text-gray-900 mb-1">Weekend</h3>
                                <p class="text-sm text-gray-600">Sat & Sun only</p>
                            </label>
                        </div>
                        @error('frequency')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Goal Type -->
                    <div class="mb-10">
                        <label class="block text-sm font-semibold text-gray-700 mb-4">
                            <i class="fas fa-bullseye mr-2 text-red-500"></i>
                            What's your goal? *
                        </label>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <label class="goal-card p-6 rounded-xl border-2 bg-white cursor-pointer">
                                <input type="radio" name="goal_type" value="once" class="hidden"
                                    {{ old('goal_type', 'once') == 'once' ? 'checked' : '' }}>
                                <div class="text-4xl mb-3 text-red-500">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                                <h3 class="font-bold text-gray-900 mb-2">Just Once</h3>
                                <p class="text-sm text-gray-600">Complete it one time</p>
                            </label>
                            
                            <label class="goal-card p-6 rounded-xl border-2 bg-white cursor-pointer">
                                <input type="radio" name="goal_type" value="multiple_times" class="hidden"
                                    {{ old('goal_type') == 'multiple_times' ? 'checked' : '' }}>
                                <div class="text-4xl mb-3 text-orange-500">
                                    <i class="fas fa-redo"></i>
                                </div>
                                <h3 class="font-bold text-gray-900 mb-2">Multiple Times</h3>
                                <p class="text-sm text-gray-600">Repeat throughout the day</p>
                            </label>
                            
                            <label class="goal-card p-6 rounded-xl border-2 bg-white cursor-pointer">
                                <input type="radio" name="goal_type" value="minutes" class="hidden"
                                    {{ old('goal_type') == 'minutes' ? 'checked' : '' }}>
                                <div class="text-4xl mb-3 text-indigo-500">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <h3 class="font-bold text-gray-900 mb-2">Duration</h3>
                                <p class="text-sm text-gray-600">Spend time on it</p>
                            </label>
                        </div>
                        @error('goal_type')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Reminder Time -->
                    <div class="mb-10">
                        <label class="block text-sm font-semibold text-gray-700 mb-4">
                            <i class="fas fa-bell mr-2 text-yellow-500"></i>
                            Daily Reminder (Optional)
                        </label>
                        <div class="flex items-center space-x-4">
                            <div class="flex-1">
                                <input type="time" 
                                    name="reminder_time" 
                                    value="{{ old('reminder_time') }}"
                                    class="w-full px-6 py-4 border-2 border-gray-200 rounded-xl focus:border-yellow-500 focus:ring-2 focus:ring-yellow-200 transition text-lg"
                                    placeholder="HH:MM">
                            </div>
                            <div class="text-gray-600 text-sm">
                                <i class="fas fa-info-circle mr-1"></i>
                                We'll remind you daily
                            </div>
                        </div>
                        @error('reminder_time')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Form Actions -->
                    <div class="flex flex-col sm:flex-row justify-between items-center pt-8 border-t border-gray-200 space-y-4 sm:space-y-0">
                        <div class="text-gray-600 text-sm">
                            <i class="fas fa-lightbulb mr-1"></i>
                            Small habits create big changes
                        </div>
                        <div class="flex space-x-4">
                            <a href="{{ route('habits.index') }}" 
                            class="px-8 py-3 border-2 border-gray-300 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 transition">
                                Cancel
                            </a>
                            <button type="submit" 
                                    class="px-8 py-3 btn-glow font-semibold rounded-xl pulse-animation flex items-center">
                                <i class="fas fa-magic mr-2"></i>
                                Create Habit
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            
            <!-- Motivation quote -->
            <div class="mt-6 text-center text-gray-500 text-sm">
                <i class="fas fa-quote-left mr-1"></i>
                "We are what we repeatedly do. Excellence, then, is not an act, but a habit."
                <i class="fas fa-quote-right ml-1"></i>
            </div>
        </div>
        
        <!-- Interactive JavaScript -->
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Frequency selection styling
                const frequencyOptions = document.querySelectorAll('.frequency-option');
                frequencyOptions.forEach(option => {
                    const radio = option.querySelector('input[type="radio"]');
                    
                    // Set initial active state
                    if(radio.checked) {
                        option.classList.add('active');
                    }
                    
                    // Click handler
                    option.addEventListener('click', function() {
                        frequencyOptions.forEach(opt => opt.classList.remove('active'));
                        this.classList.add('active');
                        radio.checked = true;
                    });
                });
                
                // Goal type selection styling
                const goalCards = document.querySelectorAll('.goal-card');
                goalCards.forEach(card => {
                    const radio = card.querySelector('input[type="radio"]');
                    
                    // Set initial active state
                    if(radio.checked) {
                        card.classList.add('active');
                    }
                    
                    // Click handler
                    card.addEventListener('click', function() {
                        goalCards.forEach(c => c.classList.remove('active'));
                        this.classList.add('active');
                        radio.checked = true;
                    });
                });
                
                // Form validation
                const form = document.getElementById('habitForm');
                form.addEventListener('submit', function(e) {
                    const title = form.querySelector('input[name="title"]').value.trim();
                    const frequency = form.querySelector('input[name="frequency"]:checked');
                    const goalType = form.querySelector('input[name="goal_type"]:checked');
                    
                    if(!title) {
                        e.preventDefault();
                        alert('Please enter a habit name');
                        return;
                    }
                    
                    if(!frequency) {
                        e.preventDefault();
                        alert('Please select a frequency');
                        return;
                    }
                    
                    if(!goalType) {
                        e.preventDefault();
                        alert('Please select a goal type');
                        return;
                    }
                });
                
                // Time input formatting
                const timeInput = document.querySelector('input[type="time"]');
                if(timeInput) {
                    timeInput.addEventListener('change', function(e) {
                        const timePattern = /^([01]?[0-9]|2[0-3]):[0-5][0-9]$/;
                        if(e.target.value && !timePattern.test(e.target.value)) {
                            e.target.classList.add('border-red-500');
                            alert('Please enter time in 24-hour format (e.g., 14:30)');
                            e.target.focus();
                        } else {
                            e.target.classList.remove('border-red-500');
                        }
                    });
                }
            });
        </script>
    </body>
    </html>

@endsection