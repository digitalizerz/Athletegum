<x-athlete-dashboard-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">Earnings & Withdrawals</h2>
    </x-slot>

    <div class="space-y-6">
        <!-- Success/Error Messages -->
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

        <!-- Earnings Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="card bg-base-100 shadow-sm border border-base-300">
                <div class="card-body">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-base-content/60">Total Earnings</p>
                            <p class="text-2xl font-bold mt-1">${{ number_format($totalEarnings, 2) }}</p>
                        </div>
                        <div class="w-12 h-12 bg-success/10 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card bg-base-100 shadow-sm border border-base-300">
                <div class="card-body">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-base-content/60">Available Balance</p>
                            <p class="text-2xl font-bold mt-1">${{ number_format($availableBalance, 2) }}</p>
                        </div>
                        <div class="w-12 h-12 bg-info/10 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-info" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card bg-base-100 shadow-sm border border-base-300">
                <div class="card-body">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-base-content/60">Pending Withdrawals</p>
                            <p class="text-2xl font-bold mt-1">${{ number_format($pendingWithdrawals, 2) }}</p>
                        </div>
                        <div class="w-12 h-12 bg-warning/10 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-wrap gap-4">
            <a href="{{ route('athlete.earnings.withdraw') }}" 
               class="btn btn-primary {{ $availableBalance < 10 ? 'btn-disabled' : '' }}"
               @if($availableBalance < 10) onclick="return false;" title="Minimum withdrawal is $10" @endif>
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                </svg>
                Withdraw Funds
            </a>
            <a href="{{ route('athlete.earnings.payment-method.create') }}" class="btn btn-outline">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Add Payment Method
            </a>
        </div>

        <!-- Payment Methods Section -->
        <div class="card bg-base-100 shadow-sm border border-base-300">
            <div class="card-body">
                <h3 class="text-lg font-semibold mb-4">Payment Methods</h3>
                @if($paymentMethods->isEmpty())
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-base-content/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                        <p class="mt-4 text-sm text-base-content/60">No payment methods added yet.</p>
                        <a href="{{ route('athlete.earnings.payment-method.create') }}" class="mt-4 btn btn-primary btn-sm">
                            Add Payment Method
                        </a>
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach($paymentMethods as $method)
                            <div class="flex items-center justify-between p-3 bg-base-200 rounded-lg">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-6 bg-primary rounded flex items-center justify-center text-primary-content font-bold text-xs">
                                        Stripe
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium">{{ $method->stripe_account_id ?? 'Connected Account' }}</div>
                                        <div class="text-xs text-base-content/60">Stripe Connect</div>
                                    </div>
                                </div>
                                @if($method->is_default)
                                    <span class="badge badge-primary">Default</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-athlete-dashboard-layout>
