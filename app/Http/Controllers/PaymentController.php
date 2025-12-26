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
        // Business pays: deal_amount + (deal_amount × 10%)
        $dealAmount = (float) $session->get('compensation_amount'); // Base deal amount
        $businessFeePercentage = 10.0; // Fixed 10% business fee
        $businessFeeAmount = round($dealAmount * ($businessFeePercentage / 100), 2);
        $businessTotal = round($dealAmount + $businessFeeAmount, 2); // Total business pays
        
        // Escrow amount is the base deal amount (what athlete will eventually receive minus athlete fee)
        $escrowAmount = round($dealAmount, 2);
        $platformFeeAmount = $businessFeeAmount; // Platform fee from business
        $platformFeePercentage = $businessFeePercentage;

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
                    'platform_fee_value' => $businessFeePercentage,
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
                        'platform_fee_value' => $businessFeePercentage,
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
                        $cardPlatformFeeAmount, // Platform fee for card portion (for tracking only)
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
                        $platformFeeAmount, // Platform fee (for tracking only - fees are handled in Stripe)
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
                    $session->put('payment_method', 'card');
                    $session->put('card_payment_method_id', $paymentMethod->id);
                    $session->put('card_amount', $cardAmount);
                    $session->put('stripe_charge_id', $paymentIntent->charges->data[0]->id ?? null);

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
            $session->put('platform_fee_value', $businessFeePercentage);
            $session->put('platform_fee_amount', $platformFeeAmount);
            $session->put('escrow_amount', $escrowAmount);
            $session->put('compensation_amount', $dealAmount); // Store original deal amount
            $session->put('total_amount', $businessTotal); // Business total (deal_amount + 10%)
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

        // Ensure deal is in correct state
        if ($deal->payment_status !== 'paid') {
            return redirect()->back()->withErrors(['error' => 'Deal payment has not been completed.']);
        }

        // Funds can be released when deal is completed (athlete submitted) or approved
        if ($deal->status !== 'completed' && !$deal->is_approved) {
            return redirect()->back()->withErrors(['error' => 'Deal must be completed by athlete or approved before payment can be released.']);
        }

        if ($deal->released_at) {
            return redirect()->back()->withErrors(['error' => 'Payment has already been released.']);
        }

        try {
            DB::beginTransaction();

            // Calculate athlete payout per marketplace rules:
            // Athlete receives: deal_amount - (deal_amount × 5%)
            $dealAmount = (float) $deal->escrow_amount; // This is the base deal amount (what athlete earns)
            $athleteFeePercentage = 5.0; // Fixed 5% athlete fee
            $athleteFeeAmount = round($dealAmount * ($athleteFeePercentage / 100), 2);
            $athleteNetPayout = round($dealAmount - $athleteFeeAmount, 2); // Athlete receives deal_amount - 5%

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

            // Get the original charge ID from the PaymentIntent (if available)
            // For Stripe card payments, use source_transaction
            // For wallet payments, we cannot release via Stripe Transfer
            $chargeId = null;
            $isStripePayment = false;
            
            // Check if this is a Stripe payment (payment_intent_id starts with 'pi_')
            // Wallet payments have payment_intent_id like 'wallet_xxxxx'
            if ($deal->payment_intent_id && str_starts_with($deal->payment_intent_id, 'pi_')) {
                $isStripePayment = true;
                try {
                    $paymentIntent = $this->stripeService->getPaymentIntent($deal->payment_intent_id);
                    if ($paymentIntent->charges && count($paymentIntent->charges->data) > 0) {
                        $chargeId = $paymentIntent->charges->data[0]->id;
                    } else {
                        Log::warning('PaymentIntent found but no charges available', [
                            'deal_id' => $deal->id,
                            'payment_intent_id' => $deal->payment_intent_id,
                            'payment_intent_status' => $paymentIntent->status ?? 'unknown',
                        ]);
                    }
                } catch (\Exception $e) {
                    Log::error('Failed to retrieve PaymentIntent for release', [
                        'deal_id' => $deal->id,
                        'payment_intent_id' => $deal->payment_intent_id,
                        'error' => $e->getMessage(),
                    ]);
                    // Will be caught below - chargeId will be null, which will trigger error
                }
            } else {
                // This is a wallet payment (payment_intent_id doesn't start with 'pi_')
                // or payment_intent_id is null/empty
                $isStripePayment = false;
                Log::info('Non-Stripe payment detected (wallet payment)', [
                    'deal_id' => $deal->id,
                    'payment_intent_id' => $deal->payment_intent_id,
                ]);
            }

            // Create REAL Stripe transfer to athlete (REQUIRED - Stripe is source of truth)
            // For card payments: transfer from the charge
            // For wallet payments: we need to create a charge first, then transfer
            $releaseTransactionId = null;
            if (!$this->stripeService->isConfigured()) {
                DB::rollBack();
                return redirect()->back()->withErrors([
                    'error' => 'Stripe is not configured. Cannot release payment.'
                ]);
            }

            try {
                // For wallet payments, we cannot create Stripe transfers because no Stripe charge exists
                if (!$isStripePayment) {
                    // Wallet payment - cannot release via Stripe Transfer
                    DB::rollBack();
                    $isTestMode = str_starts_with($this->stripeService->getPublishableKey(), 'pk_test_');
                    Log::error('Cannot release wallet payment via Stripe Transfer', [
                        'deal_id' => $deal->id,
                        'payment_intent_id' => $deal->payment_intent_id,
                    ]);
                    $errorMsg = $isTestMode 
                        ? 'Wallet payments cannot be released because they don\'t create Stripe charges. Please use a Stripe card payment method for deals that need to be released to athletes. The wallet system needs to be enhanced to create actual Stripe charges when payments are made.'
                        : 'Wallet payments cannot be released via Stripe Transfer. Please use a Stripe card payment method for deals, or ensure wallet payments create actual Stripe charges.';
                    
                    return redirect()->back()->withErrors(['error' => $errorMsg]);
                }

                // For Stripe card payments, we must have a charge ID to transfer from
                if (!$chargeId) {
                    DB::rollBack();
                    
                    // Check if this is a wallet payment
                    $isWalletPayment = $deal->payment_intent_id && !str_starts_with($deal->payment_intent_id, 'pi_');
                    
                    if ($isWalletPayment) {
                        Log::error('Cannot release wallet payment: No Stripe charge exists', [
                            'deal_id' => $deal->id,
                            'payment_intent_id' => $deal->payment_intent_id,
                        ]);
                        return redirect()->back()->withErrors([
                            'error' => 'This deal was paid using your wallet balance. Wallet payments cannot be automatically released to athletes via Stripe because they don\'t create Stripe charges. To release payment, please contact support or use a Stripe card payment method for future deals that need to be released to athletes.'
                        ]);
                    } else {
                        Log::error('Cannot release payment: No charge ID available for Stripe transfer', [
                            'deal_id' => $deal->id,
                            'payment_intent_id' => $deal->payment_intent_id,
                            'payment_status' => $deal->payment_status,
                        ]);
                        return redirect()->back()->withErrors([
                            'error' => 'Cannot release payment: The Stripe payment charge could not be found. The payment may still be processing, or there may be an issue with the payment. Please wait a few minutes and try again, or contact support if the issue persists.'
                        ]);
                    }
                }

                Log::info('Attempting Stripe transfer to athlete', [
                    'deal_id' => $deal->id,
                    'athlete_id' => $athlete->id,
                    'athlete_stripe_account_id' => $stripeAccountId,
                    'transfer_amount' => $athleteNetPayout,
                    'charge_id' => $chargeId,
                    'payment_type' => 'stripe',
                ]);

                $transfer = $this->stripeService->transferToAthlete(
                    $athleteNetPayout,
                    $stripeAccountId,
                    $chargeId, // Must have charge ID for Stripe transfers
                    [
                        'deal_id' => (string) $deal->id,
                        'athlete_id' => (string) $athlete->id,
                        'deal_amount' => (string) $dealAmount,
                        'athlete_fee_percentage' => (string) $athleteFeePercentage,
                        'athlete_fee_amount' => (string) $athleteFeeAmount,
                        'athlete_net_payout' => (string) $athleteNetPayout,
                        'payment_type' => $isStripePayment ? 'stripe' : 'wallet',
                    ]
                );

                $releaseTransactionId = $transfer->id;

                Log::info('Stripe transfer created successfully', [
                    'deal_id' => $deal->id,
                    'transfer_id' => $transfer->id,
                    'athlete_id' => $athlete->id,
                    'stripe_account_id' => $stripeAccountId,
                    'amount' => $athleteNetPayout,
                    'charge_id' => $chargeId,
                    'payment_type' => $isStripePayment ? 'stripe' : 'wallet',
                ]);
            } catch (InvalidRequestException $e) {
                DB::rollBack();
                $errorMessage = $e->getMessage();
                $stripeErrorCode = $e->getStripeCode() ?? 'unknown';
                
                // Provide user-friendly error messages for common issues
                if (str_contains($errorMessage, 'cannot be set to your own account')) {
                    $errorMessage = 'The athlete\'s Stripe account ID is the same as the platform account. The athlete must connect their own Stripe Connect account (not the platform account). Please have them remove the current payment method and add a new one with their own Stripe Connect account ID from their Stripe Dashboard.';
                } elseif (str_contains($errorMessage, 'No such destination') || str_contains($errorMessage, 'does not exist') || str_contains($errorMessage, 'No such account')) {
                    $errorMessage = 'The athlete\'s Stripe account ID "' . $stripeAccountId . '" does not exist or is not accessible. The athlete must update their payment methods with a valid Stripe Connect account ID. They can find their account ID in their Stripe Dashboard → Settings → Connect → Accounts.';
                } elseif (str_contains($errorMessage, 'Invalid account')) {
                    $errorMessage = 'The athlete\'s Stripe account ID is invalid. Please have them update their payment methods with a valid Stripe Connect account ID.';
                } elseif (str_contains($errorMessage, 'insufficient available funds') || str_contains($errorMessage, 'insufficient funds')) {
                    $errorMessage = 'Cannot release payment: This deal was paid via wallet or the original payment charge could not be found. Wallet payments cannot be released via Stripe because they don\'t create actual Stripe charges. For deals that need to be released to athletes, please use a Stripe card payment method instead of wallet.';
                }
                
                Log::error('Stripe transfer failed - InvalidRequestException', [
                    'deal_id' => $deal->id,
                    'athlete_id' => $athlete->id,
                    'athlete_stripe_account_id' => $stripeAccountId,
                    'transfer_amount' => $athleteNetPayout,
                    'charge_id' => $chargeId,
                    'error' => $e->getMessage(),
                    'stripe_error_type' => $stripeErrorCode,
                ]);
                return redirect()->back()->withErrors([
                    'error' => 'Failed to transfer funds to athlete: ' . $errorMessage
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Stripe transfer failed - unexpected error', [
                    'deal_id' => $deal->id,
                    'athlete_id' => $athlete->id,
                    'athlete_stripe_account_id' => $stripeAccountId,
                    'transfer_amount' => $athleteNetPayout,
                    'charge_id' => $chargeId,
                    'error' => $e->getMessage(),
                    'error_type' => get_class($e),
                ]);
                return redirect()->back()->withErrors([
                    'error' => 'Failed to transfer funds to athlete: ' . $e->getMessage()
                ]);
            }
            
            // Auto-approve if not already approved (when releasing from completed status)
            $wasApproved = $deal->is_approved;
            $updateData = [
                'released_at' => now(),
                'release_transaction_id' => $releaseTransactionId,
                'status' => 'completed',
                'athlete_fee_percentage' => $athleteFeePercentage,
                'athlete_fee_amount' => $athleteFeeAmount,
                'athlete_net_payout' => $athleteNetPayout,
            ];
            
            // Auto-approve if releasing from completed status
            if (!$wasApproved) {
                $updateData['is_approved'] = true;
                $updateData['approved_at'] = now();
            }
            
            $deal->update($updateData);

            // Create wallet transaction for the release (even though funds were already deducted)
            \App\Models\WalletTransaction::create([
                'user_id' => $deal->user_id,
                'type' => 'payment_release',
                'status' => 'completed',
                'amount' => 0, // Already deducted, just a record
                'balance_before' => Auth::user()->wallet_balance,
                'balance_after' => Auth::user()->wallet_balance,
                'payment_provider_transaction_id' => $releaseTransactionId,
                'deal_id' => $deal->id,
                'description' => "Payment released to athlete for deal #{$deal->id}",
            ]);

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
                    'Payment of $' . number_format($athleteNetPayout, 2) . ' has been released to your account',
                    route('athlete.earnings.index'),
                    $deal->id
                );
            }

            DB::commit();

            return redirect()->back()->with('success', 'Payment released successfully to athlete.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Failed to release payment. Please try again.']);
        }
    }
}
