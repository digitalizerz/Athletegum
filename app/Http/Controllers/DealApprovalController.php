<?php

namespace App\Http\Controllers;

use App\Models\Deal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DealApprovalController extends Controller
{
    /**
     * Approve a deal (SMB approves the deliverable)
     */
    public function approve(Request $request, Deal $deal)
    {
        // Ensure deal belongs to user
        if ($deal->user_id !== Auth::id()) {
            abort(403);
        }

        // Ensure deal is in correct state
        if ($deal->payment_status !== 'paid') {
            return redirect()->back()->withErrors(['error' => 'Deal payment has not been completed.']);
        }

        if ($deal->is_approved) {
            return redirect()->back()->withErrors(['error' => 'Deal has already been approved.']);
        }

        if ($deal->released_at) {
            return redirect()->back()->withErrors(['error' => 'Payment has already been released.']);
        }

        $validated = $request->validate([
            'approval_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $deal->update([
            'is_approved' => true,
            'approved_at' => now(),
            'approval_notes' => $validated['approval_notes'] ?? null,
            'status' => 'approved',
        ]);

        // Create system message
        \App\Models\Message::createSystemMessage(
            $deal->id,
            "Business approved the work"
        );

        // Create notification for athlete
        if ($deal->athlete_id) {
            \App\Models\Notification::createForAthlete(
                $deal->athlete_id,
                'deal_approved',
                'Deal Approved',
                'Your work has been approved. Payment will be released soon.',
                route('athlete.deals.index'),
                $deal->id
            );
        }

        return redirect()->back()->with('success', 'Deal approved successfully. Payment will be released to the athlete.');
    }

    /**
     * Cancel a deal and return escrow funds
     */
    public function cancel(Request $request, Deal $deal)
    {
        // Ensure deal belongs to user
        if ($deal->user_id !== Auth::id()) {
            abort(403);
        }

        // Cannot cancel if already released
        if ($deal->released_at) {
            return redirect()->back()->withErrors(['error' => 'Cannot cancel a deal that has already been released.']);
        }

        try {
            DB::beginTransaction();

            // Return escrow funds to SMB wallet
            if ($deal->payment_status === 'paid' && $deal->escrow_amount) {
                $user = Auth::user();
                $escrowAmount = (float) $deal->escrow_amount;
                $platformFeeAmount = (float) $deal->platform_fee_amount ?? 0;
                
                // Return escrow amount to wallet (platform fee is not returned)
                $user->addToWallet($escrowAmount, 'refund', null, [
                    'deal_id' => $deal->id,
                    'reason' => 'Deal cancelled - escrow returned',
                ]);

                // Create transaction record
                \App\Models\WalletTransaction::create([
                    'user_id' => $user->id,
                    'type' => 'refund',
                    'status' => 'completed',
                    'amount' => $escrowAmount,
                    'balance_before' => $user->wallet_balance - $escrowAmount,
                    'balance_after' => $user->wallet_balance,
                    'deal_id' => $deal->id,
                    'description' => "Escrow returned for cancelled deal #{$deal->id}",
                ]);
            }

            $deal->update([
                'status' => 'cancelled',
                'payment_status' => 'refunded',
            ]);

            DB::commit();

            return redirect()->back()->with('success', 'Deal cancelled successfully. Escrow funds have been returned to your wallet.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Failed to cancel deal. Please try again.']);
        }
    }
}
