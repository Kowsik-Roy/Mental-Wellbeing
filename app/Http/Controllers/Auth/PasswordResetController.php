<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\VerificationCode;
use App\Mail\VerificationCodeMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class PasswordResetController extends Controller
{
    /**
     * Show the password reset request form.
     */
    public function showRequestForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Send password reset verification code.
     */
    public function sendResetCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)->first();

        // Don't reveal if email exists for security
        if (!$user) {
            return redirect()
                ->route('password.reset.request')
                ->with('status', 'If that email exists, we sent a verification code to reset your password.');
        }

        // Invalidate previous unused password reset codes
        VerificationCode::where('user_id', $user->id)
            ->where('type', 'password_reset')
            ->whereNull('used_at')
            ->delete();

        // Generate a new 5-digit code (3-minute validity)
        $code = (string) random_int(10000, 99999);

        VerificationCode::create([
            'user_id'    => $user->id,
            'type'       => 'password_reset',
            'code'       => $code,
            'expires_at' => now()->addMinutes(3),
        ]);

        Mail::to($user->email)->send(new VerificationCodeMail($user, $code, 'password_reset'));

        // Store user ID in session for verification
        $request->session()->put('pending_password_reset_user_id', $user->id);

        return redirect()
            ->route('password.reset.verify')
            ->with('status', 'We sent a verification code to your email. Enter it to reset your password.');
    }

    /**
     * Show the password reset verification form.
     */
    public function showVerifyForm(Request $request)
    {
        $pendingUserId = $request->session()->get('pending_password_reset_user_id');

        if (!$pendingUserId) {
            return redirect()->route('password.reset.request')
                ->with('error', 'Password reset request not found. Please try again.');
        }

        return view('auth.reset-password-verify');
    }

    /**
     * Verify the code and show password reset form.
     */
    public function verifyCode(Request $request)
    {
        $request->validate([
            'code' => ['required', 'string'],
        ]);

        $pendingUserId = $request->session()->get('pending_password_reset_user_id');

        if (!$pendingUserId) {
            return redirect()->route('password.reset.request')
                ->with('error', 'Password reset session expired. Please try again.');
        }

        $user = User::find($pendingUserId);
        if (!$user) {
            $request->session()->forget('pending_password_reset_user_id');
            return redirect()->route('password.reset.request')
                ->with('error', 'User not found. Please try again.');
        }

        $verification = VerificationCode::where('user_id', $user->id)
            ->where('type', 'password_reset')
            ->whereNull('used_at')
            ->latest()
            ->first();

        if (!$verification ||
            $verification->code !== $request->code ||
            ($verification->expires_at && $verification->expires_at->isPast())) {
            return back()
                ->withInput()
                ->withErrors(['code' => 'Invalid or expired verification code.']);
        }

        // Mark code as used
        $verification->used_at = now();
        $verification->save();

        // Store user ID for password reset form
        $request->session()->put('verified_password_reset_user_id', $user->id);
        $request->session()->forget('pending_password_reset_user_id');

        return redirect()->route('password.reset.form')
            ->with('success', 'Code verified. Please enter your new password.');
    }

    /**
     * Show the password reset form (after verification).
     */
    public function showResetForm(Request $request)
    {
        $verifiedUserId = $request->session()->get('verified_password_reset_user_id');

        if (!$verifiedUserId) {
            return redirect()->route('password.reset.request')
                ->with('error', 'Password reset session expired. Please try again.');
        }

        return view('auth.reset-password');
    }

    /**
     * Reset the password.
     */
    public function reset(Request $request)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required|string|min:8',
        ]);

        // Explicitly check that passwords match
        if ($request->password !== $request->password_confirmation) {
            return back()
                ->withInput()
                ->withErrors(['password_confirmation' => 'The password confirmation does not match.']);
        }

        $verifiedUserId = $request->session()->get('verified_password_reset_user_id');

        if (!$verifiedUserId) {
            return redirect()->route('password.reset.request')
                ->with('error', 'Password reset session expired. Please try again.');
        }

        $user = User::find($verifiedUserId);
        if (!$user) {
            $request->session()->forget('verified_password_reset_user_id');
            return redirect()->route('password.reset.request')
                ->with('error', 'User not found. Please try again.');
        }

        // Update password (Laravel will hash it automatically due to 'hashed' cast)
        $user->password = $request->password;
        $user->save();

        // If user is authenticated, log them out for security
        if (Auth::check() && Auth::id() === $user->id) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        // Clear session
        $request->session()->forget('verified_password_reset_user_id');

        return redirect()->route('login')
            ->with('success', 'Password reset successfully. Please log in with your new password.');
    }

    /**
     * Send password reset code directly to authenticated user's email.
     */
    public function sendResetCodeForAuthenticated(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login')
                ->with('error', 'Please log in to reset your password.');
        }

        // Invalidate previous unused password reset codes
        VerificationCode::where('user_id', $user->id)
            ->where('type', 'password_reset')
            ->whereNull('used_at')
            ->delete();

        // Generate a new 5-digit code (3-minute validity)
        $code = (string) random_int(10000, 99999);

        VerificationCode::create([
            'user_id'    => $user->id,
            'type'       => 'password_reset',
            'code'       => $code,
            'expires_at' => now()->addMinutes(3),
        ]);

        Mail::to($user->email)->send(new VerificationCodeMail($user, $code, 'password_reset'));

        // Store user ID in session for verification
        $request->session()->put('pending_password_reset_user_id', $user->id);

        return redirect()
            ->route('password.reset.verify')
            ->with('status', 'We sent a verification code to your email (' . $user->email . '). Enter it to reset your password.');
    }
}
