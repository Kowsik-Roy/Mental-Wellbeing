@extends('layouts.app')

@section('content')


<!DOCTYPE html>
    <html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Edit Habit - Wellness Companion</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        <script src="https://cdn.tailwindcss.com"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        
        <style>
            /* Same styles as create.blade.php */
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
        </style>
    </head>
    <body class="min-h-screen flex items-center justify-center p-4">
        <div class="max-w-2xl w-full">
            <!-- Back button -->
            <div class="mb-6">
                <a href="{{ route('habits.index') }}" 
                class="inline-flex items-center text-gray-700 hover:text-gray-900 font-medium transition">
                    <i class="fas fa-arrow-left mr-2"></i> Back to Habits
                </a>
            </div>
            
            <!-- Main card -->
            <div class="glass-card shadow-2xl p-8">
                <!-- Header -->
                <div class="text-center mb-8">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-r from-blue-400 to-purple-500 rounded-full mb-4">
                        <i class="fas fa-edit text-white text-2xl"></i>
                    </div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Edit Habit</h1>
                    <p class="text-gray-600">Update your wellness routine</p>
                </div>
                
                <!-- Form -->
                <form method="POST" action="{{ route('habits.update', $habit) }}" id="editHabitForm">
                    @csrf
                    @method('PUT')
                    
                    <!-- Title -->
                    <div class="mb-8">
                        <label class="block text-sm font-semibold text-gray-700 mb-3">
                            <i class="fas fa-heading mr-2 text-blue-500"></i>
                            Habit Name *
                        </label>
                        <input type="text" 
                            name="title" 
                            value="{{ old('title', $habit->title) }}"
                            class="w-full px-6 py-4 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition text-lg"
                            required>
                    </div>
                    
                    <!-- Description -->
                    <div class="mb-8">
                        <label class="block text-sm font-semibold text-gray-700 mb-3">
                            <i class="fas fa-align-left mr-2 text-green-500"></i>
                            Why is this important?
                        </label>
                        <textarea name="description" 
                                rows="3"
                                class="w-full px-6 py-4 border-2 border-gray-200 rounded-xl focus:border-green-500 focus:ring-2 focus:ring-green-200 transition">{{ old('description', $habit->description) }}</textarea>
                    </div>
                    
                    <!-- Frequency -->
                    <div class="mb-10">
                        <label class="block text-sm font-semibold text-gray-700 mb-4">
                            <i class="fas fa-calendar-alt mr-2 text-purple-500"></i>
                            How often? *
                        </label>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <label class="frequency-option p-6 rounded-xl border-2 bg-white text-center cursor-pointer 
                                {{ (old('frequency', $habit->frequency) == 'daily') ? 'active' : '' }}">
                                <input type="radio" name="frequency" value="daily" class="hidden" 
                                    {{ old('frequency', $habit->frequency) == 'daily' ? 'checked' : '' }}>
                                <div class="text-4xl mb-3 text-blue-500">
                                    <i class="fas fa-sun"></i>
                                </div>
                                <h3 class="font-bold text-gray-900 mb-1">Daily</h3>
                                <p class="text-sm text-gray-600">Every single day</p>
                            </label>
                            
                            <label class="frequency-option p-6 rounded-xl border-2 bg-white text-center cursor-pointer 
                                {{ (old('frequency', $habit->frequency) == 'weekdays') ? 'active' : '' }}">
                                <input type="radio" name="frequency" value="weekdays" class="hidden"
                                    {{ old('frequency', $habit->frequency) == 'weekdays' ? 'checked' : '' }}>
                                <div class="text-4xl mb-3 text-green-500">
                                    <i class="fas fa-briefcase"></i>
                                </div>
                                <h3 class="font-bold text-gray-900 mb-1">Weekdays</h3>
                                <p class="text-sm text-gray-600">Mon - Fri only</p>
                            </label>
                            
                            <label class="frequency-option p-6 rounded-xl border-2 bg-white text-center cursor-pointer 
                                {{ (old('frequency', $habit->frequency) == 'weekend') ? 'active' : '' }}">
                                <input type="radio" name="frequency" value="weekend" class="hidden"
                                    {{ old('frequency', $habit->frequency) == 'weekend' ? 'checked' : '' }}>
                                <div class="text-4xl mb-3 text-yellow-500">
                                    <i class="fas fa-umbrella-beach"></i>
                                </div>
                                <h3 class="font-bold text-gray-900 mb-1">Weekend</h3>
                                <p class="text-sm text-gray-600">Sat & Sun only</p>
                            </label>
                        </div>
                    </div>
                    
                    <!-- Goal Type -->
                    <div class="mb-10">
                        <label class="block text-sm font-semibold text-gray-700 mb-4">
                            <i class="fas fa-bullseye mr-2 text-red-500"></i>
                            What's your goal? *
                        </label>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <label class="goal-card p-6 rounded-xl border-2 bg-white cursor-pointer 
                                {{ (old('goal_type', $habit->goal_type) == 'once') ? 'active' : '' }}">
                                <input type="radio" name="goal_type" value="once" class="hidden"
                                    {{ old('goal_type', $habit->goal_type) == 'once' ? 'checked' : '' }}>
                                <div class="text-4xl mb-3 text-red-500">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                                <h3 class="font-bold text-gray-900 mb-2">Just Once</h3>
                                <p class="text-sm text-gray-600">Complete it one time</p>
                            </label>
                            
                            <label class="goal-card p-6 rounded-xl border-2 bg-white cursor-pointer 
                                {{ (old('goal_type', $habit->goal_type) == 'multiple_times') ? 'active' : '' }}">
                                <input type="radio" name="goal_type" value="multiple_times" class="hidden"
                                    {{ old('goal_type', $habit->goal_type) == 'multiple_times' ? 'checked' : '' }}>
                                <div class="text-4xl mb-3 text-orange-500">
                                    <i class="fas fa-redo"></i>
                                </div>
                                <h3 class="font-bold text-gray-900 mb-2">Multiple Times</h3>
                                <p class="text-sm text-gray-600">Repeat throughout the day</p>
                            </label>
                            
                            <label class="goal-card p-6 rounded-xl border-2 bg-white cursor-pointer 
                                {{ (old('goal_type', $habit->goal_type) == 'minutes') ? 'active' : '' }}">
                                <input type="radio" name="goal_type" value="minutes" class="hidden"
                                    {{ old('goal_type', $habit->goal_type) == 'minutes' ? 'checked' : '' }}>
                                <div class="text-4xl mb-3 text-indigo-500">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <h3 class="font-bold text-gray-900 mb-2">Duration</h3>
                                <p class="text-sm text-gray-600">Spend time on it</p>
                            </label>
                        </div>
                    </div>
                    
                    <!-- Reminder Time -->
                    <div class="mb-10">
                        <label class="block text-sm font-semibold text-gray-700 mb-4">
                            <i class="fas fa-bell mr-2 text-yellow-500"></i>
                            Daily Reminder (Optional)
                        </label>
                        <input type="time" 
                            name="reminder_time" 
                            value="{{ old('reminder_time', $habit->reminder_time) }}"
                            class="w-full px-6 py-4 border-2 border-gray-200 rounded-xl focus:border-yellow-500 focus:ring-2 focus:ring-yellow-200 transition text-lg">
                    </div>
                    
                    <!-- Active Status -->
                    <div class="mb-10">
                        <label class="flex items-center cursor-pointer">
                            <div class="relative">
                                <input type="checkbox" 
                                    name="is_active" 
                                    value="1"
                                    {{ old('is_active', $habit->is_active) ? 'checked' : '' }}
                                    class="sr-only">
                                <div class="block bg-gray-300 w-12 h-6 rounded-full"></div>
                                <div class="dot absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition transform"></div>
                            </div>
                            <div class="ml-4 text-gray-700 font-medium">
                                Keep this habit active
                                <p class="text-sm text-gray-500 mt-1">Inactive habits won't appear in your daily list</p>
                            </div>
                        </label>
                    </div>
                    
                    <!-- Form Actions -->
                    <div class="flex flex-col sm:flex-row justify-between items-center pt-8 border-t border-gray-200 space-y-4 sm:space-y-0">
                        <form method="POST" action="{{ route('habits.destroy', $habit) }}" 
                            onsubmit="return confirm('Are you sure you want to delete this habit? All progress will be lost.')"
                            class="w-full sm:w-auto">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="w-full sm:w-auto px-8 py-3 border-2 border-red-300 text-red-600 font-semibold rounded-xl hover:bg-red-50 transition">
                                <i class="fas fa-trash mr-2"></i> Delete Habit
                            </button>
                        </form>
                        
                        <div class="flex space-x-4 w-full sm:w-auto">
                            <a href="{{ route('habits.index') }}" 
                            class="w-full sm:w-auto px-8 py-3 border-2 border-gray-300 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 transition text-center">
                                Cancel
                            </a>
                            <button type="submit" 
                                    class="w-full sm:w-auto px-8 py-3 btn-glow font-semibold rounded-xl flex items-center justify-center">
                                <i class="fas fa-save mr-2"></i>
                                Update Habit
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Interactive JavaScript -->
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Frequency selection
                const frequencyOptions = document.querySelectorAll('.frequency-option');
                frequencyOptions.forEach(option => {
                    option.addEventListener('click', function() {
                        frequencyOptions.forEach(opt => opt.classList.remove('active'));
                        this.classList.add('active');
                        this.querySelector('input[type="radio"]').checked = true;
                    });
                });
                
                // Goal type selection
                const goalCards = document.querySelectorAll('.goal-card');
                goalCards.forEach(card => {
                    card.addEventListener('click', function() {
                        goalCards.forEach(c => c.classList.remove('active'));
                        this.classList.add('active');
                        this.querySelector('input[type="radio"]').checked = true;
                    });
                });
                
                // Toggle switch styling
                const toggle = document.querySelector('input[name="is_active"]');
                const toggleDot = document.querySelector('.dot');
                const toggleBlock = document.querySelector('.block');
                
                if(toggle && toggle.checked) {
                    toggleBlock.classList.remove('bg-gray-300');
                    toggleBlock.classList.add('bg-green-500');
                    toggleDot.classList.remove('left-1');
                    toggleDot.classList.add('left-7');
                }
                
                toggle?.addEventListener('change', function() {
                    if(this.checked) {
                        toggleBlock.classList.remove('bg-gray-300');
                        toggleBlock.classList.add('bg-green-500');
                        toggleDot.classList.remove('left-1');
                        toggleDot.classList.add('left-7');
                    } else {
                        toggleBlock.classList.remove('bg-green-500');
                        toggleBlock.classList.add('bg-gray-300');
                        toggleDot.classList.remove('left-7');
                        toggleDot.classList.add('left-1');
                    }
                });
            });
        </script>
    </body>
    </html>

@endsection