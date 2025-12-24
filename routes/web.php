<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\JournalController;
use App\Http\Controllers\HabitController;
use App\Http\Controllers\GoogleCalendarController;
use App\Http\Controllers\WellnessController;

Route::get('/', function () {
    return view('welcome');
});

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);

    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);

    // Google OAuth Routes (sign in / sign up)
    Route::get('/auth/google', [\App\Http\Controllers\Auth\GoogleAuthController::class, 'redirectToGoogle'])->name('google.login');
    Route::get('/auth/google/callback', [\App\Http\Controllers\Auth\GoogleAuthController::class, 'handleGoogleCallback'])->name('google.callback');

    // Email verification during registration
    Route::get('/verify-email', [VerificationController::class, 'showVerificationForm'])->name('verification.show');
    Route::post('/verify-email', [VerificationController::class, 'verify'])->name('verification.verify');
    Route::post('/verify-email/resend', [VerificationController::class, 'resendRegistrationCode'])->name('verification.resend');
});

// Google Calendar OAuth callbacks (must be accessible from Google, no auth middleware)
// We still rely on the existing session inside the controller.
Route::get('/calendar/redirect', [GoogleCalendarController::class, 'redirect'])->name('google.calendar.redirect');
Route::get('/calendar/callback', [GoogleCalendarController::class, 'callback'])->name('google.calendar.callback');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/weekly-summary', [DashboardController::class, 'weeklySummary'])->name('dashboard.weekly-summary');
    Route::post('/dashboard/send-summary', [DashboardController::class, 'sendSummary'])->name('dashboard.send-summary');
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // === MEMBER 2: PROFILE MANAGEMENT ===
    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'show'])->name('profile.show');
        Route::get('/edit', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/update', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/delete', [ProfileController::class, 'destroy'])->name('profile.destroy');
        Route::get('/change-password', [ProfileController::class, 'showChangePasswordForm'])->name('profile.password.edit');
        Route::put('/change-password', [ProfileController::class, 'changePassword'])->name('profile.password.update');
        Route::get('/verify-password', [VerificationController::class, 'showPasswordVerificationForm'])->name('password.verify.show');
        Route::post('/verify-password', [VerificationController::class, 'verifyPasswordChange'])->name('password.verify.perform');
    });

    // === MEMBER 3: JOURNAL MANAGEMENT ===
    Route::get('/journal', [JournalController::class, 'today'])->name('journal.today');
    Route::post('/journal', [JournalController::class, 'store'])->name('journal.store');
    Route::get('/journal/{journal}/edit', [JournalController::class, 'edit'])->name('journal.edit');
    Route::put('/journal/{journal}', [JournalController::class, 'update'])->name('journal.update');
    Route::delete('/journal/{journal}', [JournalController::class, 'destroy'])->name('journal.destroy');
    Route::get('/journal/history/all', [JournalController::class, 'history'])->name('journal.history');

    // === MEMBER 3: Google Calendar (toggle only, requires auth) ===
    Route::post('/calendar/toggle', [GoogleCalendarController::class, 'toggle'])->name('calendar.toggle');

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
        Route::post('/{habit}/sync-calendar', [HabitController::class, 'syncCalendar'])->name('habits.sync-calendar');
    });

    // === WELLNESS RECOMMENDATIONS ===
    Route::get('/wellness', [WellnessController::class, 'index'])->name('wellness.index');
    Route::post('/wellness/generate', [WellnessController::class, 'generate'])->name('wellness.generate');

// === PUSH NOTIFICATIONS ===
    Route::post('/push/subscribe', [PushNotificationController::class, 'subscribe'])->name('push.subscribe');
    Route::post('/push/unsubscribe', [PushNotificationController::class, 'unsubscribe'])->name('push.unsubscribe');
    Route::get('/push/check-reminders', [PushNotificationController::class, 'checkReminders'])->name('push.check-reminders');

});
