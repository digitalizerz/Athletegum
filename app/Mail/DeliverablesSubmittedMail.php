<?php

namespace App\Mail;

use App\Models\Deal;

class DeliverablesSubmittedMail extends BaseMailable
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
        return 'Deliverables submitted for review';
    }

    protected function getView(): string
    {
        return 'emails.deliverables-submitted';
    }

    protected function getViewData(): array
    {
        return [
            'businessName' => $this->businessName,
            'athleteName' => $this->athleteName,
            'deal' => $this->deal,
            'dealUrl' => route('deals.success', $this->deal),
        ];
    }
}


