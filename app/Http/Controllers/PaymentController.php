<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessAthletePayoutJob;
use App\Models\Deal;
use App\Models\PaymentMethod;
use App\Services\AthletePayoutReleaseResult;
use App\Services\AthletePayoutReleaseService;
use App\Services\StripeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    protected StripeService $stripeService;

    public function __construct(
        StripeService $stripeService,
        protected AthletePayoutReleaseService $payoutReleaseService,
    ) {
        $this->stripeService = $stripeService;
    }
    /**
     * Store payment method selection for a deal (Step 5 - NO PAYMENT CHARGED)
     * Payment is only charged when user clicks "Pay & Create Deal" in Step 6
     */
    public function processDealPayment(Request $request)
    {
        $validated = $request->validate([
            'payment_method_id' => ['required', 'exists:payment_methods,id'],
        ]);

        $session = $request->session();

        if (!$session->has('deal_type') || !$session->has('compensation_amount') || !$session->has('deadline')) {
            return redirect()->route('deals.create')->withErrors(['error' => 'Session expired. Please start over.']);
        }

        $user = Auth::user();
        $paymentMethod = PaymentMethod::findOrFail($validated['payment_method_id']);
        if ($paymentMethod->user_id !== $user->id) {
            return redirect()->back()->withErrors(['error' => 'Invalid payment method.']);
        }

        $dealAmount = (float) $session->get('compensation_amount');
        $split = Deal::feeSplitFromCompensation($dealAmount);
        $platformFeePercentage = $split['platform_fee_percentage'];
        $platformFeeAmount = $split['platform_fee_amount'];
        $businessTotal = $split['total_amount'];
        $escrowAmount = $split['escrow_amount'];

        $session->put('platform_fee_type', $split['platform_fee_type']);
        $session->put('platform_fee_percentage', $platformFeePercentage);
        $session->put('platform_fee_amount', $platformFeeAmount);
        $session->put('athlete_fee_percentage', $split['athlete_fee_percentage']);
        $session->put('athlete_fee_amount', $split['athlete_fee_amount']);
        $session->put('athlete_net_payout', $split['athlete_net_payout']);
        $session->put('escrow_amount', $escrowAmount);
        $session->put('compensation_amount', $dealAmount);
        $session->put('total_amount', $businessTotal);
        $session->put('wallet_amount_used', 0);
        $session->put('card_amount', $businessTotal);
        $session->put('payment_method', 'card');
        $session->put('card_payment_method_id', $validated['payment_method_id']);

        return redirect()->route('deals.review');
    }

    /**
     * Actually charge payment for a deal (called from DealController::store() when "Pay & Create Deal" is clicked)
     * This is the ONLY place where payment should be charged
     */
    public function chargeDealPayment($session, $user)
    {
        if ($session->get('payment_method') !== 'card') {
            throw new \Exception('Card payment is required for new deals. Go back to the payment step and select a saved card.');
        }

        $cardPaymentMethodId = $session->get('card_payment_method_id');
        $dealAmount = (float) $session->get('compensation_amount');
        if (!$session->has('platform_fee_amount')) {
            $split = Deal::feeSplitFromCompensation($dealAmount);
            $session->put('platform_fee_type', $split['platform_fee_type']);
            $session->put('platform_fee_percentage', $split['platform_fee_percentage']);
            $session->put('platform_fee_amount', $split['platform_fee_amount']);
            $session->put('athlete_fee_percentage', $split['athlete_fee_percentage']);
            $session->put('athlete_fee_amount', $split['athlete_fee_amount']);
            $session->put('athlete_net_payout', $split['athlete_net_payout']);
        }
        $platformFeeAmount = (float) $session->get('platform_fee_amount');
        $escrowAmount = (float) $session->get('escrow_amount');
        $businessTotal = (float) $session->get('total_amount');
        $cardAmount = $businessTotal;

        $paymentIntentId = null;
        $chargeId = null;
        $paymentStatus = 'pending';

        try {
            DB::beginTransaction();

            if (!$this->stripeService->isConfigured()) {
                DB::rollBack();
                throw new \Exception('Stripe is not configured. Please contact support.');
            }

            $paymentMethodModel = PaymentMethod::findOrFail($cardPaymentMethodId);
            if ($paymentMethodModel->user_id !== $user->id) {
                DB::rollBack();
                throw new \Exception('Invalid payment method.');
            }

            $customerId = $this->stripeService->getOrCreateCustomer(
                $user->id,
                $user->email,
                $user->name
            );

            $paymentIntent = $this->stripeService->createPaymentIntent(
                $cardAmount,
                $paymentMethodModel->provider_payment_method_id,
                $platformFeeAmount,
                [
                    'user_id' => $user->id,
                    'deal_type' => $session->get('deal_type'),
                    'compensation_amount' => (string) $dealAmount,
                    'platform_fee_amount' => (string) $platformFeeAmount,
                    'escrow_amount' => (string) $escrowAmount,
                    'business_total' => (string) $businessTotal,
                ],
                $customerId
            );

            if ($paymentIntent->status === 'requires_action' || $paymentIntent->status === 'requires_confirmation') {
                DB::rollBack();
                throw new \Exception('Payment requires additional authentication. Please try again.');
            }

            if ($paymentIntent->status !== 'succeeded') {
                DB::rollBack();
                throw new \Exception('Payment failed. Status: ' . $paymentIntent->status);
            }

            $paymentIntentId = $paymentIntent->id;
            $chargeId = $paymentIntent->charges->data[0]->id ?? null;
            $paymentStatus = 'pending';

            Log::info('Stripe PaymentIntent succeeded', [
                'payment_intent_id' => $paymentIntentId,
                'amount' => $cardAmount,
                'user_id' => $user->id,
            ]);

            // Store payment info in session
            $session->put('payment_intent_id', $paymentIntentId);
            $session->put('stripe_charge_id', $chargeId);
            $session->put('payment_status', $paymentStatus);

            DB::commit();

            return [
                'payment_intent_id' => $paymentIntentId,
                'stripe_charge_id' => $chargeId,
                'payment_status' => $paymentStatus,
            ];

        } catch (\Stripe\Exception\CardException $e) {
            DB::rollBack();
            Log::error('Stripe card payment failed', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
            ]);
            throw new \Exception('Card payment failed: ' . $e->getError()->message);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Payment processing failed', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
            ]);
            throw $e;
        }
    }

    /**
     * Release escrow payment to athlete (only after SMB approval)
     */
    public function releasePayment(Deal $deal)
    {
        if ($deal->user_id !== Auth::id()) {
            abort(403);
        }

        $result = $this->payoutReleaseService->attemptRelease($deal, Auth::id());

        return match ($result->outcome) {
            AthletePayoutReleaseResult::OUTCOME_TRANSFER_INITIATED => redirect()->back()->with('success', $result->message),
            AthletePayoutReleaseResult::OUTCOME_VALIDATION_ERROR,
            AthletePayoutReleaseResult::OUTCOME_INSUFFICIENT_PLATFORM_BALANCE,
            AthletePayoutReleaseResult::OUTCOME_STRIPE_ERROR,
            AthletePayoutReleaseResult::OUTCOME_GENERIC_ERROR => redirect()->back()->withErrors([
                'error' => $result->message ?? 'Unable to release payment.',
            ]),
            AthletePayoutReleaseResult::OUTCOME_SETTLEMENT_RETRY => $this->dispatchPayoutRetryAndRedirect(
                $deal,
                $result->retryAfterSeconds,
                'Funds are still settling in Stripe. We will retry releasing to the athlete automatically — you do not need to click again.'
            ),
            AthletePayoutReleaseResult::OUTCOME_TRANSFER_FUNDS_RETRY => $this->dispatchPayoutRetryAndRedirect(
                $deal,
                $result->retryAfterSeconds,
                'Transfer could not complete yet — platform balance is still catching up. We will retry automatically.'
            ),
            default => redirect()->back()->withErrors(['error' => $result->message ?? 'Unable to release payment.']),
        };
    }

    protected function dispatchPayoutRetryAndRedirect(Deal $deal, int $retryAfterSeconds, string $successMessage)
    {
        ProcessAthletePayoutJob::dispatch($deal->id, Auth::id())
            ->delay(now()->addSeconds(max(60, $retryAfterSeconds)));

        return redirect()->back()->with('success', $successMessage);
    }
}
