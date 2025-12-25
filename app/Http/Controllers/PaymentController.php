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

        // Calculate payment breakdown using SMB platform fee
        $compensationAmount = (float) $session->get('compensation_amount');
        $smbFee = PlatformSetting::getSMBPlatformFee();
        
        if ($smbFee['type'] === 'percentage') {
            $platformFeeAmount = round($compensationAmount * ($smbFee['value'] / 100), 2);
            $platformFeePercentage = $smbFee['value'];
        } else {
            $platformFeeAmount = round($smbFee['value'], 2);
            $platformFeePercentage = null; // Fixed fee, no percentage
        }
        
        $escrowAmount = round($compensationAmount, 2);
        $totalAmount = round($compensationAmount + $platformFeeAmount, 2);

        try {
            DB::beginTransaction();

            $walletAmountUsed = 0;
            $cardAmount = 0;
            $paymentIntentId = null;

            if ($validated['payment_method'] === 'wallet_full') {
                // Pay fully from wallet
                if ($walletBalance < $totalAmount) {
                    return redirect()->back()->withErrors([
                        'error' => 'Insufficient wallet balance. Your balance is $' . number_format($walletBalance, 2) . '.'
                    ]);
                }

                $walletAmountUsed = $totalAmount;
                $user->deductFromWallet($totalAmount, 'payment', null, [
                    'platform_fee_type' => $smbFee['type'],
                    'platform_fee_percentage' => $platformFeePercentage,
                    'platform_fee_value' => $smbFee['value'],
                    'platform_fee_amount' => $platformFeeAmount,
                    'escrow_amount' => $escrowAmount,
                    'compensation_amount' => $compensationAmount,
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

                $walletAmountUsed = min($walletBalance, $totalAmount);
                $cardAmount = $totalAmount - $walletAmountUsed;

                // Verify payment method belongs to user
                $paymentMethod = PaymentMethod::findOrFail($validated['payment_method_id']);
                if ($paymentMethod->user_id !== $user->id) {
                    return redirect()->back()->withErrors(['error' => 'Invalid payment method.']);
                }

                // Deduct wallet portion
                if ($walletAmountUsed > 0) {
                    $user->deductFromWallet($walletAmountUsed, 'payment', null, [
                        'platform_fee_type' => $smbFee['type'],
                        'platform_fee_percentage' => $platformFeePercentage,
                        'platform_fee_value' => $smbFee['value'],
                        'platform_fee_amount' => $platformFeeAmount,
                        'escrow_amount' => $escrowAmount,
                        'compensation_amount' => $compensationAmount,
                        'payment_type' => 'partial_wallet',
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
                $cardPlatformFeeAmount = round(($cardAmount / $totalAmount) * $platformFeeAmount, 2);

                try {
                    $paymentIntent = $this->stripeService->createPaymentIntent(
                        $cardAmount,
                        $paymentMethod->provider_payment_method_id,
                        $cardPlatformFeeAmount, // Application fee for card portion
                        [
                            'user_id' => $user->id,
                            'deal_type' => $session->get('deal_type'),
                            'compensation_amount' => (string) $compensationAmount,
                            'platform_fee_amount' => (string) $cardPlatformFeeAmount,
                            'escrow_amount' => (string) $escrowAmount,
                            'payment_type' => 'partial_wallet',
                            'wallet_amount_used' => (string) $walletAmountUsed,
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
                $cardAmount = $totalAmount;

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
                        $platformFeeAmount, // Application fee (platform fee)
                        [
                            'user_id' => $user->id,
                            'deal_type' => $session->get('deal_type'),
                            'compensation_amount' => (string) $compensationAmount,
                            'platform_fee_amount' => (string) $platformFeeAmount,
                            'escrow_amount' => (string) $escrowAmount,
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
            $session->put('platform_fee_type', $smbFee['type']);
            $session->put('platform_fee_percentage', $platformFeePercentage);
            $session->put('platform_fee_value', $smbFee['value']);
            $session->put('platform_fee_amount', $platformFeeAmount);
            $session->put('escrow_amount', $escrowAmount);
            $session->put('total_amount', $totalAmount);
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

            // Calculate athlete fee and net payout
            $athleteFeePercentage = PlatformSetting::getAthletePlatformFeePercentage();
            $escrowAmount = (float) $deal->escrow_amount;
            $athleteFeeAmount = round($escrowAmount * ($athleteFeePercentage / 100), 2);
            $athleteNetPayout = round($escrowAmount - $athleteFeeAmount, 2);

            // Get athlete's Stripe account ID
            $athlete = $deal->athlete;
            if (!$athlete) {
                DB::rollBack();
                return redirect()->back()->withErrors([
                    'error' => 'Athlete not found for this deal.'
                ]);
            }

            // Get Stripe account ID from athlete record or from their payment methods
            $stripeAccountId = $athlete->stripe_account_id;
            
            // If not set on athlete, try to get from their payment methods
            if (!$stripeAccountId) {
                $paymentMethod = $athlete->paymentMethods()
                    ->where('is_active', true)
                    ->whereNotNull('provider_account_id')
                    ->first();
                
                if ($paymentMethod && $paymentMethod->provider_account_id) {
                    $stripeAccountId = $paymentMethod->provider_account_id;
                    // Also update athlete record for future use
                    $athlete->update(['stripe_account_id' => $stripeAccountId]);
                }
            }

            if (!$stripeAccountId) {
                DB::rollBack();
                return redirect()->back()->withErrors([
                    'error' => 'Athlete does not have a Stripe account configured. They must set up payment methods in their Earnings section first.'
                ]);
            }

            // Get the original charge ID from the PaymentIntent
            $chargeId = null;
            if ($deal->payment_intent_id && str_starts_with($deal->payment_intent_id, 'pi_')) {
                try {
                    $paymentIntent = $this->stripeService->getPaymentIntent($deal->payment_intent_id);
                    if ($paymentIntent->charges && count($paymentIntent->charges->data) > 0) {
                        $chargeId = $paymentIntent->charges->data[0]->id;
                    }
                } catch (\Exception $e) {
                    Log::error('Failed to retrieve PaymentIntent for release', [
                        'deal_id' => $deal->id,
                        'payment_intent_id' => $deal->payment_intent_id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            // Create REAL Stripe transfer to athlete
            $releaseTransactionId = null;
            if ($chargeId && $this->stripeService->isConfigured()) {
                try {
                    $transfer = $this->stripeService->transferToAthlete(
                        $athleteNetPayout,
                        $stripeAccountId,
                        $chargeId,
                        [
                            'deal_id' => (string) $deal->id,
                            'athlete_id' => (string) $athlete->id,
                            'escrow_amount' => (string) $escrowAmount,
                            'athlete_fee_percentage' => (string) $athleteFeePercentage,
                            'athlete_fee_amount' => (string) $athleteFeeAmount,
                            'athlete_net_payout' => (string) $athleteNetPayout,
                        ]
                    );

                    $releaseTransactionId = $transfer->id;

                    Log::info('Stripe transfer created for athlete', [
                        'deal_id' => $deal->id,
                        'transfer_id' => $transfer->id,
                        'athlete_id' => $athlete->id,
                        'stripe_account_id' => $stripeAccountId,
                        'amount' => $athleteNetPayout,
                    ]);
                } catch (\Exception $e) {
                    DB::rollBack();
                    Log::error('Stripe transfer failed', [
                        'deal_id' => $deal->id,
                        'athlete_id' => $athlete->id,
                        'error' => $e->getMessage(),
                    ]);
                    return redirect()->back()->withErrors([
                        'error' => 'Failed to transfer funds to athlete: ' . $e->getMessage()
                    ]);
                }
            } else {
                // Fallback: create transaction record without Stripe transfer
                // This should not happen in production, but allows graceful degradation
                $releaseTransactionId = 'txn_fallback_' . uniqid();
                Log::warning('Athlete payout without Stripe transfer', [
                    'deal_id' => $deal->id,
                    'athlete_id' => $athlete->id,
                    'reason' => $chargeId ? 'Stripe not configured' : 'No charge ID',
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
