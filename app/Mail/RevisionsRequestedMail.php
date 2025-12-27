<?php

namespace App\Mail;

use App\Models\Deal;

class RevisionsRequestedMail extends BaseMailable
{
    public function __construct(
        public string $athleteName,
        public Deal $deal,
        public string $revisionNotes
    ) {
        //
    }

    protected function getSubject(): string
    {
        return 'Revisions requested on your submission';
    }

    protected function getView(): string
    {
        return 'emails.revisions-requested';
    }

    protected function getViewData(): array
    {
        return [
            'athleteName' => $this->athleteName,
            'deal' => $this->deal,
            'revisionNotes' => $this->revisionNotes,
            'dealUrl' => route('athlete.deals.show', $this->deal),
        ];
    }
}


