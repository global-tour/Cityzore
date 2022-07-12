<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class BulkMail extends Mailable
{
    use Queueable, SerializesModels;

    private $content;

    private $subj;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($details)
    {
        $this->content = $details['content'];
        $this->subj = $details['subject'];
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject($this->subj)->view('mail.bulk-mail')->with([
            'content' => $this->content
        ]);
    }
}
