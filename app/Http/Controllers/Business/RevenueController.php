<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Models\Deal;
use App\Models\Payout;
use App\Support\PlanFeatures;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class RevenueController extends Controller
{
    /**
     * Get date range based on filter parameter
     */
    private function getDateRange($filter = 'all')
    {
        $now = Carbon::now()->endOfDay();
        
        return match($filter) {
            '7d' => ['start' => $now->copy()->subDays(7)->startOfDay(), 'end' => $now],
            '30d' => ['start' => $now->copy()->subDays(30)->startOfDay(), 'end' => $now],
            '90d' => ['start' => $now->copy()->subDays(90)->startOfDay(), 'end' => $now],
            default => ['start' => null, 'end' => null], // All time
        };
    }

    /**
     * Apply date filter to query (based on deal paid_at date)
     */
    private function applyDateFilter($query, $dateRange)
    {
        if ($dateRange['start']) {
            $query->where('paid_at', '>=', $dateRange['start'])
                  ->where('paid_at', '<=', $dateRange['end']);
        }
        return $query;
    }

    /**
     * Revenue Overview Dashboard
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Gate revenue dashboard access
        if (!PlanFeatures::canUseFeature($user, 'revenue_dashboard')) {
            return redirect()->route('business.billing.index')
                ->with('error', 'Revenue dashboard is only available on Pro and Growth plans. Upgrade to unlock this feature.');
        }
        
        $filter = $request->get('filter', 'all');
        $dateRange = $this->getDateRange($filter);

        // Total spend (from deals where payment was successful)
        $totalSpendQuery = Deal::where('user_id', $user->id)
            ->whereIn('payment_status', ['paid', 'paid_escrowed', 'released']);
        $totalSpend = $this->applyDateFilter($totalSpendQuery, $dateRange)->sum('compensation_amount');

        // Completed deals count (deals with released payouts)
        $completedDealsQuery = Deal::where('user_id', $user->id)
            ->whereNotNull('released_at')
            ->where('payment_status', 'released');
        $completedDealsCount = $this->applyDateFilter($completedDealsQuery, $dateRange)->count();

        // Pending deals count (deals that are paid but not yet released)
        $pendingDealsQuery = Deal::where('user_id', $user->id)
            ->whereIn('payment_status', ['paid', 'paid_escrowed'])
            ->whereNull('released_at');
        $pendingDealsCount = $pendingDealsQuery->count(); // Always show all pending

        // Average spend per deal
        $dealsCount = Deal::where('user_id', $user->id)
            ->whereIn('payment_status', ['paid', 'paid_escrowed', 'released']);
        $paidDealsCount = $this->applyDateFilter($dealsCount, $dateRange)->count();
        $avgSpend = $paidDealsCount > 0 ? ($totalSpend / $paidDealsCount) : 0;

        // Stats for summary
        $stats = [
            'total_spend' => $totalSpend,
            'completed_deals_count' => $completedDealsCount,
            'pending_deals_count' => $pendingDealsCount,
            'avg_spend' => $avgSpend,
            'paid_deals_count' => $paidDealsCount,
            'current_filter' => $filter,
        ];

        return view('business.revenue.index', compact('stats'));
    }

    /**
     * Deal-level Spend Breakdown
     */
    public function deals(Request $request)
    {
        $user = Auth::user();
        
        // Gate revenue dashboard access
        if (!PlanFeatures::canUseFeature($user, 'revenue_dashboard')) {
            return redirect()->route('business.billing.index')
                ->with('error', 'Revenue dashboard is only available on Pro and Growth plans. Upgrade to unlock this feature.');
        }
        
        $filter = $request->get('filter', 'all');
        $dateRange = $this->getDateRange($filter);
        $export = $request->get('export') === 'csv';

        // Get all deals for this business that have been paid (or released)
        $dealsQuery = Deal::where('user_id', $user->id)
            ->whereIn('payment_status', ['paid', 'paid_escrowed', 'released'])
            ->with(['athlete']);
        
        $dealsQuery = $this->applyDateFilter($dealsQuery, $dateRange);
        
        if ($export) {
            $deals = $dealsQuery->orderBy('paid_at', 'desc')->get();
        } else {
            $deals = $dealsQuery->orderBy('paid_at', 'desc')->paginate(50);
        }

        // Load payouts for each deal to show athlete payout amount
        foreach ($deals as $deal) {
            $payout = Payout::where('deal_id', $deal->id)
                ->where('status', 'completed')
                ->first();
            $deal->athlete_payout = $payout ? $payout->amount : ($deal->athlete_net_payout ?? 0);
        }

        // Calculate totals for display
        $totals = [
            'total_spend' => $deals->sum('compensation_amount'),
            'platform_fees' => $deals->sum('platform_fee_amount'),
            'athlete_payouts' => $deals->sum('athlete_payout'),
        ];

        // Handle CSV export
        if ($export) {
            return $this->exportDealsCsv($deals);
        }

        return view('business.revenue.deals', compact('deals', 'totals', 'filter'));
    }

    /**
     * Export deals to CSV
     */
    private function exportDealsCsv($deals)
    {
        $filename = 'business-deals-spend-' . date('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($deals) {
            $file = fopen('php://output', 'w');
            
            // Header row
            fputcsv($file, [
                'Deal ID',
                'Type',
                'Athlete',
                'Spend Amount',
                'Platform Fee',
                'Athlete Payout',
                'Status',
                'Paid Date',
            ]);

            // Data rows
            foreach ($deals as $deal) {
                $dealType = \App\Models\Deal::getDealTypes()[$deal->deal_type] ?? null;
                $dealTypeName = $dealType['name'] ?? $deal->deal_type;
                $athleteName = $deal->athlete ? ($deal->athlete->name ?? $deal->athlete->email ?? 'N/A') : 'N/A';
                
                fputcsv($file, [
                    $deal->id,
                    $dealTypeName,
                    $athleteName,
                    number_format($deal->compensation_amount ?? 0, 2),
                    number_format($deal->platform_fee_amount ?? 0, 2),
                    number_format($deal->athlete_payout ?? 0, 2),
                    ucfirst($deal->payment_status),
                    $deal->paid_at ? $deal->paid_at->format('Y-m-d H:i:s') : '',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Athlete-level Spend Breakdown
     */
    public function athletes(Request $request)
    {
        $user = Auth::user();
        
        // Gate revenue dashboard access
        if (!PlanFeatures::canUseFeature($user, 'revenue_dashboard')) {
            return redirect()->route('business.billing.index')
                ->with('error', 'Revenue dashboard is only available on Pro and Growth plans. Upgrade to unlock this feature.');
        }
        
        $filter = $request->get('filter', 'all');
        $dateRange = $this->getDateRange($filter);
        $export = $request->get('export') === 'csv';

        // Get all athletes this business has worked with
        $dealsQuery = Deal::where('user_id', $user->id)
            ->whereIn('payment_status', ['paid', 'paid_escrowed', 'released']);
        
        $dealsQuery = $this->applyDateFilter($dealsQuery, $dateRange);
        
        $deals = $dealsQuery->with('athlete')->get();

        // Group by athlete and calculate totals (filter out deals without athletes)
        $athleteStats = $deals->filter(function($deal) {
            return $deal->athlete_id !== null && $deal->athlete !== null;
        })->groupBy('athlete_id')->map(function($athleteDeals) {
            $athlete = $athleteDeals->first()->athlete;
            return (object) [
                'athlete' => $athlete,
                'deals_count' => $athleteDeals->count(),
                'total_spend' => $athleteDeals->sum('compensation_amount'),
                'platform_fees' => $athleteDeals->sum('platform_fee_amount'),
                'avg_spend_per_deal' => $athleteDeals->sum('compensation_amount') / $athleteDeals->count(),
            ];
        })->values()->sortByDesc('total_spend')->values();

        // Calculate totals
        $totals = [
            'total_athletes' => $athleteStats->count(),
            'total_spend' => $athleteStats->sum('total_spend'),
            'total_platform_fees' => $athleteStats->sum('platform_fees'),
        ];

        if ($export) {
            return $this->exportAthletesCsv($athleteStats);
        }

        // Paginate manually
        $currentPage = request()->get('page', 1);
        $perPage = 50;
        $total = $athleteStats->count();
        $items = $athleteStats->slice(($currentPage - 1) * $perPage, $perPage)->values();

        $athletes = new \Illuminate\Pagination\LengthAwarePaginator(
            $items,
            $total,
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('business.revenue.athletes', compact('athletes', 'totals', 'filter'));
    }

    /**
     * Export athletes to CSV
     */
    private function exportAthletesCsv($athleteStats)
    {
        $filename = 'business-athletes-spend-' . date('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($athleteStats) {
            $file = fopen('php://output', 'w');
            
            // Header row
            fputcsv($file, [
                'Athlete Name',
                'Email',
                'Deals Count',
                'Total Spend',
                'Avg Spend/Deal',
                'Platform Fees',
            ]);

            // Data rows
            foreach ($athleteStats as $stat) {
                fputcsv($file, [
                    $stat->athlete->name ?? 'N/A',
                    $stat->athlete->email ?? 'N/A',
                    $stat->deals_count,
                    number_format($stat->total_spend, 2),
                    number_format($stat->avg_spend_per_deal, 2),
                    number_format($stat->platform_fees, 2),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}

