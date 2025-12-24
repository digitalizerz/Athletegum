<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl leading-tight">Create a Deal</h2>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto">
            <div class="card bg-base-100 shadow-sm">
                <div class="card-body">
                    <h3 class="text-lg font-semibold mb-5">Payment Information</h3>

                    <div class="mb-6 card bg-base-200">
                        <div class="card-body p-4">
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-base-content/60">Compensation Amount:</span>
                                    <span class="font-semibold">${{ number_format($compensationAmount, 2) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-base-content/60">Platform Fee ({{ $platformFeePercentage }}%):</span>
                                    <span class="font-semibold">${{ number_format($platformFeeAmount, 2) }}</span>
                                </div>
                                <div class="flex justify-between pt-2 border-t border-base-300">
                                    <span class="text-base font-semibold">Total Amount:</span>
                                    <span class="text-base font-bold">${{ number_format($totalAmount, 2) }}</span>
                                </div>
                                <div class="pt-2 text-xs text-base-content/60">
                                    <p>${{ number_format($escrowAmount, 2) }} will be held in escrow until the deal is completed and approved.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-6 alert alert-info">
                        <div class="flex justify-between items-center w-full">
                            <span class="text-sm font-medium">Wallet Balance:</span>
                            <span class="text-lg font-bold">${{ number_format($walletBalance, 2) }}</span>
                        </div>
                        @if(!$hasSufficientBalance && $walletBalance > 0)
                            <p class="text-xs mt-1">
                                You can use ${{ number_format($walletBalance, 2) }} from your wallet and pay the remaining ${{ number_format($totalAmount - $walletBalance, 2) }} via card.
                            </p>
                        @elseif(!$hasSufficientBalance)
                            <p class="text-xs mt-1">
                                You can pay the full amount of ${{ number_format($totalAmount, 2) }} via card.
                            </p>
                        @endif
                    </div>

                    <form method="POST" action="{{ route('deals.create.payment.store') }}" id="payment-form">
                        @csrf

                        <div class="mb-6 space-y-4">
                            @if($hasSufficientBalance)
                                {{-- Option 1: Pay fully from wallet --}}
                                <label class="relative block cursor-pointer">
                                    <input 
                                        type="radio" 
                                        name="payment_method" 
                                        value="wallet_full"
                                        class="peer sr-only"
                                        checked
                                    >
                                    <div class="border-2 border-base-300 rounded-lg p-4 transition-all hover:border-primary peer-checked:border-primary peer-checked:bg-primary/10">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <div class="font-semibold">Pay from Wallet</div>
                                                <div class="text-sm text-base-content/60 mt-1">Use your full wallet balance (${{ number_format($walletBalance, 2) }})</div>
                                            </div>
                                            <svg class="w-5 h-5 text-primary opacity-0 peer-checked:opacity-100 transition-opacity" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                        </div>
                                    </div>
                                </label>
                            @endif

                            @if($walletBalance > 0 && !$hasSufficientBalance)
                                {{-- Option 1: Use wallet + pay remainder via card --}}
                                <label class="relative block cursor-pointer">
                                    <input 
                                        type="radio" 
                                        name="payment_method" 
                                        value="wallet_partial"
                                        class="peer sr-only"
                                        checked
                                    >
                                    <div class="border-2 border-base-300 rounded-lg p-4 transition-all hover:border-primary peer-checked:border-primary peer-checked:bg-primary/10">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <div class="font-semibold">Use Wallet + Card</div>
                                                <div class="text-sm text-base-content/60 mt-1">
                                                    Use ${{ number_format($walletBalance, 2) }} from wallet, pay ${{ number_format($totalAmount - $walletBalance, 2) }} via card
                                                </div>
                                            </div>
                                            <svg class="w-5 h-5 text-primary opacity-0 peer-checked:opacity-100 transition-opacity" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                        </div>
                                    </div>
                                </label>
                            @endif

                            {{-- Option 2: Pay fully via card (always available) --}}
                            <label class="relative block cursor-pointer">
                                <input 
                                    type="radio" 
                                    name="payment_method" 
                                    value="card_full"
                                    class="peer sr-only"
                                    @if(!$hasSufficientBalance && $walletBalance == 0) checked @endif
                                >
                                <div class="border-2 border-base-300 rounded-lg p-4 transition-all hover:border-primary peer-checked:border-primary peer-checked:bg-primary/10">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <div class="font-semibold">Pay via Card</div>
                                            <div class="text-sm text-base-content/60 mt-1">
                                                Pay ${{ number_format($totalAmount, 2) }} using your saved payment method
                                            </div>
                                        </div>
                                        <svg class="w-5 h-5 text-primary opacity-0 peer-checked:opacity-100 transition-opacity" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                </div>
                            </label>
                        </div>

                        {{-- Payment method selection (shown when card payment is selected) --}}
                        <div id="payment-method-selection" class="mb-6" style="display: none;">
                            <label class="label">
                                <span class="label-text font-semibold">Select Payment Method</span>
                            </label>
                            <select name="payment_method_id" class="select select-bordered w-full" required>
                                <option value="">Choose a payment method</option>
                                @foreach($paymentMethods as $method)
                                    <option value="{{ $method->id }}" @if($method->is_default) selected @endif>
                                        {{ $method->card_brand }} •••• {{ $method->card_last_four }} 
                                        @if($method->is_default) (Default) @endif
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('payment_method_id')" class="mt-2" />
                        </div>

                        <x-input-error :messages="$errors->get('payment_method')" class="mb-4" />
                        <x-input-error :messages="$errors->get('error')" class="mb-4" />

                        <div class="flex justify-between items-center pt-4 border-t border-base-300">
                            <a href="{{ route('deals.create.contract') }}" class="btn btn-ghost btn-sm">
                                ← Back
                            </a>
                            <button type="submit" class="btn btn-primary">
                                Continue to Review
                            </button>
                        </div>
                    </form>

                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const paymentMethodInputs = document.querySelectorAll('input[name="payment_method"]');
                            const paymentMethodSelection = document.getElementById('payment-method-selection');
                            const paymentMethodSelect = document.querySelector('select[name="payment_method_id"]');

                            function togglePaymentMethodSelection() {
                                const selectedMethod = document.querySelector('input[name="payment_method"]:checked');
                                if (selectedMethod && (selectedMethod.value === 'card_full' || selectedMethod.value === 'wallet_partial')) {
                                    paymentMethodSelection.style.display = 'block';
                                    paymentMethodSelect.setAttribute('required', 'required');
                                } else {
                                    paymentMethodSelection.style.display = 'none';
                                    paymentMethodSelect.removeAttribute('required');
                                }
                            }

                            paymentMethodInputs.forEach(input => {
                                input.addEventListener('change', togglePaymentMethodSelection);
                            });

                            // Initial check
                            togglePaymentMethodSelection();
                        });
                    </script>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
