@extends('layouts.app')

@section('content')

<div class="max-w-2xl mx-auto space-y-8">

    <!-- Edit Profile Card -->
    <div class="bg-green/90 backdrop-blur rounded-2xl shadow-xl border border-indigo-200">

        <!-- Header -->
        <div class="px-6 py-4 border-b border-indigo-100 bg-gradient-to-r from-green-300 to-emerald-400 rounded-t-2xl">
            <h1 class="text-xl font-bold text-white">
                 Edit Profile
            </h1>
            <p class="text-green-50 text-sm mt-1">
                Update your personal information
            </p>
        </div>

        <div class="p-6">

            {{-- Global Errors --}}
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

            <form method="POST" action="{{ route('profile.update') }}">
                @csrf
                @method('PUT')

                <!-- Name -->
                <div class="mb-6">
                    <label for="name" class="block text-sm font-medium text-gray-800 mb-2">
                        Full Name
                    </label>
                    <input type="text"
                           id="name"
                           name="name"
                           value="{{ old('name', auth()->user()->name) }}"
                           class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-green-400 focus:border-green-400"
                           required>
                    @error('name')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email (Read-only) -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Email Address
                    </label>
                    <div class="px-4 py-3 rounded-xl bg-gray-100 text-gray-700 border border-gray-300">
                        {{ auth()->user()->email }}
                    </div>
                    <p class="mt-2 text-xs text-gray-500">
                         Note: Email cannot be changed for security reasons.
                    </p>
                </div>

                <!-- Actions -->
                <div class="flex justify-end gap-3 pt-6 border-t border-gray-200">
                    <a href="{{ route('dashboard') }}"
                       class="px-6 py-2 rounded-xl border border-gray-300 text-gray-700 hover:bg-gray-100 transition">
                        Cancel
                    </a>

                    <button type="submit"
                            class="px-6 py-2 rounded-xl bg-green-400 text-white font-medium hover:bg-green-600 transition">
                         Update Profile
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Emergency Contact Card -->
    <div class="bg-red/90 backdrop-blur rounded-2xl shadow-xl border border-red-200">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-red-100 bg-gradient-to-r from-red-400 to-rose-500 rounded-t-2xl">
            <h2 class="text-xl font-bold text-white">
                Emergency Contact
            </h2>
            <p class="text-red-50 text-sm mt-1">
                Add a trusted contact who will be notified if you have 3 consecutive days of sad mood
            </p>
        </div>

        <div class="p-6">
            @php
                $emergencyContact = auth()->user()->emergencyContact()->first();
            @endphp

            <form method="POST" action="{{ route('profile.emergency-contact.update') }}">
                @csrf
                @method('PUT')

                <!-- Name -->
                <div class="mb-6">
                    <label for="emergency_name" class="block text-sm font-medium text-gray-800 mb-2">
                        Contact Name <span class="text-red-600">*</span>
                    </label>
                    <input type="text"
                           id="emergency_name"
                           name="name"
                           value="{{ old('name', $emergencyContact->name ?? '') }}"
                           class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-red-400 focus:border-red-400"
                           required>
                    @error('name')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div class="mb-6">
                    <label for="emergency_email" class="block text-sm font-medium text-gray-800 mb-2">
                        Contact Email <span class="text-red-600">*</span>
                    </label>
                    <input type="email"
                           id="emergency_email"
                           name="email"
                           value="{{ old('email', $emergencyContact->email ?? '') }}"
                           class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-red-400 focus:border-red-400"
                           required>
                    @error('email')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-2 text-xs text-gray-500">
                        This person will receive an alert if you have 3 consecutive days with sad mood.
                    </p>
                </div>

                <!-- Relationship (Optional) -->
                <div class="mb-6">
                    <label for="emergency_relationship" class="block text-sm font-medium text-gray-800 mb-2">
                        Relationship (Optional)
                    </label>
                    <input type="text"
                           id="emergency_relationship"
                           name="relationship"
                           value="{{ old('relationship', $emergencyContact->relationship ?? '') }}"
                           class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-red-400 focus:border-red-400"
                           placeholder="e.g., Family, Friend, Partner">
                    @error('relationship')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Actions -->
                <div class="flex justify-end gap-3 pt-6 border-t border-gray-200">
                    @if($emergencyContact)
                        <form method="POST" action="{{ route('profile.emergency-contact.delete') }}" id="delete-emergency-contact-form" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="button" onclick="showConfirmModal('Remove Emergency Contact', 'Are you sure you want to remove your emergency contact?', function() { document.getElementById('delete-emergency-contact-form').submit(); })"
                                    class="px-6 py-2 rounded-xl border border-gray-300 text-gray-700 hover:bg-gray-100 transition">
                                Remove Contact
                            </button>
                        </form>
                    @endif

                    <button type="submit"
                            class="px-6 py-2 rounded-xl bg-red-400 text-white font-medium hover:bg-red-600 transition">
                        {{ $emergencyContact ? 'Update Contact' : 'Save Contact' }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Danger Zone: Delete Account -->
    <div class="bg-white/90 backdrop-blur rounded-2xl shadow-lg border border-red-200">
        <div class="px-6 py-4 border-b border-red-100 bg-gradient-to-r from-red-400 to-rose-500 rounded-t-2xl">
            <h2 class="text-lg font-bold text-white flex items-center">
                 Danger Zone
            </h2>
            <p class="text-red-50 text-sm mt-1">
                Permanently delete your account and all associated data.
            </p>
        </div>
        <div class="p-6 space-y-4">
            <p class="text-sm text-gray-700">
                This action <span class="font-semibold text-red-600">cannot be undone</span>. All your habits, journal entries, and data will be permanently deleted.
            </p>
            <form method="POST" action="{{ route('profile.destroy') }}" id="delete-account-form">
                @csrf
                @method('DELETE')
                <input type="hidden" name="confirm_delete" value="DELETE">
                <button type="button" onclick="showConfirmModal('Delete Account', 'Are you sure you want to delete your account? This action cannot be undone. All your habits, journal entries, and data will be permanently deleted.', function() { document.getElementById('delete-account-form').submit(); })"
                        class="w-full md:w-auto px-6 py-2 rounded-xl bg-red-600 text-white font-medium hover:bg-red-700 transition flex items-center justify-center gap-2">
                     Delete Account
                </button>
            </form>
        </div>
    </div>

</div>

@endsection
