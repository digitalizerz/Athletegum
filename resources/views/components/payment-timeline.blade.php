@props(['deal'])

@php
    // Determine current stage based on deal payment status and dates
    $paid = !empty($deal->paid_at) || in_array($deal->payment_status, ['paid', 'paid_escrowed']);
    $processing = $paid && empty($deal->released_at);
    $released = !empty($deal->released_at);
@endphp

<div class="space-y-3">
    <h3 class="text-sm font-semibold text-base-content/80">Payment Timeline</h3>
    <div class="flex items-center gap-4">
        <!-- Paid Stage -->
        <div class="flex items-center gap-2 flex-1">
            <div class="flex flex-col items-center">
                <div class="w-8 h-8 rounded-full flex items-center justify-center 
                    {{ $paid ? 'bg-success text-success-content' : 'bg-base-300 text-base-content/40' }}">
                    @if($paid)
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    @else
                        <span class="text-xs font-semibold">1</span>
                    @endif
                </div>
                @if($paid)
                    <div class="text-xs text-base-content/60 mt-1 text-center">
                        {{ $deal->paid_at->format('M j') }}
                    </div>
                @endif
            </div>
            <div class="flex-1">
                <div class="font-medium text-sm {{ $paid ? 'text-base-content' : 'text-base-content/60' }}">
                    Paid
                </div>
                @if($paid)
                    <div class="text-xs text-base-content/60">Payment received</div>
                @endif
            </div>
        </div>

        <!-- Processing Stage -->
        <div class="w-8 h-px {{ $processing || $released ? 'bg-success' : 'bg-base-300' }}"></div>

        <div class="flex items-center gap-2 flex-1">
            <div class="flex flex-col items-center">
                <div class="w-8 h-8 rounded-full flex items-center justify-center 
                    {{ $processing ? 'bg-warning text-warning-content' : ($released ? 'bg-success text-success-content' : 'bg-base-300 text-base-content/40') }}">
                    @if($released)
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    @elseif($processing)
                        <svg class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                    @else
                        <span class="text-xs font-semibold">2</span>
                    @endif
                </div>
            </div>
            <div class="flex-1">
                <div class="font-medium text-sm {{ $processing || $released ? 'text-base-content' : 'text-base-content/60' }}">
                    Processing
                </div>
                @if($processing)
                    <div class="text-xs text-base-content/60">Payout pending</div>
                @endif
            </div>
        </div>

        <!-- Released Stage -->
        <div class="w-8 h-px {{ $released ? 'bg-success' : 'bg-base-300' }}"></div>

        <div class="flex items-center gap-2 flex-1">
            <div class="flex flex-col items-center">
                <div class="w-8 h-8 rounded-full flex items-center justify-center 
                    {{ $released ? 'bg-success text-success-content' : 'bg-base-300 text-base-content/40' }}">
                    @if($released)
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    @else
                        <span class="text-xs font-semibold">3</span>
                    @endif
                </div>
                @if($released)
                    <div class="text-xs text-base-content/60 mt-1 text-center">
                        {{ $deal->released_at->format('M j') }}
                    </div>
                @endif
            </div>
            <div class="flex-1">
                <div class="font-medium text-sm {{ $released ? 'text-base-content' : 'text-base-content/60' }}">
                    Released
                </div>
                @if($released)
                    <div class="text-xs text-base-content/60">Payout completed</div>
                @endif
            </div>
        </div>
    </div>
</div>

