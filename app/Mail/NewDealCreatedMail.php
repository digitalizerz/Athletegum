<?php

namespace App\Mail;

use App\Models\Deal;

class NewDealCreatedMail extends BaseMailable
{
    public function __construct(
        public string $athleteName,
        public Deal $deal
    ) {
        //
    }

    protected function getSubject(): string
    {
        return "You've received a new deal";
    }

    protected function getView(): string
    {
        return 'emails.new-deal-created';
    }

    protected function getViewData(): array
    {
        return [
            'athleteName' => $this->athleteName,
            'deal' => $this->deal,
            'dealUrl' => route('deals.show.token', $this->deal->token),
        ];
    }
}


