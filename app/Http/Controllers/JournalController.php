<?php

namespace App\Http\Controllers;

use App\Models\Journal;
use Illuminate\Http\Request;

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

        Journal::create([
            'user_id' => auth()->id(),
            'content' => $request->content,
            'mood' => $request->mood,
        ]);

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
}