<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EmergencyContact;

class EmergencyContactController extends Controller
{
    public function edit()
    {
        $contact = EmergencyContact::where('user_id', auth()->id())->first();

        return view('settings.emergency_contact', compact('contact'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255'],
            'relationship' => ['nullable', 'string', 'max:100'],
        ]);

        EmergencyContact::updateOrCreate(
            ['user_id' => auth()->id()],
            $data + ['user_id' => auth()->id()]
        );

        return back()->with('status', 'Emergency contact saved ✅');
    }
}
