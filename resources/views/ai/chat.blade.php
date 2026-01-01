@extends('layouts.app')

@section('title', 'AI Support Chat')

@push('styles')
<style>
    /* Pink and purple pastel background */
    .chat-container {
        background: linear-gradient(135deg, #fdf2f8 0%, #fce7f3 25%, #f3e8ff 50%, #e9d5ff 75%, #ddd6fe 100%);
        min-height: calc(100vh - 200px);
        padding: 2rem 0;
    }

    /* Chat card matching dashboard style - rounded-3xl */
    .chat-card {
        background: linear-gradient(135deg, #ffffff 0%, #fef7ff 100%);
        border: 2px solid rgba(236, 72, 153, 0.2);
        box-shadow: 0 10px 25px -5px rgba(236, 72, 153, 0.1),
                    0 8px 10px -6px rgba(139, 92, 246, 0.05);
    }

    /* User message bubble - pink and purple pastel mix */
    .user-message {
        background: linear-gradient(135deg, #f9a8d4 0%, #c084fc 100%);
        color: white;
        box-shadow: 0 4px 12px rgba(236, 72, 153, 0.25);
        border-radius: 1.5rem 1.5rem 0.5rem 1.5rem;
    }

    /* AI message bubble - soft pink and purple pastel */
    .ai-message {
        background: linear-gradient(135deg, #fce7f3 0%, #f3e8ff 100%);
        color: #831843;
        border: 2px solid rgba(251, 113, 133, 0.3);
        box-shadow: 0 2px 8px rgba(236, 72, 153, 0.1);
        border-radius: 1.5rem 1.5rem 1.5rem 0.5rem;
    }

    /* Quick action buttons - pink and purple pastel mix */
    .quick-btn {
        background: linear-gradient(135deg, #fdf2f8 0%, #f3e8ff 100%);
        border: 2px solid rgba(251, 113, 133, 0.3);
        color: #831843;
        transition: all 0.3s ease;
        box-shadow: 0 2px 6px rgba(236, 72, 153, 0.08);
    }

    .quick-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(236, 72, 153, 0.15);
        background: linear-gradient(135deg, #fce7f3 0%, #e9d5ff 100%);
        border-color: rgba(251, 113, 133, 0.5);
    }

    /* Input field - pink and purple pastel */
    .chat-input {
        background: linear-gradient(135deg, #ffffff 0%, #fef7ff 100%);
        border: 2px solid rgba(251, 113, 133, 0.3);
        color: #701a75;
        transition: all 0.3s ease;
    }

    .chat-input:focus {
        border-color: rgba(236, 72, 153, 0.5);
        box-shadow: 0 0 0 3px rgba(236, 72, 153, 0.1);
        background: #ffffff;
        outline: none;
    }

    .chat-input::placeholder {
        color: #f9a8d4;
    }

    /* Send button - pink and purple pastel mix */
    .send-btn {
        background: linear-gradient(135deg, #f472b6 0%, #a78bfa 100%);
        box-shadow: 0 4px 12px rgba(236, 72, 153, 0.3);
        transition: all 0.3s ease;
        color: white;
    }

    .send-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(236, 72, 153, 0.4);
        background: linear-gradient(135deg, #ec4899 0%, #8b5cf6 100%);
    }

    /* Scrollbar styling - pink and purple pastel */
    .messages-container::-webkit-scrollbar {
        width: 8px;
    }

    .messages-container::-webkit-scrollbar-track {
        background: rgba(251, 113, 133, 0.1);
        border-radius: 10px;
    }

    .messages-container::-webkit-scrollbar-thumb {
        background: linear-gradient(135deg, #f9a8d4 0%, #c084fc 100%);
        border-radius: 10px;
    }

    .messages-container::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(135deg, #f472b6 0%, #a78bfa 100%);
    }

    /* Typing indicator - pink and purple pastel */
    .typing-indicator {
        display: inline-flex;
        gap: 4px;
        padding: 8px 12px;
    }

    .typing-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #f472b6;
        animation: typing 1.4s infinite ease-in-out;
    }

    .typing-dot:nth-child(2) {
        animation-delay: 0.2s;
    }

    .typing-dot:nth-child(3) {
        animation-delay: 0.4s;
    }

    @keyframes typing {
        0%, 60%, 100% {
            transform: translateY(0);
            opacity: 0.7;
        }
        30% {
            transform: translateY(-10px);
            opacity: 1;
        }
    }

    /* User avatar - pink and purple pastel mix */
    .user-avatar {
        background: linear-gradient(135deg, #f9a8d4 0%, #c084fc 100%);
        color: white;
    }

    /* AI avatar - soft pink and purple pastel */
    .ai-avatar {
        background: linear-gradient(135deg, #fce7f3 0%, #e9d5ff 100%);
        color: #831843;
    }
</style>
@endpush

@section('content')
<div class="chat-container">
    <div class="w-full mx-auto px-6">
        
        {{-- Header --}}
        <div class="flex items-center justify-center mb-8">
            <div class="text-center">
                <h1 class="text-4xl md:text-5xl font-bold mb-2 bg-gradient-to-r from-pink-400 via-purple-500 to-pink-400 bg-clip-text text-transparent">
                    AI Support Chat
                </h1>
                <p class="text-gray-600 text-lg">A safe space to share what's on your mind</p>
            </div>
        </div>

        {{-- Chat container --}}
        <div class="chat-card rounded-3xl p-8 space-y-6 relative overflow-hidden">

            {{-- Messages --}}
            <div class="messages-container space-y-4 max-h-[50vh] overflow-y-auto pr-3 relative z-10">
                @foreach($messages as $index => $m)
                    @if($m['role'] === 'user')
                        <div class="flex justify-end items-start gap-3">
                            <div class="user-message max-w-[85%] px-5 py-4 text-sm leading-relaxed">
                                {{ $m['content'] }}
                            </div>
                            <div class="w-8 h-8 rounded-full user-avatar flex items-center justify-center text-white font-semibold text-sm shadow-md">
                                You
                            </div>
                        </div>
                    @else
                        <div class="flex justify-start items-start gap-3">
                            <div class="w-10 h-10 rounded-full ai-avatar flex items-center justify-center font-semibold text-sm shadow-md">
                                AI
                            </div>
                            <div class="ai-message max-w-[85%] px-5 py-4 text-sm leading-relaxed">
                                {{ $m['content'] }}
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>

            {{-- Quick action buttons --}}
            <div class="flex flex-wrap gap-3 pt-2 relative z-10">
                <button type="button"
                    onclick="document.getElementById('chatMessage').value='I feel anxious and overwhelmed.'; document.getElementById('chatMessage').focus();"
                    class="quick-btn px-4 py-2.5 rounded-full text-sm font-medium">
                    Feeling anxious
                </button>

                <button type="button"
                    onclick="document.getElementById('chatMessage').value='I feel sad and lonely.'; document.getElementById('chatMessage').focus();"
                    class="quick-btn px-4 py-2.5 rounded-full text-sm font-medium">
                    Feeling lonely
                </button>

                <button type="button"
                    onclick="document.getElementById('chatMessage').value='I had a hard day and I need comfort.'; document.getElementById('chatMessage').focus();"
                    class="quick-btn px-4 py-2.5 rounded-full text-sm font-medium">
                    Hard day
                </button>

                <button type="button"
                    onclick="document.getElementById('chatMessage').value='Can you guide me through a short breathing exercise?'; document.getElementById('chatMessage').focus();"
                    class="quick-btn px-4 py-2.5 rounded-full text-sm font-medium">
                    Breathing help
                </button>

                <button type="button"
                    onclick="document.getElementById('chatMessage').value='I need some encouragement today.'; document.getElementById('chatMessage').focus();"
                    class="quick-btn px-4 py-2.5 rounded-full text-sm font-medium">
                    Need encouragement
                </button>
            </div>

            {{-- Input form --}}
            <form method="POST" action="{{ route('ai.chat.message') }}" class="flex gap-3 pt-2 relative z-10" id="chatForm">
                @csrf
                <input
                    id="chatMessage"
                    name="message"
                    required
                    maxlength="2000"
                    class="chat-input flex-1 rounded-full px-5 py-4 text-sm focus:outline-none"
                    placeholder="Tell me how you're feeling…"
                    autocomplete="off"
                />
                <button type="submit"
                    class="send-btn px-6 py-4 rounded-full text-white text-sm font-semibold hover:shadow-lg transition">
                    Send
                </button>
            </form>

            {{-- Gentle note --}}
            <div class="rounded-2xl bg-gradient-to-br from-pink-50 to-purple-50 border-2 border-pink-200 p-4 relative z-10">
                <p class="text-xs text-gray-700 leading-relaxed">
                    This chat offers supportive tips, not medical advice. If you're in immediate danger, please contact local emergency services or a trusted person right now.
                </p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('chatForm');
    const messagesContainer = document.querySelector('.messages-container');
    const chatInput = document.getElementById('chatMessage');
    
    // Auto-scroll to bottom on load
    messagesContainer.scrollTop = messagesContainer.scrollHeight;
    
    // Smooth scroll to bottom when new message is added
    const observer = new MutationObserver(() => {
        messagesContainer.scrollTo({
            top: messagesContainer.scrollHeight,
            behavior: 'smooth'
        });
    });
    
    observer.observe(messagesContainer, { childList: true });
    
    // Focus input on load
    chatInput.focus();
    
    // Handle form submission
    form.addEventListener('submit', function(e) {
        const message = chatInput.value.trim();
        if (!message) {
            e.preventDefault();
            return;
        }
        
        // Add a subtle loading state (optional)
        const submitBtn = form.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.textContent = 'Sending...';
    });
    
    // Auto-resize textarea if needed (for future enhancement)
    chatInput.addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = (this.scrollHeight) + 'px';
    });
});
</script>
@endpush
@endsection
