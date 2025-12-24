<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl leading-tight">Wallet</h2>
            <a href="{{ route('wallet.add-funds') }}">
                <button class="btn btn-primary">Add Funds</button>
            </a>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto space-y-6">
        @if(session('success'))
            <div role="alert" class="alert alert-success">
                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div role="alert" class="alert alert-error">
                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- Wallet Balance Card -->
        <div class="card bg-primary text-primary-content shadow-lg">
            <div class="card-body">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium mb-2 uppercase tracking-wide opacity-80">Wallet Balance</p>
                        <p class="text-4xl sm:text-5xl font-bold mb-1">${{ number_format($walletBalance, 2) }}</p>
                    </div>
                    <svg class="w-20 h-20 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <div class="mt-6">
                    <a href="{{ route('wallet.add-funds') }}">
                        <button class="btn btn-sm bg-base-100 text-primary hover:bg-base-200">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Add Funds
                        </button>
                    </a>
                </div>
            </div>
        </div>

        <!-- Transaction History -->
        <div class="card bg-base-100 shadow-sm">
            <div class="card-body">
                <h3 class="text-lg font-semibold mb-4">Transaction History</h3>

                @if($transactions->isEmpty())
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-base-content/40 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <p class="text-base-content/60 text-base mb-6">No transactions yet.</p>
                        <a href="{{ route('wallet.add-funds') }}">
                            <button class="btn btn-primary">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Add Funds to Get Started
                            </button>
                        </a>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Amount</th>
                                    <th>Balance After</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($transactions as $transaction)
                                    <tr>
                                        <td class="text-sm">
                                            {{ \Carbon\Carbon::parse($transaction->created_at)->format('M j, Y g:i A') }}
                                        </td>
                                        <td class="text-sm capitalize">{{ $transaction->type }}</td>
                                        <td class="text-sm font-semibold {{ $transaction->amount >= 0 ? 'text-success' : 'text-error' }}">
                                            {{ $transaction->amount >= 0 ? '+' : '' }}${{ number_format(abs($transaction->amount), 2) }}
                                        </td>
                                        <td class="text-sm">${{ number_format($transaction->balance_after, 2) }}</td>
                                        <td>
                                            @php
                                                $statusBadges = [
                                                    'completed' => 'badge-success',
                                                    'pending' => 'badge-warning',
                                                    'failed' => 'badge-error',
                                                    'cancelled' => 'badge-ghost',
                                                ];
                                                $statusBadge = $statusBadges[$transaction->status] ?? 'badge-ghost';
                                            @endphp
                                            <span class="badge {{ $statusBadge }}">
                                                {{ ucfirst($transaction->status) }}
                                            </span>
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
