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
     */
    protected function handleTransferCreated($transfer)
    {
        // Log transfer for audit
        Log::info('Stripe transfer created', [
            'transfer_id' => $transfer->id,
            'amount' => $transfer->amount / 100,
            'destination' => $transfer->destination,
        ]);

        // Find deal by transfer metadata or charge ID
        if (isset($transfer->metadata['deal_id'])) {
            $deal = Deal::find($transfer->metadata['deal_id']);

            if ($deal) {
                $deal->update([
                    'release_transaction_id' => $transfer->id,
                ]);

                Log::info('Deal release transfer confirmed', [
                    'deal_id' => $deal->id,
                    'transfer_id' => $transfer->id,
                ]);
            }
        }
    }
}

