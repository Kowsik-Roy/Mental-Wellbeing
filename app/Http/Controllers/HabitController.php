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

        // Update streak if completed
        if ($validated['completed']) {
            $this->updateStreak($habit);
        }

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
            $service = GoogleCalendarController::googleClient($user);
            $today = now()->format('Y-m-d');
            $reminderTime = $today . ' ' . $request->reminder_time . ':00';
            
            $startDateTime = new \Google\Service\Calendar\EventDateTime();
            $startDateTime->setDateTime($reminderTime);
            
            $endDateTime = new \Google\Service\Calendar\EventDateTime();
            $endDateTime->setDateTime(now()->parse($reminderTime)->addMinutes(30)->format('Y-m-d H:i:s'));

            $event = new \Google\Service\Calendar\Event([
                'summary' => $habit->title . ' (Habit)',
                'description' => $habit->description ?? 'Daily habit reminder',
                'start' => $startDateTime,
                'end' => $endDateTime,
            ]);

            if ($habit->google_event_id) {
                $service->events->update('primary', $habit->google_event_id, $event);
            } else {
                $createdEvent = $service->events->insert('primary', $event);
                $habit->update(['google_event_id' => $createdEvent->getId()]);
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
            $service = GoogleCalendarController::googleClient($user);
            $service->events->delete('primary', $habit->google_event_id);
            $habit->update(['google_event_id' => null]);
        } catch (\Exception $e) {
            \Log::error('Google Calendar delete failed: ' . $e->getMessage());
        }
    }
}




