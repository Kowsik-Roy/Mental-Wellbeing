<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Progress - {{ $habit->title }} - Mental Wellbeing</title>
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
                    <a href="{{ route('habits.index') }}" class="inline-flex items-center text-gray-600 hover:text-gray-900">
                        <i class="fas fa-arrow-left mr-2"></i> Back to Habits
                    </a>
                </div>
            </div>

            <!-- Page Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">{{ $habit->title }}</h1>
                <p class="text-gray-600 mt-2">Progress & Analytics</p>
            </div>

            <!-- Progress Overview Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Weekly Progress -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-medium text-gray-500">This Week</h3>
                        <i class="fas fa-calendar-week text-blue-500"></i>
                    </div>
                    <div class="text-3xl font-bold text-gray-900 mb-2">{{ number_format($weeklyPercentage, 1) }}%</div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" style="width: {{ min($weeklyPercentage, 100) }}%"></div>
                    </div>
                </div>

                <!-- Monthly Progress -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-medium text-gray-500">This Month</h3>
                        <i class="fas fa-calendar-alt text-green-500"></i>
                    </div>
                    <div class="text-3xl font-bold text-gray-900 mb-2">{{ number_format($monthlyPercentage, 1) }}%</div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-green-600 h-2 rounded-full transition-all duration-300" style="width: {{ min($monthlyPercentage, 100) }}%"></div>
                    </div>
                </div>

                <!-- All-Time Progress -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-medium text-gray-500">All-Time</h3>
                        <i class="fas fa-chart-line text-purple-500"></i>
                    </div>
                    <div class="text-3xl font-bold text-gray-900 mb-2">{{ number_format($allTimePercentage, 1) }}%</div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-purple-600 h-2 rounded-full transition-all duration-300" style="width: {{ min($allTimePercentage, 100) }}%"></div>
                    </div>
                </div>

                <!-- Consistency Score -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-medium text-gray-500">Consistency</h3>
                        <i class="fas fa-fire text-orange-500"></i>
                    </div>
                    <div class="text-3xl font-bold text-gray-900 mb-2">{{ number_format($consistencyScore, 1) }}%</div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-orange-600 h-2 rounded-full transition-all duration-300" style="width: {{ min($consistencyScore, 100) }}%"></div>
                    </div>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <!-- Streaks -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Streaks</h3>
                    <div class="space-y-4">
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Current Streak</p>
                            <p class="text-2xl font-bold text-gray-900">
                                <i class="fas fa-fire text-orange-500 mr-2"></i>
                                {{ $habit->current_streak }} days
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Best Streak</p>
                            <p class="text-2xl font-bold text-gray-900">
                                <i class="fas fa-trophy text-yellow-500 mr-2"></i>
                                {{ $habit->best_streak }} days
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Overall Stats -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Overall Stats</h3>
                    <div class="space-y-4">
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Total Completions</p>
                            <p class="text-2xl font-bold text-gray-900">
                                <i class="fas fa-check-circle text-green-500 mr-2"></i>
                                {{ $totalCompletions }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Days Tracked</p>
                            <p class="text-2xl font-bold text-gray-900">
                                <i class="fas fa-calendar text-blue-500 mr-2"></i>
                                {{ $totalDays }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Completion Rate -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Completion Rate</h3>
                    <div class="space-y-4">
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Average</p>
                            <p class="text-2xl font-bold text-gray-900">
                                <i class="fas fa-percentage text-purple-500 mr-2"></i>
                                {{ number_format($completionRate, 1) }}%
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Frequency</p>
                            <p class="text-lg font-semibold text-gray-700">
                                <i class="fas fa-sync text-indigo-500 mr-2"></i>
                                {{ ucfirst($habit->frequency) }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Activity</h3>
                @if($recentLogs->isEmpty())
                    <p class="text-gray-500 text-center py-8">No activity recorded yet.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    @if($habit->goal_type !== 'boolean')
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Value</th>
                                    @endif
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Notes</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($recentLogs as $log)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $log->logged_date->format('M d, Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($log->completed)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    <i class="fas fa-check-circle mr-1"></i> Completed
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                    <i class="fas fa-times-circle mr-1"></i> Not Completed
                                                </span>
                                            @endif
                                        </td>
                                        @if($habit->goal_type !== 'boolean')
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $log->value_achieved ?? '-' }}
                                            </td>
                                        @endif
                                        <td class="px-6 py-4 text-sm text-gray-500">
                                            {{ $log->notes ?? '-' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</body>
</html>
