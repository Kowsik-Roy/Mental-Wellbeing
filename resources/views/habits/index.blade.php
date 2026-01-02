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
                    <div class="relative rounded-3xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 bg-gradient-to-br from-white to-gray-50 border border-gray-200 group hover:border-indigo-300 hover:-translate-y-1 flex flex-col">
                        <!-- Decorative accent -->
                        <div class="absolute top-4 right-4 w-16 h-16 rounded-full bg-gradient-to-br from-indigo-100 to-purple-100 opacity-50 group-hover:opacity-70 transition-opacity duration-300"></div>
                        
                        <div class="relative z-10 flex flex-col flex-1">
                            <!-- Habit Header -->
                            <div class="mb-5">
                                <div class="flex justify-between items-start mb-3">
                                    <div class="flex-1">
                                        <h3 class="text-xl font-bold text-gray-900 mb-1 group-hover:text-indigo-700 transition-colors">{{ $habit->title }}</h3>
                                        @if($habit->description)
                                            <p class="text-sm text-gray-600 leading-relaxed">{{ \Illuminate\Support\Str::limit($habit->description, 60) }}</p>
                                        @endif
                                    </div>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gradient-to-r from-blue-100 to-indigo-100 text-indigo-700 border border-indigo-200 ml-2">
                                        {{ ucfirst($habit->frequency) }}
                                    </span>
                                </div>
                            </div>

                            <!-- Progress Indicators -->
                            <div class="mb-5 space-y-4">
                                <!-- Weekly -->
                                <div>
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="text-xs font-semibold text-gray-700">This Week</span>
                                        <span class="text-xs font-bold text-indigo-600">{{ number_format($habit->getWeeklyCompletionPercentage(), 0) }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2.5 overflow-hidden">
                                        <div class="bg-gradient-to-r from-blue-500 to-indigo-600 h-2.5 rounded-full transition-all duration-500 shadow-sm" style="width: {{ min($habit->getWeeklyCompletionPercentage(), 100) }}%"></div>
                                    </div>
                                </div>
                                <!-- Monthly -->
                                <div>
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="text-xs font-semibold text-gray-700">This Month</span>
                                        <span class="text-xs font-bold text-emerald-600">{{ number_format($habit->getMonthlyCompletionPercentage(), 0) }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2.5 overflow-hidden">
                                        <div class="bg-gradient-to-r from-emerald-500 to-green-600 h-2.5 rounded-full transition-all duration-500 shadow-sm" style="width: {{ min($habit->getMonthlyCompletionPercentage(), 100) }}%"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Streak & Completion -->
                            <div class="mb-5 p-4 bg-gradient-to-r from-orange-50 to-amber-50 rounded-2xl border border-orange-100">
                                <div class="flex justify-between items-center">
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-orange-400 to-red-500 flex items-center justify-center">
                                            <i class="fas fa-fire text-white text-xs"></i>
                                        </div>
                                        <div>
                                            <div class="text-xs text-gray-600">Current Streak</div>
                                            <div class="text-sm font-bold text-orange-600">{{ $habit->current_streak }} days</div>
                                        </div>
                                    </div>
                                    @if($habit->todaysLog && $habit->todaysLog->completed)
                                        <div class="flex items-center gap-1.5 px-3 py-1.5 bg-green-100 rounded-full border border-green-200">
                                            <i class="fas fa-check-circle text-green-600 text-sm"></i>
                                            <span class="text-xs font-semibold text-green-700">Done</span>
                                        </div>
                                    @elseif($habit->isActiveToday())
                                        <form method="POST" action="{{ route('habits.log', $habit) }}" class="inline">
                                            @csrf
                                            <button type="submit" name="completed" value="1" class="inline-flex items-center gap-1.5 px-4 py-2 bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-full text-xs font-semibold hover:from-green-600 hover:to-emerald-700 shadow-md hover:shadow-lg transition-all transform hover:scale-105">
                                                <i class="fas fa-check"></i>
                                                <span>Complete</span>
                                            </button>
                                        </form>
                                    @else
                                        <div class="flex items-center gap-1.5 px-3 py-1.5 bg-gray-100 rounded-full border border-gray-200">
                                            <i class="fas fa-calendar-times text-gray-400 text-xs"></i>
                                            <span class="text-xs text-gray-500">
                                                @if($habit->frequency === 'weekdays')
                                                    Mon-Fri
                                                @elseif($habit->frequency === 'weekend')
                                                    Weekend
                                                @else
                                                    Not today
                                                @endif
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Action Buttons & Calendar Sync -->
                            <div class="mt-auto pt-4 border-t border-gray-200">
                                <div class="flex flex-col gap-3">
                                    <!-- Action Buttons -->
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <a href="{{ route('habits.progress', $habit) }}" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-full font-medium bg-gradient-to-r from-purple-400 to-indigo-500 text-white text-xs shadow-md hover:shadow-lg hover:scale-105 hover:from-purple-500 hover:to-indigo-600 transition transform">
                                            <i class="fas fa-chart-line"></i>
                                            <span>Progress</span>
                                        </a>
                                        <a href="{{ route('habits.edit', $habit) }}" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-full font-medium bg-gradient-to-r from-blue-400 to-cyan-500 text-white text-xs shadow-md hover:shadow-lg hover:scale-105 hover:from-blue-500 hover:to-cyan-600 transition transform">
                                            <i class="fas fa-edit"></i>
                                            <span>Edit</span>
                                        </a>
                                        <form method="POST" action="{{ route('habits.destroy', $habit) }}" id="delete-habit-form-{{ $habit->id }}" class="inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" onclick="showConfirmModal('Delete Habit', 'Are you sure you want to delete this habit? This action cannot be undone.', function() { document.getElementById('delete-habit-form-{{ $habit->id }}').submit(); })" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-full font-medium bg-gradient-to-r from-red-500 to-rose-600 text-white text-xs shadow-md hover:shadow-lg hover:scale-105 hover:from-red-600 hover:to-rose-700 transition transform">
                                                <i class="fas fa-trash"></i>
                                                <span>Delete</span>
                                            </button>
                                        </form>
                                    </div>

                                    <!-- Calendar Sync -->
                                    @if(auth()->user()->calendar_sync_enabled ?? false)
                                        <div class="flex items-center justify-center">
                                            @if(! $habit->reminder_time)
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gray-100 rounded-full text-xs text-gray-500 border border-gray-200" title="Set a reminder time to enable calendar sync.">
                                                    <i class="fas fa-bell-slash"></i>
                                                    <span>No reminder</span>
                                                </span>
                                            @else
                                                @if($habit->google_event_id)
                                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-green-100 rounded-full text-xs font-semibold text-green-700 border border-green-200" title="This habit is synced with Google Calendar.">
                                                        <i class="fas fa-calendar-check"></i>
                                                        <span>Synced</span>
                                                    </span>
                                                @else
                                                    <form method="POST" action="{{ route('habits.sync-calendar', $habit) }}" class="inline">
                                                        @csrf
                                                        <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 border-2 border-indigo-400 text-indigo-600 rounded-full hover:bg-indigo-50 text-xs font-semibold transition">
                                                            <i class="fas fa-calendar-plus"></i>
                                                            <span>Sync Calendar</span>
                                                        </button>
                                                    </form>
                                                @endif
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
