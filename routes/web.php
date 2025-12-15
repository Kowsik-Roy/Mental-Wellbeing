<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\JournalController;
use App\Http\Controllers\HabitController;
use App\Http\Controllers\GoogleCalendarController;

Route::get('/', function () {
    return view('welcome');
});

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);

    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);

    // Google OAuth Routes
    Route::get('/auth/google', [\App\Http\Controllers\Auth\GoogleAuthController::class, 'redirectToGoogle'])->name('google.login');
    Route::get('/auth/google/callback', [\App\Http\Controllers\Auth\GoogleAuthController::class, 'handleGoogleCallback'])->name('google.callback');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // === MEMBER 2: PROFILE MANAGEMENT ===
    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'show'])->name('profile.show');
        Route::get('/edit', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/update', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/delete', [ProfileController::class, 'destroy'])->name('profile.destroy');
        Route::get('/change-password', [ProfileController::class, 'showChangePasswordForm'])->name('profile.password.edit');
        Route::put('/change-password', [ProfileController::class, 'changePassword'])->name('profile.password.update');
    });

    // === MEMBER 3: JOURNAL MANAGEMENT ===
    Route::get('/journal', [JournalController::class, 'today'])->name('journal.today');
    Route::post('/journal', [JournalController::class, 'store'])->name('journal.store');

    Route::get('/journal/{journal}/edit', [JournalController::class, 'edit'])->name('journal.edit');
    Route::put('/journal/{journal}', [JournalController::class, 'update'])->name('journal.update');
    Route::delete('/journal/{journal}', [JournalController::class, 'destroy'])->name('journal.destroy');

    Route::get('/journal/history/all', [JournalController::class, 'history'])->name('journal.history');

    // === MEMBER 3: Google Calender ===
    Route::post('/calendar/toggle', [GoogleCalendarController::class, 'toggle'])->name('calendar.toggle');
    Route::get('/calendar/redirect', [GoogleCalendarController::class, 'redirect'])->name('google.calendar.redirect');
    Route::get('/calendar/callback', [GoogleCalendarController::class, 'callback'])->name('google.calendar.callback');

    // === MEMBER 2: HABIT MANAGEMENT ===
    Route::prefix('habits')->group(function () {
        Route::get('/', [HabitController::class, 'index'])->name('habits.index');
        Route::get('/create', [HabitController::class, 'create'])->name('habits.create');
        Route::post('/', [HabitController::class, 'store'])->name('habits.store');
        Route::get('/{habit}/edit', [HabitController::class, 'edit'])->name('habits.edit');
        Route::get('/{habit}/progress', [HabitController::class, 'progress'])->name('habits.progress');
        Route::put('/{habit}', [HabitController::class, 'update'])->name('habits.update');
        Route::delete('/{habit}', [HabitController::class, 'destroy'])->name('habits.destroy');
        Route::post('/{habit}/log', [HabitController::class, 'log'])->name('habits.log');
    });
});
