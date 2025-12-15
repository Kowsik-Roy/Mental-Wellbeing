<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Mental Wellness</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f9fafb; }
        .card { background: white; border-radius: 0.75rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e5e7eb; }
        .btn-primary { background: #3b82f6; color: white; padding: 0.5rem 1rem; border-radius: 0.5rem; font-weight: 500; }
        .btn-primary:hover { background: #2563eb; }
    </style>
</head>
<body>
    <!-- Simple Navigation -->
    <nav class="bg-white shadow border-b px-4 py-3">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <div class="flex items-center space-x-3">
                <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-purple-500 rounded-lg flex items-center justify-center">
                    <i class="fas fa-brain text-white"></i>
                </div>
                <span class="text-lg font-bold">Mental Wellness</span>
            </div>
            <div class="flex items-center space-x-4">
                <span class="text-sm">{{ Auth::user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-sm text-gray-600 hover:text-red-600">
                        <i class="fas fa-sign-out-alt mr-1"></i>Logout
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 py-8">
        <h1 class="text-2xl font-bold mb-6">Welcome back, {{ Auth::user()->name }}!</h1>
        
        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="card p-6">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-4">
                        <i class="fas fa-tasks text-blue-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Active Habits</p>
                        <p class="text-2xl font-bold">{{ $habitCount }}</p>
                    </div>
                </div>
            </div>
            
            <div class="card p-6">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-4">
                        <i class="fas fa-book text-green-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Journals</p>
                        <p class="text-2xl font-bold">{{ $journalCount }}</p>
                    </div>
                </div>
            </div>
            
            <div class="card p-6">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mr-4">
                        <i class="fas fa-fire text-purple-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Best Streak</p>
                        <p class="text-2xl font-bold">
                            {{ $habits->max('best_streak') ?? 0 }} days
                        </p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Recent Habits -->
        <div class="card p-6 mb-8">
            <h2 class="text-lg font-bold mb-4">Recent Habits</h2>
            
            @if($habits->count() > 0)
                <div class="space-y-3">
                    @foreach($habits as $habit)
                        <div class="flex justify-between items-center p-3 border rounded-lg">
                            <div>
                                <p class="font-medium">{{ $habit->title }}</p>
                                <p class="text-sm text-gray-600">
                                    {{ $habit->current_streak }} day streak
                                </p>
                            </div>
                            <a href="/habits/{{ $habit->id }}/log" 
                               class="btn-primary text-sm">
                                Log
                            </a>
                        </div>
                    @endforeach
                </div>
                
                <div class="mt-4 text-center">
                    <a href="/habits" class="text-blue-600 hover:text-blue-800 font-medium">
                        View All Habits →
                    </a>
                </div>
            @else
                <div class="text-center py-6">
                    <p class="text-gray-600 mb-4">No habits yet</p>
                    <a href="/habits/create" class="btn-primary">
                        <i class="fas fa-plus mr-2"></i>Create First Habit
                    </a>
                </div>
            @endif
        </div>
        
        <!-- Quick Actions -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <a href="/habits" class="card p-6 hover:shadow-lg transition">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mr-4">
                        <i class="fas fa-list text-blue-600 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="font-bold">All Habits</h3>
                        <p class="text-gray-600 text-sm">View and manage your habits</p>
                    </div>
                </div>
            </a>
            
            <a href="/journal" class="card p-6 hover:shadow-lg transition">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mr-4">
                        <i class="fas fa-pen text-green-600 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="font-bold">Journal</h3>
                        <p class="text-gray-600 text-sm">Write today's entry</p>
                    </div>
                </div>
            </a>
        </div>
    </main>

    <!-- Footer -->
    <footer class="mt-12 border-t px-4 py-6">
        <div class="max-w-7xl mx-auto text-center text-gray-600 text-sm">
            Mental Wellness Companion • CSE471 Project
        </div>
    </footer>
</body>
</html>