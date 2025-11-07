@extends('layouts.auth')

@section('title', 'Register')

@section('heading', 'Create an account')

@section('subheading', 'Enter your information to get started')

@section('content')
<form method="POST" action="{{ route('register') }}">
    @csrf
    
    <div class="space-y-4">
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Name</label>
            <input 
                type="text" 
                id="name" 
                name="name" 
                value="{{ old('name') }}" 
                required 
                autofocus
                autocomplete="name"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition ease-in-out duration-150"
                placeholder="Enter your name"
            >
        </div>
        
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
            <input 
                type="email" 
                id="email" 
                name="email" 
                value="{{ old('email') }}" 
                required
                autocomplete="email"
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
                autocomplete="new-password"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition ease-in-out duration-150"
                placeholder="Enter your password"
            >
        </div>
        
        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Confirm Password</label>
            <input 
                type="password" 
                id="password_confirmation" 
                name="password_confirmation" 
                required
                autocomplete="new-password"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition ease-in-out duration-150"
                placeholder="Confirm your password"
            >
        </div>
        
        <button 
            type="submit" 
            class="w-full inline-flex items-center justify-center px-4 py-3 bg-blue-600 border border-transparent rounded-lg font-semibold text-sm text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150"
        >
            Register
        </button>
    </div>
</form>
@endsection

@section('footer')
    Already have an account? 
    <a href="{{ route('login') }}" class="font-medium text-blue-600 hover:text-blue-500">
        Log in
    </a>
@endsection
