@extends('layouts.auth')

@section('title', 'Verify Email')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-b from-indigo-50 via-white to-purple-50 px-4">
    <div class="max-w-md w-full space-y-6">
        <div class="bg-white/90 backdrop-blur rounded-3xl shadow-xl border border-indigo-100 p-8">
            <h1 class="text-2xl font-bold text-gray-900 mb-2">
                Verify your email
            </h1>
            <p class="text-sm text-gray-600 mb-4">
                We sent a 5‑digit verification code to <span class="font-semibold">{{ $email }}</span>.
                Enter it below to complete your registration.
            </p>

            @if ($errors->any())
                <div class="mb-4 rounded-2xl bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">
                    {{ $errors->first() }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 rounded-2xl bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">
                    {{ session('error') }}
                </div>
            @endif

            <form method="POST" action="{{ route('verification.verify') }}" class="space-y-5">
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
                        class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-lg tracking-widest text-center"
                        placeholder="5-digit verification code"
                        required
                    >
                </div>

                <div class="space-y-3">
                    <button
                        type="submit"
                        class="w-full inline-flex justify-center items-center px-4 py-3 rounded-xl bg-indigo-600 text-white font-semibold text-sm hover:bg-indigo-700 transition shadow-md button-hover"
                    >
                        Verify and continue
                    </button>

                    <form method="POST" action="{{ route('verification.resend') }}">
                        @csrf
                        <button
                            type="submit"
                            class="w-full inline-flex justify-center items-center px-4 py-2 rounded-xl border border-gray-300 text-gray-700 text-xs hover:bg-gray-50 transition"
                        >
                            Resend code
                        </button>
                    </form>
                </div>
            </form>

            <p class="mt-4 text-xs text-gray-500">
                Didn’t receive the email or the code expired? Use “Resend code” to get a new one.
            </p>
        </div>
    </div>
</div>
@endsection

