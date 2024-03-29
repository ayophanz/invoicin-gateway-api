<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use Hashids\Hashids;

class ConfirmRegistrationMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $user;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $hashids    = new Hashids('secretkey', 12);
        $verifyLink = url('/verify-user/' . $hashids->encode($this->user->id));

        return $this->subject('Verify User')
            ->with([ 'verify_link' => $verifyLink ])
            ->markdown('emails.confirmRegistrationMail');
    }
}
