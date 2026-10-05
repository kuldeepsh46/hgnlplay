<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class HgnlNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public array $mailData;

    public function __construct(array $mailData)
    {
        $this->mailData = $mailData;
    }

    public function envelope(): Envelope
    {
        $fromAddress = config('mail.from.address')
            ?: env('MAIL_FROM_ADDRESS')
            ?: 'noreply@hgnlpay.com';

        $fromName = config('mail.from.name')
            ?: env('MAIL_FROM_NAME')
            ?: 'HGNL Pay';

        $supportAddress = $this->mailData['reply_to_email']
            ?? config('mail.support.address')
            ?? env('MAIL_SUPPORT_ADDRESS')
            ?? $fromAddress;

        $supportName = $this->mailData['reply_to_name']
            ?? config('mail.support.name')
            ?? env('MAIL_SUPPORT_NAME')
            ?? 'HGNL Pay Support';

        return new Envelope(
            from: new Address(
                $fromAddress,
                $fromName
            ),
            replyTo: [
                new Address(
                    $supportAddress,
                    $supportName
                ),
            ],
            subject: $this->mailData['subject'] ?? 'HGNL Pay Notification',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.hgnl_notification',
            with: [
                'mailData' => $this->mailData,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}