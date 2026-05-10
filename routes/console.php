<?php

use App\Jobs\ProcessAthletePayoutJob;
use App\Models\Deal;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::call(function () {
    Deal::query()
        ->whereNotNull('payout_auto_retry_requested_at')
        ->whereNull('released_at')
        ->whereIn('payment_status', ['paid_escrowed', 'paid'])
        ->each(function (Deal $deal) {
            ProcessAthletePayoutJob::dispatch($deal->id, $deal->user_id)
                ->delay(now()->addSeconds(15));
        });
})->everyFifteenMinutes()->name('sweep-athlete-payout-retries');
