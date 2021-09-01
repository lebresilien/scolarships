<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Invitation;

class InviteMail extends Mailable
{
    use Queueable, SerializesModels;
    protected $invitation;
    protected $url;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Invitation $invitation, $url)
    {
        $this->invitation = $invitation;
        $this->url = $url;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('you@example.com')
        ->view('emails.invite')->with(["url" => $this->url]);
    }
}
