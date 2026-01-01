<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <h2 class="text-xl font-semibold">Spend by Athlete</h2>
            <div class="flex gap-2 items-center flex-wrap">
                <x-date-filter :currentFilter="$filter ?? 'all'" routeName="business.revenue.athletes" />
                <a href="{{ route('business.revenue.athletes', array_merge(request()->except('page'), ['export' => 'csv', 'filter' => $filter ?? 'all'])) }}" 
                   class="btn btn-primary btn-sm">
                    Export CSV
                </a>
                <a href="{{ route('business.revenue.index', ['filter' => $filter ?? 'all']) }}" class="btn btn-ghost btn-sm">← Back to Overview</a>
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
                    <p class="text-xs text-base-content/50 mt-1">Athletes you've worked with</p>
                </div>
            </div>
            <div class="card bg-base-100 shadow-sm border border-base-300">
                <div class="card-body">
                    <p class="text-sm font-medium text-base-content/60">Total Spend</p>
                    <p class="text-2xl font-bold mt-1">${{ number_format($totals['total_spend'], 2) }}</p>
                    <p class="text-xs text-base-content/50 mt-1">Across all athletes</p>
                </div>
            </div>
            <div class="card bg-base-100 shadow-sm border border-base-300">
                <div class="card-body">
                    <p class="text-sm font-medium text-base-content/60">Platform Fees</p>
                    <p class="text-2xl font-bold mt-1">${{ number_format($totals['total_platform_fees'], 2) }}</p>
                    <p class="text-xs text-base-content/50 mt-1">Total platform fees paid</p>
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
                                <th>Total Spend</th>
                                <th>Avg Spend/Deal</th>
                                <th>Platform Fees</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($athletes as $stat)
                                <tr>
                                    <td class="font-medium">
                                        {{ $stat->athlete->name ?? 'N/A' }}
                                    </td>
                                    <td class="text-base-content/70">{{ $stat->athlete->email ?? 'N/A' }}</td>
                                    <td>{{ number_format($stat->deals_count) }}</td>
                                    <td class="font-semibold">${{ number_format($stat->total_spend, 2) }}</td>
                                    <td class="text-base-content/70">${{ number_format($stat->avg_spend_per_deal, 2) }}</td>
                                    <td class="font-medium">${{ number_format($stat->platform_fees, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-8 text-base-content/60">
                                        No athletes found.
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
</x-app-layout>

