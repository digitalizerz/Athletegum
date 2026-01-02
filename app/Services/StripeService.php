<?php

namespace App\Services;

use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\PaymentMethod;
use Stripe\Transfer;
use Stripe\AccountLink;
use Stripe\Account;
use Stripe\Checkout\Session;
use Stripe\Subscription;
use Stripe\BillingPortal\Session as BillingPortalSession;
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
     * Note: We use platform charges + separate transfers (not direct charges)
     * Platform revenue comes from remaining balance after transferring to athlete
     * 
     * @param float $amount Amount in dollars (will be converted to cents) - this is the deal_amount the business pays
     * @param string $paymentMethodId Stripe payment method ID
     * @param float $platformFeeAmount Platform fee amount in dollars (for metadata tracking only)
     * @param array $metadata Additional metadata
     * @param string|null $customerId Stripe customer ID (required if payment method is attached to a customer)
     * @return PaymentIntent
     * @throws ApiErrorException
     */
    public function createPaymentIntent(
        float $amount,
        string $paymentMethodId,
        float $platformFeeAmount = 0,
        array $metadata = [],
        ?string $customerId = null
    ): PaymentIntent {
        if (!$this->isConfigured()) {
            throw new \Exception('Stripe is not configured. Please set STRIPE_KEY and STRIPE_SECRET in .env');
        }

        $amountInCents = (int) round($amount * 100);

        // Add platform fee to metadata for tracking (not used by Stripe)
        if ($platformFeeAmount > 0) {
            $metadata['platform_fee_amount'] = (string) $platformFeeAmount;
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

        // Note: We use platform charges + separate transfers (not direct charges)
        // application_fee_amount is NOT used - platform revenue comes from balance after transfer

        try {
            $paymentIntent = PaymentIntent::create($params);
            
            Log::info('Stripe PaymentIntent created', [
                'payment_intent_id' => $paymentIntent->id,
                'amount' => $amount,
                'platform_fee_amount' => $platformFeeAmount,
                'status' => $paymentIntent->status,
            ]);

            return $paymentIntent;
        } catch (ApiErrorException $e) {
            Log::error('Stripe PaymentIntent creation failed', [
                'error' => $e->getMessage(),
                'amount' => $amount,
                'platform_fee_amount' => $platformFeeAmount,
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
     * Transfers from platform balance (not from a specific charge).
     * 
     * @param float $amount Amount in dollars (net payout after athlete fee = deal_amount - 5%)
     * @param string $athleteStripeAccountId Athlete's Stripe Connect account ID (acct_xxx)
     * @param string $idempotencyKey Idempotency key to prevent duplicate transfers
     * @param array $metadata Additional metadata
     * @return Transfer
     * @throws ApiErrorException
     */
    public function transferToAthlete(
        float $amount,
        string $athleteStripeAccountId,
        string $idempotencyKey,
        array $metadata = []
    ): Transfer {
        if (!$this->isConfigured()) {
            throw new \Exception('Stripe is not configured');
        }

        $amountInCents = (int) round($amount * 100);

        // Create transfer to athlete's connected account
        // This moves funds from platform balance to athlete account
        // Do NOT use source_transaction - transfer from platform balance
        $params = [
            'amount' => $amountInCents,
            'currency' => 'usd',
            'destination' => $athleteStripeAccountId,
            'metadata' => $metadata,
        ];

        // Use idempotency key to prevent duplicate transfers
        $transfer = Transfer::create($params, [
            'idempotency_key' => $idempotencyKey,
        ]);

        Log::info('Stripe Transfer created for athlete', [
            'transfer_id' => $transfer->id,
            'amount' => $amount,
            'athlete_account' => $athleteStripeAccountId,
            'idempotency_key' => $idempotencyKey,
            'transfer_from' => 'platform_balance',
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

    /**
     * Get Stripe account balance (available funds)
     * Returns available balance in dollars
     * 
     * @return float Available balance in dollars
     * @throws ApiErrorException
     */
    public function getAvailableBalance(): float
    {
        if (!$this->isConfigured()) {
            throw new \Exception('Stripe is not configured');
        }

        try {
            $balance = \Stripe\Balance::retrieve();
            
            // Get available balance (funds that can be transferred)
            // Stripe returns balance in cents, convert to dollars
            $availableBalance = 0;
            foreach ($balance->available as $available) {
                if ($available->currency === 'usd') {
                    $availableBalance += $available->amount;
                }
            }
            
            // Convert from cents to dollars
            $availableBalanceInDollars = $availableBalance / 100;
            
            Log::info('Stripe balance retrieved', [
                'available_balance' => $availableBalanceInDollars,
                'pending_balance' => ($balance->pending[0]->amount ?? 0) / 100,
            ]);
            
            return $availableBalanceInDollars;
        } catch (ApiErrorException $e) {
            Log::error('Failed to retrieve Stripe balance', [
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Create a subscription checkout session
     * 
     * @param int $userId User ID
     * @param string $email User email
     * @param string $name User name
     * @param string $plan Subscription plan ('pro' or 'growth')
     * @return Session Stripe Checkout Session
     * @throws ApiErrorException
     */
    public function createSubscriptionCheckoutSession(
        int $userId,
        string $email,
        string $name,
        string $plan
    ): Session {
        if (!$this->isConfigured()) {
            throw new \Exception('Stripe is not configured');
        }

        // Define plan prices (in cents)
        $planPrices = [
            'pro' => 4900, // $49.00
            'growth' => 9900, // $99.00
        ];

        if (!isset($planPrices[$plan])) {
            throw new \Exception('Invalid subscription plan');
        }

        $priceId = $this->getPriceIdForPlan($plan);
        if (!$priceId) {
            throw new \Exception('Price ID not configured for plan: ' . $plan);
        }

        // Get or create Stripe customer
        $customerId = $this->getOrCreateCustomer($userId, $email, $name);

        $checkoutSession = Session::create([
            'customer' => $customerId,
            'mode' => 'subscription',
            'line_items' => [[
                'price' => $priceId,
                'quantity' => 1,
            ]],
            'success_url' => route('subscriptions.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('subscriptions.cancel'),
            'metadata' => [
                'user_id' => $userId,
                'plan' => $plan,
            ],
            'subscription_data' => [
                'metadata' => [
                    'user_id' => $userId,
                    'plan' => $plan,
                ],
            ],
        ]);

        Log::info('Stripe subscription checkout session created', [
            'session_id' => $checkoutSession->id,
            'user_id' => $userId,
            'plan' => $plan,
            'customer_id' => $customerId,
        ]);

        return $checkoutSession;
    }

    /**
     * Get Price ID for a subscription plan
     * This should be configured in .env or config
     * 
     * @param string $plan Plan name ('pro' or 'growth')
     * @return string|null Price ID
     */
    protected function getPriceIdForPlan(string $plan): ?string
    {
        // Price IDs should be configured in .env
        // Format: STRIPE_PRICE_PRO=price_xxx, STRIPE_PRICE_GROWTH=price_xxx
        $envKey = 'STRIPE_PRICE_' . strtoupper($plan);
        return env($envKey);
    }

    /**
     * Retrieve a Stripe subscription
     * 
     * @param string $subscriptionId Subscription ID
     * @return Subscription
     * @throws ApiErrorException
     */
    public function retrieveSubscription(string $subscriptionId): Subscription
    {
        if (!$this->isConfigured()) {
            throw new \Exception('Stripe is not configured');
        }

        return Subscription::retrieve($subscriptionId);
    }

    /**
     * Create a Stripe Billing Portal session
     * 
     * @param string $customerId Stripe Customer ID
     * @param string $returnUrl URL to return to after portal session
     * @return BillingPortalSession
     * @throws ApiErrorException
     */
    public function createBillingPortalSession(string $customerId, string $returnUrl): BillingPortalSession
    {
        if (!$this->isConfigured()) {
            throw new \Exception('Stripe is not configured');
        }

        $session = BillingPortalSession::create([
            'customer' => $customerId,
            'return_url' => $returnUrl,
        ]);

        Log::info('Stripe billing portal session created', [
            'session_id' => $session->id,
            'customer_id' => $customerId,
        ]);

        return $session;
    }

    /**
     * Update subscription to cancel at period end
     * 
     * @param string $subscriptionId Stripe Subscription ID
     * @return Subscription
     * @throws ApiErrorException
     */
    public function cancelSubscriptionAtPeriodEnd(string $subscriptionId): Subscription
    {
        if (!$this->isConfigured()) {
            throw new \Exception('Stripe is not configured');
        }

        $subscription = Subscription::update($subscriptionId, [
            'cancel_at_period_end' => true,
        ]);

        Log::info('Stripe subscription set to cancel at period end', [
            'subscription_id' => $subscriptionId,
        ]);

        return $subscription;
    }

    /**
     * Change subscription plan (downgrade/upgrade)
     * 
     * @param string $subscriptionId Stripe Subscription ID
     * @param string $newPriceId New Stripe Price ID
     * @return Subscription
     * @throws ApiErrorException
     */
    public function changeSubscriptionPlan(string $subscriptionId, string $newPriceId): Subscription
    {
        if (!$this->isConfigured()) {
            throw new \Exception('Stripe is not configured');
        }

        // Retrieve subscription to get current subscription item
        $subscription = Subscription::retrieve($subscriptionId);
        $subscriptionItemId = $subscription->items->data[0]->id;

        // Update subscription with new price, no proration, unchanged billing cycle
        $updatedSubscription = Subscription::update($subscriptionId, [
            'items' => [
                [
                    'id' => $subscriptionItemId,
                    'price' => $newPriceId,
                ],
            ],
            'proration_behavior' => 'none',
            'billing_cycle_anchor' => 'unchanged',
        ]);

        Log::info('Stripe subscription plan changed', [
            'subscription_id' => $subscriptionId,
            'new_price_id' => $newPriceId,
        ]);

        return $updatedSubscription;
    }
}

