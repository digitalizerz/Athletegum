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
                                    <span class="text-base-content/60">Platform Fee{{ $platformFeePercentage !== null ? ' (' . $platformFeePercentage . '%)' : '' }}:</span>
                                    <span class="font-semibold">${{ number_format($platformFeeAmount, 2) }}</span>
                                </div>
                                <div class="flex justify-between pt-2 border-t border-base-300">
                                    <span class="text-base font-semibold">Total Amount:</span>
                                    <span class="text-base font-bold">${{ number_format($totalAmount, 2) }}</span>
                                </div>
                                <div class="pt-2 text-xs text-base-content/60">
                                    <p>${{ number_format($escrowAmount, 2) }} will be held in escrow until the deal is completed and approved. You pay this total by card (Stripe).</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($paymentMethods->isEmpty())
                        <div class="alert alert-warning mb-6">
                            <span>Add a saved card before continuing. Deal payment is charged when you submit on the review step.</span>
                            <a href="{{ route('payment-methods.create') }}" class="btn btn-sm btn-primary ml-2">Add payment method</a>
                        </div>
                    @else
                        <form method="POST" action="{{ route('deals.create.payment.store') }}" id="payment-form">
                            @csrf

                            <div class="mb-6">
                                <label class="label">
                                    <span class="label-text font-semibold">Pay with saved card</span>
                                </label>
                                <select name="payment_method_id" class="select select-bordered w-full" required>
                                    <option value="">Choose a payment method</option>
                                    @foreach($paymentMethods as $method)
                                        <option value="{{ $method->id }}" @if($method->is_default) selected @endif>
                                            {{ ucfirst($method->brand) }} •••• {{ $method->last_four }}
                                            @if($method->is_default) (Default) @endif
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('payment_method_id')" class="mt-2" />
                            </div>

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
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
