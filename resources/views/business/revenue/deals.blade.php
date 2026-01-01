<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <h2 class="text-xl font-semibold">Spend by Deal</h2>
            <div class="flex gap-2 items-center flex-wrap">
                <x-date-filter :currentFilter="$filter ?? 'all'" routeName="business.revenue.deals" />
                <a href="{{ route('business.revenue.deals', array_merge(request()->except('page'), ['export' => 'csv', 'filter' => $filter ?? 'all'])) }}" 
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
                    <p class="text-sm font-medium text-base-content/60">Total Spend</p>
                    <p class="text-2xl font-bold mt-1">${{ number_format($totals['total_spend'], 2) }}</p>
                </div>
            </div>
            <div class="card bg-base-100 shadow-sm border border-base-300">
                <div class="card-body">
                    <p class="text-sm font-medium text-base-content/60">Platform Fees</p>
                    <p class="text-2xl font-bold mt-1">${{ number_format($totals['platform_fees'], 2) }}</p>
                </div>
            </div>
            <div class="card bg-base-100 shadow-sm border border-base-300">
                <div class="card-body">
                    <p class="text-sm font-medium text-base-content/60">Athlete Payouts</p>
                    <p class="text-2xl font-bold mt-1">${{ number_format($totals['athlete_payouts'], 2) }}</p>
                </div>
            </div>
        </div>

        <!-- Deals Table -->
        <div class="card bg-base-100 shadow-sm border border-base-300">
            <div class="card-body">
                <div class="overflow-x-auto">
                    <table class="table table-zebra">
                        <thead>
                            <tr>
                                <th>Deal ID</th>
                                <th>Type</th>
                                <th>Athlete</th>
                                <th>Spend Amount</th>
                                <th>Platform Fee</th>
                                <th>Athlete Payout</th>
                                <th>Status</th>
                                <th>Paid Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($deals as $deal)
                                @php
                                    $dealType = \App\Models\Deal::getDealTypes()[$deal->deal_type] ?? null;
                                    $dealTypeName = $dealType['name'] ?? $deal->deal_type;
                                    $athleteName = $deal->athlete ? ($deal->athlete->name ?? $deal->athlete->email ?? 'N/A') : 'N/A';
                                @endphp
                                <tr>
                                    <td>
                                        <a href="{{ route('deals.index') }}" class="link link-primary">
                                            #{{ $deal->id }}
                                        </a>
                                    </td>
                                    <td>{{ $dealTypeName }}</td>
                                    <td>{{ $athleteName }}</td>
                                    <td class="font-medium">${{ number_format($deal->compensation_amount ?? 0, 2) }}</td>
                                    <td class="text-base-content/70">${{ number_format($deal->platform_fee_amount ?? 0, 2) }}</td>
                                    <td class="text-base-content/70">${{ number_format($deal->athlete_payout ?? 0, 2) }}</td>
                                    <td>
                                        @if($deal->payment_status === 'released')
                                            <span class="badge badge-success">Released</span>
                                        @elseif($deal->payment_status === 'paid_escrowed')
                                            <span class="badge badge-warning">Escrowed</span>
                                        @else
                                            <span class="badge badge-info">{{ ucfirst($deal->payment_status) }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($deal->paid_at)
                                            <div>{{ $deal->paid_at->format('M d, Y') }}</div>
                                            <div class="text-xs text-base-content/60">{{ $deal->paid_at->format('g:i A') }}</div>
                                        @else
                                            <span class="text-base-content/60">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-8 text-base-content/60">
                                        No deals found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-6">
                    {{ $deals->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

