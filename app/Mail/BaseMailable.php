<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

abstract class BaseMailable extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: config('mail.from.address', 'notifications@athletegum.com'),
            replyTo: 'business@athletegum.com',
            subject: $this->getSubject(),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: $this->getView(),
            with: $this->getViewData(),
        );
    }

    /**
     * Get the subject for the message.
     */
    abstract protected function getSubject(): string;

    /**
     * Get the view name for the message.
     */
    abstract protected function getView(): string;

    /**
     * Get the data to pass to the view.
     */
    abstract protected function getViewData(): array;
}

