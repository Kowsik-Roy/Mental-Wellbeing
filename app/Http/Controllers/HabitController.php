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
            'frequency' => 'required|in:daily,weekdays,weekend',
            'goal_type' => 'required|in:once,multiple_times,minutes',
            'reminder_time' => 'nullable|date_format:H:i',
        ]);

        // Add default values
        $validated['user_id'] = Auth::id();
        $validated['current_streak'] = 0;
        $validated['best_streak'] = 0;
        $validated['is_active'] = true;

        $habit = Habit::create($validated);

        return redirect()->route('habits.index')
            ->with('success', 'Habit created successfully!');
    }

    /**
     * Show the form for editing the specified habit.
     */
    public function edit(Habit $habit)
    {
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
        if ($habit->user_id !== Auth::id()) {
            abort(403);
        }
       
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'frequency' => 'required|in:daily,weekdays,weekend',
            'goal_type' => 'required|in:once,multiple_times,minutes',
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
        if ($habit->user_id !== Auth::id()) {
            abort(403);
        }
       
        $validated = $request->validate([
            'completed' => 'required|boolean',
            'value_achieved' => 'nullable|integer|min:0',
            'notes' => 'nullable|string',
        ]);

        // Create or update log
        $log = HabitLog::updateOrCreate(
            [
                'habit_id' => $habit->id,
                'user_id' => Auth::id(),
                'logged_date' => today(),
            ],
            array_merge($validated, ['user_id' => Auth::id()])
        );

        // Update streak if completed
        if ($validated['completed']) {
            $this->updateStreak($habit);
        }

        return redirect()->route('habits.index')
            ->with('success', 'Habit logged successfully!');
    }

    /**
     * Update streak for a habit.
     */
    private function updateStreak(Habit $habit): void
    {
        // Ensure user owns this habit
        if ($habit->user_id !== Auth::id()) {
            return;
        }

        // Get today's log
        $todayLog = $habit->logs()
            ->whereDate('logged_date', today())
            ->where('completed', true)
            ->first();

        if (!$todayLog) {
            return;
        }

        // Get yesterday's date
        $yesterday = now()->subDay()->toDateString();

        // Get yesterday's log
        $yesterdayLog = $habit->logs()
            ->whereDate('logged_date', $yesterday)
            ->where('completed', true)
            ->first();

        if ($yesterdayLog) {
            // Continue streak
            $newStreak = ($habit->current_streak ?? 0) + 1;
            $habit->current_streak = $newStreak;
           
            // Update best streak if needed
            if ($newStreak > $habit->best_streak) {
                $habit->best_streak = $newStreak;
            }
        } else {
            // Start new streak
            $habit->current_streak = 1;
            if ($habit->best_streak < 1) {
                $habit->best_streak = 1;
            }
        }
       
        $habit->save();
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

        // Get recent logs
        $recentLogs = $habit->logs()
            ->orderBy('logged_date', 'desc')
            ->limit(30)
            ->get();

        // Calculate statistics
        $totalCompletions = $habit->logs()->where('completed', true)->count();
        $totalDays = $habit->created_at->diffInDays(now()) + 1;
        $completionRate = $totalDays > 0 ? ($totalCompletions / $totalDays) * 100 : 0;

        // Get streak info
        $currentStreak = $habit->current_streak;
        $bestStreak = $habit->best_streak;

        return view('habits.progress', compact(
            'habit',
            'recentLogs',
            'totalCompletions',
            'totalDays',
            'completionRate',
            'currentStreak',
            'bestStreak'
        ));
    }
}