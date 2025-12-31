@extends('layouts.app')

@section('content')

<div class="min-h-screen flex items-center justify-center p-4">
    <div class="max-w-2xl w-full relative">

        <!-- Decorative floating elements (NON-INTERACTIVE) -->
        <div class="absolute top-10 left-10 w-24 h-24 bg-gradient-to-r from-purple-200 to-pink-200 rounded-full opacity-20 floating-icon pointer-events-none"></div>
        <div class="absolute bottom-10 right-10 w-32 h-32 bg-gradient-to-r from-blue-200 to-green-200 rounded-full opacity-20 floating-icon pointer-events-none" style="animation-delay: 1s;"></div>

        <!-- Main card -->
        <div class="glass-card shadow-2xl p-8 relative z-10">

            <!-- Header -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-r from-green-400 to-blue-500 rounded-full mb-4">
                    <i class="fas fa-plus text-white text-2xl"></i>
                </div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Create New Habit</h1>
                <p class="text-gray-600">Build a consistent routine for better mental wellness</p>
            </div>

            <!-- Form -->
            <form method="POST" action="{{ route('habits.store') }}">
                @csrf

                <!-- Habit Name -->
                <div class="mb-8">
                    <label class="block text-sm font-semibold text-gray-700 mb-3">
                        <i class="fas fa-heading mr-2 text-blue-500"></i>
                        Habit Name *
                    </label>
                    <input
                        type="text"
                        name="title"
                        value="{{ old('title') }}"
                        required
                        class="w-full px-6 py-4 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition text-lg"
                        placeholder="Medition, Jogging, Eating, etc."
                    >
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
                    <textarea
                        name="description"
                        rows="3"
                        class="w-full px-6 py-4 border-2 border-gray-200 rounded-xl focus:border-green-500 focus:ring-2 focus:ring-green-200 transition"
                        placeholder="Describe why this habit matters to you..."
                    >{{ old('description') }}</textarea>
                </div>

                <!-- Frequency -->
                <div class="mb-10">
                    <label class="block text-sm font-semibold text-gray-700 mb-4">
                        <i class="fas fa-calendar-alt mr-2 text-purple-500"></i>
                        How often? *
                    </label>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        @foreach ([
                            ['daily', 'sun', 'Daily', 'Every single day', 'text-blue-500'],
                            ['weekdays', 'briefcase', 'Weekdays', 'Mon - Fri only', 'text-green-500'],
                            ['weekend', 'umbrella-beach', 'Weekend', 'Sat & Sun only', 'text-yellow-500']
                        ] as [$value, $icon, $title, $desc, $color])
                            <label class="cursor-pointer">
                                <input
                                    type="radio"
                                    name="frequency"
                                    value="{{ $value }}"
                                    class="peer hidden"
                                    {{ old('frequency', 'daily') === $value ? 'checked' : '' }}
                                >
                                <div
                                    class="p-6 rounded-xl border-2 bg-white text-center transition
                                           hover:border-gray-400
                                           peer-checked:border-blue-500
                                           peer-checked:ring-2 peer-checked:ring-blue-300
                                           peer-checked:scale-[1.02]"
                                >
                                    <div class="text-4xl mb-3 {{ $color }}">
                                        <i class="fas fa-{{ $icon }}"></i>
                                    </div>
                                    <h3 class="font-bold text-gray-900 mb-1">{{ $title }}</h3>
                                    <p class="text-sm text-gray-600">{{ $desc }}</p>
                                </div>
                            </label>
                        @endforeach
                    </div>

                    @error('frequency')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Reminder -->
                <div class="mb-10">
                    <label class="block text-sm font-semibold text-gray-700 mb-4">
                        <i class="fas fa-bell mr-2 text-yellow-500"></i>
                        Reminder Time (Optional)
                    </label>
                    <p class="text-sm text-gray-600 mb-3">
                        Set a reminder time. Notifications will be sent based on your selected frequency:
                        <span class="font-semibold">Daily</span> (every day), 
                        <span class="font-semibold">Weekdays</span> (Mon-Fri), or 
                        <span class="font-semibold">Weekend</span> (Sat-Sun).
                    </p>

                    <p class="text-xs text-gray-500 mb-4 flex items-start gap-2">
                        <i class="fas fa-info-circle text-gray-400 mt-0.5"></i>
                        <span>
                            If you don’t set a reminder time, you won’t receive notifications and your calendar won’t be synced.
                        </span>
                    </p>

                    <input
                        type="time"
                        name="reminder_time"
                        value="{{ old('reminder_time') }}"
                        class="w-full px-6 py-4 border-2 border-gray-200 rounded-xl focus:border-yellow-500 focus:ring-2 focus:ring-yellow-200 transition text-lg"
                    >
                </div>

                <!-- Actions -->
                <div class="flex justify-end space-x-4 pt-6 border-t">
                    <a
                        href="{{ route('habits.index') }}"
                        class="px-8 py-3 border-2 border-gray-300 text-gray-700 font-semibold rounded-xl hover:bg-gray-50"
                    >
                        Cancel
                    </a>
                    <button
                        type="submit"
                        class="px-8 py-3 border-2 border-gray-300 text-gray-700 font-semibold rounded-xl hover:bg-gray-50"">
                        <i class="fas fa-magic mr-2"></i>
                        Create Habit
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

@endsection
