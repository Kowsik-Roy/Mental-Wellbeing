@extends('layouts.app')

@section('title', "Edit Journal - " . \Carbon\Carbon::parse($journal->created_at)->format('M d, Y'))

@push('styles')
<style>
    /* Modern notebook paper with subtle grid (match today page style) */
    .paper-container {
        background-image:
            linear-gradient(to right, #f0f0f0 1px, transparent 1px),
            linear-gradient(to bottom, #f0f0f0 1px, transparent 1px);
        background-size: 40px 40px;
        background-color: #fffef7;
        border-left: 8px solid #10b981;
    }

    .card-shadow {
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1),
                    0 8px 10px -6px rgba(0, 0, 0, 0.05);
    }

    .journal-textarea {
        font-family: 'Georgia', 'Times New Roman', serif;
        letter-spacing: 0.3px;
        line-height: 1.8;
    }

    .btn-primary {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    }

    .btn-secondary {
        background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    }

    .btn-danger {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    }

    .date-badge {
        background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
        color: white;
    }

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
        <!-- Page Title -->
        <div class="text-center flex-1">
            <h1 class="text-3xl font-bold text-gray-800">
                <span class="text-green-600">📝</span>
                Edit Journal Entry
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

    <!-- Date Badge -->
    <div class="flex justify-center mb-6">
        <div class="date-badge px-6 py-2 rounded-full font-semibold shadow-md">
            <span>📅</span>
            {{ \Carbon\Carbon::parse($journal->created_at)->format('l, F d, Y') }}
        </div>
    </div>

    <!-- Mood Selection Container (separate, like today page) -->
    <div class="bg-white rounded-2xl card-shadow mb-6 p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-3">
            <span class="text-lg">😊</span>
            How were you feeling this day?
        </h2>
        <p class="text-gray-500 text-sm mb-4">
            Update the mood that best matches how you felt on this day.
        </p>
        <div class="grid grid-cols-4 sm:grid-cols-7 gap-2">
            @foreach(App\Models\Journal::MOODS as $key => $label)
                <label class="mood-option cursor-pointer p-2 rounded-lg text-center 
                    {{ $journal->mood == $key ? 'selected' : 'bg-gray-50 hover:bg-gray-100' }}">
                    <input type="radio" name="mood" value="{{ $key }}" 
                        class="hidden" form="journalForm"
                        {{ $journal->mood == $key ? 'checked' : '' }}>
                    <div class="mood-emoji mb-1">{{ explode(' ', $label)[0] }}</div>
                    <div class="text-xs text-gray-600 truncate">{{ explode(' ', $label)[1] ?? $label }}</div>
                </label>
            @endforeach
        </div>
    </div>

    <!-- Notebook Card -->
    <div class="bg-white rounded-2xl card-shadow overflow-hidden paper-container">
        <!-- Card Header -->
        <div class="bg-gradient-to-r from-green-50 to-emerald-50 p-6 border-b">
            <h2 class="text-xl font-semibold text-gray-800">
                <span class="text-green-600">📖</span>
                Your Journal Content
            </h2>
            <p class="text-gray-600 text-sm mt-1">Edit your thoughts and memories from this day.</p>
        </div>

        <!-- Form -->
        <div class="p-6">
            <form action="{{ route('journal.update', $journal->id) }}" method="POST" id="journalForm">
                @csrf
                @method('PUT')

                <!-- Text Area -->
                <textarea 
                    name="content" 
                    rows="15"
                    class="w-full journal-textarea bg-transparent outline-none p-4 text-gray-800 text-lg resize-none"
                    placeholder="Write your thoughts here..."
                    autofocus
                >{{ $journal->content }}</textarea>

                <!-- Character and Word Count (PHP calculated) -->
                <div class="flex justify-between mt-2 text-sm text-gray-500">
                    <div>
                        @php
                            $contentLength = strlen($journal->content);
                            $wordCount = str_word_count($journal->content);
                        @endphp
                        <span id="charCount">{{ $contentLength }}</span> characters • 
                        <span id="wordCount">{{ $wordCount }}</span> words
                    </div>
                    <div>
                        Last updated: {{ $journal->updated_at->diffForHumans() }}
                    </div>
                </div>

                <!-- Emotional Reflection -->
                @if($journal->emotional_reflection)
                    <div class="mt-6 p-4 rounded-2xl bg-indigo-50 border border-indigo-200">
                        <div class="flex items-start gap-3">
                            <div class="flex-shrink-0 mt-0.5">
                                <span class="text-2xl">💭</span>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-sm font-semibold text-indigo-900 mb-1">Emotional Reflection</h3>
                                <p class="text-sm text-indigo-800 leading-relaxed">
                                    {{ $journal->emotional_reflection }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-4 mt-8">
                    <!-- Update Button -->
                    <button 
                        type="submit" 
                        class="btn-primary flex-1 text-white py-3 px-6 rounded-xl font-semibold text-lg transition duration-200 shadow-md hover:shadow-lg"
                    >
                        <span>💾</span>
                        Update Journal
                    </button>

                    <!-- Cancel Button -->
                    <a 
                        href="{{ route('journal.history') }}" 
                        class="btn-secondary flex-1 text-white py-3 px-6 rounded-xl font-semibold text-lg text-center transition duration-200 shadow-md hover:shadow-lg"
                    >
                        <span>❌</span>
                        Cancel
                    </a>
                </div>
            </form>

            <!-- Delete Form -->
            <div class="mt-6">
                <form action="{{ route('journal.destroy', $journal->id) }}" method="POST">
                    @csrf
                    @method('DELETE')

                    <button 
                        type="button" 
                        onclick="showConfirmModal('Delete Journal Entry', 'Are you sure you want to delete this journal entry? This action cannot be undone.', function() { document.querySelector('form[action=\'{{ route('journal.destroy', $journal->id) }}\']').submit(); })"
                        class="btn-danger w-full text-white py-3 px-6 rounded-xl font-semibold text-lg transition duration-200 shadow-md hover:shadow-lg"
                    >
                        <span>🗑️</span>
                        Delete Journal Entry
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="mt-8 mb-4">
        <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
            <div class="flex items-center">
                <div class="bg-blue-100 p-3 rounded-lg mr-4">
                    <span class="text-blue-600 text-xl">📝</span>
                </div>
                <div>
                    <p class="text-gray-600 text-sm">Entry Details</p>
                    <p class="text-lg font-semibold text-gray-800">
                        Created {{ $journal->created_at->format('M d, Y h:i A') }}
                        @if($journal->mood)
                            <br>
                            <span class="text-sm font-normal text-gray-600">
                                Mood: {{ $journal->mood_with_emoji }}
                            </span>
                        @endif
                    </p>
                </div>
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
</script>
@endpush