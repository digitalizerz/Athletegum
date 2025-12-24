<x-superadmin-dashboard-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold">Payments & Financial Oversight</h2>
    </x-slot>

    <div class="space-y-6">
        <!-- Financial Summary -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="card bg-base-100 shadow-sm border border-base-300">
                <div class="card-body">
                    <p class="text-sm font-medium text-base-content/60">Platform Fees</p>
                    <p class="text-2xl font-bold mt-1">${{ number_format($summary['total_platform_fees'], 2) }}</p>
                </div>
            </div>
            <div class="card bg-base-100 shadow-sm border border-base-300">
                <div class="card-body">
                    <p class="text-sm font-medium text-base-content/60">Escrow Held</p>
                    <p class="text-2xl font-bold mt-1">${{ number_format($summary['total_escrow'], 2) }}</p>
                </div>
            </div>
            <div class="card bg-base-100 shadow-sm border border-base-300">
                <div class="card-body">
                    <p class="text-sm font-medium text-base-content/60">Total Payouts</p>
                    <p class="text-2xl font-bold mt-1">${{ number_format($summary['total_payouts'], 2) }}</p>
                </div>
            </div>
            <div class="card bg-base-100 shadow-sm border border-base-300">
                <div class="card-body">
                    <p class="text-sm font-medium text-base-content/60">Pending Withdrawals</p>
                    <p class="text-2xl font-bold mt-1">${{ number_format($summary['pending_withdrawals'], 2) }}</p>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="card bg-base-100 shadow-sm border border-base-300">
            <div class="card-body p-4">
                <form method="GET" action="{{ route('admin.payments.index') }}" class="flex flex-wrap gap-4">
                    <select name="type" class="select select-bordered">
                        <option value="">All Types</option>
                        <option value="deposit" {{ request('type') === 'deposit' ? 'selected' : '' }}>Deposit</option>
                        <option value="payment" {{ request('type') === 'payment' ? 'selected' : '' }}>Payment</option>
                        <option value="payment_release" {{ request('type') === 'payment_release' ? 'selected' : '' }}>Payment Release</option>
                    </select>
                    <select name="status" class="select select-bordered">
                        <option value="">All Statuses</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>Processing</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
                    </select>
                    <button type="submit" class="btn btn-primary">Filter</button>
                    @if(request()->hasAny(['type', 'status']))
                        <a href="{{ route('admin.payments.index') }}" class="btn btn-ghost">Clear</a>
                    @endif
                </form>
            </div>
        </div>

        <!-- Transactions Table -->
        <div class="card bg-base-100 shadow-sm border border-base-300 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Type</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Description</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $transaction)
                            <tr>
                                <td>
                                    <div class="text-sm">{{ $transaction->user->name ?? 'N/A' }}</div>
                                    <div class="text-xs text-base-content/60">{{ $transaction->user->email ?? '' }}</div>
                                </td>
                                <td class="text-sm">
                                    {{ ucfirst(str_replace('_', ' ', $transaction->type)) }}
                                </td>
                                <td class="text-sm font-medium {{ $transaction->amount < 0 ? 'text-error' : 'text-success' }}">
                                    {{ $transaction->amount < 0 ? '-' : '+' }}${{ number_format(abs($transaction->amount), 2) }}
                                </td>
                                <td>
                                    @php
                                        $statusBadges = [
                                            'pending' => 'badge-warning',
                                            'processing' => 'badge-info',
                                            'completed' => 'badge-success',
                                            'failed' => 'badge-error',
                                        ];
                                        $statusBadge = $statusBadges[$transaction->status] ?? 'badge-ghost';
                                    @endphp
                                    <span class="badge {{ $statusBadge }}">
                                        {{ ucfirst($transaction->status) }}
                                    </span>
                                </td>
                                <td class="text-sm text-base-content/60">
                                    {{ $transaction->description }}
                                </td>
                                <td class="text-sm text-base-content/60">
                                    {{ $transaction->created_at->format('M d, Y H:i') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-8">
                                    <p class="text-base-content/60">No transactions found.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($transactions->hasPages())
                <div class="card-body border-t border-base-300">
                    {{ $transactions->links() }}
                </div>
            @endif
        </div>
    </div>
</x-superadmin-dashboard-layout>
