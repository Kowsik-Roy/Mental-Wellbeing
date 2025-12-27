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

use App\Http\Controllers\PushNotificationController;
use App\Http\Controllers\AboutController;

use App\Http\Controllers\MoodLogController;
use App\Http\Controllers\EmergencyContactController;
use App\Http\Controllers\AiChatController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return response()
        ->view('welcome')
        ->header('Cache-Control', 'no-cache, no-store, must-revalidate, max-age=0')
        ->header('Pragma', 'no-cache')
        ->header('Expires', '0')
        ->header('Last-Modified', gmdate('D, d M Y H:i:s') . ' GMT')
        ->header('ETag', md5(time()));
});

Route::get('/meditation', function () {
    return view('meditation');
})->name('meditation');

Route::get('/about', [AboutController::class, 'index'])->name('about.index');

/*
|--------------------------------------------------------------------------
| Guest Routes (Auth)
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {

    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);

    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);

    // Google OAuth (sign in / sign up)
    Route::get('/auth/google', [\App\Http\Controllers\Auth\GoogleAuthController::class, 'redirectToGoogle'])->name('google.login');
    Route::get('/auth/google/callback', [\App\Http\Controllers\Auth\GoogleAuthController::class, 'handleGoogleCallback'])->name('google.callback');

    // Email verification during registration
    Route::get('/verify-email', [VerificationController::class, 'showVerificationForm'])->name('verification.show');
    Route::post('/verify-email', [VerificationController::class, 'verify'])->name('verification.verify');
    Route::post('/verify-email/resend', [VerificationController::class, 'resendRegistrationCode'])->name('verification.resend');
});

/*
|--------------------------------------------------------------------------
| Password Reset Routes (Available for both guests and authenticated users)
|--------------------------------------------------------------------------
*/

Route::get('/forgot-password', [\App\Http\Controllers\Auth\PasswordResetController::class, 'showRequestForm'])->name('password.reset.request');
Route::post('/forgot-password', [\App\Http\Controllers\Auth\PasswordResetController::class, 'sendResetCode'])->name('password.reset.send');
Route::get('/reset-password/verify', [\App\Http\Controllers\Auth\PasswordResetController::class, 'showVerifyForm'])->name('password.reset.verify');
Route::post('/reset-password/verify', [\App\Http\Controllers\Auth\PasswordResetController::class, 'verifyCode'])->name('password.reset.verify.code');
Route::get('/reset-password', [\App\Http\Controllers\Auth\PasswordResetController::class, 'showResetForm'])->name('password.reset.form');
Route::post('/reset-password', [\App\Http\Controllers\Auth\PasswordResetController::class, 'reset'])->name('password.reset');

/*
|--------------------------------------------------------------------------
| Google Calendar OAuth (Public callback routes)
|--------------------------------------------------------------------------
*/

Route::get('/calendar/redirect', [GoogleCalendarController::class, 'redirect'])->name('google.calendar.redirect');
Route::get('/calendar/callback', [GoogleCalendarController::class, 'callback'])->name('google.calendar.callback');

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/weekly-summary', [DashboardController::class, 'weeklySummary'])->name('dashboard.weekly-summary');
    Route::post('/dashboard/send-summary', [DashboardController::class, 'sendSummary'])->name('dashboard.send-summary');

    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Profile
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

    // Password reset for authenticated users (sends code directly to their email)
    Route::post('/password/reset/send', [\App\Http\Controllers\Auth\PasswordResetController::class, 'sendResetCodeForAuthenticated'])->name('password.reset.send.authenticated');

    // Journal
    Route::get('/journal', [JournalController::class, 'today'])->name('journal.today');
    Route::post('/journal', [JournalController::class, 'store'])->name('journal.store');
    Route::get('/journal/{journal}/edit', [JournalController::class, 'edit'])->name('journal.edit');
    Route::put('/journal/{journal}', [JournalController::class, 'update'])->name('journal.update');
    Route::delete('/journal/{journal}', [JournalController::class, 'destroy'])->name('journal.destroy');
    Route::get('/journal/history/all', [JournalController::class, 'history'])->name('journal.history');

    // Google Calendar toggle (auth required)
    Route::post('/calendar/toggle', [GoogleCalendarController::class, 'toggle'])->name('calendar.toggle');

    // Habits
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

    // Wellness
    Route::get('/wellness', [WellnessController::class, 'index'])->name('wellness.index');
    Route::post('/wellness/generate', [WellnessController::class, 'generate'])->name('wellness.generate');

    // Push notifications
    Route::post('/push/subscribe', [PushNotificationController::class, 'subscribe'])->name('push.subscribe');
    Route::post('/push/unsubscribe', [PushNotificationController::class, 'unsubscribe'])->name('push.unsubscribe');
    Route::get('/push/check-reminders', [PushNotificationController::class, 'checkReminders'])->name('push.check-reminders');

    // Mood Tracker (ONLY ONCE)
    Route::get('/mood', [MoodLogController::class, 'today'])->name('mood.today');
    Route::post('/mood/morning', [MoodLogController::class, 'saveMorning'])->name('mood.morning');
    Route::post('/mood/evening', [MoodLogController::class, 'saveEvening'])->name('mood.evening');

    // If you already have these methods in MoodLogController, keep them:
    Route::post('/mood/alert/confirm', [MoodLogController::class, 'confirmAlert'])->name('mood.alert.confirm');
    Route::post('/mood/alert/dismiss', [MoodLogController::class, 'dismissAlert'])->name('mood.alert.dismiss');

    // Emergency Contact
    Route::get('/settings/emergency-contact', [EmergencyContactController::class, 'edit'])->name('emergency.edit');
    Route::post('/settings/emergency-contact', [EmergencyContactController::class, 'update'])->name('emergency.update');

    // AI Chat
    Route::get('/ai-chat', [AiChatController::class, 'index'])->name('ai.chat');
    Route::post('/ai-chat/message', [AiChatController::class, 'message'])->name('ai.chat.message');
});
