<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use Laravel\Socialite\Facades\Socialite;
use Google\Client as GoogleClient;
use Google\Service\Calendar;

class GoogleCalendarController extends Controller
{
    /**
     * Enable/disable calendar sync. When enabling, redirect to Google OAuth.
     */
    public function toggle()
    {
        $user = Auth::user();

        // Enable - go through OAuth
        if (! $user->calendar_sync_enabled) {
            return redirect()->route('google.calendar.redirect');
        }

        // Disable sync and clean up events
        $user->calendar_sync_enabled = false;
        $user->save();

        $this->deleteAllHabitEvents($user);

        return back()->with('success', 'Google Calendar sync disabled.');
    }

    /**
        * Redirect user to Google to grant calendar permissions.
        */
    public function redirect()
    {
        // User must be logged in to attach Google Calendar to their account
        if (! Auth::check()) {
            return redirect()->route('login')->with('error', 'Please log in before connecting Google Calendar.');
        }

        // Use a dedicated redirect URI for calendar, so it matches exactly what is configured in Google Cloud.
        $calendarRedirect = env('GOOGLE_CALENDAR_REDIRECT_URI', env('GOOGLE_REDIRECT_URI'));

        return Socialite::driver('google')
            ->scopes([
                'openid',
                'profile',
                'email',
                'https://www.googleapis.com/auth/calendar.events',
            ])
            ->with(['access_type' => 'offline', 'prompt' => 'consent'])
            ->redirectUrl($calendarRedirect)
            ->redirect();
    }

    /**
     * Handle Google OAuth callback for calendar access.
     *
     * NOTE: We keep this very simple and rely on the configuration that was
     * used during the redirect step, to avoid redirect/state mismatches.
     */
    public function callback()
    {
        try {
            // User must be logged in to connect Google Calendar
            if (! Auth::check()) {
                return redirect()->route('login')
                    ->with('error', 'Please log in to WellBeing first, then try connecting Google Calendar again.');
            }

            $loggedInUser = Auth::user();

            // Use the same redirect URL and scopes as in redirect(), and disable state
            $calendarRedirect = env('GOOGLE_CALENDAR_REDIRECT_URI', env('GOOGLE_REDIRECT_URI'));

            $googleUser = Socialite::driver('google')
                ->scopes([
                    'openid',
                    'profile',
                    'email',
                    'https://www.googleapis.com/auth/calendar.events',
                ])
                ->stateless()
                ->redirectUrl($calendarRedirect)
                ->user();

            // SECURITY: Verify that the Google account email matches the logged-in user's email
            $googleEmail = $googleUser->getEmail();
            if ($loggedInUser->email !== $googleEmail) {
                Log::warning('Google Calendar sync attempted with mismatched email', [
                    'logged_in_user_id' => $loggedInUser->id,
                    'logged_in_email' => $loggedInUser->email,
                    'google_email' => $googleEmail,
                ]);
                
                return redirect()->route('habits.index')
                    ->with('error', 'The Google account you selected ('.$googleEmail.') does not match your WellBeing account ('.$loggedInUser->email.'). Please select the correct Google account or use a different WellBeing account.');
            }

            // Use the logged-in user (don't switch users)
            $user = $loggedInUser;

            // Build token payload; refreshToken may be null if Google does not resend it.
            $tokenData = [
                'access_token'  => $googleUser->token,
                'refresh_token' => $googleUser->refreshToken ?? $user->google_refresh_token,
                'expires_in'    => $googleUser->expiresIn ?? 3600,
                'created'       => time(),
            ];

            $user->google_id    = $googleUser->id;
            $user->google_token = json_encode($tokenData);

            // Only overwrite refresh token if we actually received one
            if (! empty($googleUser->refreshToken)) {
                $user->google_refresh_token = $googleUser->refreshToken;
            }

            $user->calendar_sync_enabled = true;
            $user->save();

            return redirect()->route('habits.index')->with('success', 'Google Calendar sync enabled successfully!');
        } catch (\Exception $e) {
            Log::error('Google Calendar callback error: '.$e->getMessage(), ['exception' => $e]);

            return redirect()->route('habits.index')
                ->with('error', 'Failed to enable Google Calendar sync. Please try again.');
        }
    }

    /**
     * Build an authenticated Google Calendar service for a user.
     */
    public static function googleClient($user): Calendar
    {
        $client = new GoogleClient();
        $client->setClientId(config('services.google.client_id'));
        $client->setClientSecret(config('services.google.client_secret'));
        $client->setRedirectUri(env('GOOGLE_CALENDAR_REDIRECT_URI', env('GOOGLE_REDIRECT_URI')));
        $client->setAccessType('offline');
        $client->setPrompt('consent');
        $client->setScopes(['https://www.googleapis.com/auth/calendar.events']);

        // Load access token (may be JSON or raw string)
        $token = $user->google_token;
        if (is_string($token)) {
            $decoded = json_decode($token, true);
            $token   = $decoded ?: ['access_token' => $token];
        }

        if ($token && isset($token['access_token'])) {
            $client->setAccessToken($token);
        }

        // Refresh if expired and we have a refresh token
        if ($client->isAccessTokenExpired() && $user->google_refresh_token) {
            try {
                $client->fetchAccessTokenWithRefreshToken($user->google_refresh_token);
                $newToken = $client->getAccessToken();
                if ($newToken) {
                    $user->google_token = json_encode($newToken);
                    $user->save();
                }
            } catch (\Exception $e) {
                Log::error('Google Calendar token refresh failed: '.$e->getMessage(), ['user_id' => $user->id]);
                throw $e;
            }
        }

        return new Calendar($client);
    }

    /**
     * Delete all habit events from Google Calendar when disabling sync.
     */
    private function deleteAllHabitEvents($user): void
    {
        if (! $user->google_refresh_token) {
            return;
        }

        try {
            $service = self::googleClient($user);

            $habits = $user->habits()->whereNotNull('google_event_id')->get();
            foreach ($habits as $habit) {
                try {
                    $service->events->delete('primary', $habit->google_event_id);
                } catch (\Exception $e) {
                    Log::warning('Failed to delete calendar event', [
                        'habit_id' => $habit->id,
                        'error'    => $e->getMessage(),
                    ]);
                }

                $habit->update(['google_event_id' => null]);
            }
        } catch (\Exception $e) {
            Log::error('Bulk Google Calendar cleanup failed: '.$e->getMessage(), ['user_id' => $user->id]);
        }
    }
}
