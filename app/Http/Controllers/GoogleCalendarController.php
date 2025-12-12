<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Socialite;  
use Google\Client as GoogleClient;
use Google\Service\Calendar;  
use Illuminate\Support\Facades\Auth;

class GoogleCalendarController extends Controller
{
    public function toggle()
    {
        $user = auth()->user();

        // Enable - redirect to Google OAuth
        if (!$user->calendar_sync_enabled) {
            return redirect()->route('google.calendar.redirect');
        }

        // Disable
        $user->calendar_sync_enabled = false;
        $user->save();

        // Clean up all habit events when disabling
        $this->deleteAllHabitEvents($user);

        return back()->with('success', 'Google Calendar sync disabled.');
    }

    public function redirect()
    {
        return Socialite::driver('google')
            ->scopes([
                'openid',
                'profile',
                'email',
                'https://www.googleapis.com/auth/calendar.events'  
            ])
            ->with(['access_type' => 'offline', 'prompt' => 'consent'])
            ->redirect();
    }

    public function callback()
    {
        $googleUser = Socialite::driver('google')->user();
        $user = auth()->user();

        $user->google_id = $googleUser->id;
        $user->google_token = $googleUser->token;
        $user->google_refresh_token = $googleUser->refreshToken;
        $user->calendar_sync_enabled = true;
        $user->save();

        return redirect()->route('dashboard')->with('success', 'Google Calendar enabled!');
    }

    public static function googleClient($user)
    {
        $client = new GoogleClient();
        $client->setClientId(env('GOOGLE_CALENDAR_CLIENT_ID'));
        $client->setClientSecret(env('GOOGLE_CALENDAR_CLIENT_SECRET'));
        $client->setRedirectUri(env('GOOGLE_CALENDAR_REDIRECT_URI'));
        $client->setAccessToken($user->google_token);

        // Auto refresh token
        if ($client->isAccessTokenExpired() && $user->google_refresh_token) {
            $client->fetchAccessTokenWithRefreshToken($user->google_refresh_token);
            $user->google_token = $client->getAccessToken();
            $user->save();
        }

        return new GoogleCalendar($client);
    }

    /**
     * Delete all habit events from Google Calendar when disabling sync.
     */
    private function deleteAllHabitEvents($user): void
    {
        if (!$user->google_refresh_token) {
            return;
        }

        try {
            $service = self::googleClient($user);
            
            // Get all habits with google_event_id
            $habits = $user->habits()->whereNotNull('google_event_id')->get();
            
            foreach ($habits as $habit) {
                $service->events->delete('primary', $habit->google_event_id);
                $habit->update(['google_event_id' => null]);
            }
        } catch (\Exception $e) {
            \Log::error('Bulk Google Calendar cleanup failed: ' . $e->getMessage());
        }
    }
}
