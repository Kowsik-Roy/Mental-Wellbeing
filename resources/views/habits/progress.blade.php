@extends('layouts.app')

@section('content')
<!-- Back Button -->
<div class="mb-6 px-4 md:px-8 pt-4">
    <div class="max-w-6xl mx-auto">
        <a href="{{ route('habits.index') }}" 
           class="inline-flex items-center gap-2 px-4 py-2 rounded-full font-medium bg-gradient-to-r from-purple-400 to-indigo-500 shadow-lg text-white hover:scale-105 hover:from-purple-300 hover:to-indigo-400 transition transform">
            <i class="fas fa-arrow-left"></i>
            <span>Back to Habits</span>
        </a>
    </div>
</div>

<div class="min-h-screen p-4 md:p-8 bg-gradient-to-br from-gray-100 to-blue-100">
    <div class="max-w-6xl mx-auto">

        <!-- Header -->
        <div class="glass-card shadow-2xl p-8 mb-8">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $habit->title }}</h1>
                    @if($habit->description)
                        <p class="text-gray-600">{{ $habit->description }}</p>
                    @endif
                    <div class="flex items-center space-x-4 mt-4">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                            <i class="fas fa-calendar-alt mr-1"></i>
                            {{ ucfirst($habit->frequency) }}
                        </span>

                    </div>
                </div>
                <div class="mt-4 md:mt-0">
                    <a href="{{ route('habits.edit', $habit) }}" 
                       class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition">
                        <i class="fas fa-edit mr-2"></i> Edit Habit
                    </a>
                </div>
            </div>
        </div>

        <!-- Stats Overview -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="stat-card glass-card p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-gradient-to-r from-blue-400 to-blue-600 rounded-xl flex items-center justify-center mr-4">
                        <i class="fas fa-fire text-white text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Current Streak</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $currentStreak }} days</p>
                    </div>
                </div>
            </div>

            <div class="stat-card glass-card p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-gradient-to-r from-yellow-400 to-yellow-600 rounded-xl flex items-center justify-center mr-4">
                        <i class="fas fa-trophy text-white text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Best Streak</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $bestStreak }} days</p>
                    </div>
                </div>
            </div>

            <div class="stat-card glass-card p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-gradient-to-r from-green-400 to-green-600 rounded-xl flex items-center justify-center mr-4">
                        <i class="fas fa-check-circle text-white text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Total Completions</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $totalCompletions }}</p>
                    </div>
                </div>
            </div>

            <div class="stat-card glass-card p-6">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-gradient-to-r from-purple-400 to-purple-600 rounded-xl flex items-center justify-center mr-4">
                        <i class="fas fa-chart-line text-white text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Completion Rate</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($completionRate, 1) }}%</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Recent Activity -->
            <div class="glass-card shadow-2xl p-8">
                <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                    <i class="fas fa-history text-blue-500 mr-3"></i>
                    Recent Activity (Last 30 Days)
                </h2>

                @if($recentLogs->count() > 0)
                    <div class="space-y-4 max-h-96 overflow-y-auto pr-2">
                        @foreach($recentLogs as $log)
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 rounded-full flex items-center justify-center 
                                        {{ $log->completed ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' }}">
                                        <i class="fas fa-{{ $log->completed ? 'check' : 'times' }}"></i>
                                    </div>
                                    <div class="ml-4">
                                        <p class="font-medium text-gray-900">{{ $log->logged_date->format('M d, Y') }}</p>
                                        @if($log->notes)
                                            <p class="text-sm text-gray-600">{{ Str::limit($log->notes, 50) }}</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="text-right">
                                    @if($log->value_achieved)
                                        <p class="text-sm font-medium text-gray-900">{{ $log->value_achieved }}</p>
                                        <p class="text-xs text-gray-500">
                                            @if($habit->goal_type == 'minutes') minutes @else times @endif
                                        </p>
                                    @else
                                        <span class="text-sm text-gray-500">{{ $log->completed ? 'Completed' : 'Not Completed' }}</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <div class="w-16 h-16 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-chart-bar text-gray-400 text-2xl"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No activity yet</h3>
                        <p class="text-gray-600">Start tracking this habit to see your progress!</p>
                    </div>
                @endif
            </div>

            <!-- Habit Details + Pie Chart -->
            <div class="glass-card shadow-2xl p-8">
                <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                    <i class="fas fa-info-circle text-green-500 mr-3"></i>
                    Habit Details
                </h2>

                <!-- Progress Circle -->
                <div class="text-center mb-8">
                    <div class="relative inline-block">
                        <svg class="w-40 h-40 progress-ring" viewBox="0 0 100 100">
                            <circle class="text-gray-200" stroke-width="10" stroke="currentColor" fill="transparent" r="45" cx="50" cy="50" />
                            <circle class="text-green-500 progress-ring-circle" stroke-width="10" stroke-linecap="round" stroke="currentColor" fill="transparent" r="45" cx="50" cy="50" 
                                    style="stroke-dashoffset: {{ 283 - (283 * min($completionRate, 100) / 100) }};" />
                        </svg>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <div class="text-center">
                                <p class="text-3xl font-bold text-gray-900">{{ number_format($completionRate, 1) }}%</p>
                                <p class="text-sm text-gray-600">Success Rate</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Habit Info -->
                <div class="space-y-4">
                    <div class="flex justify-between items-center p-4 bg-gray-50 rounded-xl">
                        <div class="flex items-center">
                            <i class="fas fa-calendar-day text-blue-500 text-lg mr-3"></i>
                            <span class="text-gray-700">Created</span>
                        </div>
                        <span class="font-medium text-gray-900">{{ $habit->created_at->format('M d, Y') }}</span>
                    </div>

                    <div class="flex justify-between items-center p-4 bg-gray-50 rounded-xl">
                        <div class="flex items-center">
                            <i class="fas fa-history text-purple-500 text-lg mr-3"></i>
                            <span class="text-gray-700">Tracking Duration</span>
                        </div>
                        <span class="font-medium text-gray-900">{{ $totalDays }} days</span>
                    </div>

                    @if($habit->reminder_time && !empty(trim($habit->reminder_time)))
                        <div class="flex justify-between items-center p-4 bg-gray-50 rounded-xl">
                            <div class="flex items-center">
                                <i class="fas fa-bell text-yellow-500 text-lg mr-3"></i>
                                <span class="text-gray-700">Daily Reminder</span>
                            </div>
                            <span class="font-medium text-gray-900">
                                @php
                                    // Handle reminder_time - it might be a Carbon instance or a string
                                    $timeStr = is_string($habit->reminder_time) ? $habit->reminder_time : $habit->reminder_time->format('H:i');
                                    if (!empty(trim($timeStr))) {
                                        $time = \Carbon\Carbon::createFromFormat('H:i', $timeStr);
                                        echo $time->format('g:i A');
                                    }
                                @endphp
                            </span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.glass-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border-radius: 20px;
    border: 1px solid rgba(255, 255, 255, 0.2);
}
.stat-card {
    transition: all 0.3s ease;
    cursor: pointer;
}
.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
}
.progress-ring {
    transform: rotate(-90deg);
}
.progress-ring-circle {
    stroke-dasharray: 283;
    stroke-dashoffset: 283;
    transition: stroke-dashoffset 1s ease;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const progressCircle = document.querySelector('.progress-ring-circle');
    if(progressCircle) {
        progressCircle.style.transition = 'stroke-dashoffset 2s ease-in-out';
    }

    const statCards = document.querySelectorAll('.stat-card');
    statCards.forEach(card => {
        card.addEventListener('mouseenter', () => card.style.transform = 'translateY(-5px)');
        card.addEventListener('mouseleave', () => card.style.transform = 'translateY(0)');
    });
});
</script>
@endpush
