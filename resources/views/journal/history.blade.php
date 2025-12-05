<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Journal History</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        /* Calendar grid styling */
        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
        }

        /* Calendar card styling */
        .calendar-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            border: 1px solid #e5e7eb;
            transition: all 0.3s ease;
            cursor: pointer;
            height: 100%;
        }

        .calendar-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
            border-color: #3b82f6;
        }

        /* Month header styling */
        .month-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 20px;
            border-radius: 10px 10px 0 0;
            margin-bottom: 15px;
            font-weight: 600;
            font-size: 1.25rem;
        }

        /* Day circle styling */
        .day-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            margin-right: 12px;
            flex-shrink: 0;
        }

        /* Color variations for days */
        .day-sun { background-color: #fee2e2; color: #dc2626; }
        .day-mon { background-color: #fef3c7; color: #d97706; }
        .day-tue { background-color: #d1fae5; color: #059669; }
        .day-wed { background-color: #e0e7ff; color: #4f46e5; }
        .day-thu { background-color: #fce7f3; color: #db2777; }
        .day-fri { background-color: #fef9c3; color: #ca8a04; }
        .day-sat { background-color: #f3e8ff; color: #7c3aed; }

        /* Empty state styling */
        .empty-state {
            background: linear-gradient(135deg, #f3e8ff 0%, #e0e7ff 100%);
            border: 2px dashed #a78bfa;
            border-radius: 16px;
            padding: 60px 40px;
            text-align: center;
        }
    </style>
</head>

<body class="bg-gray-50 min-h-screen">

    <div class="max-w-7xl mx-auto px-4 py-8">
        
        <!-- Header with Home button -->
        <div class="flex items-center justify-between mb-8">
            <!-- Home Button -->
            <a 
                href="{{ route('dashboard') }}" 
                class="flex items-center gap-2 text-gray-600 hover:text-gray-800 font-medium text-lg"
            >
                <span class="text-xl">🏠</span>
                <span>Home</span>
            </a>

            <!-- Page Title -->
            <h1 class="text-4xl font-bold text-gray-800">📅 Journal History</h1>
            
            <!-- Today Button -->
            <a 
                href="{{ route('journal.today') }}" 
                class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold text-lg transition duration-200"
            >
                <span>📝 Today's Journal</span>
            </a>
        </div>

        <!-- Stats Summary -->
        <div class="bg-white rounded-xl shadow-md p-6 mb-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="text-center p-4 bg-blue-50 rounded-lg">
                    <div class="text-3xl font-bold text-blue-700">{{ $totalEntries }}</div>
                    <div class="text-gray-600 mt-2">Total Entries</div>
                </div>
                <div class="text-center p-4 bg-green-50 rounded-lg">
                    <div class="text-3xl font-bold text-green-700">
                        @if($entries->count() > 0)
                            @php
                                $currentMonth = now()->format('F Y');
                                $currentMonthEntries = 0;
                                foreach($entries as $month => $monthEntries) {
                                    if($month === $currentMonth) {
                                        $currentMonthEntries = count($monthEntries);
                                        break;
                                    }
                                }
                            @endphp
                            {{ $currentMonthEntries }}
                        @else
                            0
                        @endif
                    </div>
                    <div class="text-gray-600 mt-2">This Month</div>
                </div>
                <div class="text-center p-4 bg-purple-50 rounded-lg">
                    <div class="text-3xl font-bold text-purple-700">
                        @if($entries->count() > 0)
                            @php
                                $firstGroup = $entries->first();
                                $latestEntry = $firstGroup->first();
                            @endphp
                            {{ $latestEntry->created_at->format('M Y') }}
                        @else
                            N/A
                        @endif
                    </div>
                    <div class="text-gray-600 mt-2">Latest Entry</div>
                </div>
            </div>
        </div>

        @if($entries->count() === 0)
            <!-- Empty State -->
            <div class="empty-state">
                <div class="text-6xl mb-4">📭</div>
                <h3 class="text-2xl font-bold text-gray-800 mb-2">No Journal Entries Yet</h3>
                <p class="text-gray-600 mb-6">Start your journaling journey by writing your first entry!</p>
                <a 
                    href="{{ route('journal.today') }}" 
                    class="inline-flex items-center gap-2 bg-purple-600 hover:bg-purple-700 text-white px-6 py-3 rounded-lg font-semibold text-lg"
                >
                    <span>✏️ Write First Entry</span>
                </a>
            </div>
        @else
            <!-- Calendar Grid -->
            <div class="mb-8">
                <!-- Month Navigation -->
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold text-gray-800">Recent Entries</h2>
                    <div class="text-gray-600">
                        Showing {{ $totalEntries }} entries
                    </div>
                </div>

                <!-- Calendar Grid -->
                <div class="calendar-grid">
                    @foreach($entries as $month => $monthEntries)
                        
                        <div class="bg-white rounded-xl shadow-md overflow-hidden">
                            <!-- Month Header -->
                            <div class="month-header">
                                {{ $month }}
                            </div>
                            
                            <!-- Month Entries -->
                            <div class="p-4">
                                @foreach($monthEntries as $entry)
                                    <a 
                                        href="{{ route('journal.edit', $entry->id) }}"
                                        class="block mb-3 hover:no-underline"
                                    >
                                        <div class="calendar-card">
                                            <div class="flex items-center">
                                                <!-- Day Circle -->
                                                @php
                                                    $dayOfWeek = strtolower($entry->created_at->format('D'));
                                                    $dayClass = 'day-' . $dayOfWeek;
                                                @endphp
                                                <div class="day-circle {{ $dayClass }}">
                                                    {{ $entry->created_at->format('d') }}
                                                </div>
                                                
                                                <!-- Entry Info -->
                                                <div class="flex-1">
                                                    <div class="font-semibold text-gray-800 text-lg">
                                                        {{ $entry->created_at->format('l') }}
                                                    </div>
                                                    <div class="text-gray-600 text-sm">
                                                        {{ $entry->created_at->format('F d, Y') }}
                                                    </div>
                                                </div>
                                                
                                                <!-- Edit Icon -->
                                                <div class="text-blue-600 opacity-0 group-hover:opacity-100 transition-opacity">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M12.586 4.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" clip-rule="evenodd" />
                                                    </svg>
                                                </div>
                                            </div>
                                            
                                            <!-- Entry Preview -->
                                            @if($entry->content)
                                                <div class="mt-4 pt-4 border-t border-gray-100">
                                                    <p class="text-gray-600 text-sm line-clamp-2">
                                                        {{ Str::limit(strip_tags($entry->content), 80) }}
                                                    </p>
                                                </div>
                                            @endif
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- View Today Button -->
            <div class="text-center mt-8">
                <a 
                    href="{{ route('journal.today') }}" 
                    class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white px-8 py-3 rounded-lg font-semibold text-lg shadow-lg transition duration-200"
                >
                    <span>📝 Continue Journaling Today</span>
                </a>
            </div>
        @endif

    </div>

</body>
</html>