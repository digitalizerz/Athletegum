<?php

namespace App\Mail;

class VerifyEmailMail extends BaseMailable
{
    public function __construct(
        public string $firstName,
        public string $verificationUrl
    ) {
        //
    }

    protected function getSubject(): string
    {
        return 'Verify your email address';
    }

    protected function getView(): string
    {
        return 'emails.verify-email';
    }

    protected function getViewData(): array
    {
        return [
            'firstName' => $this->firstName,
            'verificationUrl' => $this->verificationUrl,
        ];
    }
}


