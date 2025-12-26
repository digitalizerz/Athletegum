<?php

namespace App\Services;

use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\PaymentMethod;
use Stripe\Transfer;
use Stripe\AccountLink;
use Stripe\Account;
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
        $this->isLive = !empty($this->secretKey) && str_starts_with($this->secretKey, 'sk_live_');
        
        // Only set API key if we have a secret key
        // Don't throw exceptions in constructor - let isConfigured() handle it
        if (!empty($this->secretKey)) {
            try {
                Stripe::setApiKey($this->secretKey);
            } catch (\Exception $e) {
                // Log but don't throw - will be caught by isConfigured() checks
                Log::warning('Failed to set Stripe API key in constructor', [
                    'error' => $e->getMessage(),
                ]);
            }
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
     * @param string|null $customerId Stripe customer ID (required if payment method is attached to a customer)
     * @return PaymentIntent
     * @throws ApiErrorException
     */
    public function createPaymentIntent(
        float $amount,
        string $paymentMethodId,
        float $applicationFeeAmount = 0,
        array $metadata = [],
        ?string $customerId = null
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
            'confirm' => true,
            'metadata' => $metadata,
            // Disable redirect-based payment methods to avoid requiring return_url
            // Note: Cannot use confirmation_method with automatic_payment_methods
            'automatic_payment_methods' => [
                'enabled' => true,
                'allow_redirects' => 'never',
            ],
        ];

        // If customer ID is provided, include it (required when payment method is attached to a customer)
        if ($customerId) {
            $params['customer'] = $customerId;
        }

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
     * 
     * @param string $paymentIntentId The PaymentIntent ID
     * @param array $expandParams Additional parameters for expanding related objects (e.g., ['expand' => ['charges']])
     */
    public function getPaymentIntent(string $paymentIntentId, array $params = []): PaymentIntent
    {
        if (!$this->isConfigured()) {
            throw new \Exception('Stripe is not configured');
        }

        if (!empty($params)) {
            return PaymentIntent::retrieve($paymentIntentId, $params);
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
     * @param float $amount Amount in dollars (net payout after athlete fee = deal_amount - 5%)
     * @param string $athleteStripeAccountId Athlete's Stripe Connect account ID (acct_xxx)
     * @param string|null $chargeId Original charge ID (ch_xxx) - if null, transfers from platform balance
     * @param array $metadata Additional metadata
     * @return Transfer
     * @throws ApiErrorException
     */
    public function transferToAthlete(
        float $amount,
        string $athleteStripeAccountId,
        ?string $chargeId = null,
        array $metadata = []
    ): Transfer {
        if (!$this->isConfigured()) {
            throw new \Exception('Stripe is not configured');
        }

        $amountInCents = (int) round($amount * 100);

        // Create transfer to athlete's connected account
        // This moves funds from platform account to athlete account
        $params = [
            'amount' => $amountInCents,
            'currency' => 'usd',
            'destination' => $athleteStripeAccountId,
            'metadata' => $metadata,
        ];

        // If charge ID is provided, transfer from that specific charge
        // If not (wallet payments), transfer from platform's available balance
        if ($chargeId) {
            $params['source_transaction'] = $chargeId;
        }

        $transfer = Transfer::create($params);

        Log::info('Stripe Transfer created for athlete', [
            'transfer_id' => $transfer->id,
            'amount' => $amount,
            'athlete_account' => $athleteStripeAccountId,
            'charge_id' => $chargeId,
            'transfer_from' => $chargeId ? 'charge' : 'platform_balance',
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

    /**
     * Create a Stripe Connect account link for OAuth
     */
    public function createAccountLink(string $accountId, string $returnUrl, string $refreshUrl): AccountLink
    {
        if (!$this->isConfigured()) {
            throw new \Exception('Stripe is not configured');
        }

        return AccountLink::create([
            'account' => $accountId,
            'refresh_url' => $refreshUrl,
            'return_url' => $returnUrl,
            'type' => 'account_onboarding',
        ]);
    }

    /**
     * Create a Stripe Connect Express account
     */
    public function createExpressAccount(string $email, ?string $country = 'US'): Account
    {
        if (!$this->isConfigured()) {
            throw new \Exception('Stripe is not configured');
        }

        return Account::create([
            'type' => 'express',
            'country' => $country,
            'email' => $email,
            'capabilities' => [
                'card_payments' => ['requested' => true],
                'transfers' => ['requested' => true],
            ],
        ]);
    }

    /**
     * Retrieve a Stripe Connect account
     */
    public function retrieveAccount(string $accountId): Account
    {
        if (!$this->isConfigured()) {
            throw new \Exception('Stripe is not configured');
        }

        return Account::retrieve($accountId);
    }

    /**
     * Get account login link for Stripe Dashboard access
     */
    public function createLoginLink(string $accountId): string
    {
        if (!$this->isConfigured()) {
            throw new \Exception('Stripe is not configured');
        }

        $loginLink = Account::createLoginLink($accountId);
        return $loginLink->url;
    }
}

