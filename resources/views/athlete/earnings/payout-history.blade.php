<x-athlete-dashboard-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl leading-tight">Payout History</h2>
            <a href="{{ route('athlete.earnings.index') }}" class="btn btn-ghost btn-sm">
                ← Back to Earnings
            </a>
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- Info Message -->
        <div class="alert alert-info">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span>Payouts typically arrive in 2–5 business days after release.</span>
        </div>

        <!-- Payouts List -->
        @if($payouts->count() > 0)
            <div class="card bg-base-100 shadow-sm border border-base-300">
                <div class="card-body">
                    <div class="overflow-x-auto">
                        <table class="table table-zebra">
                            <thead>
                                <tr>
                                    <th>Deal</th>
                                    <th>Gross Amount</th>
                                    <th>Platform Fee</th>
                                    <th>Net Payout</th>
                                    <th>Status</th>
                                    <th>Date Released</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($payouts as $payout)
                                    @php
                                        $deal = $payout->deal;
                                        $dealType = $deal ? \App\Models\Deal::getDealTypes()[$deal->deal_type] ?? null : null;
                                        $dealTypeName = $dealType['name'] ?? ($deal->deal_type ?? 'Unknown Deal');
                                        $businessName = $deal && $deal->user ? ($deal->user->business_name ?? $deal->user->name ?? 'Unknown Business') : 'Unknown Business';
                                        $grossAmount = $deal ? ($deal->compensation_amount ?? 0) : 0;
                                        $platformFee = 0.00; // Athletes pay $0 platform fee
                                        $netPayout = $payout->amount;
                                        $statusLabel = $payout->payout_status_label;
                                        $isCompleted = $statusLabel === 'Completed';
                                    @endphp
                                    <tr>
                                        <td>
                                            <div class="font-medium">{{ $dealTypeName }}</div>
                                            <div class="text-sm text-base-content/60">{{ $businessName }}</div>
                                            @if($deal && $deal->platforms && is_array($deal->platforms) && count($deal->platforms) > 0)
                                                <div class="text-xs text-base-content/50 mt-1">
                                                    @php
                                                        $platformNames = array_intersect_key(\App\Models\Deal::getPlatforms(), array_flip($deal->platforms));
                                                    @endphp
                                                    {{ implode(', ', $platformNames) }}
                                                </div>
                                            @endif
                                        </td>
                                        <td class="font-medium">${{ number_format($grossAmount, 2) }}</td>
                                        <td class="text-base-content/60">${{ number_format($platformFee, 2) }}</td>
                                        <td class="font-semibold">${{ number_format($netPayout, 2) }}</td>
                                        <td>
                                            @if($isCompleted)
                                                <span class="badge badge-success">Completed</span>
                                            @else
                                                <span class="badge badge-warning">Processing</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($payout->released_at)
                                                <div>{{ $payout->released_at->format('M d, Y') }}</div>
                                                <div class="text-xs text-base-content/60">{{ $payout->released_at->format('g:i A') }}</div>
                                            @else
                                                <span class="text-base-content/60">—</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-6">
                        {{ $payouts->links() }}
                    </div>
                </div>
            </div>
        @else
            <div class="card bg-base-100 shadow-sm border border-base-300">
                <div class="card-body text-center py-12">
                    <svg class="w-16 h-16 mx-auto text-base-content/30 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h3 class="text-lg font-semibold mb-2">No payouts yet</h3>
                    <p class="text-base-content/60">You'll see your payout history here once funds are released from deals.</p>
                </div>
            </div>
        @endif
    </div>
</x-athlete-dashboard-layout>

