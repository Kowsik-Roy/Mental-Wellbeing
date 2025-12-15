<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>My Profile - Mental Wellbeing</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Navigation -->
            <div class="mb-6">
                <a href="{{ route('dashboard') }}" class="inline-flex items-center text-gray-600 hover:text-gray-900">
                    <i class="fas fa-arrow-left mr-2"></i> Back to Dashboard
                </a>
            </div>

            <!-- Success Message -->
            @if (session('success'))
                <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-check-circle text-green-400"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Profile Card -->
            <div class="bg- rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <!-- Header -->
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-8">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-20 h-20 rounded-full bg-white/20 flex items-center justify-center">
                                <i class="fas fa-user text-white text-3xl"></i>
                            </div>
                        </div>
                        <div class="ml-6">
                            <h1 class="text-2xl font-bold text-white">{{ Auth::user()->name }}</h1>
                            <p class="text-blue-100">{{ Auth::user()->email }}</p>
                            <p class="text-blue-100 text-sm mt-1">
                                Member since {{ Auth::user()->created_at->format('F j, Y') }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Profile Details -->
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Personal Information -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Personal Information</h3>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">Full Name</label>
                                    <p class="mt-1 text-gray-900">{{ Auth::user()->name }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">Email Address</label>
                                    <p class="mt-1 text-gray-900">{{ Auth::user()->email }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Account Actions -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Account Management</h3>
                            <div class="space-y-3">
                                <a href="{{ route('profile.edit') }}" 
                                   class="w-full flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-edit text-blue-600"></i>
                                        </div>
                                        <div class="ml-4">
                                            <p class="font-medium text-gray-900">Edit Profile</p>
                                            <p class="text-sm text-gray-500">Update your name and email</p>
                                        </div>
                                    </div>
                                    <i class="fas fa-chevron-right text-gray-400"></i>
                                </a>

                                <a href="{{ route('profile.password.edit') }}" 
                                   class="w-full flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-key text-yellow-600"></i>
                                        </div>
                                        <div class="ml-4">
                                            <p class="font-medium text-gray-900">Change Password</p>
                                            <p class="text-sm text-gray-500">Update your login credentials</p>
                                        </div>
                                    </div>
                                    <i class="fas fa-chevron-right text-gray-400"></i>
                                </a>

                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" 
                                            class="w-full flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition text-left">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                                                <i class="fas fa-sign-out-alt text-red-600"></i>
                                            </div>
                                            <div class="ml-4">
                                                <p class="font-medium text-gray-900">Logout</p>
                                                <p class="text-sm text-gray-500">Sign out of your account</p>
                                            </div>
                                        </div>
                                        <i class="fas fa-chevron-right text-gray-400"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white p-4 rounded-lg border border-gray-200">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-calendar-check text-blue-600"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-500">Days Active</p>
                            <p class="text-2xl font-bold text-gray-900">{{ Auth::user()->created_at->startOfDay()->diffInDays(now()->startOfDay()) + 1 }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white p-4 rounded-lg border border-gray-200">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-user-check text-purple-600"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-500">Profile Complete</p>
                            <p class="text-2xl font-bold text-gray-900">100%</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>