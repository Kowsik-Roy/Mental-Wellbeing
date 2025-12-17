<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\VerificationCode;
use App\Mail\VerificationCodeMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    /**
     * Redirect the user to the Google authentication page.
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Obtain the user information from Google.
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            // Check if user already exists with this Google ID
            $user = User::where('google_id', $googleUser->getId())->first();

            if ($user) {
                // User exists, log them in
                Auth::login($user, true);
                return redirect()->intended('/dashboard');
            }

            // Check if user exists with this email
            $existingUser = User::where('email', $googleUser->getEmail())->first();

            if ($existingUser) {
                // Link Google account to existing user and log them in (already registered locally)
                $existingUser->google_id = $googleUser->getId();
                $existingUser->save();

                Auth::login($existingUser, true);

                return redirect()->intended('/dashboard');
            }

            // New registration via Google: create local user but require email verification code
            $user = User::create([
                'name'     => $googleUser->getName(),
                'email'    => $googleUser->getEmail(),
                'google_id'=> $googleUser->getId(),
                'password' => null, // will use Google or later password set
            ]);

            // Generate a 5‑digit verification code (3‑minute validity)
            $code = (string) random_int(10000, 99999);

            VerificationCode::create([
                'user_id'    => $user->id,
                'type'       => 'registration',
                'code'       => $code,
                'expires_at' => now()->addMinutes(3),
            ]);

            Mail::to($user->email)->send(new VerificationCodeMail($user, $code, 'registration'));

            // Store pending user id in session and send to the same verification screen as email/password signup
            session()->put('pending_verification_user_id', $user->id);

            return redirect()
                ->route('verification.show')
                ->with('status', 'We sent a verification code to your email. Enter it to complete registration.');
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Google OAuth Error: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            
            // Show user-friendly error message
            $errorMessage = 'Unable to login with Google. Please try again.';
            
            // In development, show more details
            if (config('app.debug')) {
                $errorMessage .= ' Error: ' . $e->getMessage();
            }
            
            return redirect('/login')->with('error', $errorMessage);
        }
    }
}
