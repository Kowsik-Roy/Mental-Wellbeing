@extends('layouts.auth')

@section('title', 'Forgot Password')

@section('heading', 'Reset Password')

@section('subheading', 'Enter your email to receive a verification code')

@section('content')
<form method="POST" action="{{ route('password.reset.send') }}">
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
        
        <button 
            type="submit" 
            class="w-full inline-flex items-center justify-center px-4 py-3 bg-blue-600 border border-transparent rounded-lg font-semibold text-sm text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150"
        >
            Send Verification Code
        </button>
    </div>
</form>
@endsection

@section('footer')
    Remember your password? 
    <a href="{{ route('login') }}" class="font-medium text-blue-600 hover:text-blue-500">
        Log in
    </a>
@endsection
