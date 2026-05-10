<?php

namespace App\Services;

use App\Models\Deal;
use App\Models\Payout;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Stripe\Account as StripeAccount;
use Stripe\Exception\InvalidRequestException;

class AthletePayoutReleaseService
{
    public function __construct(
        protected StripeService $stripeService,
    ) {}

    /**
     * Attempt escrow release / Stripe transfer to athlete. Used by HTTP controller and queued jobs.
     */
    public function attemptRelease(Deal $deal, int $releasedByUserId): AthletePayoutReleaseResult
    {
        if ($deal->payment_status !== 'paid_escrowed' && $deal->payment_status !== 'paid') {
            return AthletePayoutReleaseResult::validationError(
                'Deal payment has not been completed. Payment status: '.$deal->payment_status
            );
        }

        if ($deal->status !== 'completed' && ! $deal->is_approved) {
            return AthletePayoutReleaseResult::validationError(
                'Deal must be completed by athlete or approved before payment can be released.'
            );
        }

        $existingPayout = Payout::where('deal_id', $deal->id)
            ->where('status', 'completed')
            ->first();

        if ($existingPayout) {
            return AthletePayoutReleaseResult::validationError('Payment has already been released for this deal.');
        }

        if ($deal->released_at) {
            return AthletePayoutReleaseResult::validationError('Payment has already been released.');
        }

        $payoutAmounts = $deal->getPayoutAmountsForRelease();
        $dealAmount = $payoutAmounts['deal_amount'];
        $platformFeeAmount = $payoutAmounts['platform_fee_amount'];
        $athleteFeePercentage = $payoutAmounts['athlete_fee_percentage'];
        $athleteFeeAmount = $payoutAmounts['athlete_fee_amount'];
        $athleteNetPayout = $payoutAmounts['athlete_net_payout'];

        $this->refreshAwaitingFundsClearance($deal, $athleteNetPayout);
        $deal->refresh();

        $athlete = $deal->athlete;
        if (! $athlete) {
            return AthletePayoutReleaseResult::validationError('Athlete not found for this deal.');
        }

        $athlete->refresh();

        $paymentMethod = $athlete->paymentMethods()
            ->where('is_active', true)
            ->whereNotNull('provider_account_id')
            ->orderBy('is_default', 'desc')
            ->orderBy('created_at', 'desc')
            ->first();

        if ($paymentMethod && $paymentMethod->provider_account_id) {
            $stripeAccountId = $paymentMethod->provider_account_id;
            if ($athlete->stripe_account_id !== $stripeAccountId) {
                $athlete->update(['stripe_account_id' => $stripeAccountId]);
            }
        } else {
            $stripeAccountId = $athlete->stripe_account_id;
        }

        if (! $stripeAccountId) {
            return AthletePayoutReleaseResult::validationError(
                'Athlete does not have a Stripe account configured. They must set up payment methods in their Earnings section first.'
            );
        }

        if (! str_starts_with($stripeAccountId, 'acct_')) {
            Log::error('Invalid Stripe account ID format for athlete', [
                'athlete_id' => $athlete->id,
                'stripe_account_id' => $stripeAccountId,
            ]);

            return AthletePayoutReleaseResult::validationError(
                'Athlete has an invalid Stripe account configuration. The account ID must start with "acct_". Please have the athlete update their payment methods in their Earnings section.'
            );
        }

        try {
            $platformAccount = StripeAccount::retrieve();
            $platformAccountId = $platformAccount->id;

            Log::info('Preparing Stripe transfer', [
                'deal_id' => $deal->id,
                'athlete_id' => $athlete->id,
                'athlete_stripe_account_id' => $stripeAccountId,
                'platform_account_id' => $platformAccountId,
                'accounts_match' => $stripeAccountId === $platformAccountId,
            ]);

            if ($stripeAccountId === $platformAccountId) {
                return AthletePayoutReleaseResult::validationError(
                    'The athlete\'s Stripe account ID is the same as the platform account. The athlete must connect their own Stripe Connect account (not the platform account). Please have them remove the current payment method and add a new one with their own Stripe Connect account ID from their Stripe Dashboard.'
                );
            }
        } catch (\Exception $e) {
            Log::warning('Could not retrieve platform account for validation', [
                'error' => $e->getMessage(),
                'athlete_stripe_account_id' => $stripeAccountId,
            ]);
        }

        $isStripePayment = $deal->payment_intent_id && str_starts_with((string) $deal->payment_intent_id, 'pi_');

        if ($isStripePayment) {
            try {
                $paymentIntent = $this->stripeService->getPaymentIntent($deal->payment_intent_id, ['expand' => ['charges']]);

                if ($paymentIntent->status !== 'succeeded') {
                    Log::warning('PaymentIntent is not succeeded, cannot release payment', [
                        'deal_id' => $deal->id,
                        'payment_intent_id' => $deal->payment_intent_id,
                        'payment_intent_status' => $paymentIntent->status,
                    ]);

                    return AthletePayoutReleaseResult::validationError(
                        'Cannot release payment: The payment has not completed successfully. Payment status: '.$paymentIntent->status.'. Please wait a few minutes and try again, or contact support if the issue persists.'
                    );
                }
            } catch (\Exception $e) {
                Log::error('Failed to verify PaymentIntent for release', [
                    'deal_id' => $deal->id,
                    'payment_intent_id' => $deal->payment_intent_id,
                    'error' => $e->getMessage(),
                ]);

                return AthletePayoutReleaseResult::validationError(
                    'Cannot release payment: Failed to verify payment in Stripe. Please contact support.'
                );
            }
        }

        if (! $this->stripeService->isConfigured()) {
            return AthletePayoutReleaseResult::validationError('Stripe is not configured. Cannot release payment.');
        }

        if ($isStripePayment) {
            try {
                $breakdown = $this->stripeService->getUsdBalanceBreakdown();
                $availableBalance = $breakdown['available'];
                $pendingBalance = $breakdown['pending'];

                if ($availableBalance >= $athleteNetPayout) {
                    $deal->update(['awaiting_funds' => false]);

                    Log::info('Stripe balance sufficient for payout', [
                        'deal_id' => $deal->id,
                        'athlete_id' => $athlete->id,
                        'required_amount' => $athleteNetPayout,
                        'available_balance' => $availableBalance,
                        'pending_balance' => $pendingBalance,
                    ]);
                } elseif (($availableBalance + $pendingBalance) >= $athleteNetPayout) {
                    $deal->update([
                        'awaiting_funds' => true,
                        'payout_auto_retry_requested_at' => now(),
                    ]);

                    Log::warning('Stripe payout blocked: funds still pending settlement', [
                        'deal_id' => $deal->id,
                        'athlete_id' => $athlete->id,
                        'required_amount' => $athleteNetPayout,
                        'available_balance' => $availableBalance,
                        'pending_balance' => $pendingBalance,
                    ]);

                    return AthletePayoutReleaseResult::settlementRetry(120);
                }

                $deal->update(['awaiting_funds' => true]);

                Log::warning('Insufficient Stripe balance for payout', [
                    'deal_id' => $deal->id,
                    'athlete_id' => $athlete->id,
                    'required_amount' => $athleteNetPayout,
                    'available_balance' => $availableBalance,
                    'pending_balance' => $pendingBalance,
                ]);

                $message = 'Cannot release payment: Stripe does not have enough funds (available + pending) to cover this athlete payout. Wait for charges to settle or contact support.';
                $walletApplied = (float) ($deal->wallet_amount_applied ?? 0);
                if ($walletApplied > 0.0) {
                    $message .= ' Note: amounts paid from the business wallet do not appear in Stripe — transfers to athletes still require sufficient Stripe available balance.';
                }

                return AthletePayoutReleaseResult::insufficientPlatformBalance($message);
            } catch (\Exception $e) {
                Log::error('Failed to check Stripe balance', [
                    'deal_id' => $deal->id,
                    'error' => $e->getMessage(),
                ]);

                return AthletePayoutReleaseResult::stripeError(
                    'Failed to verify Stripe balance. Please try again later or contact support.'
                );
            }
        } else {
            $deal->update(['awaiting_funds' => false]);
        }

        $idempotencyKey = "deal_{$deal->id}_release_v1";

        $existingPayoutByKey = Payout::where('idempotency_key', $idempotencyKey)->first();
        if ($existingPayoutByKey && $existingPayoutByKey->status === 'completed') {
            return AthletePayoutReleaseResult::validationError('Payment has already been released for this deal.');
        }

        DB::beginTransaction();

        try {
            $existingPayoutByKey = Payout::where('idempotency_key', $idempotencyKey)->lockForUpdate()->first();
            if ($existingPayoutByKey && $existingPayoutByKey->status === 'completed') {
                DB::rollBack();

                return AthletePayoutReleaseResult::validationError('Payment has already been released for this deal.');
            }

            $payout = Payout::create([
                'deal_id' => $deal->id,
                'athlete_id' => $athlete->id,
                'amount' => $athleteNetPayout,
                'currency' => 'usd',
                'status' => 'pending',
                'released_by_admin_id' => $releasedByUserId,
                'idempotency_key' => $idempotencyKey,
            ]);

            Log::info('Attempting Stripe transfer to athlete', [
                'deal_id' => $deal->id,
                'athlete_id' => $athlete->id,
                'athlete_stripe_account_id' => $stripeAccountId,
                'transfer_amount' => $athleteNetPayout,
                'idempotency_key' => $idempotencyKey,
                'payout_id' => $payout->id,
            ]);

            $transfer = $this->stripeService->transferToAthlete(
                $athleteNetPayout,
                $stripeAccountId,
                $idempotencyKey,
                [
                    'deal_id' => (string) $deal->id,
                    'athlete_id' => (string) $athlete->id,
                    'deal_amount' => (string) $dealAmount,
                    'platform_fee_percentage' => (string) ($deal->platform_fee_percentage ?? ''),
                    'platform_fee_amount' => (string) $platformFeeAmount,
                    'athlete_fee_percentage' => (string) $athleteFeePercentage,
                    'athlete_fee_amount' => (string) $athleteFeeAmount,
                    'athlete_net_payout' => (string) $athleteNetPayout,
                    'payout_id' => (string) $payout->id,
                ]
            );

            $payout->update([
                'stripe_transfer_id' => $transfer->id,
                'status' => 'pending',
            ]);

            $deal->update([
                'status' => 'approved',
                'stripe_transfer_id' => $transfer->id,
                'stripe_transfer_status' => 'pending',
                'release_transaction_id' => $transfer->id,
                'athlete_fee_percentage' => $athleteFeePercentage,
                'athlete_fee_amount' => $athleteFeeAmount,
                'athlete_net_payout' => $athleteNetPayout,
                'is_approved' => true,
                'approved_at' => now(),
                'awaiting_funds' => false,
                'payout_auto_retry_requested_at' => null,
            ]);

            Log::info('Stripe transfer created - waiting for webhook confirmation', [
                'deal_id' => $deal->id,
                'transfer_id' => $transfer->id,
                'athlete_id' => $athlete->id,
                'stripe_account_id' => $stripeAccountId,
                'amount' => $athleteNetPayout,
                'idempotency_key' => $idempotencyKey,
                'payout_id' => $payout->id,
                'note' => 'Deal will be marked as released when transfer.paid webhook is received',
            ]);

            DB::commit();

            return AthletePayoutReleaseResult::transferInitiated(
                'Payment transfer initiated. The deal will be marked as released once Stripe confirms the transfer. This typically takes a few seconds.'
            );
        } catch (InvalidRequestException $e) {
            DB::rollBack();

            $errorMessage = $e->getMessage();
            $stripeErrorCode = $e->getStripeCode() ?? 'unknown';

            if (str_contains($errorMessage, 'cannot be set to your own account')) {
                $errorMessage = 'The athlete\'s Stripe account ID is the same as the platform account. The athlete must connect their own Stripe Connect account (not the platform account). Please have them remove the current payment method and add a new one with their own Stripe Connect account ID from their Stripe Dashboard.';
            } elseif (str_contains($errorMessage, 'No such destination') || str_contains($errorMessage, 'does not exist') || str_contains($errorMessage, 'No such account')) {
                $errorMessage = 'The athlete\'s Stripe account ID "'.$stripeAccountId.'" does not exist or is not accessible. The athlete must update their payment methods with a valid Stripe Connect account ID. They can find their account ID in their Stripe Dashboard → Settings → Connect → Accounts.';
            } elseif (str_contains($errorMessage, 'Invalid account')) {
                $errorMessage = 'The athlete\'s Stripe account ID is invalid. Please have them update their payment methods with a valid Stripe Connect account ID.';
            } elseif (str_contains($errorMessage, 'insufficient available funds') || str_contains($errorMessage, 'insufficient funds')) {
                $deal->update([
                    'awaiting_funds' => true,
                    'payout_auto_retry_requested_at' => now(),
                ]);

                Log::warning('Stripe transfer insufficient funds — will retry', [
                    'deal_id' => $deal->id,
                    'athlete_id' => $athlete->id,
                    'transfer_amount' => $athleteNetPayout,
                    'stripe_error_type' => $stripeErrorCode,
                ]);

                return AthletePayoutReleaseResult::transferFundsRetry(
                    'Insufficient available balance for transfer; retry scheduled.',
                    300
                );
            }

            Log::error('Stripe transfer failed - InvalidRequestException', [
                'deal_id' => $deal->id,
                'athlete_id' => $athlete->id,
                'athlete_stripe_account_id' => $stripeAccountId,
                'transfer_amount' => $athleteNetPayout,
                'idempotency_key' => $idempotencyKey,
                'error' => $e->getMessage(),
                'stripe_error_type' => $stripeErrorCode,
            ]);

            return AthletePayoutReleaseResult::stripeError(
                'Failed to transfer funds to athlete: '.$errorMessage
            );
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Stripe transfer failed - unexpected error', [
                'deal_id' => $deal->id,
                'athlete_id' => $athlete->id,
                'athlete_stripe_account_id' => $stripeAccountId,
                'transfer_amount' => $athleteNetPayout,
                'idempotency_key' => $idempotencyKey,
                'error' => $e->getMessage(),
                'error_type' => get_class($e),
            ]);

            return AthletePayoutReleaseResult::stripeError(
                'Failed to transfer funds to athlete: '.$e->getMessage()
            );
        }
    }

    /**
     * If card funds have cleared in Stripe, clear awaiting_funds so release is not blocked unnecessarily.
     */
    protected function refreshAwaitingFundsClearance(Deal $deal, float $athleteNetPayout): void
    {
        $usesStripeCapture = $deal->payment_intent_id && str_starts_with((string) $deal->payment_intent_id, 'pi_');

        if (! $usesStripeCapture) {
            if ($deal->awaiting_funds) {
                $deal->update([
                    'awaiting_funds' => false,
                    'payout_auto_retry_requested_at' => null,
                ]);
            }

            return;
        }

        if (! $this->stripeService->isConfigured()) {
            return;
        }

        try {
            $available = $this->stripeService->getUsdBalanceBreakdown()['available'];
            if ($available >= $athleteNetPayout && $deal->awaiting_funds) {
                $deal->update([
                    'awaiting_funds' => false,
                    'payout_auto_retry_requested_at' => null,
                ]);
                $deal->refresh();
            }
        } catch (\Exception $e) {
            Log::warning('refreshAwaitingFundsClearance skipped', [
                'deal_id' => $deal->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
