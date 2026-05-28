<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewsletterMessage extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public string $subjectText,
        public string $bodyText
    ) {
    }

    public function build()
    {
        return $this->subject($this->subjectText)
            ->view('emails.newsletter')
            ->with(['bodyText' => $this->bodyText]);
    }
}
