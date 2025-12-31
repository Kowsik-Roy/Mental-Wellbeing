<?php

namespace App\Mail;

use App\Models\User;
use App\Models\EmergencyContact;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmergencyAlertMail extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;
    public EmergencyContact $emergencyContact;

    public function __construct(User $user, EmergencyContact $emergencyContact)
    {
        $this->user = $user;
        $this->emergencyContact = $emergencyContact;
    }

    public function build(): self
    {
        return $this->subject('WellBeing Alert: ' . $this->user->name . ' may need support')
            ->view('emails.emergency-alert');
    }
}
