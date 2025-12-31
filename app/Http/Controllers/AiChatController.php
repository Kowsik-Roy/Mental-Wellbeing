<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AiChatController extends Controller
{
    public function index()
    {
        $messages = session('ai_chat_messages', [
            ['role' => 'assistant', 'content' => "Hi 💛 I’m here to listen. How are you feeling right now?"],
        ]);

        return view('ai.chat', compact('messages'));
    }

    public function message(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:2000',
        ]);

        // Load existing conversation from session (keep context)
        $messages = session('ai_chat_messages', [
            ['role' => 'assistant', 'content' => "Hi 💛 I’m here to listen. How are you feeling right now?"],
        ]);

        // Add user's new message
        $messages[] = ['role' => 'user', 'content' => trim($request->message)];

        // Safety-first system prompt (no demotivating remarks, no negative feedback)
        $systemPromptText =
"You're a cozy, soothing mental wellbeing companion.\n".
"Tone & style:\n".
"- Speak warmly, gently, and calmly. Use simple language.\n".
"- Never judge, scold, or criticize. Never be harsh.\n".
"- Avoid negativity; focus on hope, comfort, and small doable steps.\n".
"- Use 1–2 gentle emojis max (💛🌿✨) and only if it feels natural.\n".
"- Keep responses short (4–8 sentences).\n".
"Conversation:\n".
"- First: reflect the feeling in one sentence (validation).\n".
"- Then: offer 1–3 small coping ideas.\n".
"- Then: ask ONE soft follow-up question.\n".
"Tips allowed:\n".
"- breathing (e.g., 4-4-6), grounding (5-4-3-2-1), tiny actions, journaling prompts.\n".
"Safety:\n".
"- If user mentions self-harm/suicide or immediate danger: respond with empathy and urge contacting local emergency services or a trusted person immediately.\n".
"- Do not provide instructions for self-harm.\n";


        // Use Google Gemini API
        $geminiApiKey = env('GEMINI_API_KEY', 'AIzaSyBY4dIq90Ylu8UD2jRVeiVGrDjWxrh79Lw');
        $model = env('GEMINI_MODEL', 'gemini-2.5-flash');
        
        if (empty($geminiApiKey)) {
            \Log::error('Gemini API key not set');
            $messages[] = [
                'role' => 'assistant',
                'content' => "I'm here with you, but I'm having trouble responding right now 💛",
            ];
            session(['ai_chat_messages' => $messages]);
            return back();
        }

        // Convert messages to Gemini format
        // Gemini uses contents array with alternating user/assistant messages
        $geminiContents = [];
        
        // Add system instruction as system instruction (if supported) or first user message
        // For now, we'll prepend it to the conversation
        $conversationHistory = [];
        
        // Add system prompt as the first instruction
        $fullPrompt = $systemPromptText . "\n\nPlease respond as the assistant in this conversation:\n\n";
        
        // Convert messages to text format for Gemini
        foreach ($messages as $msg) {
            if ($msg['role'] === 'user') {
                $fullPrompt .= "User: " . $msg['content'] . "\n";
            } elseif ($msg['role'] === 'assistant') {
                $fullPrompt .= "Assistant: " . $msg['content'] . "\n";
            }
        }
        
        $fullPrompt .= "Assistant:";
        
        // Use Gemini generateContent API
        $response = Http::timeout(30)
            ->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$geminiApiKey}", [
                'contents' => [
                    [
                        'parts' => [
                            [
                                'text' => $fullPrompt
                            ]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.7,
                    'topK' => 40,
                    'topP' => 0.95,
                    'maxOutputTokens' => 1024,
                ],
            ]);

        if (!$response->successful()) {
            // Log the error for debugging
            \Log::error('Gemini API error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            $messages[] = [
                'role' => 'assistant',
                'content' => "I'm here with you, but I'm having trouble responding right now 💛",
            ];

            session(['ai_chat_messages' => $messages]);
            return back();
        }

        // Extract response from Gemini API
        $responseData = $response->json();
        
        // Handle Gemini API response structure
        if (isset($responseData['candidates'][0]['content']['parts'][0]['text'])) {
            $reply = $responseData['candidates'][0]['content']['parts'][0]['text'];
        } elseif (isset($responseData['candidates'][0]['content']['parts'][0])) {
            // Fallback if structure is slightly different
            $reply = $responseData['candidates'][0]['content']['parts'][0]['text'] ?? null;
        } else {
            $reply = null;
        }

        if (!$reply || trim($reply) === '') {
            \Log::warning('Gemini API returned empty response', ['response' => $responseData]);
            $reply = "I'm really glad you shared that 💛 Want to tell me a little more about what's been weighing on you?";
        }

        $messages[] = ['role' => 'assistant', 'content' => $reply];

        // Save updated conversation
        session(['ai_chat_messages' => $messages]);

        return back();
    }
}
