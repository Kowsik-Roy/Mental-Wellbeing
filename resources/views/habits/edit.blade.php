<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Habit - Mental Wellbeing</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <!-- Navigation -->
            <div class="mb-6">
                <a href="{{ route('habits.index') }}" class="inline-flex items-center text-gray-600 hover:text-gray-900">
                    <i class="fas fa-arrow-left mr-2"></i> Back to Habits
                </a>
            </div>

            <!-- Page Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Edit Habit</h1>
                <p class="text-gray-600 mt-2">Update your wellness routine</p>
            </div>

            <!-- Form Card -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-medium text-gray-900">Edit Habit Details</h2>
                </div>

                <div class="p-6">
                    <form method="POST" action="{{ route('habits.update', $habit) }}">
                        @csrf
                        @method('PUT')

                        <!-- Title -->
                        <div class="mb-6">
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                                Habit Name *
                            </label>
                            <input type="text" 
                                   id="title" 
                                   name="title" 
                                   value="{{ old('title', $habit->title) }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition"
                                   required>
                            @error('title')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="mb-6">
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                                Description (Optional)
                            </label>
                            <textarea id="description" 
                                      name="description" 
                                      rows="3"
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition">{{ old('description', $habit->description) }}</textarea>
                            @error('description')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Frequency -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Frequency *
                            </label>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                <label class="flex items-center">
                                    <input type="radio" name="frequency" value="daily" 
                                           {{ old('frequency', $habit->frequency) == 'daily' ? 'checked' : '' }}
                                           class="mr-2 text-green-600 focus:ring-green-500">
                                    <span>Daily</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="frequency" value="weekly"
                                           {{ old('frequency', $habit->frequency) == 'weekly' ? 'checked' : '' }}
                                           class="mr-2 text-green-600 focus:ring-green-500">
                                    <span>Weekly</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="frequency" value="weekdays"
                                           {{ old('frequency', $habit->frequency) == 'weekdays' ? 'checked' : '' }}
                                           class="mr-2 text-green-600 focus:ring-green-500">
                                    <span>Weekdays</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="frequency" value="custom"
                                           {{ old('frequency', $habit->frequency) == 'custom' ? 'checked' : '' }}
                                           class="mr-2 text-green-600 focus:ring-green-500">
                                    <span>Custom</span>
                                </label>
                            </div>
                            @error('frequency')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Goal Type -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Goal Type *
                            </label>
                            <div class="grid grid-cols-3 gap-3">
                                <label class="flex items-center p-3 border border-gray-300 rounded-lg hover:bg-gray-50">
                                    <input type="radio" name="goal_type" value="boolean"
                                           {{ old('goal_type', $habit->goal_type) == 'boolean' ? 'checked' : '' }}
                                           class="mr-2 text-green-600 focus:ring-green-500">
                                    <div>
                                        <div class="font-medium">Yes/No</div>
                                        <div class="text-sm text-gray-500">Just complete it</div>
                                    </div>
                                </label>
                                <label class="flex items-center p-3 border border-gray-300 rounded-lg hover:bg-gray-50">
                                    <input type="radio" name="goal_type" value="times"
                                           {{ old('goal_type', $habit->goal_type) == 'times' ? 'checked' : '' }}
                                           class="mr-2 text-green-600 focus:ring-green-500">
                                    <div>
                                        <div class="font-medium">Times</div>
                                        <div class="text-sm text-gray-500">X times per day</div>
                                    </div>
                                </label>
                                <label class="flex items-center p-3 border border-gray-300 rounded-lg hover:bg-gray-50">
                                    <input type="radio" name="goal_type" value="minutes"
                                           {{ old('goal_type', $habit->goal_type) == 'minutes' ? 'checked' : '' }}
                                           class="mr-2 text-green-600 focus:ring-green-500">
                                    <div>
                                        <div class="font-medium">Minutes</div>
                                        <div class="text-sm text-gray-500">X minutes per day</div>
                                    </div>
                                </label>
                            </div>
                            @error('goal_type')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Target Value -->
                        <div class="mb-6">
                            <label for="target_value" class="block text-sm font-medium text-gray-700 mb-2">
                                Target Value *
                            </label>
                            <input type="number" 
                                   id="target_value" 
                                   name="target_value" 
                                   value="{{ old('target_value', $habit->target_value) }}"
                                   min="1"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition"
                                   required>
                            @error('target_value')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Reminder Time -->
                        <div class="mb-6">
                            <label for="reminder_time" class="block text-sm font-medium text-gray-700 mb-2">
                                Reminder Time (Optional)
                            </label>
                            <input type="time" 
                                   id="reminder_time" 
                                   name="reminder_time" 
                                   value="{{ old('reminder_time', $habit->reminder_time) }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition">
                            @error('reminder_time')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Active Status -->
                        <div class="mb-8">
                            <label class="flex items-center">
                                <input type="checkbox" 
                                       name="is_active" 
                                       value="1"
                                       {{ old('is_active', $habit->is_active) ? 'checked' : '' }}
                                       class="mr-2 text-green-600 focus:ring-green-500">
                                <span class="text-sm text-gray-700">This habit is active</span>
                            </label>
                            <p class="mt-2 text-sm text-gray-500">
                                Inactive habits won't appear in your daily list
                            </p>
                        </div>

                        <!-- Form Actions -->
                        <div class="flex justify-between items-center pt-6 border-t border-gray-200">
                            <form method="POST" action="{{ route('habits.destroy', $habit) }}" onsubmit="return confirm('Delete this habit?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800">
                                    <i class="fas fa-trash mr-1"></i> Delete Habit
                                </button>
                            </form>
                            <div class="flex space-x-3">
                                <a href="{{ route('habits.index') }}" 
                                   class="px-6 py-3 border border-gray-300 rounded-lg font-medium text-gray-700 hover:bg-gray-50 transition">
                                    Cancel
                                </a>
                                <button type="submit" 
                                        class="px-6 py-3 bg-green-600 border border-transparent rounded-lg font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition">
                                    <i class="fas fa-save mr-2"></i> Update Habit
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>