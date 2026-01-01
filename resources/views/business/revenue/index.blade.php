<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <h2 class="text-xl font-semibold">Revenue Dashboard</h2>
            <div class="flex gap-2 items-center flex-wrap">
                <x-date-filter :currentFilter="$stats['current_filter']" routeName="business.revenue.index" />
                <a href="{{ route('business.revenue.deals', ['filter' => $stats['current_filter']]) }}" class="btn btn-ghost btn-sm">View Deals</a>
                <a href="{{ route('business.revenue.athletes', ['filter' => $stats['current_filter']]) }}" class="btn btn-ghost btn-sm">View Athletes</a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- KPI Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Total Spend -->
            <div class="card bg-base-100 shadow-sm border border-base-300">
                <div class="card-body">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-base-content/60">Total Spend</p>
                            <p class="text-2xl font-bold mt-1">${{ number_format($stats['total_spend'], 2) }}</p>
                            <p class="text-xs text-base-content/50 mt-1">On completed and paid deals</p>
                        </div>
                        <div class="w-12 h-12 bg-primary/10 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Completed Deals -->
            <div class="card bg-base-100 shadow-sm border border-base-300">
                <div class="card-body">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-base-content/60">Completed Deals</p>
                            <p class="text-2xl font-bold mt-1">{{ number_format($stats['completed_deals_count']) }}</p>
                            <p class="text-xs text-base-content/50 mt-1">Deals with completed payouts</p>
                        </div>
                        <div class="w-12 h-12 bg-success/10 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pending Deals -->
            <div class="card bg-base-100 shadow-sm border border-base-300">
                <div class="card-body">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-base-content/60">Pending Deals</p>
                            <p class="text-2xl font-bold mt-1">{{ number_format($stats['pending_deals_count']) }}</p>
                            <p class="text-xs text-base-content/50 mt-1">Paid but not yet released</p>
                        </div>
                        <div class="w-12 h-12 bg-warning/10 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Average Spend -->
            <div class="card bg-base-100 shadow-sm border border-base-300">
                <div class="card-body">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-base-content/60">Average Spend</p>
                            <p class="text-2xl font-bold mt-1">${{ number_format($stats['avg_spend'], 2) }}</p>
                            <p class="text-xs text-base-content/50 mt-1">Per deal</p>
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
        </div>

        <!-- Quick Links -->
        <div class="card bg-base-100 shadow-sm border border-base-300">
            <div class="card-body">
                <h3 class="text-lg font-semibold mb-4">Detailed Reports</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <a href="{{ route('business.revenue.deals', ['filter' => $stats['current_filter']]) }}" class="btn btn-outline justify-start">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Deal-Level Breakdown
                    </a>
                    <a href="{{ route('business.revenue.athletes', ['filter' => $stats['current_filter']]) }}" class="btn btn-outline justify-start">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Athlete-Level Breakdown
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

