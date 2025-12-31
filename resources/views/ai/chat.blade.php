@extends('layouts.app')

@section('title', 'AI Support Chat')

@push('styles')
<style>
    /* Cozy gradient background */
    .chat-container {
        background: linear-gradient(135deg, #fef3f2 0%, #fdf2f8 25%, #f3e8ff 50%, #e0e7ff 75%, #dbeafe 100%);
        min-height: calc(100vh - 200px);
        padding: 2rem 0;
    }

    /* Cozy chat card */
    .chat-card {
        background: linear-gradient(135deg, #ffffff 0%, #fef7ff 100%);
        border: 2px solid rgba(251, 146, 60, 0.2);
        box-shadow: 0 20px 60px -15px rgba(251, 146, 60, 0.3),
                    0 10px 25px -5px rgba(139, 92, 246, 0.2);
    }

    /* Floating particles */
    .floating-particles {
        position: absolute;
        width: 100%;
        height: 100%;
        overflow: hidden;
        pointer-events: none;
        border-radius: 1.5rem;
    }

    .particle {
        position: absolute;
        width: 6px;
        height: 6px;
        background: radial-gradient(circle, rgba(251, 191, 36, 0.4), rgba(236, 72, 153, 0.2));
        border-radius: 50%;
        animation: float 20s infinite ease-in-out;
    }

    @keyframes float {
        0%, 100% {
            transform: translateY(0) translateX(0) rotate(0deg);
            opacity: 0.2;
        }
        25% {
            transform: translateY(-30px) translateX(15px) rotate(90deg);
            opacity: 0.5;
        }
        50% {
            transform: translateY(-60px) translateX(-15px) rotate(180deg);
            opacity: 0.7;
        }
        75% {
            transform: translateY(-30px) translateX(10px) rotate(270deg);
            opacity: 0.5;
        }
    }

    /* User message bubble */
    .user-message {
        background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
        color: white;
        box-shadow: 0 4px 15px rgba(251, 191, 36, 0.3);
        border-radius: 1.5rem 1.5rem 0.5rem 1.5rem;
    }

    /* AI message bubble */
    .ai-message {
        background: linear-gradient(135deg, #fdf2f8 0%, #f3e8ff 100%);
        color: #7c3aed;
        border: 2px solid rgba(251, 113, 133, 0.3);
        box-shadow: 0 4px 15px rgba(236, 72, 153, 0.15);
        border-radius: 1.5rem 1.5rem 1.5rem 0.5rem;
    }

    /* Quick action buttons */
    .quick-btn {
        background: linear-gradient(135deg, #fef3f2 0%, #fdf2f8 100%);
        border: 2px solid rgba(251, 146, 60, 0.2);
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(251, 146, 60, 0.1);
    }

    .quick-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(251, 146, 60, 0.2);
        background: linear-gradient(135deg, #fef3f2 0%, #fdf2f8 100%);
        border-color: rgba(251, 146, 60, 0.4);
    }

    /* Input field */
    .chat-input {
        background: linear-gradient(135deg, #ffffff 0%, #fef7ff 100%);
        border: 2px solid rgba(251, 146, 60, 0.2);
        transition: all 0.3s ease;
    }

    .chat-input:focus {
        border-color: rgba(251, 146, 60, 0.5);
        box-shadow: 0 0 0 3px rgba(251, 146, 60, 0.1);
        background: #ffffff;
    }

    /* Send button */
    .send-btn {
        background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
        box-shadow: 0 4px 15px rgba(251, 191, 36, 0.4);
        transition: all 0.3s ease;
    }

    .send-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(251, 191, 36, 0.5);
        background: linear-gradient(135deg, #fcd34d 0%, #fbbf24 100%);
    }

    /* Scrollbar styling */
    .messages-container::-webkit-scrollbar {
        width: 8px;
    }

    .messages-container::-webkit-scrollbar-track {
        background: rgba(251, 146, 60, 0.1);
        border-radius: 10px;
    }

    .messages-container::-webkit-scrollbar-thumb {
        background: linear-gradient(135deg, #fbbf24 0%, #fb7185 100%);
        border-radius: 10px;
    }

    .messages-container::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(135deg, #fcd34d 0%, #fda4af 100%);
    }

    /* Typing indicator */
    .typing-indicator {
        display: inline-flex;
        gap: 4px;
        padding: 8px 12px;
    }

    .typing-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #fb7185;
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
</style>
@endpush

@section('content')
<div class="chat-container">
    <div class="max-w-4xl mx-auto px-4">
        
        {{-- Header --}}
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-4xl md:text-5xl font-bold mb-2 bg-gradient-to-r from-orange-400 via-pink-500 to-purple-600 bg-clip-text text-transparent">
                    💬 AI Support Chat
                </h1>
                <p class="text-gray-600 text-lg">A safe space to share what's on your mind</p>
            </div>
            <a href="{{ route('dashboard') }}" 
               class="inline-flex items-center gap-2 px-4 py-2 rounded-full font-medium bg-gradient-to-r from-indigo-500 to-purple-600 shadow-lg text-white hover:scale-105 transition transform">
                <span>🏠</span>
                <span>Dashboard</span>
            </a>
        </div>

        {{-- Chat container --}}
        <div class="chat-card rounded-3xl p-8 space-y-6 relative overflow-hidden">
            
            {{-- Floating particles background --}}
            <div class="floating-particles">
                <div class="particle" style="left: 5%; animation-delay: 0s;"></div>
                <div class="particle" style="left: 20%; animation-delay: 3s;"></div>
                <div class="particle" style="left: 40%; animation-delay: 6s;"></div>
                <div class="particle" style="left: 60%; animation-delay: 9s;"></div>
                <div class="particle" style="left: 80%; animation-delay: 12s;"></div>
                <div class="particle" style="left: 95%; animation-delay: 15s;"></div>
            </div>

            {{-- Messages --}}
            <div class="messages-container space-y-4 max-h-[50vh] overflow-y-auto pr-3 relative z-10">
                @foreach($messages as $index => $m)
                    @if($m['role'] === 'user')
                        <div class="flex justify-end items-start gap-3">
                            <div class="user-message max-w-[75%] px-5 py-4 text-sm leading-relaxed">
                                {{ $m['content'] }}
                            </div>
                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-orange-300 to-amber-400 flex items-center justify-center text-white font-semibold text-sm shadow-md">
                                You
                            </div>
                        </div>
                    @else
                        <div class="flex justify-start items-start gap-3">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-pink-300 to-purple-400 flex items-center justify-center text-2xl shadow-md">
                                💛
                            </div>
                            <div class="ai-message max-w-[75%] px-5 py-4 text-sm leading-relaxed">
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
                    class="quick-btn px-4 py-2.5 rounded-full text-sm font-medium text-orange-700">
                    🌿 Feeling anxious
                </button>

                <button type="button"
                    onclick="document.getElementById('chatMessage').value='I feel sad and lonely.'; document.getElementById('chatMessage').focus();"
                    class="quick-btn px-4 py-2.5 rounded-full text-sm font-medium text-pink-700">
                    💛 Feeling lonely
                </button>

                <button type="button"
                    onclick="document.getElementById('chatMessage').value='I had a hard day and I need comfort.'; document.getElementById('chatMessage').focus();"
                    class="quick-btn px-4 py-2.5 rounded-full text-sm font-medium text-purple-700">
                    ✨ Hard day
                </button>

                <button type="button"
                    onclick="document.getElementById('chatMessage').value='Can you guide me through a short breathing exercise?'; document.getElementById('chatMessage').focus();"
                    class="quick-btn px-4 py-2.5 rounded-full text-sm font-medium text-rose-700">
                    🌬️ Breathing help
                </button>

                <button type="button"
                    onclick="document.getElementById('chatMessage').value='I need some encouragement today.'; document.getElementById('chatMessage').focus();"
                    class="quick-btn px-4 py-2.5 rounded-full text-sm font-medium text-indigo-700">
                    💝 Need encouragement
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
                    placeholder="Tell me how you're feeling… 💭"
                    autocomplete="off"
                />
                <button type="submit"
                    class="send-btn px-6 py-4 rounded-full text-white text-sm font-semibold hover:shadow-lg transition">
                    Send ✨
                </button>
            </form>

            {{-- Gentle note --}}
            <div class="rounded-2xl bg-gradient-to-br from-pink-50 to-purple-50 border-2 border-pink-200 p-4 relative z-10">
                <p class="text-xs text-gray-700 leading-relaxed flex items-start gap-2">
                    <span class="text-base">💝</span>
                    <span>
                        This chat offers supportive tips, not medical advice. If you're in immediate danger, please contact local emergency services or a trusted person right now.
                    </span>
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
