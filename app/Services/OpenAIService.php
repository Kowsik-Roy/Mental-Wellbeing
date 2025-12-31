<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenAIService
{
    private string $apiKey;
    private string $model;

    public function __construct()
    {
        $this->apiKey = env('OPENAI_API_KEY');
        $this->model = env('OPENAI_MODEL', 'gpt-4o-mini');
    }

    /**
     * Generate mood-activity insight for today.
     * Returns JSON with insight and suggestion, or null on failure.
     */
    public function generateMoodInsight(array $data): ?array
    {
        if (empty($this->apiKey)) {
            Log::warning('OpenAI API key not set');
            return null;
        }

        $systemPrompt = "You are a supportive, warm wellness companion. Generate ONLY valid JSON. No medical advice, no diagnosis, no crisis instructions. Be encouraging and gentle.";

        $userPrompt = "Based on today's mood and activity data, generate a supportive insight.\n\n";
        $userPrompt .= "Morning mood: {$data['morning_mood_label']} (score: {$data['morning_score']})\n";
        $userPrompt .= "Evening mood: {$data['evening_mood_label']} (score: {$data['evening_score']})\n";
        $userPrompt .= "Mood change: {$data['mood_change']}\n";
        $userPrompt .= "Planned activities: {$data['planned_activities']}\n";
        $userPrompt .= "Was active: " . ($data['was_active'] ? 'Yes' : 'No') . "\n";
        if (!empty($data['day_summary'])) {
            $userPrompt .= "Reflection: {$data['day_summary']}\n";
        }
        $userPrompt .= "\nReturn ONLY valid JSON in this exact format:\n";
        $userPrompt .= '{"insight": "1-2 sentence supportive message about today", "suggestion": {"title": "Short title", "description": "One small action for tomorrow", "time_minutes": 2}}';

        try {
            $response = Http::withToken($this->apiKey)
                ->timeout(15)
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => $this->model,
                    'messages' => [
                        ['role' => 'system', 'content' => $systemPrompt],
                        ['role' => 'user', 'content' => $userPrompt],
                    ],
                    'temperature' => 0.7,
                    'max_tokens' => 300,
                    'response_format' => ['type' => 'json_object'],
                ]);

            if ($response->successful()) {
                $content = $response->json('choices.0.message.content');
                if ($content) {
                    $decoded = json_decode($content, true);
                    if ($decoded && isset($decoded['insight'])) {
                        return $decoded;
                    }
                }
            } else {
                Log::error('OpenAI API error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
            }
        } catch (\Exception $e) {
            Log::error('OpenAI API exception: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Generate gentle check-in reminder message.
     * Returns JSON with message, or null on failure.
     */
    public function generateReminderMessage(int $daysSince): ?array
    {
        if (empty($this->apiKey)) {
            Log::warning('OpenAI API key not set');
            return null;
        }

        $systemPrompt = "You are a supportive, warm wellness companion. Generate ONLY valid JSON. Be gentle, friendly, non-pressuring. No guilt, no judgment.";

        $userPrompt = "Generate a friendly, gentle check-in reminder message for someone who hasn't checked in for {$daysSince} days.\n\n";
        $userPrompt .= "Return ONLY valid JSON in this exact format:\n";
        $userPrompt .= '{"message": "One friendly sentence encouraging a quick check-in"}';

        try {
            $response = Http::withToken($this->apiKey)
                ->timeout(10)
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => $this->model,
                    'messages' => [
                        ['role' => 'system', 'content' => $systemPrompt],
                        ['role' => 'user', 'content' => $userPrompt],
                    ],
                    'temperature' => 0.7,
                    'max_tokens' => 100,
                    'response_format' => ['type' => 'json_object'],
                ]);

            if ($response->successful()) {
                $content = $response->json('choices.0.message.content');
                if ($content) {
                    $decoded = json_decode($content, true);
                    if ($decoded && isset($decoded['message'])) {
                        return $decoded;
                    }
                }
            } else {
                Log::error('OpenAI API error for reminder', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
            }
        } catch (\Exception $e) {
            Log::error('OpenAI API exception for reminder: ' . $e->getMessage());
        }

        return null;
    }
}

