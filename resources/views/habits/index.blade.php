@extends('layouts.app')

@section('content')
<div class="py-8">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
            </div>
        @endif

        <!-- Action Buttons -->
        <div class="mb-6 flex items-center justify-end">
            <a href="{{ route('habits.create') }}" 
               class="inline-flex items-center gap-2 px-4 py-2 rounded-full font-medium bg-gradient-to-r from-green-400 to-emerald-500 shadow-lg text-white hover:scale-105 hover:from-green-300 hover:to-emerald-400 transition transform text-sm">
                <i class="fas fa-plus"></i>
                <span>New Habit</span>
            </a>
        </div>

        <!-- Page Header -->
        <div class="mb-8 flex items-center justify-between">
            <h1 class="text-3xl font-bold text-gray-900">My Habits</h1>

            @if(auth()->check())
                <form action="{{ route('calendar.toggle') }}" method="POST" class="inline-flex items-center gap-3">
                    @csrf
                    <div class="flex items-center gap-2 px-3 py-1.5 rounded-full border {{ auth()->user()->calendar_sync_enabled ? 'border-green-400 bg-green-50' : 'border-gray-300 bg-white' }}">
                        <i class="fas fa-calendar-alt {{ auth()->user()->calendar_sync_enabled ? 'text-green-600' : 'text-gray-500' }}"></i>
                        <span class="text-xs font-medium {{ auth()->user()->calendar_sync_enabled ? 'text-green-700' : 'text-gray-600' }}">
                            {{ auth()->user()->calendar_sync_enabled ? 'Calendar Sync: On' : 'Calendar Sync: Off' }}
                        </span>
                    </div>
                    <button type="submit" class="text-xs px-3 py-1 rounded-full border border-indigo-500 text-indigo-600 hover:bg-indigo-50">
                        {{ auth()->user()->calendar_sync_enabled ? 'Disable' : 'Enable' }}
                    </button>
                </form>
            @endif
        </div>

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
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden flex flex-col justify-between">
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

                        <!-- Progress Indicators -->
                        <div class="px-6 py-4 space-y-3">
                            <!-- Weekly -->
                            <div>
                                <div class="flex justify-between text-xs text-gray-600 mb-1">
                                    <span>This Week</span>
                                    <span>{{ number_format($habit->getWeeklyCompletionPercentage(), 0) }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-1.5">
                                    <div class="bg-blue-600 h-1.5 rounded-full transition-all" style="width: {{ min($habit->getWeeklyCompletionPercentage(), 100) }}%"></div>
                                </div>
                            </div>
                            <!-- Monthly -->
                            <div>
                                <div class="flex justify-between text-xs text-gray-600 mb-1">
                                    <span>This Month</span>
                                    <span>{{ number_format($habit->getMonthlyCompletionPercentage(), 0) }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-1.5">
                                    <div class="bg-green-600 h-1.5 rounded-full transition-all" style="width: {{ min($habit->getMonthlyCompletionPercentage(), 100) }}%"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Streak & Completion -->
                        <div class="px-6 py-2 flex justify-between items-center text-sm text-gray-600 border-t border-gray-200">
                            <span><i class="fas fa-fire mr-1 text-orange-500"></i>{{ $habit->current_streak }} day streak</span>
                            @if($habit->todaysLog && $habit->todaysLog->completed)
                                <span class="text-green-600 flex items-center"><i class="fas fa-check-circle mr-1"></i>Completed today</span>
                            @elseif($habit->isActiveToday())
                                <form method="POST" action="{{ route('habits.log', $habit) }}" class="inline">
                                    @csrf
                                    <button type="submit" name="completed" value="1" class="inline-flex items-center px-3 py-1 bg-green-600 text-white rounded-lg hover:bg-green-700">
                                        <i class="fas fa-check mr-1"></i> Mark Complete
                                    </button>
                                </form>
                            @else
                                <span class="text-gray-500 flex items-center">
                                    <i class="fas fa-calendar-times mr-1"></i>
                                    @if($habit->frequency === 'weekdays')
                                        Available Mon-Fri only
                                    @elseif($habit->frequency === 'weekend')
                                        Available Sat-Sun only
                                    @else
                                        Not available today
                                    @endif
                                </span>
                            @endif
                        </div>

                        <!-- Action Buttons & Calendar Sync -->
                        <div class="px-6 py-4 bg-gray-50 border-top border-gray-200">
                            <div class="flex items-center justify-between flex-wrap gap-3">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('habits.progress', $habit) }}" class="px-3 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 text-sm flex items-center">
                                        <i class="fas fa-chart-line mr-1"></i> Progress
                                    </a>
                                    <a href="{{ route('habits.edit', $habit) }}" class="px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm flex items-center">
                                        <i class="fas fa-edit mr-1"></i> Edit
                                    </a>
                                    <form method="POST" action="{{ route('habits.destroy', $habit) }}" id="delete-habit-form-{{ $habit->id }}" class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" onclick="showConfirmModal('Delete Habit', 'Are you sure you want to delete this habit? This action cannot be undone.', function() { document.getElementById('delete-habit-form-{{ $habit->id }}').submit(); })" class="px-3 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 text-sm flex items-center">
                                            <i class="fas fa-trash mr-1"></i> Delete
                                        </button>
                                    </form>
                                </div>

                                @if(auth()->user()->calendar_sync_enabled ?? false)
                                    <div class="flex items-center gap-2 text-xs">
                                        @if(! $habit->reminder_time)
                                            <span class="text-gray-400 flex items-center" title="Set a reminder time to enable calendar sync.">
                                                <i class="fas fa-bell-slash mr-1"></i> No reminder set
                                            </span>
                                        @else
                                            @if($habit->google_event_id)
                                                <span class="text-green-600 flex items-center" title="This habit is synced with Google Calendar.">
                                                    <i class="fas fa-calendar-check mr-1"></i> Synced
                                                </span>
                                            @else
                                                <form method="POST" action="{{ route('habits.sync-calendar', $habit) }}" class="inline">
                                                    @csrf
                                                    <button type="submit" class="inline-flex items-center px-3 py-1 border border-indigo-500 text-indigo-600 rounded-full hover:bg-indigo-50">
                                                        <i class="fas fa-calendar-plus mr-1"></i> Sync to Calendar
                                                    </button>
                                                </form>
                                            @endif
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
