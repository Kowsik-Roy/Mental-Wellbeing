<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LlamaChatService
{
    private string $apiUrl;
    private string $apiKey;
    private int $timeout;

    public function __construct()
    {
        // Hugging Face Inference API endpoint
        $this->apiUrl = env('HF_INFERENCE_API_URL', 'https://router.huggingface.co/v1');
        $this->apiKey = env('HF_TOKEN', '');
        $this->timeout = env('LLAMA_TIMEOUT', 60);
    }

    /**
     * Check if the service is available (has API key).
     *
     * @return bool
     */
    public function isHealthy(): bool
    {
        return !empty($this->apiKey);
    }

    /**
     * Generate a chat response using Llama-3.2-3B-Instruct via Hugging Face Inference API.
     *
     * @param array $messages Conversation messages (with 'role' and 'content')
     * @param array $options Optional generation parameters
     * @return string|null The generated response, or null on failure
     */
    public function generateChatResponse(array $messages, array $options = []): ?string
    {
        if (empty($this->apiKey)) {
            Log::error('[LlamaChatService] Hugging Face API token not set in environment');
            return null;
        }

        try {
            // Use Hugging Face Inference API with OpenAI-compatible format
            $response = Http::timeout($this->timeout)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->post("{$this->apiUrl}/chat/completions", [
                    'model' => 'meta-llama/Llama-3.2-3B-Instruct:novita',
                    'messages' => $messages,
                    'max_tokens' => $options['max_tokens'] ?? 256,
                    'temperature' => $options['temperature'] ?? 0.7,
                    'top_p' => $options['top_p'] ?? 0.95,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                
                // Extract response from OpenAI-compatible format
                if (isset($data['choices'][0]['message']['content'])) {
                    $reply = trim($data['choices'][0]['message']['content']);
                    if (!empty($reply)) {
                        // Log successful model call to confirm correct model is used
                        Log::info('[LlamaChatService] Successfully called meta-llama/Llama-3.2-3B-Instruct:novita model');
                        return $reply;
                    }
                } else {
                    Log::error('[LlamaChatService] Unexpected response structure from Llama API', [
                        'model' => 'meta-llama/Llama-3.2-3B-Instruct:novita',
                        'response_keys' => array_keys($data ?? []),
                    ]);
                }
            } else {
                Log::error('[LlamaChatService] API error calling meta-llama/Llama-3.2-3B-Instruct:novita', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
            }

            return null;
        } catch (\Exception $e) {
            Log::error('[LlamaChatService] Exception calling meta-llama/Llama-3.2-3B-Instruct:novita model: ' . $e->getMessage());
            return null;
        }
    }
}
