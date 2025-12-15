<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    //  Display the user's profile.
    public function show()
    {
        $user = Auth::user();
        return view('dashboard', compact('user'));
    }

    //  Show the form for editing the profile.
    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    // Update the user's profile information.
    public function update(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $user->update($validated);

        return redirect()->route('dashboard')
            ->with('success', 'Profile updated successfully!');
    }

    // Delete the user's account.
    public function destroy(Request $request)
    {
        $user = Auth::user();

        // Optional: simple confirmation via form field
        $request->validate([
            'confirm_delete' => 'required|in:DELETE',
        ]);

        // Log out the user and delete account
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $user->delete();

        return redirect('/')
            ->with('success', 'Your account has been deleted. We hope to see you again.');
    }

    // Show the change password form.
    public function showChangePasswordForm()
    {
        $user = Auth::user();
        $hasPassword = !is_null($user->password);
        return view('profile.change-password', compact('hasPassword'));
    }

    // Change the user's password.
    public function changePassword(Request $request)
    {
        $user = Auth::user();
        $hasPassword = !is_null($user->password);

        // Validation rules
        $rules = [
            'new_password' => 'required|string|min:8|confirmed',
        ];

        // Only require current password if user has an existing password
        if ($hasPassword) {
            $rules['current_password'] = 'required';
        }

        $validator = Validator::make($request->all(), $rules);

        // Check current password only if user has an existing password
        if ($hasPassword) {
            if (!Hash::check($request->current_password, $user->password)) {
                $validator->errors()->add('current_password', 'Current password is incorrect');
                return redirect()->back()->withErrors($validator);
            }
        }

        // Update password
        $user->password = Hash::make($request->new_password);
        $user->save();

        $message = $hasPassword 
            ? 'Password changed successfully!' 
            : 'Password set successfully! You can now login with your email and password.';

        return redirect()->route('dashboard')
            ->with('success', $message);
    }
}
