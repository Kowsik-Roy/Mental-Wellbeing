<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WeeklySummaryMail extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;
    public $moodStats;
    public $habitStats;
    public string $periodLabel;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, $moodStats, $habitStats, string $periodLabel)
    {
        $this->user = $user;
        $this->moodStats = $moodStats;
        $this->habitStats = $habitStats;
        $this->periodLabel = $periodLabel;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Your Weekly Wellness Summary')
            ->view('emails.weekly_summary');
    }
}


