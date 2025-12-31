<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Mental Wellbeing') - WellBeing</title>

    <!-- Favicon / App Icon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="alternate icon" type="image/png" href="{{ asset('favicon.png') }}">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Styles (Tailwind CDN for auth pages) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @keyframes twinkle {
            0%, 100% { opacity: 0.3; transform: scale(1); }
            50% { opacity: 1; transform: scale(1.2); }
        }
        .star {
            filter: drop-shadow(0 0 4px rgba(255, 255, 255, 0.9));
        }
    </style>
</head>
<body class="min-h-screen flex items-start justify-center py-12 px-4 sm:px-6 lg:px-8 relative">
    <!-- Even Darker Berry Blue Background -->
    <div class="fixed inset-0 z-0" style="background: linear-gradient(135deg, #0f1f3a 0%, #1a2f4f 50%, #253f64 100%);"></div>
    
    <!-- Bling Bling Stars (Smaller) -->
    <div class="fixed inset-0 pointer-events-none z-0">
        @for ($i = 0; $i < 40; $i++)
        <div class="star absolute bg-white rounded-full" style="width: {{ rand(1, 2) }}px; height: {{ rand(1, 2) }}px; top: {{ rand(5, 95) }}%; left: {{ rand(5, 95) }}%; animation: twinkle {{ rand(2, 4) }}s ease-in-out infinite; animation-delay: {{ rand(0, 2) }}s; box-shadow: 0 0 {{ rand(2, 4) }}px rgba(255, 255, 255, 0.9);"></div>
        @endfor
    </div>
    
    <div class="max-w-md w-full space-y-8 relative z-10">
        <div class="bg-gradient-to-br from-slate-300 via-slate-400 to-slate-500 rounded-3xl shadow-2xl p-8 backdrop-blur-sm relative overflow-hidden border border-slate-600/30" style="box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3), 0 0 0 1px rgba(255, 255, 255, 0.1) inset, 0 1px 0 rgba(255, 255, 255, 0.2) inset;">
        <!-- Logo -->
        <div class="flex items-center justify-center gap-3 mb-4">
            <img src="{{ asset('favicon.svg') }}" alt="Mental Wellness Companion Logo" class="w-10 h-10">
            <div class="leading-tight">
                <div class="font-semibold text-lg text-slate-800">Mental Wellness Companion</div>
                <div class="text-xs text-slate-600">Your peaceful space</div>
            </div>
        </div>

            <div class="mb-6 text-center">
                <h1 class="text-2xl font-bold text-slate-800">@yield('heading')</h1>
                <p class="text-sm text-slate-700 mt-1">@yield('subheading')</p>
            </div>
            
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
            
            @if ($errors->any())
                <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <ul class="text-sm text-red-800 list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif
            
            @yield('content')
            
            <div class="mt-6 text-center text-sm text-gray-600">
                @yield('footer')
            </div>
        </div>
    </div>
    
    <style>
        @keyframes twinkle {
            0%, 100% { opacity: 0.3; transform: scale(1); }
            50% { opacity: 1; transform: scale(1.2); }
        }
        .star {
            filter: drop-shadow(0 0 4px rgba(255, 255, 255, 0.9));
        }
    </style>
    
</body>
</html>
