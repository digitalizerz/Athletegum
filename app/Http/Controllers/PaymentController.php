<?php

namespace App\Http\Controllers;

use App\Models\Deal;
use App\Models\PaymentMethod;
use App\Models\PlatformSetting;
use App\Services\StripeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Stripe\Account as StripeAccount;
use Stripe\Exception\InvalidRequestException;

class PaymentController extends Controller
{
    protected StripeService $stripeService;

    public function __construct(StripeService $stripeService)
    {
        $this->stripeService = $stripeService;
    }
    /**
     * Process payment for a deal (wallet, card, or split payment)
     */
    public function processDealPayment(Request $request)
    {
        $validated = $request->validate([
            'payment_method' => ['required', 'in:wallet_full,wallet_partial,card_full'],
            'payment_method_id' => ['required_if:payment_method,wallet_partial,card_full', 'exists:payment_methods,id'],
        ]);

        $session = $request->session();
        
        // Validate session data exists
        if (!$session->has('deal_type') || !$session->has('compensation_amount') || !$session->has('deadline')) {
            return redirect()->route('deals.create')->withErrors(['error' => 'Session expired. Please start over.']);
        }

        $user = Auth::user();
        $walletBalance = (float) $user->wallet_balance ?? 0.00;

        // Calculate payment breakdown per marketplace rules:
        // Business pays: deal_amount only
        // Platform fee (10%) comes OUT OF the deal_amount
        // Athlete receives: deal_amount - platform_fee (90% of deal_amount)
        $dealAmount = (float) $session->get('compensation_amount'); // Base deal amount
        $platformFeePercentage = 10.0; // Fixed 10% platform fee
        $platformFeeAmount = round($dealAmount * ($platformFeePercentage / 100), 2); // Platform fee deducted from deal_amount
        $businessTotal = round($dealAmount, 2); // Business pays deal_amount only
        
        // Escrow amount is the base deal amount (athlete will receive: deal_amount - platform_fee)
        $escrowAmount = round($dealAmount, 2);

        try {
            DB::beginTransaction();

            $walletAmountUsed = 0;
            $cardAmount = 0;
            $paymentIntentId = null;

            if ($validated['payment_method'] === 'wallet_full') {
                // Pay fully from wallet
                if ($walletBalance < $businessTotal) {
                    return redirect()->back()->withErrors([
                        'error' => 'Insufficient wallet balance. Your balance is $' . number_format($walletBalance, 2) . '.'
                    ]);
                }

                $walletAmountUsed = $businessTotal;
                $user->deductFromWallet($businessTotal, 'payment', null, [
                    'platform_fee_type' => 'percentage',
                    'platform_fee_percentage' => $platformFeePercentage,
                    'platform_fee_amount' => $platformFeeAmount,
                    'escrow_amount' => $escrowAmount,
                    'compensation_amount' => $dealAmount,
                    'business_total' => $businessTotal,
                ]);

                $paymentIntentId = 'wallet_' . uniqid();
                $session->put('payment_method', 'wallet');

            } elseif ($validated['payment_method'] === 'wallet_partial') {
                // Use wallet + pay remainder via card
                if ($walletBalance <= 0) {
                    return redirect()->back()->withErrors([
                        'error' => 'You selected to use wallet, but your wallet balance is $0.00. Please select a different payment method.'
                    ]);
                }

                $walletAmountUsed = min($walletBalance, $businessTotal);
                $cardAmount = $businessTotal - $walletAmountUsed;

                // Verify payment method belongs to user
                $paymentMethod = PaymentMethod::findOrFail($validated['payment_method_id']);
                if ($paymentMethod->user_id !== $user->id) {
                    return redirect()->back()->withErrors(['error' => 'Invalid payment method.']);
                }

                // Deduct wallet portion
                if ($walletAmountUsed > 0) {
                    // Calculate proportional platform fee for wallet portion
                    $walletPlatformFeeAmount = round(($walletAmountUsed / $businessTotal) * $platformFeeAmount, 2);
                    $user->deductFromWallet($walletAmountUsed, 'payment', null, [
                        'platform_fee_type' => 'percentage',
                        'platform_fee_percentage' => $platformFeePercentage,
                        'platform_fee_amount' => $walletPlatformFeeAmount,
                        'escrow_amount' => $escrowAmount,
                        'compensation_amount' => $dealAmount,
                        'payment_type' => 'partial_wallet',
                        'business_total' => $businessTotal,
                    ]);
                }

                // Process card payment for remainder via REAL Stripe
                if (!$this->stripeService->isConfigured()) {
                    DB::rollBack();
                    return redirect()->back()->withErrors([
                        'error' => 'Stripe is not configured. Please contact support.'
                    ]);
                }

                // Create or get Stripe customer
                $customerId = $this->stripeService->getOrCreateCustomer(
                    $user->id,
                    $user->email,
                    $user->name
                );

                // Calculate platform fee proportion for card portion
                $cardPlatformFeeAmount = round(($cardAmount / $businessTotal) * $platformFeeAmount, 2);

                try {
                    $paymentIntent = $this->stripeService->createPaymentIntent(
                        $cardAmount,
                        $paymentMethod->provider_payment_method_id,
                        $cardPlatformFeeAmount, // Platform fee for card portion (for metadata tracking only)
                        [
                            'user_id' => $user->id,
                            'deal_type' => $session->get('deal_type'),
                            'compensation_amount' => (string) $dealAmount,
                            'platform_fee_amount' => (string) $cardPlatformFeeAmount,
                            'escrow_amount' => (string) $escrowAmount,
                            'payment_type' => 'partial_wallet',
                            'wallet_amount_used' => (string) $walletAmountUsed,
                            'business_total' => (string) $businessTotal,
                        ],
                        $customerId // Include customer ID
                    );

                    if ($paymentIntent->status !== 'succeeded') {
                        DB::rollBack();
                        return redirect()->back()->withErrors([
                            'error' => 'Card payment failed. Status: ' . $paymentIntent->status
                        ]);
                    }

                    $paymentIntentId = $paymentIntent->id;
                    $session->put('payment_method', 'wallet_card');
                    $session->put('card_payment_method_id', $paymentMethod->id);
                    $session->put('card_amount', $cardAmount);
                    $session->put('wallet_amount_used', $walletAmountUsed);
                    $session->put('stripe_charge_id', $paymentIntent->charges->data[0]->id ?? null);

                    Log::info('Stripe PaymentIntent succeeded (partial wallet)', [
                        'payment_intent_id' => $paymentIntentId,
                        'card_amount' => $cardAmount,
                        'wallet_amount' => $walletAmountUsed,
                        'user_id' => $user->id,
                    ]);
                } catch (\Stripe\Exception\CardException $e) {
                    DB::rollBack();
                    Log::error('Stripe card payment failed (partial wallet)', [
                        'error' => $e->getMessage(),
                        'user_id' => $user->id,
                    ]);
                    return redirect()->back()->withErrors([
                        'error' => 'Card payment failed: ' . $e->getError()->message
                    ]);
                } catch (\Exception $e) {
                    DB::rollBack();
                    Log::error('Stripe payment processing failed (partial wallet)', [
                        'error' => $e->getMessage(),
                        'user_id' => $user->id,
                    ]);
                    return redirect()->back()->withErrors([
                        'error' => 'Payment processing failed: ' . $e->getMessage()
                    ]);
                }

            } else { // card_full
                // Pay fully via card
                $cardAmount = $businessTotal;

                // Verify payment method belongs to user
                $paymentMethod = PaymentMethod::findOrFail($validated['payment_method_id']);
                if ($paymentMethod->user_id !== $user->id) {
                    return redirect()->back()->withErrors(['error' => 'Invalid payment method.']);
                }

                // Check if Stripe is configured
                if (!$this->stripeService->isConfigured()) {
                    DB::rollBack();
                    return redirect()->back()->withErrors([
                        'error' => 'Stripe is not configured. Please contact support.'
                    ]);
                }

                // Create or get Stripe customer
                $customerId = $this->stripeService->getOrCreateCustomer(
                    $user->id,
                    $user->email,
                    $user->name
                );

                // Create REAL Stripe PaymentIntent with application fee
                try {
                    $paymentIntent = $this->stripeService->createPaymentIntent(
                        $cardAmount,
                        $paymentMethod->provider_payment_method_id,
                        $platformFeeAmount, // Platform fee (for metadata tracking only)
                        [
                            'user_id' => $user->id,
                            'deal_type' => $session->get('deal_type'),
                            'compensation_amount' => (string) $dealAmount,
                            'platform_fee_amount' => (string) $platformFeeAmount,
                            'escrow_amount' => (string) $escrowAmount,
                            'business_total' => (string) $businessTotal,
                        ],
                        $customerId // Include customer ID
                    );

                    // Check payment intent status
                    if ($paymentIntent->status === 'requires_action' || $paymentIntent->status === 'requires_confirmation') {
                        // Payment needs additional authentication
                        DB::rollBack();
                        return redirect()->back()->withErrors([
                            'error' => 'Payment requires additional authentication. Please try again.'
                        ]);
                    }

                    if ($paymentIntent->status !== 'succeeded') {
                        DB::rollBack();
                        return redirect()->back()->withErrors([
                            'error' => 'Payment failed. Status: ' . $paymentIntent->status
                        ]);
                    }

                    $paymentIntentId = $paymentIntent->id;
                    $chargeId = $paymentIntent->charges->data[0]->id ?? null;
                    $session->put('payment_method', 'card');
                    $session->put('card_payment_method_id', $paymentMethod->id);
                    $session->put('card_amount', $cardAmount);
                    $session->put('stripe_charge_id', $chargeId);

                    Log::info('Stripe PaymentIntent succeeded', [
                        'payment_intent_id' => $paymentIntentId,
                        'amount' => $cardAmount,
                        'user_id' => $user->id,
                    ]);
                } catch (\Stripe\Exception\CardException $e) {
                    DB::rollBack();
                    Log::error('Stripe card payment failed', [
                        'error' => $e->getMessage(),
                        'user_id' => $user->id,
                    ]);
                    return redirect()->back()->withErrors([
                        'error' => 'Card payment failed: ' . $e->getError()->message
                    ]);
                } catch (\Exception $e) {
                    DB::rollBack();
                    Log::error('Stripe payment processing failed', [
                        'error' => $e->getMessage(),
                        'user_id' => $user->id,
                    ]);
                    return redirect()->back()->withErrors([
                        'error' => 'Payment processing failed: ' . $e->getMessage()
                    ]);
                }
            }

            // Store payment info in session
            $session->put('platform_fee_type', 'percentage');
            $session->put('platform_fee_percentage', $platformFeePercentage);
            $session->put('platform_fee_amount', $platformFeeAmount);
            $session->put('escrow_amount', $escrowAmount);
            $session->put('compensation_amount', $dealAmount); // Store original deal amount
            $session->put('total_amount', $businessTotal); // Business total (deal_amount only)
            $session->put('wallet_amount_used', $walletAmountUsed ?? 0);
            $session->put('card_amount', $cardAmount ?? 0);
            $session->put('payment_intent_id', $paymentIntentId);
            
            // For wallet payments, mark as paid immediately
            // For Stripe payments, wait for webhook confirmation
            if ($validated['payment_method'] === 'wallet_full') {
                $session->put('payment_status', 'paid');
            } else {
                // Card payments: wait for webhook to confirm
                $session->put('payment_status', 'pending');
            }

            DB::commit();

            return redirect()->route('deals.review');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Payment processing failed: ' . $e->getMessage()]);
        }
    }

    /**
     * Release escrow payment to athlete (only after SMB approval)
     */
    public function releasePayment(Deal $deal)
    {
        // Ensure deal belongs to user
        if ($deal->user_id !== Auth::id()) {
            abort(403);
        }

        // Ensure deal is in correct state (paid_escrowed means payment succeeded and funds are in escrow)
        if ($deal->payment_status !== 'paid_escrowed' && $deal->payment_status !== 'paid') {
            // Allow 'paid' for backward compatibility with existing deals
            return redirect()->back()->withErrors(['error' => 'Deal payment has not been completed. Payment status: ' . $deal->payment_status]);
        }

        // Check if funds are still pending in Stripe
        if ($deal->awaiting_funds) {
            return redirect()->back()->withErrors([
                'error' => 'Payment complete – payout pending clearing. Stripe funds are still pending. The transfer will be processed automatically once funds are available. Please check back later.'
            ]);
        }

        // Funds can be released when deal is completed (athlete submitted) or approved
        if ($deal->status !== 'completed' && !$deal->is_approved) {
            return redirect()->back()->withErrors(['error' => 'Deal must be completed by athlete or approved before payment can be released.']);
        }

        // Check if payout already exists for this deal (prevent double pay)
        $existingPayout = \App\Models\Payout::where('deal_id', $deal->id)
            ->where('status', 'completed')
            ->first();
        
        if ($existingPayout) {
            return redirect()->back()->withErrors(['error' => 'Payment has already been released for this deal.']);
        }

        if ($deal->released_at) {
            return redirect()->back()->withErrors(['error' => 'Payment has already been released.']);
        }

        try {
            DB::beginTransaction();

            // Calculate athlete payout per marketplace rules:
            // Platform takes 10% via application_fee_amount at payment time
            // Athlete receives: deal_amount - platform_fee_amount (90% of deal amount)
            // Athletes pay $0 platform fee
            $dealAmount = (float) $deal->compensation_amount; // Base deal amount
            $platformFeePercentage = 10.0; // Platform fee is 10%
            $platformFeeAmount = (float) ($deal->platform_fee_amount ?? round($dealAmount * ($platformFeePercentage / 100), 2));
            $athleteFeePercentage = 0.0; // Athletes pay $0 platform fee
            $athleteFeeAmount = 0.0; // No athlete fee
            $athleteNetPayout = round($dealAmount - $platformFeeAmount, 2); // Athlete receives deal_amount - platform_fee

            // Get athlete's Stripe account ID
            $athlete = $deal->athlete;
            if (!$athlete) {
                DB::rollBack();
                return redirect()->back()->withErrors([
                    'error' => 'Athlete not found for this deal.'
                ]);
            }

            // Always refresh athlete to get latest data (in case payment methods were updated)
            $athlete->refresh();
            
            // Get Stripe account ID from active payment methods first (most up-to-date)
            // Then fall back to athlete record if no active payment methods
            $paymentMethod = $athlete->paymentMethods()
                ->where('is_active', true)
                ->whereNotNull('provider_account_id')
                ->orderBy('is_default', 'desc')
                ->orderBy('created_at', 'desc')
                ->first();
            
            if ($paymentMethod && $paymentMethod->provider_account_id) {
                $stripeAccountId = $paymentMethod->provider_account_id;
                // Always update athlete record with the latest active payment method's account ID
                if ($athlete->stripe_account_id !== $stripeAccountId) {
                    $athlete->update(['stripe_account_id' => $stripeAccountId]);
                }
            } else {
                // Fall back to athlete record if no active payment methods
                $stripeAccountId = $athlete->stripe_account_id;
            }

            if (!$stripeAccountId) {
                DB::rollBack();
                return redirect()->back()->withErrors([
                    'error' => 'Athlete does not have a Stripe account configured. They must set up payment methods in their Earnings section first.'
                ]);
            }

            // Validate that the Stripe account ID is a valid connected account format (starts with acct_)
            if (!str_starts_with($stripeAccountId, 'acct_')) {
                DB::rollBack();
                Log::error('Invalid Stripe account ID format for athlete', [
                    'athlete_id' => $athlete->id,
                    'stripe_account_id' => $stripeAccountId,
                ]);
                return redirect()->back()->withErrors([
                    'error' => 'Athlete has an invalid Stripe account configuration. The account ID must start with "acct_". Please have the athlete update their payment methods in their Earnings section.'
                ]);
            }

            // Log the account IDs for debugging
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
                
                // If accounts match, provide helpful error message
                if ($stripeAccountId === $platformAccountId) {
                    DB::rollBack();
                    return redirect()->back()->withErrors([
                        'error' => 'The athlete\'s Stripe account ID is the same as the platform account. The athlete must connect their own Stripe Connect account (not the platform account). Please have them remove the current payment method and add a new one with their own Stripe Connect account ID from their Stripe Dashboard.'
                    ]);
                }
            } catch (\Exception $e) {
                // If we can't retrieve platform account, log but continue
                // Stripe API will validate the transfer anyway
                Log::warning('Could not retrieve platform account for validation', [
                    'error' => $e->getMessage(),
                    'athlete_stripe_account_id' => $stripeAccountId,
                ]);
            }

            // Verify payment succeeded (for Stripe payments, check PaymentIntent status)
            $isStripePayment = $deal->payment_intent_id && str_starts_with($deal->payment_intent_id, 'pi_');
            
            if ($isStripePayment) {
                try {
                    $paymentIntent = $this->stripeService->getPaymentIntent($deal->payment_intent_id, ['expand' => ['charges']]);
                    
                    if ($paymentIntent->status !== 'succeeded') {
                        DB::rollBack();
                        Log::warning('PaymentIntent is not succeeded, cannot release payment', [
                            'deal_id' => $deal->id,
                            'payment_intent_id' => $deal->payment_intent_id,
                            'payment_intent_status' => $paymentIntent->status,
                        ]);
                        return redirect()->back()->withErrors([
                            'error' => 'Cannot release payment: The payment has not completed successfully. Payment status: ' . $paymentIntent->status . '. Please wait a few minutes and try again, or contact support if the issue persists.'
                        ]);
                    }
                } catch (\Exception $e) {
                    DB::rollBack();
                    Log::error('Failed to verify PaymentIntent for release', [
                        'deal_id' => $deal->id,
                        'payment_intent_id' => $deal->payment_intent_id,
                        'error' => $e->getMessage(),
                    ]);
                    return redirect()->back()->withErrors([
                        'error' => 'Cannot release payment: Failed to verify payment in Stripe. Please contact support.'
                    ]);
                }
            }

            // Check Stripe balance availability BEFORE attempting transfer
            // This prevents "insufficient funds" errors when funds are still pending
            if (!$this->stripeService->isConfigured()) {
                DB::rollBack();
                return redirect()->back()->withErrors([
                    'error' => 'Stripe is not configured. Cannot release payment.'
                ]);
            }

            try {
                // Fetch Stripe available balance
                $availableBalance = $this->stripeService->getAvailableBalance();
                
                // Check if available balance is sufficient for the payout
                if ($availableBalance < $athleteNetPayout) {
                    // Funds are not yet available - keep deal in awaiting_funds status
                    DB::rollBack();
                    
                    // Update deal outside transaction to mark as awaiting funds
                    $deal->update(['awaiting_funds' => true]);
                    
                    Log::warning('Insufficient Stripe balance for payout', [
                        'deal_id' => $deal->id,
                        'athlete_id' => $athlete->id,
                        'required_amount' => $athleteNetPayout,
                        'available_balance' => $availableBalance,
                    ]);
                    
                    return redirect()->back()->withErrors([
                        'error' => 'Payment complete – payout pending clearing. Stripe funds are still pending. The transfer will be processed automatically once funds are available. Please check back later.'
                    ]);
                }
                
                // Funds are available - clear awaiting_funds flag (will be updated in transaction later)
                
                Log::info('Stripe balance sufficient for payout', [
                    'deal_id' => $deal->id,
                    'athlete_id' => $athlete->id,
                    'required_amount' => $athleteNetPayout,
                    'available_balance' => $availableBalance,
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Failed to check Stripe balance', [
                    'deal_id' => $deal->id,
                    'error' => $e->getMessage(),
                ]);
                return redirect()->back()->withErrors([
                    'error' => 'Failed to verify Stripe balance. Please try again later or contact support.'
                ]);
            }

            // Create payout record first (with idempotency key to prevent double pay)
            $idempotencyKey = "deal_{$deal->id}_release_v1";
            
            // Check if payout with this idempotency key already exists
            $existingPayoutByKey = \App\Models\Payout::where('idempotency_key', $idempotencyKey)->first();
            if ($existingPayoutByKey && $existingPayoutByKey->status === 'completed') {
                DB::rollBack();
                return redirect()->back()->withErrors(['error' => 'Payment has already been released for this deal.']);
            }

            // Create payout record (pending status)
            $payout = \App\Models\Payout::create([
                'deal_id' => $deal->id,
                'athlete_id' => $athlete->id,
                'amount' => $athleteNetPayout,
                'currency' => 'usd',
                'status' => 'pending',
                'released_by_admin_id' => Auth::id(),
                'idempotency_key' => $idempotencyKey,
            ]);

            // Create Stripe Transfer from platform balance to athlete's connected account
            $releaseTransactionId = null;

            try {
                Log::info('Attempting Stripe transfer to athlete', [
                    'deal_id' => $deal->id,
                    'athlete_id' => $athlete->id,
                    'athlete_stripe_account_id' => $stripeAccountId,
                    'transfer_amount' => $athleteNetPayout,
                    'idempotency_key' => $idempotencyKey,
                    'payout_id' => $payout->id,
                ]);

                // Transfer from platform balance (not from a specific charge)
                // This works for both Stripe card payments and wallet payments
                $transfer = $this->stripeService->transferToAthlete(
                    $athleteNetPayout,
                    $stripeAccountId,
                    $idempotencyKey, // Use idempotency key instead of charge ID
                    [
                        'deal_id' => (string) $deal->id,
                        'athlete_id' => (string) $athlete->id,
                        'deal_amount' => (string) $dealAmount,
                        'platform_fee_percentage' => (string) $platformFeePercentage,
                        'platform_fee_amount' => (string) $platformFeeAmount,
                        'athlete_fee_percentage' => (string) $athleteFeePercentage, // 0%
                        'athlete_fee_amount' => (string) $athleteFeeAmount, // $0
                        'athlete_net_payout' => (string) $athleteNetPayout, // deal_amount - platform_fee
                        'payout_id' => (string) $payout->id,
                    ]
                );

                $releaseTransactionId = $transfer->id;

                // Update payout record with transfer ID (status remains 'pending' until webhook confirms)
                $payout->update([
                    'stripe_transfer_id' => $transfer->id,
                    'status' => 'pending', // Keep as pending - webhook will update to 'completed'
                ]);

                // Store transfer ID on deal but DO NOT mark as released yet
                // Wait for Stripe webhook (transfer.paid) to confirm transfer succeeded
                // Also store fee information for reference
                $deal->update([
                    'stripe_transfer_id' => $transfer->id,
                    'stripe_transfer_status' => 'pending', // Will be updated by webhook
                    'release_transaction_id' => $transfer->id,
                    'athlete_fee_percentage' => $athleteFeePercentage,
                    'athlete_fee_amount' => $athleteFeeAmount,
                    'athlete_net_payout' => $athleteNetPayout,
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

                // Commit transaction - transfer is created, but deal is NOT released yet
                DB::commit();

                // Return success message - transfer is in progress
                return redirect()->back()->with('success', 'Payment transfer initiated. The deal will be marked as released once Stripe confirms the transfer. This typically takes a few seconds.');
            } catch (InvalidRequestException $e) {
                $errorMessage = $e->getMessage();
                $stripeErrorCode = $e->getStripeCode() ?? 'unknown';
                
                // Update payout record with error
                $payout->update([
                    'status' => 'failed',
                    'error_message' => $errorMessage,
                ]);
                
                DB::rollBack();
                
                // Provide user-friendly error messages for common issues
                if (str_contains($errorMessage, 'cannot be set to your own account')) {
                    $errorMessage = 'The athlete\'s Stripe account ID is the same as the platform account. The athlete must connect their own Stripe Connect account (not the platform account). Please have them remove the current payment method and add a new one with their own Stripe Connect account ID from their Stripe Dashboard.';
                } elseif (str_contains($errorMessage, 'No such destination') || str_contains($errorMessage, 'does not exist') || str_contains($errorMessage, 'No such account')) {
                    $errorMessage = 'The athlete\'s Stripe account ID "' . $stripeAccountId . '" does not exist or is not accessible. The athlete must update their payment methods with a valid Stripe Connect account ID. They can find their account ID in their Stripe Dashboard → Settings → Connect → Accounts.';
                } elseif (str_contains($errorMessage, 'Invalid account')) {
                    $errorMessage = 'The athlete\'s Stripe account ID is invalid. Please have them update their payment methods with a valid Stripe Connect account ID.';
                } elseif (str_contains($errorMessage, 'insufficient available funds') || str_contains($errorMessage, 'insufficient funds')) {
                    $errorMessage = 'Cannot release payment: Insufficient funds in platform balance. Please ensure the payment has been processed and funds are available.';
                }
                
                Log::error('Stripe transfer failed - InvalidRequestException', [
                    'deal_id' => $deal->id,
                    'athlete_id' => $athlete->id,
                    'athlete_stripe_account_id' => $stripeAccountId,
                    'transfer_amount' => $athleteNetPayout,
                    'idempotency_key' => $idempotencyKey,
                    'payout_id' => $payout->id,
                    'error' => $e->getMessage(),
                    'stripe_error_type' => $stripeErrorCode,
                ]);
                return redirect()->back()->withErrors([
                    'error' => 'Failed to transfer funds to athlete: ' . $errorMessage
                ]);
            } catch (\Exception $e) {
                // Update payout record with error
                $payout->update([
                    'status' => 'failed',
                    'error_message' => $e->getMessage(),
                ]);
                
                DB::rollBack();
                Log::error('Stripe transfer failed - unexpected error', [
                    'deal_id' => $deal->id,
                    'athlete_id' => $athlete->id,
                    'athlete_stripe_account_id' => $stripeAccountId,
                    'transfer_amount' => $athleteNetPayout,
                    'idempotency_key' => $idempotencyKey,
                    'payout_id' => $payout->id,
                    'error' => $e->getMessage(),
                    'error_type' => get_class($e),
                ]);
                return redirect()->back()->withErrors([
                    'error' => 'Failed to transfer funds to athlete: ' . $e->getMessage()
                ]);
            }
            
            // NOTE: Deal is NOT marked as released here
            // The deal will be marked as released when the transfer.paid webhook is received
            // This ensures Stripe is the source of truth
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Failed to release payment. Please try again.']);
        }
    }
}
