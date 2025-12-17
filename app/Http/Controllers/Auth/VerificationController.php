<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\VerificationCode;
use App\Mail\VerificationCodeMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class VerificationController extends Controller
{
    public function showVerificationForm(Request $request)
    {
        $pendingUserId = $request->session()->get('pending_verification_user_id');

        if (! $pendingUserId) {
            return redirect()->route('register')
                ->with('error', 'Please register first to receive a verification code.');
        }

        $user = User::find($pendingUserId);
        if (! $user) {
            return redirect()->route('register')
                ->with('error', 'User not found. Please register again.');
        }

        return view('auth.verify-code', [
            'email' => $user->email,
        ]);
    }

    public function verify(Request $request)
    {
        $request->validate([
            'code' => ['required', 'string'],
        ]);

        $pendingUserId = $request->session()->get('pending_verification_user_id');

        if (! $pendingUserId) {
            return redirect()->route('register')
                ->with('error', 'Your verification session has expired. Please register again.');
        }

        $user = User::find($pendingUserId);
        if (! $user) {
            return redirect()->route('register')
                ->with('error', 'User not found. Please register again.');
        }

        $verification = VerificationCode::where('user_id', $user->id)
            ->where('type', 'registration')
            ->whereNull('used_at')
            ->latest()
            ->first();

        if (! $verification ||
            $verification->code !== $request->code ||
            ($verification->expires_at && $verification->expires_at->isPast())) {
            return back()
                ->withInput()
                ->withErrors(['code' => 'Invalid or expired verification code.']);
        }

        $verification->used_at = now();
        $verification->save();

        $user->email_verified_at = now();
        $user->save();

        // Clear session marker
        $request->session()->forget('pending_verification_user_id');

        Auth::login($user);

        return redirect()->route('dashboard')
            ->with('success', 'Email verified successfully. Welcome to WellBeing!');
    }

    /**
     * Resend a registration verification code to the pending user.
     */
    public function resendRegistrationCode(Request $request)
    {
        $pendingUserId = $request->session()->get('pending_verification_user_id');

        if (! $pendingUserId) {
            return redirect()->route('register')
                ->with('error', 'Your verification session has expired. Please register again.');
        }

        $user = User::find($pendingUserId);
        if (! $user) {
            $request->session()->forget('pending_verification_user_id');

            return redirect()->route('register')
                ->with('error', 'User not found. Please register again.');
        }

        // Invalidate previous unused registration codes
        VerificationCode::where('user_id', $user->id)
            ->where('type', 'registration')
            ->whereNull('used_at')
            ->delete();

        // Generate a new 5‑digit code (3‑minute validity)
        $code = (string) random_int(10000, 99999);

        VerificationCode::create([
            'user_id'    => $user->id,
            'type'       => 'registration',
            'code'       => $code,
            'expires_at' => now()->addMinutes(3),
        ]);

        Mail::to($user->email)->send(new VerificationCodeMail($user, $code, 'registration'));

        return redirect()
            ->route('verification.show')
            ->with('status', 'We sent you a new verification code. It is valid for 3 minutes.');
    }

    public function showPasswordVerificationForm(Request $request)
    {
        $pending = $request->session()->get('pending_password_change');

        if (! $pending || ! isset($pending['user_id'])) {
            return redirect()->route('profile.password.edit')
                ->with('error', 'Password change request not found. Please try again.');
        }

        return view('profile.verify-password');
    }

    public function verifyPasswordChange(Request $request)
    {
        $request->validate([
            'code' => ['required', 'string'],
        ]);

        $pending = $request->session()->get('pending_password_change');
        if (! $pending || ! isset($pending['user_id'], $pending['password'])) {
            return redirect()->route('profile.password.edit')
                ->with('error', 'Password change session expired. Please submit the form again.');
        }

        $user = User::find($pending['user_id']);
        if (! $user || ! Auth::check() || Auth::id() !== $user->id) {
            $request->session()->forget('pending_password_change');

            return redirect()->route('login')
                ->with('error', 'Please log in again to change your password.');
        }

        $verification = VerificationCode::where('user_id', $user->id)
            ->where('type', 'password_change')
            ->whereNull('used_at')
            ->latest()
            ->first();

        if (! $verification ||
            $verification->code !== $request->code ||
            ($verification->expires_at && $verification->expires_at->isPast())) {
            return back()
                ->withInput()
                ->withErrors(['code' => 'Invalid or expired verification code.']);
        }

        $verification->used_at = now();
        $verification->save();

        // Apply the new password from session
        $user->password = $pending['password'];
        $user->save();

        $request->session()->forget('pending_password_change');

        return redirect()->route('dashboard')
            ->with('success', 'Password updated successfully.');
    }
}

