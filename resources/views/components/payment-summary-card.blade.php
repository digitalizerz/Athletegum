@props(['deal'])

@php
    // Get payout data if available
    $payout = \App\Models\Payout::where('deal_id', $deal->id)->first();
    $athletePayoutAmount = $payout ? $payout->amount : ($deal->athlete_net_payout ?? 0);
    $platformFeeAmount = $deal->platform_fee_amount ?? 0;
    $grossAmount = $deal->compensation_amount ?? 0;
    
    // Get business-friendly payment status
    $paymentStatus = 'Pending Payment';
    $statusColor = 'warning';
    
    if (!empty($deal->released_at)) {
        $paymentStatus = 'Released';
        $statusColor = 'success';
    } elseif (!empty($deal->paid_at)) {
        if ($deal->awaiting_funds) {
            $paymentStatus = 'Processing';
            $statusColor = 'info';
        } else {
            $paymentStatus = 'In Escrow';
            $statusColor = 'info';
        }
    } elseif ($deal->payment_status === 'paid' || $deal->payment_status === 'paid_escrowed') {
        $paymentStatus = 'In Escrow';
        $statusColor = 'info';
    }
@endphp

<div class="card bg-base-100 shadow-sm border border-base-300">
    <div class="card-body">
        <h3 class="text-lg font-semibold mb-4">Payment Summary</h3>
        
        <div class="space-y-3">
            <!-- Gross Amount -->
            <div class="flex justify-between items-center">
                <span class="text-sm text-base-content/70">Gross Amount</span>
                <span class="font-semibold">${{ number_format($grossAmount, 2) }}</span>
            </div>
            
            <!-- Platform Fee -->
            <div class="flex justify-between items-center">
                <span class="text-sm text-base-content/70">Platform Fee (10%)</span>
                <span class="text-sm text-base-content/70">-${{ number_format($platformFeeAmount, 2) }}</span>
            </div>
            
            <!-- Divider -->
            <div class="divider my-2"></div>
            
            <!-- Athlete Payout -->
            <div class="flex justify-between items-center">
                <span class="text-sm font-medium">Athlete Payout</span>
                <span class="font-semibold text-lg">${{ number_format($athletePayoutAmount, 2) }}</span>
            </div>
            
            <!-- Payment Status -->
            <div class="pt-3 border-t border-base-300">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-sm text-base-content/70">Status</span>
                    <span class="badge badge-{{ $statusColor }} badge-sm">
                        {{ $paymentStatus }}
                    </span>
                </div>
                
                @if(!empty($deal->paid_at))
                    <div class="text-xs text-base-content/60">
                        Paid: {{ $deal->paid_at->format('M j, Y g:i A') }}
                    </div>
                @endif
                
                @if(!empty($deal->released_at))
                    <div class="text-xs text-base-content/60">
                        Released: {{ $deal->released_at->format('M j, Y g:i A') }}
                    </div>
                @endif
            </div>
            
            <!-- Payout Messaging -->
            @if(!empty($deal->paid_at) && empty($deal->released_at))
                <div class="pt-3 border-t border-base-300">
                    <p class="text-xs text-base-content/60">
                        Payouts typically arrive in 2–5 business days.
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>

