<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard - {{ config('app.name', 'Laravel') }}</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    
    <!-- Styles / Scripts -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @endif

    <style>
.journal-highlight {
    font-weight: bold;
    font-size: 18px;
    padding: 5px 24px;
    box-shadow: 0 0 5px rgba(0, 0, 0, 0.6);
    border-radius: 5px;
    transition: 0.3s;
}


</style>
</head>
<body class="bg-gray-50">
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Success/Error Messages -->
            @if(session('success'))
                <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Welcome Section -->
            <div class="mb-8">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">Welcome back, {{ Auth::user()->name }}!</h1>
                            <p class="text-gray-600 mt-1">This is your dashboard</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-gray-500">User Status</p>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                <svg class="w-2 h-2 mr-1" fill="currentColor" viewBox="0 0 8 8">
                                    <circle cx="4" cy="4" r="3"/>
                                </svg>
                                Active
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dashboard Content -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
                <div class="flex items-center justify-center mb-4">
                    <div class="p-2 rounded-full bg-blue-100">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <p class="text-lg font-medium text-gray-900 mb-2">
                    dashboard is ready!
                </p>
                <a href="{{ route('journal.today') }}" 
                 class="btn btn-primary journal-highlight">
                    📘 My Journal
                </a>

                <p class="text-sm text-gray-600">
                    You are successfully logged in. This is where all of my mental wellbeing features will be displayed.
                </p>
            </div>

            <!-- Profile Management Section - Added by NODI -->
            <div class="mt-8 bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Profile Management</h3>
                    <p class="text-sm text-gray-600 mt-1">Manage your account settings and security</p>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <a href="{{ route('profile.show') }}" 
                           class="group p-4 border border-gray-200 rounded-lg hover:border-blue-300 hover:bg-blue-50 transition duration-150 ease-in-out">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center group-hover:bg-blue-200">
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-900">View Profile</p>
                                    <p class="text-xs text-gray-500">See your account details</p>
                                </div>
                            </div>
                        </a>

                        <a href="{{ route('profile.edit') }}" 
                           class="group p-4 border border-gray-200 rounded-lg hover:border-green-300 hover:bg-green-50 transition duration-150 ease-in-out">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center group-hover:bg-green-200">
                                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-900">Edit Profile</p>
                                    <p class="text-xs text-gray-500">Update your name and email</p>
                                </div>
                            </div>
                        </a>

                        <a href="{{ route('profile.password.edit') }}" 
                           class="group p-4 border border-gray-200 rounded-lg hover:border-yellow-300 hover:bg-yellow-50 transition duration-150 ease-in-out">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center group-hover:bg-yellow-200">
                                    <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-900">Change Password</p>
                                    <p class="text-xs text-gray-500">Update your login credentials</p>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>        

            <!-- Habits Overview Section - Added by Member 2 -->
            <div class="mt-8 bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">Daily Habits</h3>
                        <p class="text-sm text-gray-600 mt-1">Track your wellness routines</p>
                    </div>
                    <a href="{{ route('habits.index') }}" class="text-blue-600 hover:text-blue-800">
                        View All <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
                <div class="p-6">
                    @php
                        try {
                            $habits = Auth::user()->habits()->where('is_active', true)->take(3)->get();
                        } catch (\Exception $e) {
                            // Fallback if relationship not working
                            $habits = collect([]);
                        }
                    @endphp
                    
                    @if($habits->isEmpty())
                        <div class="text-center py-8">
                            <div class="mx-auto w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                <i class="fas fa-tasks text-gray-400"></i>
                            </div>
                            <p class="text-gray-600 mb-4">No habits yet. Start building your wellness routine.</p>
                            <a href="{{ route('habits.create') }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                                <i class="fas fa-plus mr-2"></i> Create First Habit
                            </a>
                        </div>
                    @else
                        <div class="space-y-4">
                            @foreach($habits as $habit)
                                <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-tasks text-blue-600"></i>
                                        </div>
                                        <div class="ml-4">
                                            <p class="font-medium text-gray-900">{{ $habit->title }}</p>
                                            <p class="text-sm text-gray-500">
                                                {{ $habit->current_streak }} day{{ $habit->current_streak != 1 ? 's' : '' }} streak
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        @if($habit->todaysLog && $habit->todaysLog->completed)
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <i class="fas fa-check mr-1"></i> Done
                                            </span>
                                        @else
                                            <form method="POST" action="{{ route('habits.log', $habit) }}" class="inline">
                                                @csrf
                                                <button type="submit" name="completed" value="1" 
                                                        class="inline-flex items-center px-3 py-1 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 text-sm">
                                                    Mark Complete
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <div class="mt-6 text-center">
                            <a href="{{ route('habits.index') }}" class="inline-flex items-center text-blue-600 hover:text-blue-800">
                                View all {{ Auth::user()->habits()->where('is_active', true)->count() }} habits
                                <i class="fas fa-arrow-right ml-2"></i>
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="mt-8 bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="mt-8 bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Quick Actions</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button 
                                type="submit"
                                class="w-full inline-flex items-center justify-center px-4 py-3 bg-gray-600 border border-transparent rounded-lg font-semibold text-sm text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150"
                            >
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                </svg>
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
