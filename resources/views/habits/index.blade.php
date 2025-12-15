@extends('layouts.app')

@section('content')
<div class="py-8">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- New Habit Button -->
        <div class="mb-6 flex justify-end">
            <a href="{{ route('habits.create') }}" class="inline-flex items-center px-4 py-2 bg-green-400 text-white rounded-lg hover:bg-green-700">
                <i class="fas fa-plus mr-2"></i> + New Habit
            </a>
        </div>

        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">My Habits</h1>
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
                            <!-- Consistency -->
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

                        <!-- Streak & Completion -->
                        <div class="px-6 py-2 flex justify-between items-center text-sm text-gray-600 border-t border-gray-200">
                            <span><i class="fas fa-fire mr-1"></i>{{ $habit->current_streak }} day streak</span>
                            @if($habit->todaysLog && $habit->todaysLog->completed)
                                <span class="text-green-600"><i class="fas fa-check-circle mr-1"></i>Completed today</span>
                            @else
                                <form method="POST" action="{{ route('habits.log', $habit) }}" class="inline">
                                    @csrf
                                    <button type="submit" name="completed" value="1" class="inline-flex items-center px-3 py-1 bg-green-600 text-white rounded-lg hover:bg-green-700">
                                        <i class="fas fa-check mr-1"></i> Mark Complete
                                    </button>
                                </form>
                            @endif
                        </div>

                        <!-- Action Buttons -->
                        <div class="px-6 py-4 flex justify-between items-center bg-gray-50 border-t border-gray-200 space-x-2">
                            <a href="{{ route('habits.progress', $habit) }}" class="flex-1 text-center px-3 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 text-sm">
                                <i class="fas fa-chart-line mr-1"></i> Progress
                            </a>
                            <a href="{{ route('habits.edit', $habit) }}" class="flex-1 text-center px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm">
                                <i class="fas fa-edit mr-1"></i> Edit
                            </a>
                            <form method="POST" action="{{ route('habits.destroy', $habit) }}" onsubmit="return confirm('Delete this habit?')" class="flex-1">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full px-3 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 text-sm">
                                    <i class="fas fa-trash mr-1"></i> Delete
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
