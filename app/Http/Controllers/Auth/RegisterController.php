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
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    /**
     * Show the registration form.
     */
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /**
     * Handle a registration request.
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        // Generate a 5‑digit verification code
        $code = (string) random_int(10000, 99999);

        VerificationCode::create([
            'user_id'    => $user->id,
            'type'       => 'registration',
            'code'       => $code,
            'expires_at' => now()->addMinutes(3),
        ]);

        // Send verification email (will go to log if MAIL_MAILER=log)
        Mail::to($user->email)->send(new VerificationCodeMail($user, $code, 'registration'));

        // Store pending user id in session and redirect to verification screen
        $request->session()->put('pending_verification_user_id', $user->id);

        return redirect()
            ->route('verification.show')
            ->with('status', 'We sent a verification code to your email. Enter it to complete registration.');
    }
}

