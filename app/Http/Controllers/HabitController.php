<?php

namespace App\Http\Controllers;

use App\Models\Habit;
use App\Models\HabitLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\GoogleCalendarController;
use Google\Service\Calendar\Event;
use Google\Service\Calendar\EventDateTime;

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

        // Sync to Google Calendar if enabled and reminder time exists
        $this->syncToGoogleCalendar($habit, $request);

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
            'frequency' => 'required|in:daily,weekly,weekdays,custom',
            'goal_type' => 'required|in:times,minutes,boolean',
            'target_value' => 'required|integer|min:1',
            'reminder_time' => 'nullable|date_format:H:i',
            'is_active' => 'boolean',
        ]);

        $habit->update($validated);

        // Sync to Google Calendar if enabled
        $this->syncToGoogleCalendar($habit, $request);

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
        
        // Delete from Google Calendar first
        $this->deleteFromGoogleCalendar($habit);
        
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

        $log = $habit->logs()->updateOrCreate(
            ['logged_date' => today()],
            array_merge($validated, ['user_id' => Auth::id()])
        );

        if ($validated['completed']) {
            $this->updateStreak($habit);
        }

        return redirect()->route('habits.index')
            ->with('success', 'Habit logged successfully!');
    }

    /**
     * Sync habit to Google Calendar (create or update).
     */
    private function syncToGoogleCalendar(Habit $habit, Request $request): void
    {
        $user = Auth::user();
        
        if (!$user->calendar_sync_enabled || !$user->google_refresh_token || !$request->reminder_time) {
            return;
        }

        try {
            $googleController = new GoogleCalendarController();
            $service = $googleController->googleClient($user);

            $reminderTime = $request->reminder_time . ':00'; // Add seconds
            $startDateTime = new EventDateTime(['dateTime' => $reminderTime]);
            $endDateTime = new EventDateTime(['dateTime' => $reminderTime]); // Same time for now

            $event = new Event([
                'summary' => $habit->title . ' (Habit)',
                'description' => $habit->description,
                'start' => $startDateTime,
                'end' => $endDateTime,
            ]);

            if ($habit->google_event_id) {
                // Update existing event
                $event = $service->events->update('primary', $habit->google_event_id, $event);
            } else {
                // Create new event
                $event = $service->events->insert('primary', $event);
                $habit->update(['google_event_id' => $event->getId()]);
            }
        } catch (\Exception $e) {
            \Log::error('Google Calendar sync failed: ' . $e->getMessage());
        }
    }

    /**
     * Delete habit event from Google Calendar.
     */
    private function deleteFromGoogleCalendar(Habit $habit): void
    {
        $user = Auth::user();
        
        if (!$habit->google_event_id || !$user->calendar_sync_enabled || !$user->google_refresh_token) {
            return;
        }

        try {
            $googleController = new GoogleCalendarController();
            $service = $googleController->googleClient($user);
            $service->events->delete('primary', $habit->google_event_id);
        } catch (\Exception $e) {
            \Log::error('Google Calendar delete failed: ' . $e->getMessage());
        }
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
