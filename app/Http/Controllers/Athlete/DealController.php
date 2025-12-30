<?php

namespace App\Http\Controllers\Athlete;

use App\Http\Controllers\Controller;
use App\Models\Deal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DealController extends Controller
{
    /**
     * List all deals for the athlete
     * Includes deals they've accepted AND deals they've been invited to (pending invitations)
     */
    public function index(Request $request)
    {
        $athlete = Auth::guard('athlete')->user();
        
        // Get deals where athlete_id matches (accepted deals)
        // OR deals where athlete has a pending invitation
        $query = Deal::where(function($q) use ($athlete) {
            $q->where('athlete_id', $athlete->id)
              ->orWhereHas('invitations', function($inviteQuery) use ($athlete) {
                  $inviteQuery->where('status', 'pending')
                              ->where(function($iq) use ($athlete) {
                                  $iq->where('athlete_id', $athlete->id)
                                     ->orWhere('athlete_email', strtolower(trim($athlete->email)));
                              });
              });
        })->with('user')->orderBy('created_at', 'desc');
        
        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('deal_type', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%");
            });
        }
        
        // Eager load messages to check for revision requests
        $deals = $query->with('messages')->paginate(15);
        
        return view('athlete.deals.index', [
            'deals' => $deals,
        ]);
    }

    /**
     * Accept a deal (athlete accepts the deal invitation)
     * Now includes identity verification via invitation
     */
    public function accept(Request $request, string $token)
    {
        $athlete = Auth::guard('athlete')->user();

        // Find invitation by token (new system with identity guardrails)
        $invitation = \App\Models\DealInvitation::where('token', $token)->first();
        
        if ($invitation) {
            $deal = $invitation->deal;

            // Verify invitation is valid
            if (!$invitation->isValid()) {
                if ($invitation->status === 'accepted') {
                    return redirect()->back()->withErrors(['error' => 'This invitation has already been accepted.']);
                }
                if ($invitation->status === 'expired') {
                    return redirect()->back()->withErrors(['error' => 'This invitation has expired.']);
                }
                return redirect()->back()->withErrors(['error' => 'This invitation is no longer valid.']);
            }

            // Identity verification: Check if athlete matches invitation
            $identityMatches = false;
            
            if ($invitation->athlete_id && $invitation->athlete_id === $athlete->id) {
                $identityMatches = true;
            } elseif ($invitation->athlete_email && $invitation->matchesAthleteEmail($athlete->email)) {
                $identityMatches = true;
                // Update invitation with athlete_id now that we know the match
                $invitation->update(['athlete_id' => $athlete->id]);
            }

            if (!$identityMatches) {
                return redirect()->back()->withErrors([
                    'error' => 'This deal invitation was sent to a different athlete. Only the intended recipient can accept this deal.'
                ]);
            }

            // Validate deal can be accepted
            if ($deal->status !== 'pending') {
                return redirect()->back()->withErrors(['error' => 'This deal is no longer available for acceptance.']);
            }

            // Validate contract agreement if required
            if ($deal->contract_text) {
                $request->validate([
                    'contract_agreed' => ['required', 'accepted'],
                ]);
            }

            // Mark invitation as accepted
            $invitation->markAsAccepted();

            // Assign deal to athlete and mark as accepted
            $deal->update([
                'athlete_id' => $athlete->id,
                'status' => 'accepted',
                'contract_signed' => true,
                'contract_signed_at' => now(),
            ]);

            // Create system message
            \App\Models\Message::createSystemMessage(
                $deal->id,
                "Athlete accepted the deal"
            );

            // Create notification for SMB
            if ($deal->user_id) {
                \App\Models\Notification::createForUser(
                    $deal->user_id,
                    'deal_accepted',
                    'Deal Accepted',
                    $athlete->name . ' accepted your deal',
                    route('deals.messages', $deal),
                    $deal->id
                );
            }

            return redirect()->route('athlete.deals.index')->with('success', 'Deal accepted successfully! You can now view it in your deals.');
        }

        // Fallback: Legacy deal token (backward compatibility)
        $deal = Deal::where('token', $token)->firstOrFail();

        // Validate deal can be accepted
        if ($deal->status !== 'pending') {
            return redirect()->back()->withErrors(['error' => 'This deal is no longer available for acceptance.']);
        }

        if ($deal->athlete_id && $deal->athlete_id !== $athlete->id) {
            return redirect()->back()->withErrors(['error' => 'This deal has already been accepted by another athlete.']);
        }

        // Validate contract agreement if required
        if ($deal->contract_text) {
            $request->validate([
                'contract_agreed' => ['required', 'accepted'],
            ]);
        }

        // Assign deal to athlete and mark as accepted
        $deal->update([
            'athlete_id' => $athlete->id,
            'status' => 'accepted',
            'contract_signed' => true,
            'contract_signed_at' => now(),
        ]);

        return redirect()->route('athlete.deals.index')->with('success', 'Deal accepted successfully! You can now view it in your deals.');
    }

    /**
     * Show deal details for athlete
     */
    public function show(Deal $deal)
    {
        $athlete = Auth::guard('athlete')->user();

        // Ensure deal belongs to athlete
        if ($deal->athlete_id !== $athlete->id) {
            abort(403);
        }

        // Eager load messages to check for revision requests
        $deal->load('messages');

        return view('athlete.deals.show', [
            'deal' => $deal,
        ]);
    }

    /**
     * Show cancel deal form
     */
    public function showCancel(Deal $deal)
    {
        $athlete = Auth::guard('athlete')->user();

        // Ensure deal belongs to athlete
        if ($deal->athlete_id !== $athlete->id) {
            abort(403);
        }

        // Can only cancel if deal is accepted (status = 'accepted') and in progress
        // In progress means: accepted, not completed, not cancelled, payment not released
        if ($deal->status !== 'accepted') {
            return redirect()->route('athlete.deals.show', $deal)
                ->withErrors(['error' => 'You can only cancel deals that have been accepted and are in progress.']);
        }

        // Cannot cancel if already completed
        if ($deal->completed_at) {
            return redirect()->route('athlete.deals.show', $deal)
                ->withErrors(['error' => 'Cannot cancel a deal that has already been completed.']);
        }

        // Cannot cancel if payment already released
        if ($deal->released_at) {
            return redirect()->route('athlete.deals.show', $deal)
                ->withErrors(['error' => 'Cannot cancel a deal that has already been paid.']);
        }

        return view('athlete.deals.cancel', [
            'deal' => $deal,
        ]);
    }

    /**
     * Process deal cancellation
     */
    public function cancel(Request $request, Deal $deal)
    {
        $athlete = Auth::guard('athlete')->user();

        // Ensure deal belongs to athlete
        if ($deal->athlete_id !== $athlete->id) {
            abort(403);
        }

        // Can only cancel if deal is accepted (status = 'accepted') and in progress
        // In progress means: accepted, not completed, not cancelled, payment not released
        if ($deal->status !== 'accepted') {
            return redirect()->route('athlete.deals.show', $deal)
                ->withErrors(['error' => 'You can only cancel deals that have been accepted and are in progress.']);
        }

        // Cannot cancel if already completed
        if ($deal->completed_at) {
            return redirect()->route('athlete.deals.show', $deal)
                ->withErrors(['error' => 'Cannot cancel a deal that has already been completed.']);
        }

        // Cannot cancel if payment already released
        if ($deal->released_at) {
            return redirect()->route('athlete.deals.show', $deal)
                ->withErrors(['error' => 'Cannot cancel a deal that has already been paid.']);
        }

        $validated = $request->validate([
            'cancellation_reason' => ['required', 'string', 'min:10', 'max:1000'],
        ], [
            'cancellation_reason.required' => 'Please provide a reason for cancelling this deal.',
            'cancellation_reason.min' => 'Cancellation reason must be at least 10 characters.',
        ]);

        try {
            DB::beginTransaction();

            // Update deal status to cancelled
            $deal->update([
                'status' => 'cancelled',
                'approval_notes' => 'Athlete cancellation: ' . $validated['cancellation_reason'],
            ]);

            // Create system message
            \App\Models\Message::createSystemMessage(
                $deal->id,
                "Athlete cancelled this deal. Reason: " . $validated['cancellation_reason']
            );

            // Create notification for business
            if ($deal->user_id) {
                \App\Models\Notification::createForUser(
                    $deal->user_id,
                    'deal_cancelled',
                    'Deal Cancelled',
                    "Athlete cancelled deal #{$deal->id}. Reason: " . Str::limit($validated['cancellation_reason'], 100),
                    route('deals.index'),
                    $deal->id
                );
            }

            // Note: When athlete cancels, payment is NOT released
            // The escrow funds remain in the system and should be handled according to business rules
            // (typically returned to the SMB, but that's handled separately)

            DB::commit();

            return redirect()->route('athlete.deals.index')
                ->with('success', 'Deal cancelled successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error('Failed to cancel deal', [
                'deal_id' => $deal->id,
                'error' => $e->getMessage(),
            ]);
            return redirect()->back()
                ->withErrors(['error' => 'Failed to cancel deal: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Show submit deliverables form
     */
    public function showSubmit(Deal $deal)
    {
        $athlete = Auth::guard('athlete')->user();

        // Ensure deal belongs to athlete
        if ($deal->athlete_id !== $athlete->id) {
            abort(403);
        }

        // Ensure deal is in correct state
        if ($deal->status !== 'accepted') {
            return redirect()->route('athlete.deals.index')->withErrors(['error' => 'This deal cannot be submitted.']);
        }

        return view('athlete.deals.submit', [
            'deal' => $deal,
        ]);
    }

    /**
     * Submit deliverables for a deal
     */
    public function submitDeliverables(Request $request, Deal $deal)
    {
        $athlete = Auth::guard('athlete')->user();

        // Ensure deal belongs to athlete
        if ($deal->athlete_id !== $athlete->id) {
            abort(403);
        }

        // Ensure deal is in correct state
        if ($deal->status !== 'accepted') {
            return redirect()->back()->withErrors(['error' => 'This deal cannot be submitted.']);
        }

        $validated = $request->validate([
            'completion_notes' => ['required', 'string', 'max:5000'],
            'deliverables' => ['nullable', 'array', 'max:10'],
            'deliverables.*' => ['file', 'max:10240', 'mimes:pdf,doc,docx,jpg,jpeg,png,gif,mp4,mov,avi'],
        ]);

        // Handle file uploads
        $deliverableFiles = [];
        if ($request->hasFile('deliverables')) {
            foreach ($request->file('deliverables') as $file) {
                $path = $file->store('deal-deliverables', 'public');
                $deliverableFiles[] = [
                    'path' => $path,
                    'original_name' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                ];
            }
        }

        // Update deal with deliverables
        // If this is a resubmission (deal was sent back), append to existing deliverables or replace
        // For simplicity, we'll replace deliverables on resubmission
        $deal->update([
            'status' => 'completed',
            'completion_notes' => $validated['completion_notes'],
            'deliverables' => !empty($deliverableFiles) ? $deliverableFiles : null,
            'completed_at' => now(),
            // Clear approval flags when resubmitting
            'is_approved' => false,
            'approved_at' => null,
            'approval_notes' => null,
        ]);

        // Create system message
        \App\Models\Message::createSystemMessage(
            $deal->id,
            "Athlete submitted deliverables"
        );

        // Create notification for SMB
        if ($deal->user_id) {
            \App\Models\Notification::createForUser(
                $deal->user_id,
                'deal_completed',
                'Deliverables Submitted',
                $athlete->name . ' submitted deliverables for review',
                route('deals.index'),
                $deal->id
            );

            // Send email to business
            try {
                $business = $deal->user;
                if ($business && $business->email) {
                    $businessName = $business->business_name ?? $business->name ?? explode('@', $business->email)[0];
                    \Illuminate\Support\Facades\Mail::to($business->email)->send(
                        new \App\Mail\DeliverablesSubmittedMail($businessName, $athlete->name, $deal)
                    );
                }
            } catch (\Exception $e) {
                \Log::error('Failed to send deliverables submitted email', [
                    'deal_id' => $deal->id,
                    'business_id' => $deal->user_id,
                    'error' => $e->getMessage(),
                ]);
                // Don't fail the submission if email fails
            }
        }

        return redirect()->route('athlete.deals.index')->with('success', 'Deliverables submitted successfully! The business will review and approve your work.');
    }
}

