<?php

namespace App\Http\Controllers;

use App\Models\Habit;
use App\Models\HabitLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HabitController extends Controller
{
    /**
     * Display a listing of the habits.
     */
    public function index()
    {
        $habits = Auth::user()->habits()
            ->where('is_active', true)
            ->with(['todaysLog'])
            ->latest()
            ->get();
            
        return view('habits.index', compact('habits'));
    }

    /**
     * Show the form for creating a new habit.
     */
    public function create()
    {
        return view('habits.create');
    }

    /**
     * Store a newly created habit in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'frequency' => 'required|in:daily,weekly,weekdays,custom',
            'goal_type' => 'required|in:times,minutes,boolean',
            'target_value' => 'required|integer|min:1',
            'reminder_time' => 'nullable|date_format:H:i',
        ]);

        $habit = Auth::user()->habits()->create($validated);

        return redirect()->route('habits.index')
            ->with('success', 'Habit created successfully!');
    }

    /**
     * Show the form for editing the specified habit.
     */
    public function edit(Habit $habit)
    {
        // Ensure user owns this habit
        if ($habit->user_id !== Auth::id()) {
            abort(403);
        }
        
        return view('habits.edit', compact('habit'));
    }

    /**
     * Update the specified habit in storage.
     */
    public function update(Request $request, Habit $habit)
    {
        // Ensure user owns this habit
        if ($habit->user_id !== Auth::id()) {
            abort(403);
        }
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'frequency' => 'required|in:daily,weekly,weekdays,custom',
            'goal_type' => 'required|in:times,minutes,boolean',
            'target_value' => 'required|integer|min:1',
            'reminder_time' => 'nullable|date_format:H:i',
            'is_active' => 'boolean',
        ]);

        $habit->update($validated);

        return redirect()->route('habits.index')
            ->with('success', 'Habit updated successfully!');
    }

    /**
     * Remove the specified habit from storage.
     */
    public function destroy(Habit $habit)
    {
        // Ensure user owns this habit
        if ($habit->user_id !== Auth::id()) {
            abort(403);
        }
        
        $habit->delete();

        return redirect()->route('habits.index')
            ->with('success', 'Habit deleted successfully!');
    }

    /**
     * Log completion for a habit today.
     */
    public function log(Request $request, Habit $habit)
    {
        // Ensure user owns this habit
        if ($habit->user_id !== Auth::id()) {
            abort(403);
        }
        
        $validated = $request->validate([
            'completed' => 'required|boolean',
            'value_achieved' => 'nullable|integer|min:0',
            'notes' => 'nullable|string',
        ]);

        $log = $habit->logs()->updateOrCreate(
            ['logged_date' => today()],
            array_merge($validated, ['user_id' => Auth::id()])
        );

        // Recalculate streaks based on all logs
        $habit->recalculateStreaks();

        return redirect()->route('habits.index')
            ->with('success', 'Habit logged successfully!');
    }

    /**
     * Show progress and analytics for a specific habit.
     */
    public function progress(Habit $habit)
    {
        // Ensure user owns this habit
        if ($habit->user_id !== Auth::id()) {
            abort(403);
        }

        // Get progress data
        $weeklyPercentage = $habit->getWeeklyCompletionPercentage();
        $monthlyPercentage = $habit->getMonthlyCompletionPercentage();
        $allTimePercentage = $habit->getAllTimeCompletionPercentage();
        $consistencyScore = $habit->getConsistencyScore();

        // Get recent logs
        $recentLogs = $habit->logs()
            ->orderBy('logged_date', 'desc')
            ->limit(30)
            ->get();

        // Calculate statistics
        $totalCompletions = $habit->logs()->where('completed', true)->count();
        $totalDays = $habit->created_at->diffInDays(now()) + 1;
        $completionRate = $totalDays > 0 ? ($totalCompletions / $totalDays) * 100 : 0;

        return view('habits.progress', compact(
            'habit',
            'weeklyPercentage',
            'monthlyPercentage',
            'allTimePercentage',
            'consistencyScore',
            'recentLogs',
            'totalCompletions',
            'totalDays',
            'completionRate'
        ));
    }
}