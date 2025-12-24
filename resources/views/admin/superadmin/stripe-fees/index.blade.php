<x-superadmin-dashboard-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold">Stripe & Fees Configuration</h2>
    </x-slot>

    <div x-data="{
        showSMBFeeModal: false,
        showAthleteFeeModal: false,
        smbFeeType: '{{ $smbFee['type'] }}',
        smbFeeValue: {{ $smbFee['value'] }},
        athleteFeeValue: {{ $athleteFee }},
    }" class="space-y-6">
        @if(session('success'))
            <div class="alert alert-success">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-error">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- Stripe Connection -->
        <div class="card bg-base-100 shadow-sm border border-base-300">
            <div class="card-body">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="card-title">Stripe Connection</h3>
                    @if($stripeConnected && $stripeMode === 'test')
                        <span class="badge badge-warning badge-lg">TEST MODE</span>
                    @elseif($stripeConnected && $stripeMode === 'live')
                        <span class="badge badge-error badge-lg">LIVE MODE</span>
                    @endif
                </div>
                
                @if($stripeConnected)
                    @if($stripeMode === 'test')
                        <div class="alert alert-warning mb-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <div>
                                <h4 class="font-bold">Stripe Connected (TEST MODE)</h4>
                                <p class="text-sm">Using test keys. All transactions are test transactions and will appear in Stripe Dashboard → Test Mode → Logs.</p>
                                @if($maskedKey)
                                    <p class="text-sm mt-1">Publishable Key: <code class="bg-base-200 px-2 py-1 rounded">{{ $maskedKey }}</code></p>
                                @endif
                            </div>
                        </div>
                    @elseif($stripeMode === 'live')
                        @if(in_array(config('app.env'), ['local', 'development']))
                            <div class="alert alert-error mb-4">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                <div>
                                    <h4 class="font-bold">⚠️ LIVE MODE DETECTED IN DEVELOPMENT</h4>
                                    <p class="text-sm font-bold text-error">Live keys are detected in .env file. For local development, only TEST keys (pk_test_ / sk_test_) are allowed.</p>
                                    <p class="text-sm mt-2">Please update your .env file with test keys only.</p>
                                </div>
                            </div>
                        @else
                            <div class="alert alert-info mb-4">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <div>
                                    <h4 class="font-bold">Stripe Connected (LIVE MODE)</h4>
                                    <p class="text-sm">Using live keys. All transactions are real and will process actual payments.</p>
                                    @if($maskedKey)
                                        <p class="text-sm mt-1">Publishable Key: <code class="bg-base-200 px-2 py-1 rounded">{{ $maskedKey }}</code></p>
                                    @endif
                                </div>
                            </div>
                        @endif
                    @else
                        <div class="alert alert-error mb-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div>
                                <h4 class="font-bold">Invalid Stripe Keys</h4>
                                <p class="text-sm">The Stripe keys in your .env file are invalid. Please ensure you're using test keys (pk_test_ / sk_test_).</p>
                            </div>
                        </div>
                    @endif

                    @if($stripeAccountId)
                        <p class="text-sm text-base-content/60 mb-4">Account ID: <code class="bg-base-200 px-2 py-1 rounded">{{ $stripeAccountId }}</code></p>
                    @endif

                    <form method="POST" action="{{ route('admin.stripe-fees.verify-stripe') }}" class="inline">
                        @csrf
                        <button type="submit" class="btn btn-primary btn-sm">Verify Connection</button>
                    </form>
                @else
                    <div class="alert alert-warning mb-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <div>
                            <h4 class="font-bold">Stripe Not Configured</h4>
                            <p class="text-sm">Stripe keys are not configured in your .env file.</p>
                        </div>
                    </div>

                    <div class="bg-base-200 rounded-lg p-4 space-y-3">
                        <h4 class="font-semibold">Configuration Instructions</h4>
                        <p class="text-sm">To configure Stripe, add the following to your <code class="bg-base-300 px-2 py-1 rounded">.env</code> file:</p>
                        @if(in_array(config('app.env'), ['local', 'development']))
                            <pre class="bg-base-300 p-4 rounded text-sm overflow-x-auto"><code>STRIPE_KEY=pk_test_your_publishable_key_here
STRIPE_SECRET=sk_test_your_secret_key_here
STRIPE_ACCOUNT_ID=acct_your_account_id (optional)</code></pre>
                        @else
                            <pre class="bg-base-300 p-4 rounded text-sm overflow-x-auto"><code>STRIPE_KEY=pk_live_your_publishable_key_here
STRIPE_SECRET=sk_live_your_secret_key_here
STRIPE_ACCOUNT_ID=acct_your_account_id (optional)</code></pre>
                        @endif
                        @if(in_array(config('app.env'), ['local', 'development']))
                            <div class="alert alert-warning">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                <div>
                                    <p class="text-sm font-bold">⚠️ DEVELOPMENT MODE</p>
                                    <p class="text-sm">Only TEST keys (pk_test_ / sk_test_) are allowed in local development. Live keys (pk_live_ / sk_live_) are NOT permitted.</p>
                                </div>
                            </div>
                        @else
                            <div class="alert alert-info">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <div>
                                    <p class="text-sm font-bold">Production Environment</p>
                                    <p class="text-sm">Use your live Stripe keys (pk_live_ / sk_live_) for production. Ensure your keys are secure and never committed to version control.</p>
                                </div>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        <!-- SMB Platform Fee -->
        <div class="card bg-base-100 shadow-sm border border-base-300">
            <div class="card-body">
                <h3 class="card-title mb-4">SMB Platform Fee</h3>
                <p class="text-sm text-base-content/60 mb-4">
                    This fee is added on top of the athlete compensation when an SMB pays for a deal. 
                    It is clearly shown to SMBs before payment.
                </p>

                <form id="smb-fee-form" method="POST" action="{{ route('admin.stripe-fees.update-smb-fee') }}" class="space-y-4">
                    @csrf
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Fee Type</span>
                        </label>
                        <select x-model="smbFeeType" name="fee_type" class="select select-bordered w-full" required>
                            <option value="percentage" {{ $smbFee['type'] === 'percentage' ? 'selected' : '' }}>Percentage</option>
                            <option value="fixed" {{ $smbFee['type'] === 'fixed' ? 'selected' : '' }}>Fixed Amount</option>
                        </select>
                    </div>
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Fee Value</span>
                        </label>
                        <div class="input-group">
                            <input x-model="smbFeeValue" type="number" name="fee_value" 
                                   value="{{ $smbFee['value'] }}" 
                                   step="0.01" min="0"
                                   class="input input-bordered w-full" required>
                            <span class="badge badge-lg">
                                <span x-text="smbFeeType === 'percentage' ? '%' : '$'"></span>
                            </span>
                        </div>
                        <label class="label">
                            <span class="label-text-alt">
                                <span x-show="smbFeeType === 'percentage'">Percentage of the athlete compensation amount</span>
                                <span x-show="smbFeeType === 'fixed'">Fixed dollar amount added to each deal</span>
                            </span>
                        </label>
                    </div>
                    <button type="button" @click="showSMBFeeModal = true" class="btn btn-primary">Update SMB Fee</button>
                </form>
            </div>
        </div>

        <!-- Athlete Platform Fee -->
        <div class="card bg-base-100 shadow-sm border border-base-300">
            <div class="card-body">
                <h3 class="card-title mb-4">Athlete Platform Fee</h3>
                <p class="text-sm text-base-content/60 mb-4">
                    This percentage is automatically deducted from the athlete's payout when they are paid. 
                    Athletes see their net payout before accepting a deal. No fee is charged if no payment occurs.
                </p>

                <form id="athlete-fee-form" method="POST" action="{{ route('admin.stripe-fees.update-athlete-fee') }}" class="space-y-4">
                    @csrf
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Fee Percentage</span>
                        </label>
                        <div class="input-group">
                            <input x-model="athleteFeeValue" type="number" name="fee_percentage" 
                                   value="{{ $athleteFee }}" 
                                   step="0.1" min="0" max="100"
                                   class="input input-bordered w-full" required>
                            <span class="badge badge-lg">%</span>
                        </div>
                        <label class="label">
                            <span class="label-text-alt">Percentage deducted from athlete payout (0-100%)</span>
                        </label>
                    </div>
                    <button type="button" @click="showAthleteFeeModal = true" class="btn btn-primary">Update Athlete Fee</button>
                </form>
            </div>
        </div>

        <!-- Fee Summary & Reporting -->
        <div class="card bg-base-100 shadow-sm border border-base-300">
            <div class="card-body">
                <h3 class="card-title mb-4">Fee Summary & Reporting</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div class="stat bg-base-200 rounded-lg p-4">
                        <div class="stat-title">Total Platform Fees</div>
                        <div class="stat-value text-lg">${{ number_format($feeStats['total_platform_fees'], 2) }}</div>
                        <div class="stat-desc">All time collected</div>
                    </div>
                    
                    <div class="stat bg-base-200 rounded-lg p-4">
                        <div class="stat-title">Total SMB Fees</div>
                        <div class="stat-value text-lg">${{ number_format($feeStats['total_smb_fees'], 2) }}</div>
                        <div class="stat-desc">From SMB payments</div>
                    </div>
                    
                    <div class="stat bg-base-200 rounded-lg p-4">
                        <div class="stat-title">Total Athlete Fees</div>
                        <div class="stat-value text-lg">${{ number_format($feeStats['total_athlete_fees'], 2) }}</div>
                        <div class="stat-desc">From athlete payouts</div>
                    </div>
                    
                    <div class="stat bg-base-200 rounded-lg p-4">
                        <div class="stat-title">Athlete Payouts (Gross)</div>
                        <div class="stat-value text-lg">${{ number_format($feeStats['total_athlete_payouts_gross'], 2) }}</div>
                        <div class="stat-desc">Before fees</div>
                    </div>
                    
                    <div class="stat bg-base-200 rounded-lg p-4">
                        <div class="stat-title">Athlete Payouts (Net)</div>
                        <div class="stat-value text-lg">${{ number_format($feeStats['total_athlete_payouts_net'], 2) }}</div>
                        <div class="stat-desc">After fees</div>
                    </div>
                    
                    <div class="stat bg-base-200 rounded-lg p-4">
                        <div class="stat-title">Total SMB Charges</div>
                        <div class="stat-value text-lg">${{ number_format($feeStats['total_smb_charges'], 2) }}</div>
                        <div class="stat-desc">Including fees</div>
                    </div>
                </div>

                <div class="mt-6">
                    <a href="{{ route('admin.payments.index') }}" class="btn btn-outline">
                        View Detailed Payment Reports
                    </a>
                </div>
            </div>
        </div>

        <!-- Current Fee Rules Summary -->
        <div class="card bg-base-100 shadow-sm border border-base-300">
            <div class="card-body">
                <h3 class="card-title mb-4">Current Fee Rules</h3>
                
                <div class="space-y-3">
                    <div class="flex items-center justify-between p-3 bg-base-200 rounded-lg">
                        <div>
                            <p class="font-medium">SMB Platform Fee</p>
                            <p class="text-sm text-base-content/60">
                                @if($smbFee['type'] === 'percentage')
                                    {{ number_format($smbFee['value'], 2) }}% of compensation amount
                                @else
                                    ${{ number_format($smbFee['value'], 2) }} per deal
                                @endif
                            </p>
                        </div>
                        <span class="badge badge-primary">Active</span>
                    </div>
                    
                    <div class="flex items-center justify-between p-3 bg-base-200 rounded-lg">
                        <div>
                            <p class="font-medium">Athlete Platform Fee</p>
                            <p class="text-sm text-base-content/60">
                                {{ number_format($athleteFee, 2) }}% of payout amount
                            </p>
                        </div>
                        <span class="badge badge-primary">Active</span>
                    </div>
                </div>

                <div class="alert alert-info mt-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div>
                        <h4 class="font-bold">Important</h4>
                        <p class="text-sm">Fee changes apply to future deals only. Existing deals are not affected.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Update SMB Fee Modal -->
        <dialog x-show="showSMBFeeModal" 
                @click.away="showSMBFeeModal = false"
                class="modal"
                :class="{ 'modal-open': showSMBFeeModal }">
            <div class="modal-box">
                <h3 class="font-bold text-lg">Update SMB Platform Fee</h3>
                <p class="py-4">
                    Are you sure you want to update the SMB platform fee? This will apply to all future deals. Existing deals are not affected.
                </p>
                <div class="modal-action">
                    <button type="button" @click="document.getElementById('smb-fee-form').submit();" class="btn btn-primary">Update Fee</button>
                    <button @click="showSMBFeeModal = false" class="btn btn-ghost">Cancel</button>
                </div>
            </div>
            <form method="dialog" class="modal-backdrop">
                <button>close</button>
            </form>
        </dialog>

        <!-- Update Athlete Fee Modal -->
        <dialog x-show="showAthleteFeeModal" 
                @click.away="showAthleteFeeModal = false"
                class="modal"
                :class="{ 'modal-open': showAthleteFeeModal }">
            <div class="modal-box">
                <h3 class="font-bold text-lg">Update Athlete Platform Fee</h3>
                <p class="py-4">
                    Are you sure you want to update the athlete platform fee to <span x-text="athleteFeeValue"></span>%? This will apply to all future payouts. Existing deals are not affected.
                </p>
                <div class="modal-action">
                    <button type="button" @click="document.getElementById('athlete-fee-form').submit();" class="btn btn-primary">Update Fee</button>
                    <button @click="showAthleteFeeModal = false" class="btn btn-ghost">Cancel</button>
                </div>
            </div>
            <form method="dialog" class="modal-backdrop">
                <button>close</button>
            </form>
        </dialog>
    </div>
</x-superadmin-dashboard-layout>

