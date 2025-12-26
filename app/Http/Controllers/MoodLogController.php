<?php 

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MoodLog;
use Carbon\Carbon;

class MoodLogController extends Controller
{
    public function today()
{
    $log = $this->getTodayLog();

    $needsAlert = app(\App\Services\MoodStreakService::class)
        ->needsAlert(auth()->id());

    return view('mood.today', compact('log', 'needsAlert'));
}


    public function saveMorning(Request $request)
    {
        $data = $request->validate([
            'morning_mood' => ['nullable', 'string', 'max:50'],
            'planned_activities' => ['nullable', 'string', 'max:2000'],
        ]);

        $log = $this->getTodayLog();
        $log->update($data);

        return back()->with('status', 'Morning check-in saved 🌅');
    }

    public function saveEvening(Request $request)
    {
        $data = $request->validate([
            'evening_mood' => ['nullable', 'string', 'max:50'],
            'day_summary' => ['nullable', 'string', 'max:2000'],
            'was_active' => ['nullable', 'in:0,1'],
        ]);

        // convert "0"/"1" to boolean
        if (array_key_exists('was_active', $data)) {
            $data['was_active'] = $data['was_active'] === '1';
        }

        $log = $this->getTodayLog();
        $log->update($data);

        return back()->with('status', 'Evening check-out saved 🌙');
    }

    private function getTodayLog()
    {
        $userId = auth()->id();

        // Important: matches date even if DB stores datetime (2025-12-27 00:00:00)
        $log = MoodLog::where('user_id', $userId)
            ->whereDate('log_date', Carbon::today())
            ->first();

        if (!$log) {
            $log = MoodLog::create([
                'user_id' => $userId,
                'log_date' => Carbon::today()->startOfDay(),
            ]);
        }

        return $log;
    }
    public function confirmAlert()
    {
        return back()->with('status', '✅ Confirmation received. (Email sending will be added next)');
    }

    public function dismissAlert()
    {
        return back()->with('status', 'Okay — no alert was sent.');
    }

}
