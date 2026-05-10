<?php

namespace App\Jobs;

use App\Models\Deal;
use App\Models\Payout;
use App\Services\AthletePayoutReleaseResult;
use App\Services\AthletePayoutReleaseService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessAthletePayoutJob implements ShouldBeUnique, ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 100;

    public function __construct(
        public int $dealId,
        public int $businessUserId,
    ) {}

    public function uniqueId(): string
    {
        return 'process-athlete-payout-'.$this->dealId;
    }

    public function uniqueFor(): int
    {
        return 86400;
    }

    public function backoff(): array
    {
        return [120, 300, 600, 900, 1800, 3600];
    }

    public function handle(AthletePayoutReleaseService $service): void
    {
        $deal = Deal::find($this->dealId);
        if (! $deal) {
            return;
        }

        if ($deal->released_at) {
            return;
        }

        if (Payout::where('deal_id', $deal->id)->where('status', 'completed')->exists()) {
            return;
        }

        $result = $service->attemptRelease($deal, $this->businessUserId);

        if ($result->outcome === AthletePayoutReleaseResult::OUTCOME_TRANSFER_INITIATED) {
            return;
        }

        if ($result->outcome === AthletePayoutReleaseResult::OUTCOME_SETTLEMENT_RETRY
            || $result->outcome === AthletePayoutReleaseResult::OUTCOME_TRANSFER_FUNDS_RETRY) {
            $delay = max(60, $result->retryAfterSeconds);
            $this->release($delay);

            return;
        }

        if ($result->outcome === AthletePayoutReleaseResult::OUTCOME_INSUFFICIENT_PLATFORM_BALANCE) {
            $deal->update(['payout_auto_retry_requested_at' => null]);
            $this->fail(new \RuntimeException($result->message ?? 'Insufficient platform balance'));

            return;
        }

        if ($result->outcome === AthletePayoutReleaseResult::OUTCOME_VALIDATION_ERROR) {
            $deal->update(['payout_auto_retry_requested_at' => null]);
            $this->fail(new \RuntimeException($result->message ?? 'Validation error'));

            return;
        }

        $deal->update(['payout_auto_retry_requested_at' => null]);
        Log::warning('ProcessAthletePayoutJob stopped', [
            'deal_id' => $this->dealId,
            'outcome' => $result->outcome,
            'message' => $result->message,
        ]);
        $this->fail(new \RuntimeException($result->message ?? 'Release failed'));
    }
}
