@extends('layouts.app')

@section('title', 'Verify Password Change')

@section('content')
<div class="max-w-xl mx-auto">
    <div class="bg-white/90 backdrop-blur rounded-2xl shadow-xl border border-indigo-200 mt-10">
        <div class="px-6 py-4 border-b border-indigo-200 bg-gradient-to-r from-red-300 to-amber-500 rounded-t-2xl">
            <h1 class="text-xl font-bold text-white">
                Verify Password Change
            </h1>
            <p class="text-yellow-100 text-sm mt-1">
                Enter the 5‑digit code we sent to your email to confirm your new password.
            </p>
        </div>

        <div class="p-6">
            @if (session('status'))
                <div class="mb-4 bg-green-50 border border-green-200 rounded-xl p-4 text-sm text-green-800">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-4 bg-red-50 border border-red-200 rounded-xl p-4 text-sm text-red-700">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.verify.perform') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="code" class="block text-sm font-medium text-gray-700 mb-1">
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
                        class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400 text-lg tracking-widest text-center"
                        placeholder="12345"
                        required
                    >
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
                    <a href="{{ route('profile.password.edit') }}"
                       class="px-5 py-2 rounded-xl border border-gray-300 text-gray-700 hover:bg-gray-100 text-sm">
                        Cancel
                    </a>
                    <button type="submit"
                            class="px-6 py-2 rounded-xl bg-red-500 text-white font-medium hover:bg-red-600 text-sm transition">
                        Confirm Password Change
                    </button>
                </div>
            </form>

            <p class="mt-4 text-xs text-gray-500">
                The code is valid for 3 minutes. If it expires, go back and submit the password change form again to receive a new code.
            </p>
        </div>
    </div>
</div>
@endsection

