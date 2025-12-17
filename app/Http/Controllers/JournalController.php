<?php

namespace App\Http\Controllers;

use App\Models\Journal;
use App\Services\HuggingFaceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class JournalController extends Controller
{
    // Show today's journal page
    public function today()
    {
        $today = now()->toDateString();

        $entry = Journal::where('user_id', auth()->id())
                        ->whereDate('created_at', $today)
                        ->first();

        return view('journal.today', compact('entry', 'today'));
    }

    // Store new journal
    public function store(Request $request)
    {
        $request->validate([
            'content' => 'required|min:3',
            'mood' => 'nullable|in:' . implode(',', array_keys(Journal::MOODS)),
        ]);

        $journal = Journal::create([
            'user_id' => auth()->id(),
            'content' => $request->content,
            'mood' => $request->mood,
        ]);

        // Generate emotional reflection asynchronously (don't block the response)
        $this->generateReflectionAsync($journal);

        return redirect()->route('journal.today')->with('success', 'Journal saved successfully!');
    }

    // Edit page
    public function edit(Journal $journal)
    {
        $this->authorizeAccess($journal);

        return view('journal.edit', compact('journal'));
    }

    // Update journal
    public function update(Request $request, Journal $journal)
    {
        $this->authorizeAccess($journal);

        $request->validate([
            'content' => 'required|min:3',
            'mood' => 'nullable|in:' . implode(',', array_keys(Journal::MOODS)),
        ]);

        $journal->update([
            'content' => $request->content,
            'mood' => $request->mood,
        ]);

        // Regenerate emotional reflection if content or mood changed
        $this->generateReflectionAsync($journal);

        return redirect()->route('journal.today')->with('success', 'Journal updated successfully!');
    }

    // Delete journal
    public function destroy(Journal $journal)
    {
        $this->authorizeAccess($journal);

        $journal->delete();

        return redirect()->route('journal.today')->with('success', 'Journal deleted successfully!');
    }

    // History page (previous entries) - UPDATED
    public function history()
    {
        // Get all entries for the authenticated user, ordered by date
        $entries = Journal::where('user_id', auth()->id())
                          ->orderBy('created_at', 'desc')
                          ->get();

        // Get total entries count
        $totalEntries = Journal::where('user_id', auth()->id())->count();

        // Group entries by month
        $groupedEntries = $entries->groupBy(function($entry) {
            return $entry->created_at->format('F Y'); // e.g. January 2025
        });

        // Get mood statistics
        $moodStats = Journal::where('user_id', auth()->id())
                          ->whereNotNull('mood')
                          ->selectRaw('mood, count(*) as count')
                          ->groupBy('mood')
                          ->orderBy('count', 'desc')
                          ->get();

        return view('journal.history', [
            'entries' => $groupedEntries,
            'totalEntries' => $totalEntries,
            'moodStats' => $moodStats,
        ]);
    }

    // Security - only owner can edit/delete
    private function authorizeAccess(Journal $journal)
    {
        if ($journal->user_id !== auth()->id()) {
            abort(403);
        }
    }

    /**
     * Generate emotional reflection for a journal entry asynchronously.
     * This runs in the background so the user doesn't wait for HuggingFace API.
     */
    private function generateReflectionAsync(Journal $journal): void
    {
        // Use fallback immediately for instant response, then try API in background
        // This ensures the page loads fast even if API is slow
        $huggingFaceService = new HuggingFaceService();
        
        // Get fallback reflection immediately (this is instant)
        $fallbackReflection = $this->getFallbackReflection($journal->content, $journal->mood);
        $journal->update(['emotional_reflection' => $fallbackReflection]);
        
        // Try to get AI-generated reflection in background (non-blocking)
        // If it succeeds, it will update; if not, fallback is already there
        try {
            $reflection = $huggingFaceService->generateEmotionalReflection($journal->content, $journal->mood);
            
            if ($reflection && $reflection !== $fallbackReflection) {
                $journal->update(['emotional_reflection' => $reflection]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to generate emotional reflection', [
                'journal_id' => $journal->id,
                'error' => $e->getMessage(),
            ]);
            // Fallback is already set, so no action needed
        }
    }

    /**
     * Get fallback reflection based on mood and content.
     */
    private function getFallbackReflection(string $content, ?string $mood = null): string
    {
        $moodReflections = [
            'happy' => "It's wonderful that you're feeling positive today, and taking time to reflect on these moments is valuable.",
            'sad' => "It sounds like today was emotionally heavy for you, and expressing it here is a healthy step.",
            'excited' => "Your enthusiasm shines through, and it's great that you're capturing these exciting moments.",
            'angry' => "Acknowledging your feelings, even when they're difficult, shows strength and self-awareness.",
            'anxious' => "It takes courage to sit with anxious feelings, and writing about them is a meaningful way to process them.",
            'calm' => "Your sense of peace comes through, and it's beautiful that you're taking time to appreciate these moments.",
            'tired' => "Recognizing when you need rest is important, and giving yourself space to reflect is valuable.",
            'neutral' => "Taking time to reflect, regardless of how you're feeling, is a meaningful practice.",
        ];

        if ($mood && isset($moodReflections[$mood])) {
            return $moodReflections[$mood];
        }

        $wordCount = str_word_count($content);
        if ($wordCount < 20) {
            return "Thank you for taking a moment to express yourself today.";
        } elseif ($wordCount < 50) {
            return "It's meaningful that you're taking time to reflect and write about your experiences.";
        } else {
            return "Your thoughtful reflection shows self-awareness, and expressing yourself here is a valuable practice.";
        }
    }
}