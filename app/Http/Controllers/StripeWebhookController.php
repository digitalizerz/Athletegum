<?php

namespace App\Http\Controllers;

use App\Models\Deal;
use App\Services\StripeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;

class StripeWebhookController extends Controller
{
    protected StripeService $stripeService;

    public function __construct(StripeService $stripeService)
    {
        $this->stripeService = $stripeService;
    }

    /**
     * Handle Stripe webhook events
     */
    public function handle(Request $request)
    {
        $payload = $request->getContent();
        $signature = $request->header('Stripe-Signature');
        $webhookSecret = env('STRIPE_WEBHOOK_SECRET');

        if (!$webhookSecret) {
            Log::error('STRIPE_WEBHOOK_SECRET is not set');
            return response()->json(['error' => 'Webhook secret not configured'], 500);
        }

        try {
            $event = Webhook::constructEvent($payload, $signature, $webhookSecret);
        } catch (SignatureVerificationException $e) {
            Log::error('Stripe webhook signature verification failed', [
                'error' => $e->getMessage(),
            ]);
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        Log::info('Stripe webhook received', [
            'event_id' => $event->id,
            'event_type' => $event->type,
        ]);

        // Handle different event types
        switch ($event->type) {
            case 'payment_intent.succeeded':
                $this->handlePaymentIntentSucceeded($event->data->object);
                break;

            case 'payment_intent.payment_failed':
                $this->handlePaymentIntentFailed($event->data->object);
                break;

            case 'charge.succeeded':
                $this->handleChargeSucceeded($event->data->object);
                break;

            case 'transfer.created':
                $this->handleTransferCreated($event->data->object);
                break;

            default:
                Log::info('Unhandled Stripe webhook event', [
                    'event_type' => $event->type,
                ]);
        }

        return response()->json(['status' => 'ok']);
    }

    /**
     * Handle successful payment intent
     */
    protected function handlePaymentIntentSucceeded($paymentIntent)
    {
        $paymentIntentId = $paymentIntent->id;

        // Find deal by payment intent ID
        $deal = Deal::where('payment_intent_id', $paymentIntentId)->first();

        if (!$deal) {
            Log::warning('PaymentIntent succeeded but deal not found', [
                'payment_intent_id' => $paymentIntentId,
            ]);
            return;
        }

        // Update deal payment status to paid_escrowed (escrow is internal DB state)
        // Mark as awaiting_funds because Stripe funds may still be pending
        // Store charge ID if available
        $chargeId = null;
        if (isset($paymentIntent->charges) && count($paymentIntent->charges->data) > 0) {
            $chargeId = $paymentIntent->charges->data[0]->id;
        }
        
        $deal->update([
            'payment_status' => 'paid_escrowed', // Mark as paid_escrowed (escrow is internal)
            'awaiting_funds' => true, // Funds may still be pending in Stripe
            'stripe_charge_id' => $chargeId, // Store charge ID for reference
            'paid_at' => now(),
        ]);

        Log::info('Deal payment confirmed via webhook', [
            'deal_id' => $deal->id,
            'payment_intent_id' => $paymentIntentId,
            'amount' => $paymentIntent->amount / 100,
        ]);
    }

    /**
     * Handle failed payment intent
     */
    protected function handlePaymentIntentFailed($paymentIntent)
    {
        $paymentIntentId = $paymentIntent->id;

        $deal = Deal::where('payment_intent_id', $paymentIntentId)->first();

        if (!$deal) {
            return;
        }

        $deal->update([
            'payment_status' => 'failed',
        ]);

        Log::warning('Deal payment failed via webhook', [
            'deal_id' => $deal->id,
            'payment_intent_id' => $paymentIntentId,
        ]);
    }

    /**
     * Handle successful charge
     */
    protected function handleChargeSucceeded($charge)
    {
        // Charge succeeded - payment is confirmed
        // This is a backup confirmation in case payment_intent.succeeded wasn't received
        $paymentIntentId = $charge->payment_intent;

        if ($paymentIntentId) {
            $deal = Deal::where('payment_intent_id', $paymentIntentId)->first();

            if ($deal && $deal->payment_status !== 'paid_escrowed' && $deal->payment_status !== 'paid') {
                $deal->update([
                    'payment_status' => 'paid_escrowed', // Mark as paid_escrowed (escrow is internal)
                    'awaiting_funds' => true, // Funds may still be pending in Stripe
                    'stripe_charge_id' => $charge->id, // Store charge ID for reference
                    'paid_at' => now(),
                ]);

                Log::info('Deal payment confirmed via charge webhook', [
                    'deal_id' => $deal->id,
                    'charge_id' => $charge->id,
                    'payment_intent_id' => $paymentIntentId,
                ]);
            }
        }
    }

    /**
     * Handle transfer created (athlete payout)
     * Stripe Transfers are considered successful immediately on transfer.created
     */
    protected function handleTransferCreated($transfer)
    {
        Log::info('Stripe transfer created webhook received', [
            'transfer_id' => $transfer->id,
            'amount' => $transfer->amount / 100,
            'destination' => $transfer->destination,
        ]);

        // Find deal by transfer metadata
        if (isset($transfer->metadata['deal_id'])) {
            $deal = Deal::find($transfer->metadata['deal_id']);

            if (!$deal) {
                Log::warning('Transfer created but deal not found', [
                    'transfer_id' => $transfer->id,
                    'deal_id' => $transfer->metadata['deal_id'] ?? null,
                ]);
                return;
            }

            // Also find the payout record
            $payout = \App\Models\Payout::where('stripe_transfer_id', $transfer->id)->first();

            try {
                \Illuminate\Support\Facades\DB::beginTransaction();

                // Update deal to released status - Stripe Transfer is successful on created
                $wasApproved = $deal->is_approved;
                $updateData = [
                    'stripe_transfer_id' => $transfer->id,
                    'stripe_transfer_status' => 'paid',
                    'released_at' => now(),
                    'release_transaction_id' => $transfer->id,
                    'status' => 'completed',
                    'payment_status' => 'released', // Mark as released - Stripe confirmed
                ];

                // Auto-approve if not already approved
                if (!$wasApproved) {
                    $updateData['is_approved'] = true;
                    $updateData['approved_at'] = now();
                }

                $deal->update($updateData);

                // Update payout record
                if ($payout) {
                    $payout->update([
                        'status' => 'completed',
                        'released_at' => now(),
                    ]);
                }

                \Illuminate\Support\Facades\DB::commit();

                // Create system message
                $messageText = $wasApproved 
                    ? "Payment released from escrow"
                    : "Deal approved and payment released from escrow";
                \App\Models\Message::createSystemMessage(
                    $deal->id,
                    $messageText
                );

                // Create notification for athlete
                if ($deal->athlete_id && $deal->athlete) {
                    \App\Models\Notification::createForAthlete(
                        $deal->athlete_id,
                        'payment_released',
                        'Payment Released',
                        "Your payment of $" . number_format($transfer->amount / 100, 2) . " has been released for deal #{$deal->id}",
                        route('athlete.deals.show', $deal->id),
                        $deal->id
                    );
                }

                // Send email to athlete (if PaymentReleasedMail exists)
                try {
                    if ($deal->athlete && $deal->athlete->email) {
                        if (class_exists(\App\Mail\PaymentReleasedMail::class)) {
                            \Illuminate\Support\Facades\Mail::to($deal->athlete->email)->send(
                                new \App\Mail\PaymentReleasedMail($deal->athlete->name, $deal, $transfer->amount / 100)
                            );
                            Log::info('Payment released email sent', [
                                'deal_id' => $deal->id,
                                'athlete_email' => $deal->athlete->email,
                            ]);
                        }
                    }
                } catch (\Exception $e) {
                    Log::error('Failed to send payment released email', [
                        'deal_id' => $deal->id,
                        'athlete_id' => $deal->athlete_id,
                        'error' => $e->getMessage(),
                    ]);
                    // Don't fail the payment release if email fails
                }

                Log::info('Deal marked as released via transfer.created webhook', [
                    'deal_id' => $deal->id,
                    'transfer_id' => $transfer->id,
                    'payout_id' => $payout->id ?? null,
                ]);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\DB::rollBack();
                Log::error('Failed to process transfer.created webhook', [
                    'deal_id' => $deal->id,
                    'transfer_id' => $transfer->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
            }
        }
    }


    /**
     * Handle transfer failed (athlete payout failed)
     * Do NOT mark deal as released if transfer fails
     */
    protected function handleTransferFailed($transfer)
    {
        Log::warning('Stripe transfer failed webhook received', [
            'transfer_id' => $transfer->id,
            'amount' => $transfer->amount / 100,
            'destination' => $transfer->destination,
        ]);

        // Find deal by transfer metadata
        if (isset($transfer->metadata['deal_id'])) {
            $deal = Deal::find($transfer->metadata['deal_id']);

            if ($deal) {
                $deal->update([
                    'stripe_transfer_status' => 'failed',
                ]);

                // Update payout record
                $payout = \App\Models\Payout::where('stripe_transfer_id', $transfer->id)->first();
                if ($payout) {
                    $payout->update([
                        'status' => 'failed',
                        'error_message' => 'Transfer failed as reported by Stripe',
                    ]);
                }

                Log::warning('Deal transfer failed - deal remains in paid_escrowed status', [
                    'deal_id' => $deal->id,
                    'transfer_id' => $transfer->id,
                ]);
            }
        }
    }

    /**
     * Handle transfer reversed (athlete payout reversed)
     * Do NOT mark deal as released if transfer is reversed
     */
    protected function handleTransferReversed($transfer)
    {
        Log::warning('Stripe transfer reversed webhook received', [
            'transfer_id' => $transfer->id,
            'amount' => $transfer->amount / 100,
            'destination' => $transfer->destination,
        ]);

        // Find deal by transfer metadata
        if (isset($transfer->metadata['deal_id'])) {
            $deal = Deal::find($transfer->metadata['deal_id']);

            if ($deal) {
                $deal->update([
                    'stripe_transfer_status' => 'reversed',
                    'payment_status' => 'paid_escrowed', // Revert to paid_escrowed
                    'released_at' => null, // Clear released_at
                ]);

                // Update payout record
                $payout = \App\Models\Payout::where('stripe_transfer_id', $transfer->id)->first();
                if ($payout) {
                    $payout->update([
                        'status' => 'failed',
                        'error_message' => 'Transfer reversed as reported by Stripe',
                    ]);
                }

                Log::warning('Deal transfer reversed - deal reverted to paid_escrowed status', [
                    'deal_id' => $deal->id,
                    'transfer_id' => $transfer->id,
                ]);
            }
        }
    }
}

