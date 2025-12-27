<?php

namespace App\Mail;

use App\Models\Deal;

class PaymentReleasedMail extends BaseMailable
{
    public function __construct(
        public string $athleteName,
        public Deal $deal,
        public float $payoutAmount
    ) {
        //
    }

    protected function getSubject(): string
    {
        return 'Your payment has been released';
    }

    protected function getView(): string
    {
        return 'emails.payment-released';
    }

    protected function getViewData(): array
    {
        return [
            'athleteName' => $this->athleteName,
            'deal' => $this->deal,
            'payoutAmount' => $this->payoutAmount,
            'earningsUrl' => route('athlete.earnings.index'),
        ];
    }
}


