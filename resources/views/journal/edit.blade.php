<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Journal - {{ \Carbon\Carbon::parse($journal->created_at)->format('M d, Y') }}</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        /* Modern notebook paper with subtle grid */
        .paper-container {
            background-image: 
                linear-gradient(to right, #f0f0f0 1px, transparent 1px),
                linear-gradient(to bottom, #f0f0f0 1px, transparent 1px);
            background-size: 40px 40px;
            background-color: #fffef7;
            border-left: 8px solid #3b82f6;
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
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
        }

        .btn-danger {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        }

        .btn-secondary {
            background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);
        }

        /* Hover effects */
        .btn-primary:hover {
            background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
        }

        .btn-danger:hover {
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
        }

        .btn-secondary:hover {
            background: linear-gradient(135deg, #4b5563 0%, #374151 100%);
        }

        /* Date badge */
        .date-badge {
            background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
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
</head>

<body class="bg-gradient-to-br from-gray-50 to-blue-50 min-h-screen py-8">

    <div class="max-w-4xl mx-auto px-4">

        <!-- Header -->
        <div class="flex items-center justify-between mb-8">
            <!-- Home Button -->
            <a 
                href="{{ route('dashboard') }}" 
                class="flex items-center gap-2 text-gray-700 hover:text-gray-900 font-medium text-lg transition duration-200"
            >
                <span class="text-xl">🏠</span>
                <span>Dashboard</span>
            </a>

            <!-- Page Title -->
            <div class="text-center">
                <h1 class="text-3xl font-bold text-gray-800">
                    <span class="text-blue-600">📝</span>
                    Edit Journal Entry
                </h1>
            </div>

            <!-- History Button -->
            <a 
                href="{{ route('journal.history') }}" 
                class="flex items-center gap-2 text-gray-700 hover:text-gray-900 font-medium text-lg transition duration-200"
            >
                <span class="text-xl">📅</span>
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
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 p-6 border-b">
                <h2 class="text-xl font-semibold text-gray-800">
                    <span class="text-blue-600">📖</span>
                    Your Journal Content
                </h2>
                <p class="text-gray-600 text-sm mt-1">Edit your thoughts and memories from this day</p>
            </div>

            <!-- Form -->
            <div class="p-6">
                <form action="{{ route('journal.update', $journal->id) }}" method="POST" id="journalForm">
                    @csrf
                    @method('PUT')

                    <!-- Mood Selection -->
                    <div class="mb-6">
                        <label class="block text-gray-700 text-sm font-medium mb-3">
                            <span class="text-lg">😊</span> How were you feeling this day?
                        </label>
                        <div class="grid grid-cols-4 sm:grid-cols-7 gap-2">
                            @foreach(App\Models\Journal::MOODS as $key => $label)
                                <label class="mood-option cursor-pointer p-2 rounded-lg text-center 
                                    {{ $journal->mood == $key ? 'selected' : 'bg-gray-50 hover:bg-gray-100' }}">
                                    <input type="radio" name="mood" value="{{ $key }}" 
                                        class="hidden" 
                                        {{ $journal->mood == $key ? 'checked' : '' }}>
                                    <div class="mood-emoji mb-1">{{ explode(' ', $label)[0] }}</div>
                                    <div class="text-xs text-gray-600 truncate">{{ explode(' ', $label)[1] ?? $label }}</div>
                                </label>
                            @endforeach
                        </div>
                    </div>

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
                            type="submit" 
                            onclick="return confirm('Are you sure you want to delete this journal entry? This action cannot be undone.')"
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
        <div class="mt-8">
            <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100">
                <div class="flex items-center">
                    <div class="bg-blue-100 p-3 rounded-lg mr-4">
                        <span class="text-blue-600 text-xl">📝</span>
                    </div>
                    <div>
                        <p class="text-gray-600 text-sm">Entry Details</p>
                        <p class="text-lg font-semibold text-gray-800">
                            Created {{ $journal->created_at->format('M d, Y') }}
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

        <!-- Navigation Links -->
        <div class="mt-8 flex flex-col sm:flex-row gap-4 justify-center">
            <a 
                href="{{ route('journal.today') }}" 
                class="text-center bg-gradient-to-r from-green-500 to-emerald-600 text-white py-3 px-6 rounded-xl font-semibold text-lg transition duration-200 shadow-md hover:shadow-lg"
            >
                <span>📘</span>
                Go to Today's Journal
            </a>
            
            <a 
                href="{{ route('journal.history') }}" 
                class="text-center bg-gradient-to-r from-purple-500 to-indigo-600 text-white py-3 px-6 rounded-xl font-semibold text-lg transition duration-200 shadow-md hover:shadow-lg"
            >
                <span>📚</span>
                View All Entries
            </a>
        </div>

    </div>

    <!-- JavaScript for mood selection -->
    <script>
        // Mood selection
        document.querySelectorAll('.mood-option').forEach(option => {
            option.addEventListener('click', function() {
                // Remove selected class from all options
                document.querySelectorAll('.mood-option').forEach(opt => {
                    opt.classList.remove('selected');
                    opt.classList.add('bg-gray-50', 'hover:bg-gray-100');
                });
                
                // Add selected class to clicked option
                this.classList.add('selected');
                this.classList.remove('bg-gray-50', 'hover:bg-gray-100');
                
                // Check the radio button
                const radio = this.querySelector('input[type="radio"]');
                if (radio) {
                    radio.checked = true;
                }
            });
        });
    </script>

</body>
</html>