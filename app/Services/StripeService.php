<?php

namespace App\Services;

use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\PaymentMethod;
use Stripe\Transfer;
use Stripe\Exception\ApiErrorException;
use Illuminate\Support\Facades\Log;

class StripeService
{
    protected string $secretKey;
    protected string $publishableKey;
    protected bool $isLive;

    public function __construct()
    {
        $this->secretKey = config('services.stripe.secret') ?? env('STRIPE_SECRET');
        $this->publishableKey = config('services.stripe.key') ?? env('STRIPE_KEY');
        
        // Determine if we're in live mode
        $this->isLive = str_starts_with($this->secretKey, 'sk_live_');
        
        if ($this->secretKey) {
            Stripe::setApiKey($this->secretKey);
        }
    }

    /**
     * Check if Stripe is properly configured
     */
    public function isConfigured(): bool
    {
        return !empty($this->secretKey) && !empty($this->publishableKey);
    }

    /**
     * Check if using live mode
     */
    public function isLiveMode(): bool
    {
        return $this->isLive;
    }

    /**
     * Create a PaymentIntent for a deal payment
     * 
     * @param float $amount Amount in dollars (will be converted to cents) - this is the TOTAL amount (compensation + platform fee)
     * @param string $paymentMethodId Stripe payment method ID
     * @param float $applicationFeeAmount Platform fee amount in dollars (for metadata only, not used in charge)
     * @param array $metadata Additional metadata
     * @return PaymentIntent
     * @throws ApiErrorException
     */
    public function createPaymentIntent(
        float $amount,
        string $paymentMethodId,
        float $applicationFeeAmount = 0,
        array $metadata = []
    ): PaymentIntent {
        if (!$this->isConfigured()) {
            throw new \Exception('Stripe is not configured. Please set STRIPE_KEY and STRIPE_SECRET in .env');
        }

        $amountInCents = (int) round($amount * 100);

        // Add platform fee to metadata for tracking
        if ($applicationFeeAmount > 0) {
            $metadata['platform_fee_amount'] = (string) $applicationFeeAmount;
        }

        $params = [
            'amount' => $amountInCents,
            'currency' => 'usd',
            'payment_method' => $paymentMethodId,
            'confirmation_method' => 'manual',
            'confirm' => true,
            'metadata' => $metadata,
        ];

        // Note: Application fees require Stripe Connect
        // For v1, we charge the full amount and track platform fees separately
        // Platform fees are retained by the platform account automatically

        try {
            $paymentIntent = PaymentIntent::create($params);
            
            Log::info('Stripe PaymentIntent created', [
                'payment_intent_id' => $paymentIntent->id,
                'amount' => $amount,
                'status' => $paymentIntent->status,
            ]);

            return $paymentIntent;
        } catch (ApiErrorException $e) {
            Log::error('Stripe PaymentIntent creation failed', [
                'error' => $e->getMessage(),
                'amount' => $amount,
            ]);
            throw $e;
        }
    }

    /**
     * Retrieve a PaymentIntent from Stripe
     */
    public function getPaymentIntent(string $paymentIntentId): PaymentIntent
    {
        if (!$this->isConfigured()) {
            throw new \Exception('Stripe is not configured');
        }

        return PaymentIntent::retrieve($paymentIntentId);
    }

    /**
     * Create a payment method from card details
     */
    public function createPaymentMethod(array $cardDetails): PaymentMethod
    {
        if (!$this->isConfigured()) {
            throw new \Exception('Stripe is not configured');
        }

        $paymentMethod = PaymentMethod::create([
            'type' => 'card',
            'card' => [
                'number' => $cardDetails['number'],
                'exp_month' => $cardDetails['exp_month'],
                'exp_year' => $cardDetails['exp_year'],
                'cvc' => $cardDetails['cvc'],
            ],
        ]);

        Log::info('Stripe PaymentMethod created', [
            'payment_method_id' => $paymentMethod->id,
            'last4' => $paymentMethod->card->last4,
        ]);

        return $paymentMethod;
    }

    /**
     * Attach a payment method to a customer
     */
    public function attachPaymentMethodToCustomer(string $paymentMethodId, string $customerId): PaymentMethod
    {
        $paymentMethod = PaymentMethod::retrieve($paymentMethodId);
        $paymentMethod->attach(['customer' => $customerId]);
        return $paymentMethod;
    }

    /**
     * Create or retrieve a Stripe customer for a user
     */
    public function getOrCreateCustomer(int $userId, string $email, string $name): string
    {
        if (!$this->isConfigured()) {
            throw new \Exception('Stripe is not configured');
        }

        // Check if user already has a Stripe customer ID stored
        $user = \App\Models\User::find($userId);
        if ($user && $user->stripe_customer_id) {
            try {
                \Stripe\Customer::retrieve($user->stripe_customer_id);
                return $user->stripe_customer_id;
            } catch (\Exception $e) {
                // Customer doesn't exist, create new one
            }
        }

        // Create new customer
        $customer = \Stripe\Customer::create([
            'email' => $email,
            'name' => $name,
            'metadata' => [
                'user_id' => $userId,
                'platform' => 'athletegum',
            ],
        ]);

        // Store customer ID
        if ($user) {
            $user->update(['stripe_customer_id' => $customer->id]);
        }

        return $customer->id;
    }

    /**
     * Transfer funds to athlete (payout on deal release)
     * 
     * Note: This requires Stripe Connect. The athlete must have a connected Stripe account.
     * 
     * @param float $amount Amount in dollars (net payout after athlete fee)
     * @param string $athleteStripeAccountId Athlete's Stripe Connect account ID (acct_xxx)
     * @param string $chargeId Original charge ID (ch_xxx)
     * @param array $metadata Additional metadata
     * @return Transfer
     * @throws ApiErrorException
     */
    public function transferToAthlete(
        float $amount,
        string $athleteStripeAccountId,
        string $chargeId,
        array $metadata = []
    ): Transfer {
        if (!$this->isConfigured()) {
            throw new \Exception('Stripe is not configured');
        }

        $amountInCents = (int) round($amount * 100);

        // Create transfer to athlete's connected account
        // This moves funds from platform account to athlete account
        $transfer = Transfer::create([
            'amount' => $amountInCents,
            'currency' => 'usd',
            'destination' => $athleteStripeAccountId,
            'source_transaction' => $chargeId, // Transfer from the original charge
            'metadata' => $metadata,
        ]);

        Log::info('Stripe Transfer created for athlete', [
            'transfer_id' => $transfer->id,
            'amount' => $amount,
            'athlete_account' => $athleteStripeAccountId,
            'charge_id' => $chargeId,
        ]);

        return $transfer;
    }

    /**
     * Verify webhook signature
     */
    public function verifyWebhookSignature(string $payload, string $signature, string $secret): bool
    {
        try {
            \Stripe\Webhook::constructEvent($payload, $signature, $secret);
            return true;
        } catch (\Exception $e) {
            Log::error('Stripe webhook signature verification failed', [
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Get publishable key
     */
    public function getPublishableKey(): string
    {
        return $this->publishableKey;
    }
}

