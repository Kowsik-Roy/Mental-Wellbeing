@extends('layouts.app')

@section('title', 'Emergency Contact')

@section('content')
<div class="max-w-xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-800">Emergency Contact</h1>
        <a href="{{ route('dashboard') }}" class="text-sm text-gray-600 hover:text-gray-900">← Back</a>
    </div>

    @if (session('status'))
        <div class="rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 text-sm">
            {{ session('status') }}
        </div>
    @endif

    @if (session('error'))
        <div class="rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 text-sm">
            {{ session('error') }}
        </div>
    @endif

    <form method="POST" action="{{ route('emergency.update') }}"
          class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 space-y-4">
        @csrf

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
            <input name="name" value="{{ old('name', $contact->name ?? '') }}"
                   class="w-full rounded-2xl border-gray-200 focus:border-indigo-400 focus:ring-indigo-400" />
            @error('name') <div class="text-sm text-rose-600 mt-1">{{ $message }}</div> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
            <input name="email" value="{{ old('email', $contact->email ?? '') }}"
                   class="w-full rounded-2xl border-gray-200 focus:border-indigo-400 focus:ring-indigo-400" />
            @error('email') <div class="text-sm text-rose-600 mt-1">{{ $message }}</div> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Relationship (optional)</label>
            <input name="relationship" value="{{ old('relationship', $contact->relationship ?? '') }}"
                   class="w-full rounded-2xl border-gray-200 focus:border-indigo-400 focus:ring-indigo-400" />
            @error('relationship') <div class="text-sm text-rose-600 mt-1">{{ $message }}</div> @enderror
        </div>

        <button type="submit"
                class="inline-flex items-center gap-2 px-5 py-3 rounded-full bg-indigo-700 text-white text-sm font-medium hover:bg-indigo-800 shadow-md">
            Save contact →
        </button>
    </form>

    <p class="text-xs text-gray-500">
        This contact will only be notified if you explicitly confirm an alert.
    </p>
</div>
@endsection
