<?php

namespace App\Http\Controllers;

use App\Models\Habit;
use App\Models\HabitLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Google\Service\Calendar\Event;
use Google\Service\Calendar\EventDateTime;
use Google\Service\Calendar\EventReminders;

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
            'reminder_time' => 'nullable|date_format:H:i',
        ]);

        Habit::create([
            'user_id' => Auth::id(),
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'frequency' => $validated['frequency'],
            'reminder_time' => $validated['reminder_time'] ?? null,
            'current_streak' => 0,
            'best_streak' => 0,
            'is_active' => true,
        ]);

        return redirect()
            ->route('habits.index')
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
       
        $user = Auth::user();
        
        // Delete Google Calendar event if habit was synced
        if ($habit->google_event_id && $user->calendar_sync_enabled) {
            try {
                $service = GoogleCalendarController::googleClient($user);
                $service->events->delete('primary', $habit->google_event_id);
                Log::info('Google Calendar event deleted for habit', [
                    'habit_id' => $habit->id,
                    'event_id' => $habit->google_event_id,
                ]);
            } catch (\Exception $e) {
                // Log error but continue with habit deletion
                Log::warning('Failed to delete Google Calendar event when deleting habit', [
                    'habit_id' => $habit->id,
                    'event_id' => $habit->google_event_id,
                    'error' => $e->getMessage(),
                ]);
            }
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

        // Check if habit is active today based on frequency
        if (!$habit->isActiveToday()) {
            $message = match($habit->frequency) {
                'weekdays' => 'This habit can only be completed on weekdays (Monday-Friday).',
                'weekend' => 'This habit can only be completed on weekends (Saturday-Sunday).',
                default => 'This habit cannot be completed today.',
            };
            
            return redirect()->route('habits.index')
                ->with('error', $message);
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

        // Recalculate streaks whenever a log is saved
        $habit->recalculateStreaks();

        return redirect()->route('habits.index')
            ->with('success', 'Habit logged successfully!');
    }

    /**
     * Manually sync a habit to Google Calendar.
     */
    public function syncCalendar(Habit $habit)
    {
        if ($habit->user_id !== Auth::id()) {
            abort(403);
        }
        $user = Auth::user();

        if (! $user->calendar_sync_enabled) {
            return back()->with('error', 'Google Calendar sync is not enabled. Please enable it first.');
        }

        if (! $habit->reminder_time) {
            return back()->with('error', 'Please set a reminder time on this habit before syncing to Calendar.');
        }

        try {
            $service = GoogleCalendarController::googleClient($user);

            // Determine next occurrence based on habit frequency
            $base = now();
            $time = $habit->reminder_time instanceof \Carbon\Carbon
                ? $habit->reminder_time
                : \Carbon\Carbon::parse($habit->reminder_time);

            $start = $base->copy()->setTime($time->hour, $time->minute, 0);

            // Move to next appropriate day based on frequency if needed
            if ($habit->frequency === 'weekdays') {
                while (! $start->isWeekday()) {
                    $start->addDay();
                }
            } elseif ($habit->frequency === 'weekend') {
                while (! $start->isWeekend()) {
                    $start->addDay();
                }
            } else { // daily
                if ($start->isPast()) {
                    $start->addDay();
                }
            }

            $end = $start->copy()->addMinutes(30);
            $timezone = config('app.timezone', 'UTC');

            $startDateTime = new EventDateTime();
            $startDateTime->setDateTime($start->toRfc3339String());
            $startDateTime->setTimeZone($timezone);

            $endDateTime = new EventDateTime();
            $endDateTime->setDateTime($end->toRfc3339String());
            $endDateTime->setTimeZone($timezone);

            // Build recurrence rule based on frequency
            $recurrence = [];
            if ($habit->frequency === 'daily') {
                $recurrence = ['RRULE:FREQ=DAILY'];
            } elseif ($habit->frequency === 'weekdays') {
                $recurrence = ['RRULE:FREQ=WEEKLY;BYDAY=MO,TU,WE,TH,FR'];
            } elseif ($habit->frequency === 'weekend') {
                $recurrence = ['RRULE:FREQ=WEEKLY;BYDAY=SA,SU'];
            }

            $event = new Event([
                'summary'     => $habit->title.' (Habit)',
                'description' => $habit->description ?? 'Habit from WellBeing app',
                'start'       => $startDateTime,
                'end'         => $endDateTime,
            ]);

            if (! empty($recurrence)) {
                $event->setRecurrence($recurrence);
            }

            // Configure reminders using the proper EventReminders type
            $reminders = new EventReminders();
            $reminders->setUseDefault(false);
            $reminders->setOverrides([
                ['method' => 'popup', 'minutes' => 10],
            ]);
            $event->setReminders($reminders);

            if ($habit->google_event_id) {
                $service->events->update('primary', $habit->google_event_id, $event);
            } else {
                $created = $service->events->insert('primary', $event);
                $habit->update(['google_event_id' => $created->getId()]);
            }

            return back()->with('success', 'Habit synced to Google Calendar.');
        } catch (\Exception $e) {
            \Log::error('Google Calendar sync failed for habit '.$habit->id.': '.$e->getMessage());

            return back()->with('error', 'Could not sync with Google Calendar. Please try again or reconnect your account.');
        }
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

        // Get recent logs and filter to only show logs on active days for this habit's frequency
        $allRecentLogs = $habit->logs()
            ->orderBy('logged_date', 'desc')
            ->limit(30)
            ->get();
        
        // Filter to only show logs on active days
        $recentLogs = $allRecentLogs->filter(function($log) use ($habit) {
            $logDate = $log->logged_date instanceof \Carbon\Carbon 
                ? $log->logged_date->copy()
                : \Carbon\Carbon::parse($log->logged_date);
            $logDate->setTimezone('Asia/Dhaka')->startOfDay();
            return $habit->isActiveOnDate($logDate);
        })->values();

        // Calculate statistics (frequency‑aware)
        // Only count completions that occurred on active days for this habit's frequency
        $allCompletedLogs = $habit->logs()->where('completed', true)->get();
        $totalCompletions = 0;
        
        foreach ($allCompletedLogs as $log) {
            $logDate = $log->logged_date instanceof \Carbon\Carbon 
                ? $log->logged_date->copy()
                : \Carbon\Carbon::parse($log->logged_date);
            $logDate->setTimezone('Asia/Dhaka')->startOfDay();
            
            // Only count if this log is on an active day for the habit's frequency
            if ($habit->isActiveOnDate($logDate)) {
                $totalCompletions++;
            }
        }
        
        $expectedDays = $habit->getExpectedDaysSinceCreation();
        $completionRate = $expectedDays > 0 ? min(($totalCompletions / $expectedDays) * 100, 100) : 0;
        $completionRate = round($completionRate, 1);

        // Recalculate streaks to ensure they're up to date with frequency-based logic
        $habit->recalculateStreaks();
        $habit->refresh();

        // Get streak info
        $currentStreak = $habit->current_streak;
        $bestStreak = $habit->best_streak;

        // Tracking duration: actual calendar days since creation (not frequency-aware)
        $createdAt = $habit->created_at->copy()->setTimezone('Asia/Dhaka')->startOfDay();
        $now = \Carbon\Carbon::now('Asia/Dhaka')->startOfDay();
        $totalDays = $createdAt->diffInDays($now) + 1; // +1 to include today

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
