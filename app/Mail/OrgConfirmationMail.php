<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;
use App\Models\Organization;
use Hashids\Hashids;

class OrgConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $organization;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Organization $organization)
    {
        $this->organization = $organization;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $secretKey  = Str::random(40);
        $hashids    = new Hashids($secretKey);
        $verifyLink = $hashids->encode($this->organization->uuid);

        return $this->subject('Verify Organization')
            ->with([ 'verify_link' => $verifyLink ])
            ->markdown('emails.orgConfirmationMail');
    }
}
