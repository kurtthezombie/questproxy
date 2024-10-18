<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use URL;

class AccountDeletionMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $user;
    protected $url;

    /**
     * Create a new message instance.
     */
    public function __construct($user)
    {
        $this->user = $user;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address('no-reply@questproxy.com',''),
            subject: 'Confirm Your Account Deletion',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        //$deleteUrl = URL::signedRoute('user.delete',['id' => $this->user->id]);
        $url = env(key: 'APP_FRONTEND_URL') . '/confirm-delete?id=' . $this->user->id;

        return new Content(
            view: 'emails.delete-account',
            with: [
                'deleteUrl' => $url,
                'user' => $this->user,
            ]
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
