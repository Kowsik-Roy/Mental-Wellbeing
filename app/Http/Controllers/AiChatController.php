<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\LlamaChatService;
use Illuminate\Support\Facades\Log;

class AiChatController extends Controller
{
    protected $llamaChatService;

    public function __construct(LlamaChatService $llamaChatService)
    {
        $this->llamaChatService = $llamaChatService;
    }

    public function index()
    {
        $messages = session('ai_chat_messages', [
            ['role' => 'assistant', 'content' => "Hi 💛 I'm here to listen. How are you feeling right now?"],
        ]);

        // Check if service is available
        $serviceAvailable = $this->llamaChatService->isHealthy();

        return view('ai.chat', compact('messages', 'serviceAvailable'));
    }

    public function message(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:2000',
        ]);

        // Load existing conversation from session (keep context)
        $messages = session('ai_chat_messages', [
            ['role' => 'assistant', 'content' => "Hi 💛 I'm here to listen. How are you feeling right now?"],
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

        // Check if service is available
        if (!$this->llamaChatService->isHealthy()) {
            Log::error('[AiChatController] Hugging Face API token not configured - AI chat unavailable');
            $messages[] = [
                'role' => 'assistant',
                'content' => "I'm here with you, but I'm having trouble responding right now 💛 Please configure the Hugging Face API token.",
            ];
            session(['ai_chat_messages' => $messages]);
            return back()->with('error', 'AI service is not available. Please configure HF_TOKEN in your .env file.');
        }

        // Format messages for Llama-3.2-3B-Instruct
        // Llama expects: [{"role": "system", "content": "..."}, {"role": "user", "content": "..."}, {"role": "assistant", "content": "..."}]
        $llamaMessages = [
            ['role' => 'system', 'content' => $systemPromptText]
        ];

        // Add conversation history (skip the first assistant greeting if it's the only message)
        $startIndex = 0;
        if (count($messages) === 1 && $messages[0]['role'] === 'assistant') {
            // Only the initial greeting, skip it
            $startIndex = 1;
        }

        // Add conversation messages
        for ($i = $startIndex; $i < count($messages); $i++) {
            $llamaMessages[] = $messages[$i];
        }

        // Generate response using Llama-3.2-3B-Instruct
        $reply = $this->llamaChatService->generateChatResponse(
            $llamaMessages,
            [
                'max_tokens' => 256,
                'temperature' => 0.7,
                'top_p' => 0.95,
            ]
        );

        if (!$reply || trim($reply) === '') {
            $reply = "I'm really glad you shared that 💛 Want to tell me a little more about what's been weighing on you?";
        }

        $messages[] = ['role' => 'assistant', 'content' => $reply];

        // Save updated conversation
        session(['ai_chat_messages' => $messages]);

        return back();
    }
}
