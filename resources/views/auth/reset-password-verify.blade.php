@extends('layouts.auth')

@section('title', 'Verify Reset Code')

@section('heading', 'Verify Your Code')

@section('subheading', 'Enter the verification code sent to your email')

@section('content')
<form method="POST" action="{{ route('password.reset.verify.code') }}">
    @csrf
    
    <div class="space-y-4">
        <div>
            <label for="code" class="block text-sm font-medium text-gray-700 mb-2">
                Verification Code
            </label>
            <input
                id="code"
                type="text"
                name="code"
                inputmode="numeric"
                pattern="[0-9]*"
                maxlength="5"
                value="{{ old('code') }}"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-lg tracking-widest text-center transition ease-in-out duration-150"
                placeholder="12345"
                required
                autofocus
            >
        </div>
        
        <button 
            type="submit" 
            class="w-full inline-flex items-center justify-center px-4 py-3 bg-blue-600 border border-transparent rounded-lg font-semibold text-sm text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150"
        >
            Verify Code
        </button>
    </div>
</form>

<p class="mt-4 text-xs text-gray-500 text-center">
    The code is valid for 3 minutes. If it expires, go back and request a new code.
</p>
@endsection

@section('footer')
    <a href="{{ route('password.reset.request') }}" class="font-medium text-blue-600 hover:text-blue-500">
        Back to reset request
    </a>
@endsection
