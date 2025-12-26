<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">Review your deal</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto">
            <div class="card bg-base-100 shadow-sm">
                <div class="card-body">
                    <h3 class="text-lg font-semibold mb-5">Review your deal</h3>

                    <div class="border border-base-300 rounded-lg p-4 mb-6 space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <div class="text-xs text-base-content/60 mb-1">Type</div>
                                <div class="flex items-center space-x-2">
                                    <span class="text-xl">{{ $dealTypeIcon }}</span>
                                    <span class="text-sm font-semibold">{{ $dealTypeName }}</span>
                                </div>
                            </div>

                            <div>
                                <div class="text-xs text-base-content/60 mb-1">Payment</div>
                                <div class="text-xl font-semibold">${{ number_format($compensationAmount, 2) }}</div>
                            </div>
                        </div>

                        @if(!empty($platforms))
                            <div class="border-t border-base-300 pt-3">
                                <div class="text-xs text-base-content/60 mb-1.5">Platforms</div>
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach($platformNames as $platform)
                                        <span class="badge badge-outline">{{ $platform }}</span>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <div class="border-t border-base-300 pt-3">
                            <div class="text-xs text-base-content/60 mb-1">Deadline</div>
                            <div class="text-sm font-semibold">
                                {{ \Carbon\Carbon::parse($deadline)->format('M j, Y') }}
                                @if($deadlineTime)
                                    @php
                                        try {
                                            $time = \Carbon\Carbon::createFromFormat('H:i:s', $deadlineTime)->format('g:i A');
                                        } catch (\Exception $e) {
                                            $time = \Carbon\Carbon::createFromFormat('H:i', $deadlineTime)->format('g:i A');
                                        }
                                    @endphp
                                    at {{ $time }}
                                @endif
                            </div>
                            @if($frequency && $frequency !== 'one-time')
                                <div class="text-xs text-base-content/60 mt-0.5">
                                    Recurring: {{ \App\Models\Deal::getFrequencyOptions()[$frequency] ?? $frequency }}
                                </div>
                            @endif
                        </div>

                        @if($notes)
                            <div class="border-t border-base-300 pt-3">
                                <div class="text-xs text-base-content/60 mb-1.5">Instructions</div>
                                <div class="whitespace-pre-wrap text-xs">{{ $notes }}</div>
                            </div>
                        @endif

                        @if(!empty($attachments) && is_array($attachments))
                            <div class="border-t border-base-300 pt-3">
                                <div class="text-xs text-base-content/60 mb-1.5">Attachments</div>
                                <div class="space-y-1">
                                    @foreach($attachments as $attachment)
                                        <div class="flex items-center justify-between p-1.5 bg-base-200 rounded text-xs">
                                            <span class="truncate">{{ $attachment['original_name'] ?? basename($attachment['path'] ?? '') }}</span>
                                            <span class="text-base-content/60 ml-2 flex-shrink-0">
                                                {{ isset($attachment['size']) ? number_format($attachment['size'] / 1024, 1) . ' KB' : '' }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if($contractText)
                            <div class="border-t border-base-300 pt-3">
                                <div class="text-xs text-base-content/60 mb-1.5">Contract Agreement</div>
                                <div class="bg-base-200 border border-base-300 rounded-lg p-2 max-h-32 overflow-y-auto">
                                    <pre class="whitespace-pre-wrap text-xs font-sans">{{ $contractText }}</pre>
                                </div>
                                <div class="mt-1.5 text-xs text-success font-medium">
                                    ✓ Contract signed
                                </div>
                            </div>
                        @endif

                        @if(isset($paymentMethod))
                            <div class="border-t border-base-300 pt-3">
                                <div class="text-xs text-base-content/60 mb-1.5">Payment Method</div>
                                <div class="space-y-2">
                                    @if($paymentMethod === 'wallet')
                                        <div class="flex items-center space-x-2">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                            </svg>
                                            <span class="text-sm font-medium">Wallet - ${{ number_format($totalAmount, 2) }}</span>
                                        </div>
                                    @elseif($paymentMethod === 'wallet_card')
                                        <div class="space-y-1.5">
                                            <div class="flex items-center space-x-2">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                                </svg>
                                                <span class="text-sm font-medium">Wallet - ${{ number_format($walletAmountUsed, 2) }}</span>
                                            </div>
                                            @if($cardPaymentMethod)
                                                <div class="flex items-center space-x-2 pl-8">
                                                    @if($cardPaymentMethod->brand === 'visa')
                                                        <div class="w-10 h-6 bg-blue-600 rounded flex items-center justify-center text-white font-bold text-xs">VISA</div>
                                                    @elseif($cardPaymentMethod->brand === 'mastercard')
                                                        <div class="w-10 h-6 bg-red-600 rounded flex items-center justify-center text-white font-bold text-xs">MC</div>
                                                    @elseif($cardPaymentMethod->brand === 'amex')
                                                        <div class="w-10 h-6 bg-blue-500 rounded flex items-center justify-center text-white font-bold text-xs">AMEX</div>
                                                    @else
                                                        <div class="w-10 h-6 bg-base-300 rounded flex items-center justify-center text-base-content/60 font-bold text-xs">CARD</div>
                                                    @endif
                                                    <span class="text-sm font-medium">Card - ${{ number_format($cardAmount, 2) }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    @elseif($paymentMethod === 'card' && $cardPaymentMethod)
                                        <div class="flex items-center space-x-2">
                                            @if($cardPaymentMethod->brand === 'visa')
                                                <div class="w-10 h-6 bg-blue-600 rounded flex items-center justify-center text-white font-bold text-xs">VISA</div>
                                            @elseif($cardPaymentMethod->brand === 'mastercard')
                                                <div class="w-10 h-6 bg-red-600 rounded flex items-center justify-center text-white font-bold text-xs">MC</div>
                                            @elseif($cardPaymentMethod->brand === 'amex')
                                                <div class="w-10 h-6 bg-blue-500 rounded flex items-center justify-center text-white font-bold text-xs">AMEX</div>
                                            @else
                                                <div class="w-10 h-6 bg-base-300 rounded flex items-center justify-center text-base-content/60 font-bold text-xs">CARD</div>
                                            @endif
                                            <span class="text-sm font-medium">•••• {{ $cardPaymentMethod->last_four }} - ${{ number_format($totalAmount, 2) }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif

                        @if(isset($totalAmount) && $totalAmount)
                            <div class="border-t border-base-300 pt-3">
                                <div class="text-xs text-base-content/60 mb-1.5">Payment Breakdown</div>
                                <div class="space-y-1 text-xs">
                                    <div class="flex justify-between">
                                        <span class="text-base-content/60">Compensation:</span>
                                        <span>${{ number_format($compensationAmount, 2) }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-base-content/60">Platform Fee ({{ $platformFeePercentage }}%):</span>
                                        <span>${{ number_format($platformFeeAmount, 2) }}</span>
                                    </div>
                                    <div class="flex justify-between pt-1 border-t border-base-300 font-semibold">
                                        <span>Total:</span>
                                        <span>${{ number_format($totalAmount, 2) }}</span>
                                    </div>
                                    <div class="pt-1 text-base-content/60">
                                        ${{ number_format($escrowAmount, 2) }} in escrow
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- Payment Method Warning --}}
                    @if(!$hasPaymentMethod)
                        <div class="alert alert-warning mb-6">
                            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <div>
                                <h3 class="font-bold">Payment Method Required</h3>
                                <div class="text-sm">
                                    You need to add a payment method before submitting this deal to an athlete. You can save your progress as a draft and come back later.
                                </div>
                                <div class="mt-2">
                                    <a href="{{ route('payment-methods.create') }}" class="btn btn-sm btn-primary">
                                        Add Payment Method
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('deals.store') }}" id="submit-form">
                        @csrf
                        <input type="hidden" name="deal_type" value="{{ $dealType }}">
                        @if(!empty($platforms))
                            @foreach($platforms as $platform)
                                <input type="hidden" name="platforms[]" value="{{ $platform }}">
                            @endforeach
                        @endif
                        <input type="hidden" name="compensation_amount" value="{{ $compensationAmount }}">
                        <input type="hidden" name="deadline" value="{{ $deadline }}">
                        <input type="hidden" name="notes" value="{{ $notes }}">

                        <div class="flex justify-between items-center pt-4 border-t border-base-300">
                            <a href="{{ route('deals.create.payment') }}" class="btn btn-ghost btn-sm">
                                ← Back
                            </a>
                            <div class="flex gap-2">
                                <form method="POST" action="{{ route('deals.save-draft') }}" class="inline">
                                    @csrf
                                    <button type="submit" class="btn btn-outline btn-sm">
                                        Save as Draft
                                    </button>
                                </form>
                                @if($hasPaymentMethod)
                                    <button type="submit" class="btn btn-primary">
                                        Pay & Create Deal
                                    </button>
                                @else
                                    <button type="button" class="btn btn-primary btn-disabled" disabled>
                                        Pay & Create Deal
                                    </button>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
