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
        $yesterday = now()->subDay();
        $yesterdayLog = $habit->logs()
            ->whereDate('logged_date', $yesterday)
            ->where('completed', true)
            ->exists();

        if ($yesterdayLog) {
            $habit->increment('current_streak');
            if ($habit->current_streak > $habit->best_streak) {
                $habit->update(['best_streak' => $habit->current_streak]);
            }
        } else {
            $habit->update(['current_streak' => 1]);
        }
    }
}