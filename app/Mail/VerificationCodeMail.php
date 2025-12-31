<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VerificationCodeMail extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;
    public string $code;
    public string $type;

    public function __construct(User $user, string $code, string $type)
    {
        $this->user = $user;
        $this->code = $code;
        $this->type = $type;
    }

    public function build(): self
    {
        $subject = match($this->type) {
            'registration' => 'Verify your email for WellBeing',
            'password_reset' => 'Reset your password for WellBeing',
            'password_change' => 'Verify your password change for WellBeing',
            default => 'Verification code for WellBeing',
        };

        return $this->subject($subject)
            ->view('emails.verification-code');
    }
}

