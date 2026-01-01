<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Deal;
use App\Models\Payout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
     * Apply date filter to payout query (based on payout released_at date)
     */
    private function applyPayoutDateFilter($query, $dateRange)
    {
        if ($dateRange['start']) {
            $query->where('released_at', '>=', $dateRange['start'])
                  ->where('released_at', '<=', $dateRange['end']);
        }
        return $query;
    }

    /**
     * Revenue Overview Dashboard
     */
    public function index(Request $request)
    {
        $filter = $request->get('filter', 'all');
        $dateRange = $this->getDateRange($filter);

        // Total platform revenue (from deals where payment was successful)
        $totalPlatformRevenueQuery = Deal::whereIn('payment_status', ['paid', 'paid_escrowed', 'released']);
        $totalPlatformRevenue = $this->applyDateFilter($totalPlatformRevenueQuery, $dateRange)->sum('platform_fee_amount');

        // Total athlete payouts (completed payouts)
        $totalAthletePayoutsQuery = Payout::where('status', 'completed');
        $totalAthletePayouts = $this->applyPayoutDateFilter($totalAthletePayoutsQuery, $dateRange)->sum('amount');

        // Pending payouts (payouts that are pending processing)
        $pendingPayouts = Payout::where('status', 'pending')->sum('amount'); // Always show all pending

        // Total deal volume (gross compensation amounts from paid deals)
        $totalDealVolumeQuery = Deal::whereIn('payment_status', ['paid', 'paid_escrowed', 'released']);
        $totalDealVolume = $this->applyDateFilter($totalDealVolumeQuery, $dateRange)->sum('compensation_amount');

        // Number of paid deals
        $paidDealsCountQuery = Deal::whereIn('payment_status', ['paid', 'paid_escrowed', 'released']);
        $paidDealsCount = $this->applyDateFilter($paidDealsCountQuery, $dateRange)->count();

        // Number of released deals (payouts completed)
        $releasedDealsCountQuery = Deal::whereNotNull('released_at')->where('payment_status', 'released');
        $releasedDealsCount = $this->applyDateFilter($releasedDealsCountQuery, $dateRange)->count();

        // Monthly platform revenue snapshot (last 12 months)
        $monthlyRevenue = [];
        for ($i = 11; $i >= 0; $i--) {
            $monthStart = Carbon::now()->subMonths($i)->startOfMonth();
            $monthEnd = $monthStart->copy()->endOfMonth();
            $monthRevenue = Deal::whereIn('payment_status', ['paid', 'paid_escrowed', 'released'])
                ->whereBetween('paid_at', [$monthStart, $monthEnd])
                ->sum('platform_fee_amount');
            $monthlyRevenue[] = [
                'month' => $monthStart->format('M Y'),
                'revenue' => $monthRevenue,
            ];
        }

        // Top 5 athletes by total payouts
        $topAthletes = \App\Models\Athlete::withCount(['deals' => function($query) {
                $query->whereIn('payment_status', ['paid', 'paid_escrowed', 'released']);
            }])
            ->having('deals_count', '>', 0)
            ->get();

        foreach ($topAthletes as $athlete) {
            $payoutQuery = Payout::where('athlete_id', $athlete->id)->where('status', 'completed');
            $athlete->total_payouts = $this->applyPayoutDateFilter($payoutQuery, $dateRange)->sum('amount');
        }

        $topAthletes = $topAthletes->sortByDesc('total_payouts')->take(5)->values();

        // Stats for summary
        $stats = [
            'total_platform_revenue' => $totalPlatformRevenue,
            'total_athlete_payouts' => $totalAthletePayouts,
            'pending_payouts' => $pendingPayouts,
            'total_deal_volume' => $totalDealVolume,
            'paid_deals_count' => $paidDealsCount,
            'released_deals_count' => $releasedDealsCount,
            'monthly_revenue' => $monthlyRevenue,
            'top_athletes' => $topAthletes,
            'current_filter' => $filter,
        ];

        return view('admin.revenue.index', compact('stats'));
    }

    /**
     * Deal-level Revenue Breakdown
     */
    public function deals(Request $request)
    {
        $filter = $request->get('filter', 'all');
        $dateRange = $this->getDateRange($filter);
        $export = $request->get('export') === 'csv';

        // Get all deals that have been paid (or released) with related data
        $dealsQuery = Deal::whereIn('payment_status', ['paid', 'paid_escrowed', 'released'])
            ->with(['user', 'athlete']);
        
        $dealsQuery = $this->applyDateFilter($dealsQuery, $dateRange);
        
        if ($export) {
            $deals = $dealsQuery->orderBy('paid_at', 'desc')->get();
        } else {
            $deals = $dealsQuery->orderBy('paid_at', 'desc')->paginate(50);
        }

        // Load payouts for each deal and calculate net payout
        foreach ($deals as $deal) {
            $payout = Payout::where('deal_id', $deal->id)
                ->where('status', 'completed')
                ->first();
            $deal->payout_amount = $payout ? $payout->amount : ($deal->athlete_net_payout ?? 0);
        }

        // Calculate totals for display
        $totals = [
            'gross_volume' => $deals->sum('compensation_amount'),
            'platform_fees' => $deals->sum('platform_fee_amount'),
            'net_payouts' => $deals->sum('payout_amount'),
        ];

        // Handle CSV export
        if ($export) {
            return $this->exportDealsCsv($deals);
        }

        return view('admin.revenue.deals', compact('deals', 'totals', 'filter'));
    }

    /**
     * Export deals to CSV
     */
    private function exportDealsCsv($deals)
    {
        $filename = 'deals-revenue-' . date('Y-m-d') . '.csv';
        
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
                'Business',
                'Athlete',
                'Gross Amount',
                'Platform Fee',
                'Net Payout',
                'Status',
                'Paid Date',
            ]);

            // Data rows
            foreach ($deals as $deal) {
                $dealType = \App\Models\Deal::getDealTypes()[$deal->deal_type] ?? null;
                $dealTypeName = $dealType['name'] ?? $deal->deal_type;
                $businessName = $deal->user ? ($deal->user->business_name ?? $deal->user->name ?? 'N/A') : 'N/A';
                $athleteName = $deal->athlete ? ($deal->athlete->name ?? $deal->athlete->email ?? 'N/A') : 'N/A';
                
                fputcsv($file, [
                    $deal->id,
                    $dealTypeName,
                    $businessName,
                    $athleteName,
                    number_format($deal->compensation_amount ?? 0, 2),
                    number_format($deal->platform_fee_amount ?? 0, 2),
                    number_format($deal->payout_amount ?? 0, 2),
                    ucfirst($deal->payment_status),
                    $deal->paid_at ? $deal->paid_at->format('Y-m-d H:i:s') : '',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Athlete-level Revenue Breakdown
     */
    public function athletes(Request $request)
    {
        $filter = $request->get('filter', 'all');
        $dateRange = $this->getDateRange($filter);
        $export = $request->get('export') === 'csv';

        // Get all athletes that have deals with paid status
        // Apply date filter to deals count
        $dealsCountQuery = function($query) use ($dateRange) {
            $query->whereIn('payment_status', ['paid', 'paid_escrowed', 'released']);
            if ($dateRange['start']) {
                $query->where('paid_at', '>=', $dateRange['start'])
                      ->where('paid_at', '<=', $dateRange['end']);
            }
        };

        $allAthletes = \App\Models\Athlete::withCount(['deals' => $dealsCountQuery])
            ->having('deals_count', '>', 0)
            ->get();

        // Calculate payout totals and platform fees for each athlete
        foreach ($allAthletes as $athlete) {
            $payoutQuery = Payout::where('athlete_id', $athlete->id)->where('status', 'completed');
            $athlete->payouts_sum_amount = $this->applyPayoutDateFilter($payoutQuery, $dateRange)->sum('amount');
            
            $platformFeeQuery = Deal::where('athlete_id', $athlete->id)
                ->whereIn('payment_status', ['paid', 'paid_escrowed', 'released']);
            $athlete->platform_fees_generated = $this->applyDateFilter($platformFeeQuery, $dateRange)->sum('platform_fee_amount');
        }

        // Sort by total payouts (descending)
        $allAthletes = $allAthletes->sortByDesc('payouts_sum_amount')->values();

        if ($export) {
            return $this->exportAthletesCsv($allAthletes);
        }

        // Manual pagination
        $currentPage = request()->get('page', 1);
        $perPage = 50;
        $total = $allAthletes->count();
        $items = $allAthletes->slice(($currentPage - 1) * $perPage, $perPage)->values();

        // Create paginator manually
        $athletes = new \Illuminate\Pagination\LengthAwarePaginator(
            $items,
            $total,
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        // Calculate totals (across all athletes, not just current page)
        $totals = [
            'total_athletes' => $total,
            'total_payouts' => $allAthletes->sum('payouts_sum_amount'),
            'total_platform_fees' => $allAthletes->sum('platform_fees_generated'),
        ];

        return view('admin.revenue.athletes', compact('athletes', 'totals', 'filter'));
    }

    /**
     * Export athletes to CSV
     */
    private function exportAthletesCsv($athletes)
    {
        $filename = 'athletes-revenue-' . date('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($athletes) {
            $file = fopen('php://output', 'w');
            
            // Header row
            fputcsv($file, [
                'Athlete Name',
                'Email',
                'Deals Count',
                'Total Payouts',
                'Avg Payout/Deal',
                'Platform Fees Generated',
            ]);

            // Data rows
            foreach ($athletes as $athlete) {
                $avgPayout = $athlete->deals_count > 0 
                    ? ($athlete->payouts_sum_amount / $athlete->deals_count) 
                    : 0;
                
                fputcsv($file, [
                    $athlete->name ?? 'N/A',
                    $athlete->email,
                    $athlete->deals_count,
                    number_format($athlete->payouts_sum_amount ?? 0, 2),
                    number_format($avgPayout, 2),
                    number_format($athlete->platform_fees_generated ?? 0, 2),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}

