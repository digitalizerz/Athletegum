<x-superadmin-dashboard-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold">Revenue Dashboard</h2>
            <div class="flex gap-2">
                <x-date-filter :currentFilter="$stats['current_filter']" routeName="admin.revenue.index" />
                <a href="{{ route('admin.revenue.deals', ['filter' => $stats['current_filter']]) }}" class="btn btn-ghost btn-sm">View Deals</a>
                <a href="{{ route('admin.revenue.athletes', ['filter' => $stats['current_filter']]) }}" class="btn btn-ghost btn-sm">View Athletes</a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- KPI Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Total Platform Revenue -->
            <div class="card bg-base-100 shadow-sm border border-base-300">
                <div class="card-body">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-base-content/60">Platform Revenue</p>
                            <p class="text-2xl font-bold mt-1">${{ number_format($stats['total_platform_revenue'], 2) }}</p>
                            <p class="text-xs text-base-content/50 mt-1">From platform fees (10%)</p>
                        </div>
                        <div class="w-12 h-12 bg-primary/10 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Athlete Payouts -->
            <div class="card bg-base-100 shadow-sm border border-base-300">
                <div class="card-body">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-base-content/60">Athlete Payouts</p>
                            <p class="text-2xl font-bold mt-1">${{ number_format($stats['total_athlete_payouts'], 2) }}</p>
                            <p class="text-xs text-base-content/50 mt-1">Completed payouts</p>
                        </div>
                        <div class="w-12 h-12 bg-success/10 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pending Payouts -->
            <div class="card bg-base-100 shadow-sm border border-base-300">
                <div class="card-body">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-base-content/60">Pending Payouts</p>
                            <p class="text-2xl font-bold mt-1">${{ number_format($stats['pending_payouts'], 2) }}</p>
                            <p class="text-xs text-base-content/50 mt-1">Awaiting processing</p>
                        </div>
                        <div class="w-12 h-12 bg-warning/10 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Deal Volume -->
            <div class="card bg-base-100 shadow-sm border border-base-300">
                <div class="card-body">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-base-content/60">Total Deal Volume</p>
                            <p class="text-2xl font-bold mt-1">${{ number_format($stats['total_deal_volume'], 2) }}</p>
                            <p class="text-xs text-base-content/50 mt-1">Gross compensation</p>
                        </div>
                        <div class="w-12 h-12 bg-info/10 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-info" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Summary Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="card bg-base-100 shadow-sm border border-base-300">
                <div class="card-body">
                    <p class="text-sm font-medium text-base-content/60">Paid Deals</p>
                    <p class="text-2xl font-bold mt-1">{{ number_format($stats['paid_deals_count']) }}</p>
                    <p class="text-xs text-base-content/50 mt-1">Deals with successful payment</p>
                </div>
            </div>

            <div class="card bg-base-100 shadow-sm border border-base-300">
                <div class="card-body">
                    <p class="text-sm font-medium text-base-content/60">Released Deals</p>
                    <p class="text-2xl font-bold mt-1">{{ number_format($stats['released_deals_count']) }}</p>
                    <p class="text-xs text-base-content/50 mt-1">Deals with completed payouts</p>
                </div>
            </div>

            <div class="card bg-base-100 shadow-sm border border-base-300">
                <div class="card-body">
                    <p class="text-sm font-medium text-base-content/60">Revenue Rate</p>
                    <p class="text-2xl font-bold mt-1">
                        @if($stats['total_deal_volume'] > 0)
                            {{ number_format(($stats['total_platform_revenue'] / $stats['total_deal_volume']) * 100, 1) }}%
                        @else
                            0%
                        @endif
                    </p>
                    <p class="text-xs text-base-content/50 mt-1">Platform revenue / Deal volume</p>
                </div>
            </div>
        </div>

        <!-- Monthly Revenue Snapshot -->
        <div class="card bg-base-100 shadow-sm border border-base-300">
            <div class="card-body">
                <h3 class="text-lg font-semibold mb-4">Monthly Platform Revenue (Last 12 Months)</h3>
                <div class="space-y-3">
                    @foreach($stats['monthly_revenue'] as $month)
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium">{{ $month['month'] }}</span>
                            <div class="flex items-center gap-4 flex-1 max-w-md">
                                <div class="flex-1 bg-base-200 rounded-full h-4 overflow-hidden">
                                    @php
                                        $maxRevenue = max(array_column($stats['monthly_revenue'], 'revenue'));
                                        $width = $maxRevenue > 0 ? ($month['revenue'] / $maxRevenue) * 100 : 0;
                                    @endphp
                                    <div class="bg-primary h-full" style="width: {{ $width }}%"></div>
                                </div>
                                <span class="text-sm font-semibold min-w-[80px] text-right">${{ number_format($month['revenue'], 2) }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Top 5 Athletes Chart -->
        @if($stats['top_athletes']->count() > 0)
        <div class="card bg-base-100 shadow-sm border border-base-300">
            <div class="card-body">
                <h3 class="text-lg font-semibold mb-4">Top 5 Athletes by Total Payouts</h3>
                <div class="space-y-4">
                    @php
                        $maxPayout = $stats['top_athletes']->max('total_payouts');
                    @endphp
                    @foreach($stats['top_athletes'] as $athlete)
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-sm font-medium">{{ $athlete->name ?? 'N/A' }}</span>
                                <span class="text-sm font-semibold">${{ number_format($athlete->total_payouts, 2) }}</span>
                            </div>
                            @php
                                $barWidth = $maxPayout > 0 ? ($athlete->total_payouts / $maxPayout) * 100 : 0;
                            @endphp
                            <div class="w-full bg-base-200 rounded-full h-3 overflow-hidden">
                                <div class="bg-success h-full transition-all" style="width: {{ $barWidth }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <!-- Quick Links -->
        <div class="card bg-base-100 shadow-sm border border-base-300">
            <div class="card-body">
                <h3 class="text-lg font-semibold mb-4">Detailed Reports</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <a href="{{ route('admin.revenue.deals', ['filter' => $stats['current_filter']]) }}" class="btn btn-outline justify-start">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Deal-Level Breakdown
                    </a>
                    <a href="{{ route('admin.revenue.athletes', ['filter' => $stats['current_filter']]) }}" class="btn btn-outline justify-start">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Athlete-Level Breakdown
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-superadmin-dashboard-layout>

