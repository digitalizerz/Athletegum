<x-athlete-dashboard-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">Earnings & Withdrawals</h2>
    </x-slot>

    <div class="space-y-8">
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

        @if($errors->any())
            <div role="alert" class="alert alert-error">
                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    <h3 class="font-bold">Error</h3>
                    <div class="text-xs">
                        @foreach($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        @php
            $hasStripeConnected = $paymentMethods->isNotEmpty();
            $canWithdraw = $hasStripeConnected && $availableBalance >= 10;
        @endphp

        <!-- Earnings Section (Hero) -->
        <div class="card bg-base-100 shadow-sm border border-base-300">
            <div class="card-body">
                <h3 class="text-lg font-semibold mb-6">Your Earnings</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <p class="text-sm font-medium text-base-content/60">Total Earnings</p>
                        <p class="text-3xl font-bold mt-2">${{ number_format($totalEarnings, 2) }}</p>
                        <p class="text-xs text-base-content/50 mt-2">All-time earnings from completed deals</p>
                    </div>

                    <div>
                        <p class="text-sm font-medium text-base-content/60">Available Balance</p>
                        <p class="text-3xl font-bold mt-2">${{ number_format($availableBalance, 2) }}</p>
                        <p class="text-xs text-base-content/50 mt-2">Ready to withdraw once Stripe is connected and payments are released</p>
                    </div>

                    <div>
                        <p class="text-sm font-medium text-base-content/60">Pending Withdrawals</p>
                        <p class="text-3xl font-bold mt-2">${{ number_format($pendingWithdrawals, 2) }}</p>
                        <p class="text-xs text-base-content/50 mt-2">Transfers currently processing via Stripe</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Divider -->
        <div class="divider">
            <div class="flex items-center justify-between w-full">
                <span class="text-sm font-medium text-base-content/60">Payouts</span>
                <a href="{{ route('athlete.earnings.payout-history') }}" class="text-sm text-primary hover:underline">
                    View payout history →
                </a>
            </div>
        </div>

        <!-- Payout Status (Single Consolidated Section) -->
        <div class="card bg-base-100 shadow-sm border border-base-300 {{ !$hasStripeConnected ? 'opacity-75' : '' }}">
            <div class="card-body">
                <h3 class="text-lg font-semibold mb-4">Payout Status</h3>
                
                @if(!$hasStripeConnected)
                    <div class="bg-warning/10 border border-warning/30 rounded-lg p-4 mb-6">
                        <div class="flex items-center gap-2 mb-2">
                            <svg class="w-5 h-5 text-warning" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                            <span class="font-semibold text-warning">Not ready for payouts</span>
                        </div>
                        <p class="text-sm text-warning/80">
                            Connect Stripe to withdraw your earnings. You can earn money without setting it up.
                        </p>
                    </div>
                @else
                    <div class="bg-success/10 border border-success/30 rounded-lg p-4 mb-6">
                        <div class="flex items-center gap-2 mb-2">
                            <svg class="w-5 h-5 text-success" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            <span class="font-semibold text-success">Ready to withdraw</span>
                        </div>
                        <p class="text-sm text-success/80">
                            You can withdraw your earnings anytime.
                        </p>
                    </div>
                @endif

                <!-- Progress Steps -->
                <div class="space-y-3 mb-6">
                    <div class="flex items-center gap-3 text-sm">
                        <span class="text-success text-base">✓</span>
                        <span class="text-base-content/70 font-medium">Step 1: Earn money</span>
                    </div>
                    <div class="flex items-center gap-3 text-sm">
                        @if($hasStripeConnected)
                            <span class="text-success text-base">✓</span>
                            <span class="text-base-content/70 font-medium">Step 2: Connect Stripe</span>
                        @else
                            <svg class="w-4 h-4 text-base-content/40" fill="currentColor" viewBox="0 0 20 20">
                                <circle cx="10" cy="10" r="8" stroke="currentColor" stroke-width="2" fill="none"/>
                            </svg>
                            <span class="text-base-content/70 font-medium">Step 2: Connect Stripe</span>
                        @endif
                    </div>
                    <div class="flex items-center gap-3 text-sm">
                        @if($canWithdraw)
                            <svg class="w-4 h-4 text-base-content/40" fill="currentColor" viewBox="0 0 20 20">
                                <rect x="4" y="4" width="12" height="12" rx="2" stroke="currentColor" stroke-width="2" fill="none"/>
                            </svg>
                            <span class="text-base-content/70 font-medium">Step 3: Withdraw earnings</span>
                        @else
                            <svg class="w-4 h-4 text-base-content/40" fill="currentColor" viewBox="0 0 20 20">
                                <rect x="4" y="4" width="12" height="12" rx="2" stroke="currentColor" stroke-width="2" fill="none"/>
                            </svg>
                            <span class="text-base-content/50 font-medium">Step 3: Withdraw earnings</span>
                        @endif
                    </div>
                </div>

                <!-- Single Primary CTA -->
                <div class="space-y-3">
                    @if(!$hasStripeConnected)
                        <!-- Not connected: Set up payouts button -->
                        <a href="{{ route('athlete.earnings.stripe-connect.initiate', ['redirect_to' => 'athlete.earnings.index']) }}" 
                           class="btn btn-primary w-full btn-lg">
                            Set up payouts
                        </a>
                        <p class="text-xs text-base-content/50 text-center">
                            One-time setup · Handled securely by Stripe · Trusted by millions of businesses worldwide
                        </p>
                    @elseif($availableBalance < 10)
                        <!-- Connected but no balance: Disabled withdraw button -->
                        <button type="button" disabled class="btn btn-primary btn-disabled w-full btn-lg">
                            Withdraw earnings
                        </button>
                        <p class="text-xs text-base-content/60 text-center">No available balance yet</p>
                    @else
                        <!-- Connected and has balance: Active withdraw button -->
                        <a href="{{ route('athlete.earnings.withdraw') }}" 
                           class="btn btn-primary w-full btn-lg">
                            Withdraw earnings
                        </a>
                    @endif
                </div>

                <!-- Connected Payment Methods (if any) -->
                @if($hasStripeConnected && $paymentMethods->isNotEmpty())
                    <div class="mt-6 pt-6 border-t border-base-300">
                        <h4 class="text-sm font-medium mb-3">Connected Payment Method</h4>
                        <div class="space-y-3">
                            @foreach($paymentMethods as $method)
                                <div class="flex items-center justify-between p-3 bg-base-200 rounded-lg">
                                    <div class="flex items-center space-x-3 flex-1">
                                        <div class="w-10 h-6 bg-primary rounded flex items-center justify-center text-primary-content font-bold text-xs">
                                            Stripe
                                        </div>
                                        <div class="flex-1">
                                            <div class="text-sm font-medium">
                                                {{ $method->provider_account_id ? substr($method->provider_account_id, 0, 20) . '...' : 'Connected Account' }}
                                            </div>
                                            <div class="text-xs text-base-content/60">Stripe Connect</div>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        @if($method->is_default)
                                            <span class="badge badge-primary">Default</span>
                                        @else
                                            <form method="POST" action="{{ route('athlete.earnings.payment-method.default', $method) }}" class="inline">
                                                @csrf
                                                <button type="submit" class="btn btn-ghost btn-xs" title="Set as default">
                                                    Set Default
                                                </button>
                                            </form>
                                        @endif
                                        <button 
                                            type="button"
                                            onclick="document.getElementById('delete-modal-{{ $method->id }}').showModal()"
                                            class="btn btn-ghost btn-xs text-error" 
                                            title="Delete">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                                
                                <!-- Delete Modal for this payment method -->
                                <dialog id="delete-modal-{{ $method->id }}" class="modal">
                                    <div class="modal-box">
                                        <h3 class="font-bold text-lg text-error">Delete Stripe Account</h3>
                                        <p class="py-4">
                                            Are you sure you want to delete this Stripe account?
                                            <br><br>
                                            <strong>Account:</strong> {{ $method->provider_account_id ? substr($method->provider_account_id, 0, 20) . '...' : 'Connected Account' }}
                                            <br><br>
                                            <span class="text-error font-semibold">This action cannot be undone.</span> You will need to reconnect your Stripe account to receive future payouts.
                                        </p>
                                        <div class="modal-action">
                                            <form method="POST" action="{{ route('athlete.earnings.payment-method.destroy', $method->id) }}" id="delete-form-{{ $method->id }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-error" id="delete-submit-btn-{{ $method->id }}">Delete Account</button>
                                            </form>
                                            <button type="button" class="btn btn-ghost" onclick="document.getElementById('delete-modal-{{ $method->id }}').close()">Cancel</button>
                                        </div>
                                        <script>
                                            (function() {
                                                const formId = 'delete-form-{{ $method->id }}';
                                                const submitBtnId = 'delete-submit-btn-{{ $method->id }}';
                                                
                                                function initForm() {
                                                    const form = document.getElementById(formId);
                                                    const submitBtn = document.getElementById(submitBtnId);
                                                    
                                                    if (!form || !submitBtn) {
                                                        console.error('Form elements not found', { formId, submitBtnId });
                                                        return;
                                                    }
                                                    
                                                    form.addEventListener('submit', function(e) {
                                                        submitBtn.disabled = true;
                                                        submitBtn.textContent = 'Deleting...';
                                                    });
                                                }
                                                
                                                if (document.readyState === 'loading') {
                                                    document.addEventListener('DOMContentLoaded', initForm);
                                                } else {
                                                    initForm();
                                                }
                                            })();
                                        </script>
                                    </div>
                                    <form method="dialog" class="modal-backdrop">
                                        <button>close</button>
                                    </form>
                                </dialog>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-athlete-dashboard-layout>
