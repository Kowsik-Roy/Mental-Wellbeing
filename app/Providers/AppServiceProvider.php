<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use App\Models\Habit;
use App\Policies\HabitPolicy;
use App\Services\QuoteService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register Habit Policy
        Gate::policy(Habit::class, HabitPolicy::class);

        // Share daily quote with all views (user-specific)
        View::composer('*', function ($view) {
            $quoteService = app(QuoteService::class);
            // Get user ID if authenticated, otherwise null for guests
            $userId = auth()->check() ? auth()->id() : null;
            $view->with('dailyQuote', $quoteService->getDailyQuote($userId));
        });
    }
}
