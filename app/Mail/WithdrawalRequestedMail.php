<?php

namespace App\Mail;

class WithdrawalRequestedMail extends BaseMailable
{
    public function __construct(
        public string $athleteName,
        public float $amount
    ) {
        //
    }

    protected function getSubject(): string
    {
        return 'Withdrawal request received';
    }

    protected function getView(): string
    {
        return 'emails.withdrawal-requested';
    }

    protected function getViewData(): array
    {
        return [
            'athleteName' => $this->athleteName,
            'amount' => $this->amount,
            'earningsUrl' => route('athlete.earnings.index'),
        ];
    }
}


