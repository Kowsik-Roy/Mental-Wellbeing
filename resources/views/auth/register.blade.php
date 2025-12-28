@extends('layouts.auth')

@section('title', 'Register')

@section('heading', 'Create an account')

@section('subheading', 'Enter your information to get started')

@section('content')
<form method="POST" action="{{ route('register') }}">
    @csrf
    
    <div class="space-y-4">
        <div>
            <label for="name" class="block text-sm font-medium text-slate-700 mb-2">Name</label>
            <input 
                type="text" 
                id="name" 
                name="name" 
                value="{{ old('name') }}" 
                required 
                autofocus
                autocomplete="name"
                class="w-full px-4 py-3 border-2 border-slate-300 rounded-xl bg-white/90 focus:ring-2 focus:ring-slate-400 focus:border-slate-500 transition ease-in-out duration-150 text-slate-800 placeholder-slate-400 shadow-inner"
                placeholder="Enter your name"
            >
        </div>
        
        <div>
            <label for="email" class="block text-sm font-medium text-slate-700 mb-2">Email</label>
            <input 
                type="email" 
                id="email" 
                name="email" 
                value="{{ old('email') }}" 
                required
                autocomplete="email"
                class="w-full px-4 py-3 border-2 border-slate-300 rounded-xl bg-white/90 focus:ring-2 focus:ring-slate-400 focus:border-slate-500 transition ease-in-out duration-150 text-slate-800 placeholder-slate-400 shadow-inner"
                placeholder="Enter your email"
            >
        </div>
        
        <div>
            <label for="password" class="block text-sm font-medium text-slate-700 mb-2">Password</label>
            <div class="relative">
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    required
                    autocomplete="new-password"
                    class="w-full px-4 py-3 pr-10 border-2 border-slate-300 rounded-xl bg-white/90 focus:ring-2 focus:ring-slate-400 focus:border-slate-500 transition ease-in-out duration-150 text-slate-800 placeholder-slate-400 shadow-inner"
                    placeholder="Enter your password"
                >
                <button 
                    type="button" 
                    onclick="togglePassword('password')"
                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500 hover:text-gray-700"
                >
                    <i class="fas fa-eye" id="password-eye"></i>
                </button>
            </div>
        </div>
        
        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-slate-700 mb-2">Confirm Password</label>
            <div class="relative">
                <input 
                    type="password" 
                    id="password_confirmation" 
                    name="password_confirmation" 
                    required
                    autocomplete="new-password"
                    class="w-full px-4 py-3 pr-10 border-2 border-slate-300 rounded-xl bg-white/90 focus:ring-2 focus:ring-slate-400 focus:border-slate-500 transition ease-in-out duration-150 text-slate-800 placeholder-slate-400 shadow-inner"
                    placeholder="Confirm your password"
                >
                <button 
                    type="button" 
                    onclick="togglePassword('password_confirmation')"
                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500 hover:text-gray-700"
                >
                    <i class="fas fa-eye" id="password_confirmation-eye"></i>
                </button>
            </div>
        </div>
        
        <div class="flex items-center justify-end">
            <a href="{{ route('password.reset.request') }}" class="text-sm text-slate-600 hover:text-slate-800 font-medium">
                Forgot password?
            </a>
        </div>
        
        <button 
            type="submit" 
            class="w-full inline-flex items-center justify-center px-4 py-3 bg-gradient-to-r from-slate-500 via-slate-600 to-slate-700 border border-transparent rounded-xl font-semibold text-sm text-white uppercase tracking-widest hover:from-slate-600 hover:via-slate-700 hover:to-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-400 focus:ring-offset-2 transition ease-in-out duration-150 shadow-lg hover:shadow-xl transform hover:scale-105"
        >
            Register
        </button>
    </div>
</form>

<div class="mt-6">
    <div class="relative">
        <div class="absolute inset-0 flex items-center">
            <div class="w-full border-t border-gray-300"></div>
        </div>
        <div class="relative flex justify-center text-sm">
            <span class="px-2 bg-white text-gray-500">Or continue with</span>
        </div>
    </div>

    <div class="mt-6">
        <a 
            href="{{ route('google.login') }}" 
            class="w-full inline-flex items-center justify-center px-4 py-3 bg-white/90 border-2 border-slate-300 rounded-xl font-semibold text-sm text-slate-700 hover:bg-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-400 transition ease-in-out duration-150 shadow-md hover:shadow-lg"
        >
            <svg class="w-5 h-5 mr-2" viewBox="0 0 24 24">
                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
            </svg>
            Sign up with Google
        </a>
    </div>
</div>

<script>
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const eye = document.getElementById(fieldId + '-eye');
    
    if (field.type === 'password') {
        field.type = 'text';
        eye.classList.remove('fa-eye');
        eye.classList.add('fa-eye-slash');
    } else {
        field.type = 'password';
        eye.classList.remove('fa-eye-slash');
        eye.classList.add('fa-eye');
    }
}
</script>
@endsection

@section('footer')
    Already have an account? 
    <a href="{{ route('login') }}" class="font-medium text-slate-700 hover:text-slate-900">
        Log in
    </a>
@endsection
