<?php

namespace App\Http\Controllers\Athlete;

use App\Http\Controllers\Controller;
use App\Models\Deal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Show athlete dashboard
     */
    public function index()
    {
        $athlete = Auth::guard('athlete')->user();
        
        // Get all deals for this athlete (accepted + pending invitations)
        $allDealsQuery = Deal::where(function($q) use ($athlete) {
            $q->where('athlete_id', $athlete->id)
              ->orWhereHas('invitations', function($inviteQuery) use ($athlete) {
                  $inviteQuery->where('status', 'pending')
                              ->where(function($iq) use ($athlete) {
                                  $iq->where('athlete_id', $athlete->id)
                                     ->orWhere('athlete_email', strtolower(trim($athlete->email)));
                              });
              });
        })->with('user')->orderBy('created_at', 'desc');
        
        // Get deals by status
        $acceptedDeals = (clone $allDealsQuery)->where('status', 'accepted')->get();
        $completedDeals = (clone $allDealsQuery)->where('status', 'completed')->get();
        $pendingDeals = (clone $allDealsQuery)->where('status', 'pending')->get(); // These are deals that haven't been accepted yet (invitations)
        
        // Stats
        // Total deals: only count accepted deals (where athlete_id is set)
        $totalDeals = Deal::where('athlete_id', $athlete->id)->count();
        $totalCompleted = Deal::where('athlete_id', $athlete->id)->where('status', 'completed')->count();
        
        // Pending count: deals with pending invitations (not yet accepted)
        $pendingCount = $pendingDeals->count();
        
        // Total earnings from released deals
        // Use athlete_net_payout if available, otherwise calculate from escrow_amount and athlete_fee_percentage
        $totalEarnings = $athlete->releasedDeals()->get()->sum(function($deal) {
            if ($deal->athlete_net_payout !== null) {
                return (float) $deal->athlete_net_payout;
            }
            // Fallback: calculate net payout if not set
            $escrowAmount = (float) ($deal->escrow_amount ?? 0);
            $athleteFeePercentage = (float) ($deal->athlete_fee_percentage ?? 0);
            $athleteFeeAmount = round($escrowAmount * ($athleteFeePercentage / 100), 2);
            return max(0, $escrowAmount - $athleteFeeAmount);
        });

        return view('athlete.dashboard', compact('athlete', 'acceptedDeals', 'completedDeals', 'pendingDeals', 'totalDeals', 'totalCompleted', 'totalEarnings', 'pendingCount'));
    }
}
