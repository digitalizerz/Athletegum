<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl leading-tight">Account balance</h2>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto space-y-6">
        @if(session('success'))
            <div role="alert" class="alert alert-success">
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div role="alert" class="alert alert-error">
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <div class="alert alert-info text-sm">
            Deal payments are charged to your saved card (Stripe). This balance only reflects credits such as refunds or adjustments — you can no longer add funds here.
        </div>

        <div class="card bg-primary text-primary-content shadow-lg">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium mb-2 uppercase tracking-wide opacity-80">Balance</p>
                        <p class="text-4xl sm:text-5xl font-bold mb-1">${{ number_format($walletBalance, 2) }}</p>
                    </div>
                    <svg class="w-20 h-20 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="card bg-base-100 shadow-sm">
            <div class="card-body">
                <h3 class="text-lg font-semibold mb-4">Transaction history</h3>

                @if($transactions->isEmpty())
                    <div class="text-center py-12 text-base-content/60">
                        <p>No transactions yet.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Description</th>
                                    <th class="text-right">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($transactions as $tx)
                                    <tr>
                                        <td class="whitespace-nowrap">{{ $tx->created_at->format('M j, Y g:i A') }}</td>
                                        <td>{{ $tx->description ?? $tx->type }}</td>
                                        <td class="text-right font-medium {{ $tx->amount >= 0 ? 'text-success' : 'text-error' }}">
                                            {{ $tx->amount >= 0 ? '+' : '' }}${{ number_format(abs($tx->amount), 2) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $transactions->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
