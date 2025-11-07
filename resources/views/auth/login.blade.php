@extends('layouts.auth')

@section('title', 'Login')

@section('heading', 'Welcome back')

@section('subheading', 'Sign in to your account to continue')

@section('content')
<form method="POST" action="{{ route('login') }}">
    @csrf
    
    <div class="space-y-4">
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
            <input 
                type="email" 
                id="email" 
                name="email" 
                value="{{ old('email') }}" 
                required 
                autofocus
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition ease-in-out duration-150"
                placeholder="Enter your email"
            >
        </div>
        
        <div>
            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
            <input 
                type="password" 
                id="password" 
                name="password" 
                required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition ease-in-out duration-150"
                placeholder="Enter your password"
            >
        </div>
        
        <div class="flex items-center">
            <input 
                type="checkbox" 
                id="remember" 
                name="remember" 
                value="1"
                {{ old('remember') ? 'checked' : '' }}
                class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
            >
            <label for="remember" class="ml-2 block text-sm text-gray-600 cursor-pointer">
                Remember me
            </label>
        </div>
        
        <button 
            type="submit" 
            class="w-full inline-flex items-center justify-center px-4 py-3 bg-blue-600 border border-transparent rounded-lg font-semibold text-sm text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150"
        >
            Log in
        </button>
    </div>
</form>
@endsection

@section('footer')
    Don't have an account? 
    <a href="{{ route('register') }}" class="font-medium text-blue-600 hover:text-blue-500">
        Register
    </a>
@endsection
