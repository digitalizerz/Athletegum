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
        if ($deal->payment_status !== 'paid' && $deal->payment_status !== 'paid_escrowed') {
            return redirect()->back()->withErrors(['error' => 'Deal payment has not been completed.']);
        }

        // Can approve if deal is completed (athlete submitted) or if it was sent back for revisions
        if ($deal->status !== 'completed' && $deal->status !== 'accepted') {
            return redirect()->back()->withErrors(['error' => 'Deal must be completed by athlete before it can be approved.']);
        }

        // Cannot approve if payment has already been released (payment release = automatic approval)
        if ($deal->released_at || $deal->is_approved) {
            return redirect()->back()->withErrors(['error' => 'Deal has already been approved and payment released.']);
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
            "Business approved the work" . ($validated['approval_notes'] ? ': ' . $validated['approval_notes'] : '')
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
     * Request revisions - Send deal back to athlete for updates
     */
    public function requestRevisions(Request $request, Deal $deal)
    {
        // Ensure deal belongs to user
        if ($deal->user_id !== Auth::id()) {
            abort(403);
        }

        // Can only request revisions if deal is completed (athlete submitted) or approved
        // Cannot request revisions if payment already released OR approved (payment release = final approval)
        if ($deal->released_at || $deal->is_approved) {
            return redirect()->back()->withErrors(['error' => 'Cannot request revisions for a deal that has already been approved and paid.']);
        }

        // Cannot request revisions if payment is pending clearance
        if ($deal->awaiting_funds) {
            return redirect()->back()->withErrors(['error' => 'Cannot request revisions while payment is pending clearance. Please wait until the payment clears.']);
        }

        // Deal must be completed (athlete submitted) or approved to request revisions
        if ($deal->status !== 'completed' && $deal->status !== 'approved') {
            return redirect()->back()->withErrors(['error' => 'Can only request revisions for deals that have been submitted by the athlete.']);
        }

        $validated = $request->validate([
            'revision_notes' => ['required', 'string', 'min:10', 'max:1000'],
        ], [
            'revision_notes.required' => 'Please provide feedback on what needs to be revised.',
            'revision_notes.min' => 'Revision notes must be at least 10 characters.',
        ]);

        try {
            DB::beginTransaction();

            // Reset deal to accepted status (in progress)
            // Clear approval flags but keep completion history
            $deal->update([
                'status' => 'accepted',
                'is_approved' => false,
                'approved_at' => null,
                'approval_notes' => null,
                // Keep completed_at and deliverables for history, but allow resubmission
            ]);

            // Create system message
            \App\Models\Message::createSystemMessage(
                $deal->id,
                "Business requested revisions: " . $validated['revision_notes']
            );

            // Create notification for athlete
            if ($deal->athlete_id && $deal->athlete) {
                \App\Models\Notification::createForAthlete(
                    $deal->athlete_id,
                    'deal_revision_requested',
                    'Revisions Requested',
                    'The business has requested revisions to your work. Please review the feedback and submit updated deliverables.',
                    route('athlete.deals.show', $deal),
                    $deal->id
                );

                // Send email to athlete
                try {
                    if ($deal->athlete->email) {
                        \Illuminate\Support\Facades\Mail::to($deal->athlete->email)->send(
                            new \App\Mail\RevisionsRequestedMail($deal->athlete->name, $deal, $validated['revision_notes'])
                        );
                        \Log::info('Revision request email sent', [
                            'deal_id' => $deal->id,
                            'athlete_email' => $deal->athlete->email,
                        ]);
                    }
                } catch (\Exception $e) {
                    \Log::error('Failed to send revision request email', [
                        'deal_id' => $deal->id,
                        'athlete_id' => $deal->athlete_id,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString(),
                    ]);
                    // Don't fail the revision request if email fails
                }
            }

            DB::commit();

            return redirect()->back()->with('success', 'Revision request sent to athlete. They can now submit updated deliverables.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Failed to request revisions. Please try again.']);
        }
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

        // Cannot cancel if payment is pending clearance
        if ($deal->awaiting_funds) {
            return redirect()->back()->withErrors(['error' => 'Cannot cancel deal while payment is pending clearance. Please wait until the payment clears.']);
        }

        try {
            DB::beginTransaction();

            // Return escrow funds to SMB wallet
            if (($deal->payment_status === 'paid' || $deal->payment_status === 'paid_escrowed') && $deal->escrow_amount) {
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
