<?php

namespace App\Mail;

use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class PasswordResetMail extends BaseMailable
{
    public function __construct(
        public string $firstName,
        public string $resetUrl
    ) {
        //
    }

    protected function getSubject(): string
    {
        return 'Reset your AthleteGum password';
    }

    protected function getView(): string
    {
        return 'emails.password-reset';
    }

    protected function getViewData(): array
    {
        return [
            'firstName' => $this->firstName,
            'resetUrl' => $this->resetUrl,
        ];
    }
}


