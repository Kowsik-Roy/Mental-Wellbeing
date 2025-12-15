
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Today's Journal - {{ \Carbon\Carbon::today()->format('M d, Y') }}</title>

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
</head>

<body class="bg-gradient-to-br from-gray-50 to-green-50 min-h-screen py-8">

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
                    <span class="text-green-600">📘</span>
                    Daily Journal
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
                        Edit Today's Entry
                    @else
                        <span class="text-green-600">✨</span>
                        Create Today's Entry
                    @endif
                </h2>
                <p class="text-gray-600 text-sm mt-1">
                    @if($entry)
                        You already wrote today. Feel free to update it.
                    @else
                        Start writing your thoughts for today...
                    @endif
                </p>
            </div>

            <div class="p-6">
                @if($entry)
                    <!-- EDIT TODAY'S ENTRY -->
                    <form action="{{ route('journal.update', $entry->id) }}" method="POST" id="journalForm">
                        @csrf
                        @method('PUT')

                        <!-- Mood Selection -->
                        <div class="mb-6">
                            <label class="block text-gray-700 text-sm font-medium mb-3">
                                <span class="text-lg">😊</span> How are you feeling today?
                            </label>
                            <div class="grid grid-cols-4 sm:grid-cols-7 gap-2">
                                @foreach(App\Models\Journal::MOODS as $key => $label)
                                    <label class="mood-option cursor-pointer p-2 rounded-lg text-center 
                                        {{ $entry->mood == $key ? 'selected' : 'bg-gray-50 hover:bg-gray-100' }}">
                                        <input type="radio" name="mood" value="{{ $key }}" 
                                            class="hidden" 
                                            {{ $entry->mood == $key ? 'checked' : '' }}>
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
                            placeholder="What's on your mind today?"
                            autofocus
                        >{{ $entry->content }}</textarea>

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

                        <!-- Action Buttons -->
                        <div class="flex flex-col sm:flex-row gap-4 mt-8">
                            <!-- Update Button -->
                            <button 
                                type="submit" 
                                class="btn-secondary flex-1 text-white py-3 px-6 rounded-xl font-semibold text-lg transition duration-200 shadow-md hover:shadow-lg"
                            >
                                <span>💾</span>
                                Update Entry
                            </button>

                            <!-- Delete Button -->
                            <button 
                                type="button"
                                onclick="confirmDelete('{{ route('journal.destroy', $entry->id) }}')"
                                class="btn-danger flex-1 text-white py-3 px-6 rounded-xl font-semibold text-lg transition duration-200 shadow-md hover:shadow-lg"
                            >
                                <span>🗑️</span>
                                Delete Entry
                            </button>
                        </div>
                    </form>

                    <!-- Delete Form -->
                    <form action="{{ route('journal.destroy', $entry->id) }}" method="POST" id="deleteForm" class="hidden">
                        @csrf
                        @method('DELETE')
                    </form>

                @else
                    <!-- CREATE NEW ENTRY -->
                    <form action="{{ route('journal.store') }}" method="POST" id="journalForm">
                        @csrf

                        <!-- Mood Selection -->
                        <div class="mb-6">
                            <label class="block text-gray-700 text-sm font-medium mb-3">
                                <span class="text-lg">😊</span> How are you feeling today?
                            </label>
                            <div class="grid grid-cols-4 sm:grid-cols-7 gap-2">
                                @foreach(App\Models\Journal::MOODS as $key => $label)
                                    <label class="mood-option cursor-pointer p-2 rounded-lg text-center bg-gray-50 hover:bg-gray-100">
                                        <input type="radio" name="mood" value="{{ $key }}" 
                                            class="hidden">
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
                        // Array of writing prompts
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
                        
                        $dayOfMonth = date('j');
                        $promptIndex = ($dayOfMonth - 1) % count($prompts);
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
        <div class="mt-8">
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
                            {{ date('l') }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Week Number</span>
                        <span class="font-semibold text-gray-800">
                            Week {{ date('W') }}
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

    <!-- JavaScript for mood selection and delete confirmation -->
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

        // Delete confirmation
        function confirmDelete(url) {
            if (confirm('Are you sure you want to delete today\'s journal entry? This action cannot be undone.')) {
                // Create a form and submit it
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
        }
    </script>

</body>
</html>