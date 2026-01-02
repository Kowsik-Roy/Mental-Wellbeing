<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MeditationController extends Controller
{
    /**
     * Display the meditation timer page.
     */
    public function index()
    {
        $user = auth()->user();
        $userCity = $user ? ($user->city ?? 'Dhaka') : 'Dhaka';
        $userCountry = $user ? ($user->country ?? 'Bangladesh') : 'Bangladesh';
        
        return view('meditation', compact('userCity', 'userCountry'));
    }
}

