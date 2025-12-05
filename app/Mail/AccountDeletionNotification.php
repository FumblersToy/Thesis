<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AccountDeletionNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $reason;
    public $daysUntilDeletion;
    public $appealUrl;

    /**
     * Create a new message instance.
     */
    public function __construct($user, $reason, $daysUntilDeletion = 15)
    {
        $this->user = $user;
        $this->reason = $reason;
        $this->daysUntilDeletion = $daysUntilDeletion;
        $this->appealUrl = url('/account/appeal');
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Account Deletion Notice - Action Required',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.account-deletion',
        );
    }
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
