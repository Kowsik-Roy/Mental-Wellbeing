<?php

namespace App\Http\Controllers;

use App\Models\Habit;
use App\Models\HabitLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
            'goal_type' => 'once', // Default value, no longer user-selectable
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

        // Preserve goal_type if it exists, otherwise set default
        if (!isset($validated['goal_type'])) {
            $validated['goal_type'] = $habit->goal_type ?? 'once';
        }

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

        // Get recent logs
        $recentLogs = $habit->logs()
            ->orderBy('logged_date', 'desc')
            ->limit(30)
            ->get();

        // Calculate statistics (frequency‑aware)
        $totalCompletions = $habit->logs()->where('completed', true)->count();
        $expectedDays = $habit->getExpectedDaysSinceCreation();
        $completionRate = $expectedDays > 0 ? ($totalCompletions / $expectedDays) * 100 : 0;

        // Get streak info
        $currentStreak = $habit->current_streak;
        $bestStreak = $habit->best_streak;

        $totalDays = $expectedDays; // show active days rather than raw calendar days

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
