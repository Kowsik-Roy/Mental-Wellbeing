@extends('layouts.app')

@section('title', $hasPassword ? 'Change Password' : 'Set Password')

@section('content')

<div class="max-w-2xl mx-auto">

    <!-- Card -->
    <div class="bg-red/90 backdrop-blur rounded-2xl shadow-xl border border-indigo-200">

        <!-- Header -->
        <div class="px-6 py-4 border-b border-indigo-200 bg-gradient-to-r from-red-300 to-amber-500 rounded-t-2xl">
            <h1 class="text-xl font-bold text-white">
                 {{ $hasPassword ? 'Change Password' : 'Set Password' }}
            </h1>
            <p class="text-yellow-100 text-sm mt-1">
                {{ $hasPassword ? 'Update your login credentials' : 'Set a password for your account' }}
            </p>
        </div>

        <div class="p-6">

            {{-- Errors --}}
            @if ($errors->any())
                <div class="mb-6 bg-red-50 border border-red-200 rounded-xl p-4">
                    <h3 class="text-sm font-semibold text-red-800 mb-2">
                        Please fix the following errors:
                    </h3>
                    <ul class="text-sm text-red-700 list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- OAuth Info --}}
            @if(!$hasPassword)
                <div class="mb-6 bg-yellow-50 border border-yellow-200 rounded-xl p-4">
                    <h3 class="text-sm font-semibold text-yellow-800">
                        Set Your Password
                    </h3>
                    <p class="mt-1 text-sm text-yellow-700">
                        You signed in with Google. Set a password to enable email/password login.
                    </p>
                </div>
            @endif

            <form method="POST" action="{{ route('profile.password.update') }}">
                @csrf
                @method('PUT')

                <div class="space-y-6">

                    @if($hasPassword)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Current Password
                            </label>
                            <div class="relative">
                                <input type="password" name="current_password" id="current_password"
                                       class="w-full px-4 py-3 pr-10 rounded-xl border border-gray-300 focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400"
                                       required>
                                <button 
                                    type="button" 
                                    onclick="togglePassword('current_password')"
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500 hover:text-gray-700"
                                >
                                    <i class="fas fa-eye" id="current_password-eye"></i>
                                </button>
                            </div>
                            @error('current_password')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <div class="mt-2">
                                <a 
                                    href="{{ route('password.reset.send.authenticated') }}" 
                                    id="reset-password-link"
                                    class="text-sm text-blue-600 hover:text-blue-500 underline"
                                >
                                    Forgot your current password? Reset it via email
                                </a>
                            </div>
                        </div>
                    @endif

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            New Password
                        </label>
                        <div class="relative">
                            <input type="password" name="new_password" id="new_password"
                                   class="w-full px-4 py-3 pr-10 rounded-xl border border-gray-300 focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400"
                                   required>
                            <button 
                                type="button" 
                                onclick="togglePassword('new_password')"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500 hover:text-gray-700"
                            >
                                <i class="fas fa-eye" id="new_password-eye"></i>
                            </button>
                        </div>
                        @error('new_password')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Confirm New Password
                        </label>
                        <div class="relative">
                            <input type="password" name="new_password_confirmation" id="new_password_confirmation"
                                   class="w-full px-4 py-3 pr-10 rounded-xl border border-gray-300 focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400"
                                   required>
                            <button 
                                type="button" 
                                onclick="togglePassword('new_password_confirmation')"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500 hover:text-gray-700"
                            >
                                <i class="fas fa-eye" id="new_password_confirmation-eye"></i>
                            </button>
                        </div>
                    </div>

                </div>

                <!-- Actions -->
                <div class="flex justify-end gap-3 pt-6 mt-6 border-t border-gray-200">
                    <a href="{{ route('dashboard') }}"
                       class="px-6 py-2 rounded-xl border border-gray-300 text-gray-700 hover:bg-gray-100 transition">
                        Cancel
                    </a>

                    <button type="submit"
                            class="px-6 py-2 rounded-xl bg-red-400 text-white font-medium hover:bg-red-600 transition">
                             {{ $hasPassword ? 'Change Password' : 'Set Password' }}
                    </button>
                </div>

            </form>
        </div>
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

// Handle password reset link click
document.addEventListener('DOMContentLoaded', function() {
    const resetLink = document.getElementById('reset-password-link');
    if (resetLink) {
        resetLink.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            // Create and submit form outside of the parent form
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("password.reset.send.authenticated") }}';
            form.style.display = 'none';
            
            // Add CSRF token
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = '{{ csrf_token() }}';
            form.appendChild(csrfInput);
            
            // Append to body (outside any other form) and submit
            document.body.appendChild(form);
            form.submit();
            
            return false;
        });
    }
});
</script>

@endsection
