<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class HuggingFaceService
{
    private string $apiKey;
    private string $apiUrl;
    private string $model;

    public function __construct()
    {
        $this->apiKey = env('HUGGINGFACE_API_KEY');
        // Use OpenAI-compatible model via HuggingFace router
        // Options: 'openai/gpt-oss-120b:fastest', 'google/gemma-2-2b-it', 'Qwen/Qwen2.5-7B-Instruct-1M'
        $this->model = env('HUGGINGFACE_MODEL', 'openai/gpt-oss-120b:fastest');
        // Use OpenAI-compatible chat completions endpoint
        $this->apiUrl = "https://router.huggingface.co/v1/chat/completions";
        
        if (empty($this->apiKey)) {
            Log::warning('HuggingFace API key is not set in .env file');
        }
    }

    /**
     * Generate an emotional reflection for a journal entry.
     * 
     * @param string $content The journal entry content
     * @param string|null $mood The selected mood (e.g., 'happy', 'sad', 'anxious')
     * @return string|null The generated reflection or null on failure
     */
    public function generateEmotionalReflection(string $content, ?string $mood = null): ?string
    {
        if (empty($this->apiKey)) {
            Log::error('HuggingFace API key is missing');
            return $this->generateFallbackReflection($content, $mood);
        }

        // Build the mood context
        $moodContext = '';
        if ($mood && isset(\App\Models\Journal::MOODS[$mood])) {
            $moodLabel = \App\Models\Journal::MOODS[$mood];
            $moodContext = "The user selected the mood: {$moodLabel}. ";
        }

        // Construct the system and user messages for OpenAI-compatible API
        $systemMessage = "You are a supportive, empathetic assistant. Your role is to provide brief, warm emotional acknowledgment for journal entries. You do NOT give advice, diagnosis, or analysis. You simply acknowledge the user's feelings in a supportive way.";
        
        $userMessage = "Read the following journal entry and selected mood. Return ONE supportive sentence acknowledging the user's feelings. Do NOT give advice. Do NOT be negative. Keep it brief and warm.\n\n";
        $userMessage .= "{$moodContext}Journal entry:\n{$content}";

        try {
            // Use OpenAI-compatible chat completions endpoint
            Log::debug('Calling HuggingFace API (OpenAI-compatible)', [
                'url' => $this->apiUrl,
                'model' => $this->model,
            ]);
            
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(5)->post($this->apiUrl, [
                'model' => $this->model,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => $systemMessage,
                    ],
                    [
                        'role' => 'user',
                        'content' => $userMessage,
                    ],
                ],
                'max_tokens' => 100,
                'temperature' => 0.7,
            ]);
            
            Log::debug('HuggingFace API response', [
                'status' => $response->status(),
                'successful' => $response->successful(),
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                // OpenAI-compatible response format
                if (isset($data['choices'][0]['message']['content'])) {
                    $reflection = $data['choices'][0]['message']['content'];
                } elseif (isset($data['choices'][0]['text'])) {
                    $reflection = $data['choices'][0]['text'];
                } else {
                    $reflection = null;
                }
                
                if ($reflection) {
                    // Clean up the response (remove quotes, extra whitespace, prompt remnants)
                    $reflection = trim($reflection);
                    $reflection = preg_replace('/^(Supportive acknowledgment:|Acknowledgment:)\s*/i', '', $reflection);
                    $reflection = trim($reflection, ' "\'');
                    
                    // Extract first sentence if multiple sentences
                    $sentences = preg_split('/([.!?]+)/', $reflection, 2, PREG_SPLIT_DELIM_CAPTURE);
                    if (count($sentences) >= 2) {
                        $reflection = $sentences[0] . $sentences[1];
                    }
                    
                    if (strlen($reflection) > 10) {
                        return $reflection;
                    }
                }
            } else {
                $errorData = $response->json();
                $errorMessage = $errorData['error'] ?? ($errorData['message'] ?? 'Unknown error');
                
                Log::error('HuggingFace API error', [
                    'status' => $response->status(),
                    'message' => $errorMessage,
                    'body' => $response->body(),
                ]);
                
                // Special handling for specific error codes
                if ($response->status() === 403) {
                    Log::warning('HuggingFace API token lacks permissions. Token needs "Write" permission to use Inference API. Check: https://huggingface.co/settings/tokens');
                } elseif ($response->status() === 404) {
                    Log::warning('HuggingFace model not found. The model may not be available on the router endpoint. Try a different model in .env: HUGGINGFACE_MODEL=google/gemma-2-2b-it');
                } elseif ($response->status() === 410) {
                    Log::warning('HuggingFace endpoint deprecated. This may indicate the router endpoint format has changed or the model is unavailable.');
                } elseif ($response->status() === 503) {
                    Log::warning('HuggingFace model is loading. Using fallback reflection.');
                }
            }
        } catch (\Exception $e) {
            Log::error('HuggingFace API exception: ' . $e->getMessage());
        }

        // Return fallback if API fails
        return $this->generateFallbackReflection($content, $mood);
    }

    /**
     * Generate wellness recommendations based on mood trends and habit data.
     * 
     * @param string $moodTrend Mood trend for the period (last 7 days or fewer)
     * @param string $habitSummary Habit completion summary
     * @return string|null The generated recommendation or null on failure
     */
    public function generateWellnessRecommendation(
        string $moodTrend,
        string $habitSummary
    ): ?string {
        if (empty($this->apiKey)) {
            Log::error('HuggingFace API key is missing');
            return $this->generateFallbackWellnessRecommendation($moodTrend, $habitSummary);
        }

        // Construct the system and user messages
        $systemMessage = "You are a supportive wellness assistant. Your role is to provide gentle, encouraging, and non-judgmental weekly reflections. You must never give medical advice, diagnoses, or negative feedback. Focus on progress, small improvements, and encouragement. Use warm and human language.";

        $userMessage = "Based on the user's wellness data below, write a weekly reflection (5–6 sentences).\n\n";
        $userMessage .= "Data provided:\n";
        $userMessage .= "- Mood trend: {$moodTrend}\n";
        $userMessage .= "- Habit completion summary: {$habitSummary}\n\n";
        $userMessage .= "- Start with a phrase like 'This week...' or 'Over the past week...'\n";
        $userMessage .= "- Reflect on the user's mood patterns and habit completion for the last 7 days (or fewer days if that's all the data available).\n";
        $userMessage .= "- Highlight at least one positive aspect or strength you notice.\n";
        $userMessage .= "- Suggest one small, realistic improvement for next week.\n";
        $userMessage .= "- End with a motivating and hopeful closing sentence.\n";
        $userMessage .= "- Keep the tone kind, supportive, and encouraging.";

        try {
            Log::debug('Calling HuggingFace API for wellness recommendation', [
                'url' => $this->apiUrl,
                'model' => $this->model,
            ]);
            
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(10)->post($this->apiUrl, [
                'model' => $this->model,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => $systemMessage,
                    ],
                    [
                        'role' => 'user',
                        'content' => $userMessage,
                    ],
                ],
                'max_tokens' => 300,
                'temperature' => 0.7,
            ]);
            
            Log::debug('HuggingFace API response for wellness', [
                'status' => $response->status(),
                'successful' => $response->successful(),
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                // OpenAI-compatible response format
                if (isset($data['choices'][0]['message']['content'])) {
                    $recommendation = $data['choices'][0]['message']['content'];
                } elseif (isset($data['choices'][0]['text'])) {
                    $recommendation = $data['choices'][0]['text'];
                } else {
                    $recommendation = null;
                }
                
                if ($recommendation) {
                    $recommendation = trim($recommendation);
                    if (strlen($recommendation) > 20) {
                        return $recommendation;
                    }
                }
            } else {
                $errorData = $response->json();
                $errorMessage = $errorData['error'] ?? ($errorData['message'] ?? 'Unknown error');
                
                Log::error('HuggingFace API error for wellness', [
                    'status' => $response->status(),
                    'message' => $errorMessage,
                    'body' => $response->body(),
                ]);
            }
        } catch (\Exception $e) {
            Log::error('HuggingFace API exception for wellness: ' . $e->getMessage());
        }

        // Return fallback if API fails
        return $this->generateFallbackWellnessRecommendation($moodTrend, $habitSummary);
    }

    /**
     * Generate fallback wellness recommendation when API is unavailable.
     */
    private function generateFallbackWellnessRecommendation(
        string $moodTrend,
        string $habitSummary
    ): string {
        $recommendation = "This week, you've been tracking your wellness journey, and that's a meaningful step in itself. ";
        
        if (strpos($habitSummary, '0/') === false && strpos($habitSummary, 'No active habits') === false) {
            $recommendation .= "I notice you've been working on your habits, which shows real dedication. ";
        }
        
        $recommendation .= "Remember that building new habits takes time, and it's okay to have days when things don't go as planned. ";
        $recommendation .= "For next week, consider focusing on just one small habit you'd like to strengthen, and celebrate each day you complete it. ";
        $recommendation .= "You're making progress, and every step forward, no matter how small, is valuable. Keep going!";
        
        return $recommendation;
    }

    /**
     * Generate a simple fallback reflection when API is unavailable.
     * Provides supportive acknowledgment based on mood and content length.
     */
    private function generateFallbackReflection(string $content, ?string $mood = null): string
    {
        $moodReflections = [
            'happy' => "It's wonderful that you're feeling positive today, and taking time to reflect on these moments is valuable.",
            'sad' => "It sounds like today was emotionally heavy for you, and expressing it here is a healthy step.",
            'excited' => "Your enthusiasm shines through, and it's great that you're capturing these exciting moments.",
            'angry' => "Acknowledging your feelings, even when they're difficult, shows strength and self-awareness.",
            'anxious' => "It takes courage to sit with anxious feelings, and writing about them is a meaningful way to process them.",
            'calm' => "Your sense of peace comes through, and it's beautiful that you're taking time to appreciate these moments.",
            'tired' => "Recognizing when you need rest is important, and giving yourself space to reflect is valuable.",
            'neutral' => "Taking time to reflect, regardless of how you're feeling, is a meaningful practice.",
        ];

        // Use mood-specific reflection if available
        if ($mood && isset($moodReflections[$mood])) {
            return $moodReflections[$mood];
        }

        // Generic supportive message based on content length
        $wordCount = str_word_count($content);
        if ($wordCount < 20) {
            return "Thank you for taking a moment to express yourself today.";
        } elseif ($wordCount < 50) {
            return "It's meaningful that you're taking time to reflect and write about your experiences.";
        } else {
            return "Your thoughtful reflection shows self-awareness, and expressing yourself here is a valuable practice.";
        }
    }
}
