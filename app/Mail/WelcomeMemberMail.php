<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WelcomeMemberMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $password;

    /**
     * Pass the user and their plain-text password to the email.
     */
    public function __construct($user, $password)
    {
        $this->user = $user;
        $this->password = $password;
    }

    /**
     * Set the email subject.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Welcome to HG NL PAY - Account Details',
        );
    }

    /**
     * Define the view and pass data.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.welcome-member', // Make sure this file exists!
            with: [
                'userName' => $this->user->name,
                'userEmail' => $this->user->email,
                'userPassword' => $this->password,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}