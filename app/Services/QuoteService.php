<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class QuoteService
{
    public function getDailyQuote(): array
    {
        return Cache::remember('motivation_quote_12h', now()->addHours(12), function () {
            try {
                $response = Http::timeout(5)->get('https://zenquotes.io/api/random');
                $data = $response->json();

                return [
                    'text' => $data[0]['q'] ?? 'Small steps matter.',
                    'author' => $data[0]['a'] ?? 'Mental Wellness Companion'
                ];
            } catch (\Exception $e) {
                return [
                    'text' => 'Small steps matter.',
                    'author' => 'Mental Wellness Companion'
                ];
            }
        });
    }
}
