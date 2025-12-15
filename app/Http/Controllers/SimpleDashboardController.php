<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class SimpleDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Get habits WITHOUT any relationships
        $habits = $user->habits()
            ->where('is_active', true)
            ->select('id', 'title', 'description', 'current_streak', 'best_streak')
            ->orderBy('current_streak', 'desc')
            ->limit(3)
            ->get();
        
        // Count without loading models
        $habitCount = $user->habits()->where('is_active', true)->count();
        $journalCount = $user->journals()->count();
        
        return view('dashboard-simple', compact('habits', 'habitCount', 'journalCount'));
    }
}