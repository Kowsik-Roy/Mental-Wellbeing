
@extends('layouts.app')

@section('content')

    <!DOCTYPE html>
    <html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>My Habits - Mental Wellbeing</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
        <script src="https://cdn.tailwindcss.com"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    </head>
    <body class="bg-gray-50 min-h-screen">
        <div class="py-8">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Navigation -->
                <div class="mb-6 flex justify-between items-center">
                    <div>
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center text-gray-600 hover:text-gray-900">
                            <i class="fas fa-arrow-left mr-2"></i> Back to Dashboard
                        </a>
                    </div>
                    <div>
                        <a href="{{ route('habits.create') }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                            <i class="fas fa-plus mr-2"></i> New Habit
                        </a>
                    </div>
                </div>

                <!-- Page Header -->
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-900">My Habits</h1>
                    <p class="text-gray-600 mt-2">Track your daily wellness routines</p>
                </div>

                <!-- Success Message -->
                @if (session('success'))
                    <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-check-circle text-green-400"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Consistency Overview -->
                @if($habits->isNotEmpty())
                    <div class="mb-8 bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">
                            <i class="fas fa-fire text-orange-500 mr-2"></i>
                            Consistency Overview
                        </h2>
                        <p class="text-sm text-gray-600 mb-4">See which habits you're maintaining most consistently over time.</p>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($habits->sortByDesc(function($habit) { return $habit->getConsistencyScore(); })->take(6) as $habit)
                                <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition">
                                    <div class="flex items-center justify-between mb-2">
                                        <h3 class="font-medium text-gray-900 text-sm">{{ $habit->title }}</h3>
                                        <span class="text-xs font-semibold {{ $habit->getConsistencyScore() >= 70 ? 'text-green-600' : ($habit->getConsistencyScore() >= 40 ? 'text-yellow-600' : 'text-red-600') }}">
                                            {{ number_format($habit->getConsistencyScore(), 0) }}%
                                        </span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="h-2 rounded-full transition-all {{ $habit->getConsistencyScore() >= 70 ? 'bg-green-600' : ($habit->getConsistencyScore() >= 40 ? 'bg-yellow-600' : 'bg-red-600') }}" 
                                            style="width: {{ min($habit->getConsistencyScore(), 100) }}%"></div>
                                    </div>
                                    <div class="flex items-center justify-between mt-2 text-xs text-gray-500">
                                        <span><i class="fas fa-fire mr-1"></i>{{ $habit->current_streak }} day streak</span>
                                        <a href="{{ route('habits.progress', $habit) }}" class="text-blue-600 hover:text-blue-800">
                                            View Details <i class="fas fa-arrow-right ml-1"></i>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Habits List -->
                @if($habits->isEmpty())
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
                        <div class="mx-auto w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                            <i class="fas fa-tasks text-gray-400 text-2xl"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No habits yet</h3>
                        <p class="text-gray-600 mb-6">Start building your wellness routine by creating your first habit.</p>
                        <a href="{{ route('habits.create') }}" class="inline-flex items-center px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700">
                            <i class="fas fa-plus mr-2"></i> Create Your First Habit
                        </a>
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($habits as $habit)
                            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                                <!-- Habit Header -->
                                <div class="px-6 py-4 border-b border-gray-200">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h3 class="text-lg font-medium text-gray-900">{{ $habit->title }}</h3>
                                            @if($habit->description)
                                                <p class="text-sm text-gray-600 mt-1">{{ $habit->description }}</p>
                                            @endif
                                        </div>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ ucfirst($habit->frequency) }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Habit Stats -->
                                <div class="mb-8 grid grid-cols-1 md:grid-cols-4 gap-4">
                                    <div class="bg-gradient-to-r from-blue-50 to-blue-100 p-4 rounded-lg">
                                        <p class="text-sm text-blue-600">Total Habits</p>
                                        <p class="text-2xl font-bold text-blue-900">{{ $habits->count() }}</p>
                                    </div>
                                    <div class="bg-gradient-to-r from-green-50 to-green-100 p-4 rounded-lg">
                                        <p class="text-sm text-green-600">Active Streak</p>
                                        <p class="text-2xl font-bold text-green-900">{{ $habits->max('current_streak') ?? 0 }} days</p>
                                    </div>
                                    <!-- Add more stat cards as needed -->
                                </div>

                                    <!-- Progress Indicators -->
                                    <div class="mb-4 space-y-3">
                                        <!-- Weekly Progress -->
                                        <div>
                                            <div class="flex justify-between text-xs text-gray-600 mb-1">
                                                <span>This Week</span>
                                                <span>{{ number_format($habit->getWeeklyCompletionPercentage(), 0) }}%</span>
                                            </div>
                                            <div class="w-full bg-gray-200 rounded-full h-1.5">
                                                <div class="bg-blue-600 h-1.5 rounded-full transition-all" style="width: {{ min($habit->getWeeklyCompletionPercentage(), 100) }}%"></div>
                                            </div>
                                        </div>
                                        
                                        <!-- Monthly Progress -->
                                        <div>
                                            <div class="flex justify-between text-xs text-gray-600 mb-1">
                                                <span>This Month</span>
                                                <span>{{ number_format($habit->getMonthlyCompletionPercentage(), 0) }}%</span>
                                            </div>
                                            <div class="w-full bg-gray-200 rounded-full h-1.5">
                                                <div class="bg-green-600 h-1.5 rounded-full transition-all" style="width: {{ min($habit->getMonthlyCompletionPercentage(), 100) }}%"></div>
                                            </div>
                                        </div>

                                        <!-- Consistency Score -->
                                        <div>
                                            <div class="flex justify-between text-xs text-gray-600 mb-1">
                                                <span>Consistency</span>
                                                <span>{{ number_format($habit->getConsistencyScore(), 0) }}%</span>
                                            </div>
                                            <div class="w-full bg-gray-200 rounded-full h-1.5">
                                                <div class="bg-orange-600 h-1.5 rounded-full transition-all" style="width: {{ min($habit->getConsistencyScore(), 100) }}%"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Today's Log -->
                                    <div class="mb-4">
                                        @if($habit->todaysLog && $habit->todaysLog->completed)
                                            <div class="inline-flex items-center text-green-600">
                                                <i class="fas fa-check-circle mr-2"></i>
                                                <span>Completed today</span>
                                            </div>
                                        @else
                                            <form method="POST" action="{{ route('habits.log', $habit) }}" class="inline">
                                                @csrf
                                                <button type="submit" name="completed" value="1" 
                                                        class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                                                    <i class="fas fa-check mr-2"></i> Mark Complete
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                                    <div class="flex justify-between items-center mb-2">
                                        <a href="{{ route('habits.progress', $habit) }}" class="text-purple-600 hover:text-purple-800 text-sm font-medium">
                                            <i class="fas fa-chart-line mr-1"></i> View Progress
                                        </a>
                                        <div class="flex space-x-3">
                                            <a href="{{ route('habits.edit', $habit) }}" class="text-blue-600 hover:text-blue-800 text-sm">
                                                <i class="fas fa-edit mr-1"></i> Edit
                                            </a>
                                            <form method="POST" action="{{ route('habits.destroy', $habit) }}" onsubmit="return confirm('Delete this habit?')" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-800 text-sm">
                                                    <i class="fas fa-trash mr-1"></i> Delete
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </body>
    </html>

@endsection