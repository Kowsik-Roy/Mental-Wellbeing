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
                        <div class="inline">
                            @csrf
                            <input type="hidden" name="_token" value="{{ csrf_token() }}" id="delete-form-token">
                            <button type="button" 
                                    id="remove-emergency-contact-btn"
                                    class="px-6 py-2 rounded-xl bg-red-500 text-white font-medium hover:bg-red-600 transition">
                                Remove Contact
                            </button>
                        </div>
                    @endif

                    <button type="submit"
                            id="update-emergency-contact-btn"
                            class="px-6 py-2 rounded-xl bg-gray-300 text-gray-500 font-medium cursor-not-allowed transition"
                            disabled>
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Remove contact button functionality
    const removeBtn = document.getElementById('remove-emergency-contact-btn');
    
    if (removeBtn) {
        removeBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            showConfirmModal(
                'Remove Emergency Contact', 
                'Are you sure you want to remove your emergency contact?', 
                submitEmergencyContactDeletion
            );
        });
    }
    
    // Update button enable/disable logic
    const updateBtn = document.getElementById('update-emergency-contact-btn');
    const nameInput = document.getElementById('emergency_name');
    const emailInput = document.getElementById('emergency_email');
    const relationshipInput = document.getElementById('emergency_relationship');
    
    if (updateBtn && nameInput && emailInput) {
        // Store original values
        const originalValues = {
            name: nameInput.value.trim(),
            email: emailInput.value.trim(),
            relationship: relationshipInput ? relationshipInput.value.trim() : ''
        };
        
        /**
         * Check if any field has changed and enable/disable update button accordingly
         */
        function checkForChanges() {
            const currentValues = {
                name: nameInput.value.trim(),
                email: emailInput.value.trim(),
                relationship: relationshipInput ? relationshipInput.value.trim() : ''
            };
            
            const hasChanges = 
                currentValues.name !== originalValues.name ||
                currentValues.email !== originalValues.email ||
                currentValues.relationship !== originalValues.relationship;
            
            if (hasChanges) {
                // Enable button - make it green and clickable
                updateBtn.disabled = false;
                updateBtn.classList.remove('bg-gray-300', 'text-gray-500', 'cursor-not-allowed');
                updateBtn.classList.add('bg-green-500', 'text-white', 'hover:bg-green-600', 'cursor-pointer');
            } else {
                // Disable button - make it gray and unclickable
                updateBtn.disabled = true;
                updateBtn.classList.remove('bg-green-500', 'text-white', 'hover:bg-green-600', 'cursor-pointer');
                updateBtn.classList.add('bg-gray-300', 'text-gray-500', 'cursor-not-allowed');
            }
        }
        
        // Listen for changes on all input fields
        nameInput.addEventListener('input', checkForChanges);
        nameInput.addEventListener('change', checkForChanges);
        emailInput.addEventListener('input', checkForChanges);
        emailInput.addEventListener('change', checkForChanges);
        
        if (relationshipInput) {
            relationshipInput.addEventListener('input', checkForChanges);
            relationshipInput.addEventListener('change', checkForChanges);
        }
        
        // Initial check (in case page loads with changes already made)
        checkForChanges();
    }
    
    /**
     * Submit emergency contact deletion form
     * Creates a temporary form outside nested structure to avoid HTML nesting issues
     */
    function submitEmergencyContactDeletion() {
        const action = '{{ route('profile.emergency-contact.delete') }}';
        let token = document.getElementById('delete-form-token');
        
        // If token not found by ID, search for it elsewhere
        if (!token) {
            token = document.querySelector('input[name="_token"]');
        }
        
        if (!token) {
            alert('Error: Could not find security token. Please refresh the page and try again.');
            return;
        }
        
        // Create temporary form outside nested structure
        const tempForm = document.createElement('form');
        tempForm.method = 'POST';
        tempForm.action = action;
        tempForm.style.display = 'none';
        
        // Add CSRF token
        const tokenInput = document.createElement('input');
        tokenInput.type = 'hidden';
        tokenInput.name = '_token';
        tokenInput.value = token.value;
        tempForm.appendChild(tokenInput);
        
        // Add method override for DELETE
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';
        tempForm.appendChild(methodInput);
        
        // Append to body and submit
        document.body.appendChild(tempForm);
        tempForm.submit();
    }
});
</script>
@endpush

@endsection
