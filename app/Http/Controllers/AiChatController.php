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


        // ✅ NEW: Use OpenAI Responses API
        $response = Http::withToken(env('OPENAI_API_KEY'))
            ->timeout(30)
            ->post('https://api.openai.com/v1/responses', [
                'model' => env('OPENAI_MODEL', 'gpt-4.1-mini'),
                'input' => array_merge(
                    [
                        ['role' => 'system', 'content' => $systemPromptText],
                    ],
                    $messages
                ),
                'temperature' => 0.7,
            ]);

        if (!$response->successful()) {
            // Optional: log the error for debugging
            \Log::error('OpenAI API error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            $messages[] = [
                'role' => 'assistant',
                'content' => "I’m here with you, but I’m having trouble responding right now 💛",
            ];

            session(['ai_chat_messages' => $messages]);
            return back();
        }

        // ✅ NEW: Responses API returns output_text
        $reply = $response->json('output_text');

        if (!$reply) {
            $reply = "I’m really glad you shared that 💛 Want to tell me a little more about what’s been weighing on you?";
        }

        $messages[] = ['role' => 'assistant', 'content' => $reply];

        // Save updated conversation
        session(['ai_chat_messages' => $messages]);

        return back();
    }
}
