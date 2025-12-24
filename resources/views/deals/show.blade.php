<x-full-width>
    <div class="min-h-screen bg-base-200 pb-24">
        {{-- Offer Summary Section (Above the fold) - Compact and Mobile-First --}}
        <div class="bg-base-100 border-b border-base-300">
            <div class="w-full px-4 py-6">
                {{-- Identity Guardrail Message --}}
                @if(isset($invitation) && $invitation && $invitation->athlete_email)
                    <div class="max-w-2xl mx-auto mb-4">
                        <div class="bg-primary/10 border border-primary/20 rounded-lg p-3 text-sm">
                            <div class="flex items-start gap-2">
                                <svg class="w-5 h-5 text-primary mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                                <div class="flex-1">
                                    <p class="font-medium text-primary mb-1">This deal invitation was sent specifically to you.</p>
                                    <p class="text-base-content/70 text-xs">Only the intended recipient can accept this deal.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Identity Mismatch Warning --}}
                @if(isset($identityMismatch) && $identityMismatch)
                    <div class="max-w-2xl mx-auto mb-4">
                        <div class="bg-error/10 border border-error/20 rounded-lg p-3 text-sm">
                            <div class="flex items-start gap-2">
                                <svg class="w-5 h-5 text-error mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                <div class="flex-1">
                                    <p class="font-medium text-error mb-1">This invitation is not for your account</p>
                                    <p class="text-base-content/70 text-xs">This deal invitation was sent to a different athlete. Please contact the business if you believe this is an error.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Compact Summary Block --}}
                <div class="max-w-2xl mx-auto">
                    {{-- Business Identity Section --}}
                    @if($deal->user)
                        <div class="mb-4 pb-4 border-b border-base-300">
                            <div class="flex items-start gap-3">
                                <div class="flex-1">
                                    <div class="text-xs text-base-content/60 mb-1">Business</div>
                                    @if($deal->user->business_name)
                                        <div class="font-semibold text-base">{{ $deal->user->business_name }}</div>
                                        @if($deal->user->business_information)
                                            <div class="text-xs text-base-content/60 mt-1 line-clamp-2">{{ Str::limit($deal->user->business_information, 100) }}</div>
                                        @endif
                                    @else
                                        <div class="font-semibold text-base">{{ $deal->user->name ?? 'Business' }}</div>
                                    @endif
                                    @if($deal->deal_type === 'in_person_appearance' && ($deal->user->city || $deal->user->state))
                                        <div class="text-xs text-base-content/60 mt-1">
                                            📍 {{ $deal->user->city }}{{ $deal->user->city && $deal->user->state ? ', ' : '' }}{{ $deal->user->state }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Deal Type & Platform --}}
                    <div class="flex items-center gap-2 mb-4">
                        <span class="text-2xl">{{ \App\Models\Deal::getDealTypes()[$deal->deal_type]['icon'] ?? '📋' }}</span>
                        <div class="flex-1">
                            <h1 class="text-lg font-semibold">{{ \App\Models\Deal::getDealTypes()[$deal->deal_type]['name'] ?? $deal->deal_type }}</h1>
                            @if(!empty($deal->platforms) && is_array($deal->platforms))
                                <div class="flex flex-wrap gap-1.5 mt-1">
                                    @foreach($deal->platforms as $platform)
                                        <span class="text-xs badge badge-outline badge-sm">{{ \App\Models\Deal::getPlatforms()[$platform] ?? $platform }}</span>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                        @if($deal->status === 'pending')
                            <span class="badge badge-warning badge-sm">Pending</span>
                        @endif
                    </div>

                    {{-- Compensation (Visually Dominant) --}}
                    <div class="mb-3">
                        <div class="text-4xl sm:text-5xl font-bold text-primary mb-1">
                            ${{ number_format($deal->compensation_amount, 2) }}
                        </div>
                        <p class="text-xs text-base-content/60">
                            Held in escrow until completion and approval
                        </p>
                    </div>

                    {{-- Escrow Trust Message --}}
                    <div class="mb-3">
                        <div class="bg-primary/10 border border-primary/20 rounded-lg p-3 text-xs">
                            <div class="flex items-start gap-2">
                                <svg class="w-4 h-4 text-primary mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                                <div class="flex-1">
                                    <p class="font-semibold text-primary mb-0.5">Funded & Protected via AthleteGum Escrow</p>
                                    <p class="text-base-content/70">This deal is fully funded and managed through AthleteGum. Your payment will be released automatically upon approval of completed work.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Deadline --}}
                    <div class="flex items-center gap-2 text-sm text-base-content/70 pt-3 border-t border-base-300">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span>
                            Due {{ \Carbon\Carbon::parse($deal->deadline)->format('M j, Y') }}
                            @if($deal->deadline_time)
                                @php
                                    try {
                                        $time = \Carbon\Carbon::createFromFormat('H:i:s', $deal->deadline_time)->format('g:i A');
                                    } catch (\Exception $e) {
                                        $time = \Carbon\Carbon::createFromFormat('H:i', $deal->deadline_time)->format('g:i A');
                                    }
                                @endphp
                                at {{ $time }}
                            @endif
                            @if($deal->frequency && $deal->frequency !== 'one-time')
                                • {{ \App\Models\Deal::getFrequencyOptions()[$deal->frequency] ?? $deal->frequency }}
                            @endif
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Main Content - No Nested Cards --}}
        <div class="w-full px-4 py-6 space-y-6">
            <div class="max-w-2xl mx-auto space-y-6">
                {{-- Instructions Section - Clear and Scannable --}}
                @if($deal->notes)
                    <div>
                        <h2 class="text-base font-semibold mb-3">What You'll Do</h2>
                        <div class="text-sm leading-relaxed text-base-content/80 whitespace-pre-wrap">{{ $deal->notes }}</div>
                    </div>
                @endif

                {{-- Attachments --}}
                @if(!empty($deal->attachments) && is_array($deal->attachments))
                    <div>
                        <h2 class="text-base font-semibold mb-3">Resources</h2>
                        <div class="space-y-2">
                            @foreach($deal->attachments as $attachment)
                                <a href="{{ Storage::url($attachment['path'] ?? '') }}" target="_blank" class="flex items-center justify-between p-3 bg-base-100 rounded-lg border border-base-300 hover:border-primary transition text-sm">
                                    <span class="font-medium flex-1 truncate">{{ $attachment['original_name'] ?? basename($attachment['path'] ?? '') }}</span>
                                    <svg class="w-4 h-4 ml-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                    </svg>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Agreement Section - Progressive Disclosure --}}
                @if($deal->contract_text)
                    <div>
                        <h2 class="text-base font-semibold mb-3">Agreement</h2>
                        <div id="contract-summary">
                            <ul class="list-disc list-inside space-y-1.5 text-sm text-base-content/70 mb-4">
                                <li>Complete the work as specified in the deal terms</li>
                                <li>Submit proof of completion</li>
                                <li>Follow all guidelines and instructions provided</li>
                                <li>Maintain professional standards</li>
                            </ul>
                            
                            {{-- Agreement Checkbox (Immediately After Summary) --}}
                            @if($deal->status === 'pending')
                                <label class="flex items-start gap-3 cursor-pointer p-3 bg-base-100 rounded-lg border border-base-300">
                                    <input 
                                        type="checkbox" 
                                        id="agree-checkbox"
                                        class="checkbox checkbox-primary mt-0.5"
                                        required
                                    >
                                    <span class="text-sm text-base-content/80 flex-1">
                                        I have read and agree to the terms of this agreement
                                    </span>
                                </label>
                            @endif

                            {{-- View Full Agreement Button --}}
                            <button 
                                type="button" 
                                onclick="toggleContract()" 
                                class="mt-3 text-sm text-primary hover:text-primary/80 font-medium flex items-center gap-1"
                                id="view-contract-btn"
                            >
                                View full agreement
                                <svg class="w-4 h-4 transition-transform" id="contract-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                        </div>
                        
                        {{-- Full Agreement (Collapsed by Default) --}}
                        <div id="contract-full" class="hidden mt-4">
                            <div class="bg-base-100 rounded-lg border border-base-300 p-4 max-h-64 overflow-y-auto">
                                <pre class="whitespace-pre-wrap text-xs font-sans leading-relaxed text-base-content/70">{{ $deal->contract_text }}</pre>
                            </div>
                            <button 
                                type="button" 
                                onclick="toggleContract()" 
                                class="mt-2 text-sm text-primary hover:text-primary/80 font-medium flex items-center gap-1"
                            >
                                Hide full agreement
                                <svg class="w-4 h-4 transition-transform rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                        </div>
                    </div>
                @endif

                {{-- Earnings Breakdown - Clear Fee Display --}}
                @php
                    $athleteFeePercentage = \App\Models\PlatformSetting::getAthletePlatformFeePercentage();
                    $athleteFeeAmount = round($deal->compensation_amount * ($athleteFeePercentage / 100), 2);
                    $netPayout = $deal->compensation_amount - $athleteFeeAmount;
                @endphp
                <div class="border-t border-base-300 pt-6">
                    <h2 class="text-base font-semibold mb-4">Payment Details</h2>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between items-center">
                            <span class="text-base-content/60">Total compensation</span>
                            <span class="font-semibold">${{ number_format($deal->compensation_amount, 2) }}</span>
                        </div>
                        @if($athleteFeePercentage > 0)
                            <div class="flex justify-between items-center text-base-content/60">
                                <span>Platform fee ({{ number_format($athleteFeePercentage, 1) }}%)</span>
                                <span>-${{ number_format($athleteFeeAmount, 2) }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between items-center pt-2 border-t border-base-300">
                            <span class="font-medium">You'll receive</span>
                            <span class="text-lg font-bold text-primary">${{ number_format($netPayout, 2) }}</span>
                        </div>
                        <p class="text-xs text-base-content/60 mt-3 pt-3 border-t border-base-300">
                            Payment will be released from escrow after you complete the work and the business approves it.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sticky Action Bar - Always Visible on Mobile --}}
        @if($deal->status === 'pending' && (!isset($canAccept) || $canAccept))
            <div class="fixed bottom-0 left-0 right-0 bg-base-100 border-t border-base-300 shadow-lg z-50 safe-area-inset-bottom">
                <div class="w-full px-4 py-4">
                    <div class="max-w-2xl mx-auto">
                        @if(!Auth::guard('athlete')->check())
                            <p class="text-xs text-base-content/60 text-center mb-3">
                                @if(isset($invitation) && $invitation && $invitation->athlete_email)
                                    Sign in or create an account with <strong>{{ $invitation->athlete_email }}</strong> to accept this deal
                                @else
                                    You'll create an account after accepting this deal
                                @endif
                            </p>
                        @endif
                        
                        @if(Auth::guard('athlete')->check())
                            @php
                                $invitationToken = isset($invitation) && $invitation ? $invitation->token : $deal->token;
                            @endphp
                            <form method="POST" action="{{ route('athlete.deals.accept', $invitationToken) }}" id="accept-form">
                                @csrf
                                @if($deal->contract_text)
                                    <input type="hidden" name="contract_agreed" value="0" id="contract_agreed_input">
                                @endif
                                <button 
                                    type="submit" 
                                    class="btn btn-primary w-full text-base font-semibold py-3"
                                    id="accept-btn"
                                    @if($deal->contract_text) disabled @endif
                                >
                                    Accept Deal
                                </button>
                            </form>
                        @else
                            <div class="flex flex-col gap-2">
                                <a 
                                    href="{{ route('athlete.login') }}?redirect={{ urlencode(request()->fullUrl()) }}" 
                                    class="btn btn-primary w-full text-base font-semibold py-3"
                                >
                                    Accept Deal
                                </a>
                                <a 
                                    href="{{ route('athlete.register') }}?email={{ isset($invitation) && $invitation && $invitation->athlete_email ? urlencode($invitation->athlete_email) : '' }}&redirect={{ urlencode(request()->fullUrl()) }}" 
                                    class="btn btn-ghost w-full text-sm text-base-content/60"
                                >
                                    Create Account
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </div>

    <script>
        function toggleContract() {
            const summary = document.getElementById('contract-summary');
            const full = document.getElementById('contract-full');
            const chevron = document.getElementById('contract-chevron');
            
            if (full.classList.contains('hidden')) {
                full.classList.remove('hidden');
                if (chevron) chevron.classList.add('rotate-180');
            } else {
                full.classList.add('hidden');
                if (chevron) chevron.classList.remove('rotate-180');
            }
        }

        // Enable accept button only when checkbox is checked
        document.addEventListener('DOMContentLoaded', function() {
            const checkbox = document.getElementById('agree-checkbox');
            const acceptBtn = document.getElementById('accept-btn');
            const acceptForm = document.getElementById('accept-form');
            const contractAgreedInput = document.getElementById('contract_agreed_input');
            
            if (checkbox && acceptBtn && acceptForm) {
                checkbox.addEventListener('change', function() {
                    acceptBtn.disabled = !this.checked;
                    if (contractAgreedInput) {
                        contractAgreedInput.value = this.checked ? '1' : '0';
                    }
                });
                
                // Prevent form submission if checkbox not checked
                acceptForm.addEventListener('submit', function(e) {
                    if (!checkbox.checked) {
                        e.preventDefault();
                        alert('Please agree to the terms before accepting the deal.');
                        return false;
                    }
                });
            }
        });
    </script>
</x-full-width>
