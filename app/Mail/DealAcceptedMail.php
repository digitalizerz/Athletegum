<?php

namespace App\Mail;

use App\Models\Deal;

class DealAcceptedMail extends BaseMailable
{
    public function __construct(
        public string $businessName,
        public string $athleteName,
        public Deal $deal
    ) {
        //
    }

    protected function getSubject(): string
    {
        return 'Deal accepted by athlete';
    }

    protected function getView(): string
    {
        return 'emails.deal-accepted';
    }

    protected function getViewData(): array
    {
        return [
            'businessName' => $this->businessName,
            'athleteName' => $this->athleteName,
            'deal' => $this->deal,
            'dealUrl' => route('deals.messages', $this->deal),
        ];
    }
}

