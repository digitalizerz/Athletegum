<?php

namespace App\Http\Controllers\Athlete;

use App\Http\Controllers\Controller;
use App\Models\Deal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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
        
        $deals = $query->paginate(15);
        
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

        return view('athlete.deals.show', [
            'deal' => $deal,
        ]);
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
        $deal->update([
            'status' => 'completed',
            'completion_notes' => $validated['completion_notes'],
            'deliverables' => !empty($deliverableFiles) ? $deliverableFiles : null,
            'completed_at' => now(),
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
        }

        return redirect()->route('athlete.deals.index')->with('success', 'Deliverables submitted successfully! The business will review and approve your work.');
    }
}

