@extends('layouts.app')

@section('title', "Today's Journal - " . \Carbon\Carbon::today()->format('M d, Y'))

@push('styles')
<style>
    /* Modern notebook paper with subtle grid */
    .paper-container {
        background-image:
            linear-gradient(to right, #f0f0f0 1px, transparent 1px),
            linear-gradient(to bottom, #f0f0f0 1px, transparent 1px);
        background-size: 40px 40px;
        background-color: #fffef7;
        border-left: 8px solid #10b981;
    }

    /* Smooth shadow and border */
    .card-shadow {
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1),
                    0 8px 10px -6px rgba(0, 0, 0, 0.05);
    }

    /* Custom textarea styling */
    .journal-textarea {
        font-family: 'Georgia', 'Times New Roman', serif;
        letter-spacing: 0.3px;
        line-height: 1.8;
    }

    /* Gradient buttons */
    .btn-primary {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    }

    .btn-secondary {
        background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    }

    .btn-danger {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    }

    /* Date badge */
    .date-badge {
        background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
        color: white;
    }

    /* Prompt badge */
    .prompt-badge {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: white;
    }

    /* Mood selection styling */
    .mood-option {
        transition: all 0.2s ease;
        border: 2px solid transparent;
    }

    .mood-option:hover {
        transform: scale(1.05);
        border-color: #3b82f6;
    }

    .mood-option.selected {
        border-color: #10b981;
        background-color: #f0fdf4;
        transform: scale(1.05);
    }

    .mood-emoji {
        font-size: 1.5rem;
    }
</style>
@endpush

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="flex items-center justify-between mb-8">
        <!-- Dashboard Button -->
        <a 
            href="{{ route('dashboard') }}" 
            class="inline-flex items-center gap-2 px-4 py-2 rounded-full font-medium bg-gradient-to-r from-purple-400 to-indigo-500 shadow-lg text-white hover:scale-105 hover:from-purple-300 hover:to-indigo-400 transition transform"
        >
            <i class="fas fa-home"></i>
            <span>Dashboard</span>
        </a>

        <!-- Page Title -->
        <div class="text-center">
            <h1 class="text-3xl font-bold text-gray-800">
                <span class="text-green-600">📘</span>
                Daily Journal
            </h1>
        </div>

        <!-- History Button -->
        <a 
            href="{{ route('journal.history') }}" 
            class="inline-flex items-center gap-2 px-4 py-2 rounded-full font-medium bg-gradient-to-r from-pink-400 to-rose-500 shadow-lg text-white hover:scale-105 hover:from-pink-300 hover:to-rose-400 transition transform"
        >
            <i class="fas fa-history"></i>
            <span>History</span>
        </a>
    </div>

    <!-- Welcome Message -->
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-5 mb-6">
        <div class="flex items-center">
            <div class="bg-blue-100 p-3 rounded-lg mr-4">
                <span class="text-blue-600 text-2xl">✍️</span>
            </div>
            <div>
                <h2 class="text-xl font-bold text-gray-800">Welcome to Your Daily Journal</h2>
                <p class="text-gray-600 mt-1">Reflect on your day, record memories, and track your journey.</p>
            </div>
        </div>
    </div>

    <!-- Date Badge -->
    <div class="flex justify-center mb-6">
        <div class="date-badge px-6 py-2 rounded-full font-semibold shadow-md">
            <span>📅</span>
            {{ \Carbon\Carbon::today()->format('l, F d, Y') }}
        </div>
    </div>

    <!-- Mood Selection Container (separate card when creating) -->
    @if(!$entry)
    <div class="bg-white rounded-2xl card-shadow mb-6 p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-3">
            <span class="text-lg"></span>
            How are you feeling today?
        </h2>
        <p class="text-gray-500 text-sm mb-4">
            Choose a mood that best matches how you feel right now.
        </p>
        <div class="grid grid-cols-4 sm:grid-cols-7 gap-2">
            @foreach(App\Models\Journal::MOODS as $key => $label)
                <label class="mood-option cursor-pointer p-2 rounded-lg text-center bg-gray-50 hover:bg-gray-100">
                    <input type="radio" name="mood" value="{{ $key }}" 
                        class="hidden" form="journalForm">
                    <div class="mood-emoji mb-1">{{ explode(' ', $label)[0] }}</div>
                    <div class="text-xs text-gray-600 truncate">{{ explode(' ', $label)[1] ?? $label }}</div>
                </label>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Success Message -->
    @if(session('success'))
    <div class="bg-gradient-to-r from-green-50 to-emerald-50 border-l-4 border-green-500 p-4 rounded-r-lg mb-6 shadow-sm">
        <div class="flex items-center">
            <span class="text-green-500 text-xl mr-3">✅</span>
            <div>
                <p class="text-green-800 font-medium">{{ session('success') }}</p>
            </div>
        </div>
    </div>
    @endif

    <!-- Notebook Card -->
    <div class="bg-white rounded-2xl card-shadow overflow-hidden paper-container">
        <!-- Card Header -->
        <div class="bg-gradient-to-r from-green-50 to-emerald-50 p-6 border-b">
            <h2 class="text-xl font-semibold text-gray-800">
                @if($entry)
                    <span class="text-blue-600">📝</span>
                    Today's Entry
                @else
                    <span class="text-green-600">✨</span>
                    Create Today's Entry
                @endif
            </h2>
            <p class="text-gray-600 text-sm mt-1">
                @if($entry)
                    Your journal for today has been saved. You can review it here or edit it from the edit page.
                @else
                    Start writing your thoughts for today...
                @endif
            </p>
        </div>

        <div class="p-6">
            @if($entry)
                <!-- VIEW TODAY'S ENTRY (READ-ONLY) -->
                <!-- Mood Display -->
                @if($entry->mood)
                    <div class="mb-6">
                        <label class="block text-gray-700 text-sm font-medium mb-3">
                            <span class="text-lg"></span> Today's Mood
                        </label>
                        @php
                            $moodLabel = App\Models\Journal::MOODS[$entry->mood] ?? $entry->mood;
                            $moodEmoji = explode(' ', $moodLabel)[0] ?? '';
                            $moodText = explode(' ', $moodLabel)[1] ?? $moodLabel;
                        @endphp
                        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-green-50 border border-green-200">
                            <span class="text-xl">{{ $moodEmoji }}</span>
                            <span class="text-sm font-semibold text-gray-800">{{ $moodText }}</span>
                        </div>
                    </div>
                @endif

                <!-- Read-only Text Content -->
                <div class="border border-gray-200 rounded-2xl bg-white/60">
                    <div class="p-4">
                        <p class="journal-textarea text-gray-800 text-lg whitespace-pre-line">
                            {{ $entry->content }}
                        </p>
                    </div>
                </div>

                <!-- Character and Word Count (PHP calculated) -->
                <div class="flex justify-between mt-2 text-sm text-gray-500">
                    <div>
                        @php
                            $contentLength = strlen($entry->content);
                            $wordCount = str_word_count($entry->content);
                        @endphp
                        <span>{{ $contentLength }}</span> characters • 
                        <span>{{ $wordCount }}</span> words
                    </div>
                    <div>
                        Created: {{ $entry->created_at->format('h:i A') }}
                    </div>
                </div>

                <!-- Emotional Reflection -->
                @if($entry->emotional_reflection)
                    <div class="mt-6 p-4 rounded-2xl bg-indigo-50 border border-indigo-200">
                        <div class="flex items-start gap-3">
                            <div class="flex-shrink-0 mt-0.5">
                                <span class="text-2xl">💭</span>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-sm font-semibold text-indigo-900 mb-1">Emotional Reflection</h3>
                                <p class="text-sm text-indigo-800 leading-relaxed">
                                    {{ $entry->emotional_reflection }}
                                </p>
                            </div>
                        </div>
                    </div>
                @else
                    <!-- Show message if reflection is being generated (entry is recent, less than 1 minute old) -->
                    @if($entry->created_at->isAfter(now()->subMinute()) || $entry->updated_at->isAfter(now()->subMinute()))
                        <div class="mt-6 p-4 rounded-2xl bg-gray-50 border border-gray-200">
                            <div class="flex items-center gap-3">
                                <div class="flex-shrink-0">
                                    <span class="text-xl">⏳</span>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm text-gray-600 italic">
                                        Generating your emotional reflection... Please refresh the page in a few seconds.
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif
                @endif

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-4 mt-8">
                    <!-- Edit Button (go to edit page) -->
                    <a 
                        href="{{ route('journal.edit', $entry->id) }}"
                        class="btn-secondary flex-1 text-white py-3 px-6 rounded-xl font-semibold text-lg text-center transition duration-200 shadow-md hover:shadow-lg"
                    >
                        <span></span>
                        Edit Entry
                    </a>

                    <!-- Delete Button -->
                    <button 
                        type="button"
                        onclick="confirmDelete('{{ route('journal.destroy', $entry->id) }}')"
                        class="btn-danger flex-1 text-white py-3 px-6 rounded-xl font-semibold text-lg transition duration-200 shadow-md hover:shadow-lg"
                    >
                        <span></span>
                        Delete Entry
                    </button>
                </div>

            @else
                <!-- CREATE NEW ENTRY -->
                <form action="{{ route('journal.store') }}" method="POST" id="journalForm">
                    @csrf
                    <!-- Text Area -->
                    <textarea 
                        name="content" 
                        rows="15"
                        class="w-full journal-textarea bg-transparent outline-none p-4 text-gray-800 text-lg resize-none"
                        placeholder="Write about your day, your thoughts, your goals... This is your space to reflect."
                        autofocus
                    ></textarea>

                    <!-- Character Count Placeholder -->
                    <div class="flex justify-end mt-2 text-sm text-gray-500">
                        <span>Start typing to see character count</span>
                    </div>

                    <!-- Save Button -->
                    <button 
                        type="submit" 
                        class="btn-primary w-full text-white py-3 px-6 rounded-xl font-semibold text-lg mt-8 transition duration-200 shadow-md hover:shadow-lg"
                    >
                        <span>💾</span>
                        Save Today's Journal
                    </button>
                </form>
            @endif
        </div>
    </div>

    <!-- Daily Writing Prompt -->
    <div class="mt-8">
        <div class="flex items-center justify-between mb-3">
            <h3 class="text-lg font-semibold text-gray-800">
                <span class="text-amber-600">💡</span>
                Daily Writing Prompt
            </h3>
            <span class="prompt-badge text-xs px-3 py-1 rounded-full">Today's Inspiration</span>
        </div>
        <div class="bg-gradient-to-r from-amber-50 to-orange-50 rounded-xl p-5 border border-amber-200">
            <p class="text-gray-800 text-lg font-medium mb-2">
                @php
                    $prompts = [
                        "What are three things you're grateful for today?",
                        "What was the highlight of your day?",
                        "What's something you learned today?",
                        "How are you feeling right now, and why?",
                        "What's a challenge you faced today and how did you handle it?",
                        "What are you looking forward to tomorrow?",
                        "Describe a moment that made you smile today.",
                        "What's something you'd like to remember from today?",
                        "How did you take care of yourself today?",
                        "What's one thing you would do differently if you could relive today?",
                        "What made you proud of yourself today?",
                        "Who made a positive impact on your day and how?",
                        "What's a small win you had today?",
                        "How did you show kindness to someone today?",
                        "What are you currently worried about, and what can you do about it?"
                    ];

                    // Pick a random prompt on each refresh
                    $promptIndex = array_rand($prompts);
                    $todaysPrompt = $prompts[$promptIndex];
                @endphp
                {{ $todaysPrompt }}
            </p>
            <div class="flex justify-between items-center mt-4">
                <p class="text-sm text-gray-600">
                    <span class="font-medium">Tip:</span> Use this prompt as inspiration for your journal entry.
                </p>
                <div class="text-xs text-gray-500">
                    Prompt {{ $promptIndex + 1 }} of {{ count($prompts) }}
                </div>
            </div>
        </div>
    </div>

    <!-- Today's Status -->
    <div class="mt-8 mb-4">
        <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">
                <span class="text-blue-600">📊</span>
                Today's Status
            </h3>
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Journal Entry</span>
                    <span class="font-semibold">
                        @if($entry)
                            <span class="text-green-600">✓ Completed</span>
                        @else
                            <span class="text-amber-600">⏳ Pending</span>
                        @endif
                    </span>
                </div>
                @if($entry && $entry->mood)
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Today's Mood</span>
                    <span class="font-semibold text-gray-800">
                        {{ $entry->mood_with_emoji }}
                    </span>
                </div>
                @endif
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Day of Week</span>
                    <span class="font-semibold text-gray-800">
                        @if($entry)
                            {{ $entry->created_at->format('l') }}
                        @else
                            {{ date('l') }}
                        @endif
                    </span>
                </div>
                @if($entry)
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Time Written</span>
                    <span class="font-semibold text-gray-800">
                        {{ $entry->created_at->format('h:i A') }}
                    </span>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Mood selection
    document.querySelectorAll('.mood-option').forEach(option => {
        option.addEventListener('click', function() {
            document.querySelectorAll('.mood-option').forEach(opt => {
                opt.classList.remove('selected');
                opt.classList.add('bg-gray-50', 'hover:bg-gray-100');
            });

            this.classList.add('selected');
            this.classList.remove('bg-gray-50', 'hover:bg-gray-100');

            const radio = this.querySelector('input[type="radio"]');
            if (radio) {
                radio.checked = true;
            }
        });
    });

    // Delete confirmation
    function confirmDelete(url) {
        showConfirmModal(
            "Delete Journal Entry",
            "Are you sure you want to delete today's journal entry? This action cannot be undone.",
            function() {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = url;

                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                form.appendChild(csrfToken);

                const methodField = document.createElement('input');
                methodField.type = 'hidden';
                methodField.name = '_method';
                methodField.value = 'DELETE';
                form.appendChild(methodField);

                document.body.appendChild(form);
                form.submit();
            }
        );
    }
</script>
@endpush
