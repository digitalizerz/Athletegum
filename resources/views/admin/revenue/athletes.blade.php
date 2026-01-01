<x-superadmin-dashboard-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <h2 class="text-xl font-semibold">Revenue by Athlete</h2>
            <div class="flex gap-2 items-center flex-wrap">
                <x-date-filter :currentFilter="$filter ?? 'all'" routeName="admin.revenue.athletes" />
                <a href="{{ route('admin.revenue.athletes', array_merge(request()->except('page'), ['export' => 'csv', 'filter' => $filter ?? 'all'])) }}" 
                   class="btn btn-primary btn-sm">
                    Export CSV
                </a>
                <a href="{{ route('admin.revenue.index', ['filter' => $filter ?? 'all']) }}" class="btn btn-ghost btn-sm">← Back to Overview</a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- Summary Totals -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="card bg-base-100 shadow-sm border border-base-300">
                <div class="card-body">
                    <p class="text-sm font-medium text-base-content/60">Total Athletes</p>
                    <p class="text-2xl font-bold mt-1">{{ number_format($totals['total_athletes']) }}</p>
                    <p class="text-xs text-base-content/50 mt-1">With paid deals</p>
                </div>
            </div>
            <div class="card bg-base-100 shadow-sm border border-base-300">
                <div class="card-body">
                    <p class="text-sm font-medium text-base-content/60">Total Payouts</p>
                    <p class="text-2xl font-bold mt-1">${{ number_format($totals['total_payouts'], 2) }}</p>
                    <p class="text-xs text-base-content/50 mt-1">To all athletes</p>
                </div>
            </div>
            <div class="card bg-base-100 shadow-sm border border-base-300">
                <div class="card-body">
                    <p class="text-sm font-medium text-base-content/60">Platform Fees Generated</p>
                    <p class="text-2xl font-bold mt-1">${{ number_format($totals['total_platform_fees'], 2) }}</p>
                    <p class="text-xs text-base-content/50 mt-1">From athlete deals</p>
                </div>
            </div>
        </div>

        <!-- Athletes Table -->
        <div class="card bg-base-100 shadow-sm border border-base-300">
            <div class="card-body">
                <div class="overflow-x-auto">
                    <table class="table table-zebra">
                        <thead>
                            <tr>
                                <th>Athlete</th>
                                <th>Email</th>
                                <th>Deals</th>
                                <th>Total Payouts</th>
                                <th>Avg Payout/Deal</th>
                                <th>Platform Fees</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($athletes as $athlete)
                                @php
                                    $avgPayout = $athlete->deals_count > 0 
                                        ? (($athlete->payouts_sum_amount ?? 0) / $athlete->deals_count) 
                                        : 0;
                                @endphp
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.athletes.show', $athlete) }}" class="link link-primary font-medium">
                                            {{ $athlete->name ?? 'N/A' }}
                                        </a>
                                    </td>
                                    <td class="text-base-content/70">{{ $athlete->email }}</td>
                                    <td>{{ number_format($athlete->deals_count) }}</td>
                                    <td class="font-semibold">${{ number_format($athlete->payouts_sum_amount ?? 0, 2) }}</td>
                                    <td class="text-base-content/70">${{ number_format($avgPayout, 2) }}</td>
                                    <td class="font-medium">${{ number_format($athlete->platform_fees_generated ?? 0, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-8 text-base-content/60">
                                        No athletes with paid deals found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-6">
                    {{ $athletes->links() }}
                </div>
            </div>
        </div>
    </div>
</x-superadmin-dashboard-layout>

