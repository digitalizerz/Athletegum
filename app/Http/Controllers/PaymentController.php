<?php

namespace App\Http\Controllers;

use App\Models\Deal;
use App\Models\PaymentMethod;
use App\Models\PlatformSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
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

                // Process card payment for remainder
                // In production, this would process via Stripe
                $paymentIntentId = 'card_' . uniqid();
                $session->put('payment_method', 'wallet_card');
                $session->put('card_payment_method_id', $paymentMethod->id);
                $session->put('card_amount', $cardAmount);
                $session->put('wallet_amount_used', $walletAmountUsed);

            } else { // card_full
                // Pay fully via card
                $cardAmount = $totalAmount;

                // Verify payment method belongs to user
                $paymentMethod = PaymentMethod::findOrFail($validated['payment_method_id']);
                if ($paymentMethod->user_id !== $user->id) {
                    return redirect()->back()->withErrors(['error' => 'Invalid payment method.']);
                }

                // Process card payment
                // In production, this would process via Stripe
                $paymentIntentId = 'card_' . uniqid();
                $session->put('payment_method', 'card');
                $session->put('card_payment_method_id', $paymentMethod->id);
                $session->put('card_amount', $cardAmount);
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
            $session->put('payment_status', 'paid');

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

            // Create a transaction record for the release
            // In production, this would transfer funds to athlete's account via Stripe
            // The athlete receives net payout, platform retains the fee
            $releaseTransactionId = 'txn_' . uniqid();
            
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
