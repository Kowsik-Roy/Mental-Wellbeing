@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-800">💬 AI Support Chat</h1>
        <a href="{{ route('dashboard') }}" class="text-sm text-gray-600 hover:text-gray-900">
            ← Back
        </a>
    </div>

    {{-- Chat container --}}
    <div class="bg-white/90 backdrop-blur rounded-3xl border border-white shadow-lg p-6 space-y-4">

        {{-- Messages --}}
        <div class="space-y-3 max-h-[55vh] overflow-y-auto pr-2">
            @foreach($messages as $m)
                @if($m['role'] === 'user')
                    <div class="flex justify-end">
                        <div class="max-w-[80%] bg-indigo-600 text-white px-4 py-3 rounded-2xl shadow-sm text-sm">
                            {{ $m['content'] }}
                        </div>
                    </div>
                @else
                    <div class="flex justify-start">
                        <div class="max-w-[80%] bg-amber-50 text-gray-800 px-4 py-3 rounded-2xl border border-amber-100 shadow-sm text-sm">
                            {{ $m['content'] }}
                        </div>
                    </div>
                @endif
            @endforeach
        </div>

        {{-- Quick buttons (cozy shortcuts) --}}
        <div class="flex flex-wrap gap-2 pt-1">
            <button type="button"
                onclick="document.getElementById('chatMessage').value='I feel anxious and overwhelmed.'; document.getElementById('chatMessage').focus();"
                class="px-3 py-2 rounded-full bg-indigo-50 text-indigo-700 text-xs font-medium hover:bg-indigo-100">
                🌿 Feeling anxious
            </button>

            <button type="button"
                onclick="document.getElementById('chatMessage').value='I feel sad and lonely.'; document.getElementById('chatMessage').focus();"
                class="px-3 py-2 rounded-full bg-purple-50 text-purple-700 text-xs font-medium hover:bg-purple-100">
                💛 Feeling lonely
            </button>

            <button type="button"
                onclick="document.getElementById('chatMessage').value='I had a hard day and I need comfort.'; document.getElementById('chatMessage').focus();"
                class="px-3 py-2 rounded-full bg-rose-50 text-rose-700 text-xs font-medium hover:bg-rose-100">
                ✨ Hard day
            </button>

            <button type="button"
                onclick="document.getElementById('chatMessage').value='Can you guide me through a short breathing exercise?'; document.getElementById('chatMessage').focus();"
                class="px-3 py-2 rounded-full bg-emerald-50 text-emerald-700 text-xs font-medium hover:bg-emerald-100">
                🌬️ Breathing help
            </button>
        </div>

        {{-- Input --}}
        <form method="POST" action="{{ route('ai.chat.message') }}" class="flex gap-3 pt-2">
            @csrf
            <input
                id="chatMessage"
                name="message"
                required
                maxlength="2000"
                class="flex-1 rounded-full border-gray-200 focus:border-indigo-400 focus:ring-indigo-400 px-4 py-3 text-sm"
                placeholder="Tell me how you’re feeling…"
                autocomplete="off"
            />
            <button type="submit"
                class="px-5 py-3 rounded-full bg-indigo-700 text-white text-sm font-medium hover:bg-indigo-800 shadow-md">
                Send →
            </button>
        </form>

        {{-- Gentle note --}}
        <p class="text-xs text-gray-500 leading-relaxed">
            This chat offers supportive tips, not medical advice. If you’re in immediate danger, please contact local emergency services or a trusted person right now.
        </p>
    </div>
</div>
@endsection
