<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Add Funds to Wallet
            </h2>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow-sm p-6 sm:p-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-5">Add Funds</h3>

            @if(request('amount'))
                <div class="mb-6 p-4 bg-indigo-50 rounded-lg border-l-4 border-indigo-400">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-indigo-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-indigo-800">
                                <strong>Suggested amount:</strong> ${{ number_format(request('amount'), 2) }}
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('wallet.add-funds.store') }}">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div class="md:col-span-2">
                        <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">Amount</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 text-lg font-medium">$</span>
                            </div>
                            <x-text-input
                                id="amount"
                                type="number"
                                name="amount"
                                value="{{ old('amount', request('amount')) }}"
                                step="0.01"
                                min="1"
                                max="10000"
                                class="block w-full pl-8 text-lg font-semibold py-2.5"
                                placeholder="0.00"
                                required
                                autofocus
                            />
                        </div>
                        <x-input-error :messages="$errors->get('amount')" class="mt-1" />
                        <p class="mt-1 text-xs text-gray-500">Minimum $1.00, Maximum $10,000.00</p>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-3">Payment Provider</label>
                        <div class="grid grid-cols-2 gap-4">
                            <label class="relative block cursor-pointer">
                                <input 
                                    type="radio" 
                                    name="provider" 
                                    value="stripe"
                                    class="peer sr-only"
                                    {{ old('provider', 'stripe') === 'stripe' ? 'checked' : '' }}
                                    required
                                >
                                <div class="border-2 border-gray-200 rounded-lg p-4 transition-all hover:border-indigo-300 peer-checked:border-indigo-600 peer-checked:bg-indigo-50">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <div class="font-semibold text-gray-900">Stripe</div>
                                            <div class="text-xs text-gray-600 mt-1">Credit/Debit Cards</div>
                                        </div>
                                        <svg class="w-5 h-5 text-indigo-600 opacity-0 peer-checked:opacity-100 transition-opacity" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                </div>
                            </label>

                            <label class="relative block cursor-pointer">
                                <input 
                                    type="radio" 
                                    name="provider" 
                                    value="paypal"
                                    class="peer sr-only"
                                    {{ old('provider') === 'paypal' ? 'checked' : '' }}
                                    required
                                >
                                <div class="border-2 border-gray-200 rounded-lg p-4 transition-all hover:border-indigo-300 peer-checked:border-indigo-600 peer-checked:bg-indigo-50">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <div class="font-semibold text-gray-900">PayPal</div>
                                            <div class="text-xs text-gray-600 mt-1">PayPal Account</div>
                                        </div>
                                        <svg class="w-5 h-5 text-indigo-600 opacity-0 peer-checked:opacity-100 transition-opacity" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                </div>
                            </label>
                        </div>
                        <x-input-error :messages="$errors->get('provider')" class="mt-2" />
                    </div>

                    <div class="md:col-span-2">
                        <label for="payment_method_id" class="block text-sm font-medium text-gray-700 mb-3">Payment Method</label>
                        <div class="space-y-3">
                            @foreach($paymentMethods as $method)
                                <label class="relative block cursor-pointer">
                                    <input 
                                        type="radio" 
                                        name="payment_method_id" 
                                        value="{{ $method->id }}"
                                        class="peer sr-only"
                                        {{ ($method->is_default || old('payment_method_id') == $method->id) ? 'checked' : '' }}
                                        required
                                    >
                                    <div class="border-2 border-gray-200 rounded-lg p-3 transition-all hover:border-indigo-300 peer-checked:border-indigo-600 peer-checked:bg-indigo-50">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-3">
                                                @if($method->brand === 'visa')
                                                    <div class="w-12 h-8 bg-blue-600 rounded flex items-center justify-center text-white font-bold text-xs">VISA</div>
                                                @elseif($method->brand === 'mastercard')
                                                    <div class="w-12 h-8 bg-red-600 rounded flex items-center justify-center text-white font-bold text-xs">MC</div>
                                                @elseif($method->brand === 'amex')
                                                    <div class="w-12 h-8 bg-blue-500 rounded flex items-center justify-center text-white font-bold text-xs">AMEX</div>
                                                @else
                                                    <div class="w-12 h-8 bg-gray-300 rounded flex items-center justify-center text-gray-600 font-bold text-xs">CARD</div>
                                                @endif
                                                <div>
                                                    <div class="font-semibold text-gray-900 text-sm">
                                                        •••• •••• •••• {{ $method->last_four }}
                                                    </div>
                                                    <div class="text-xs text-gray-500">
                                                        Expires {{ $method->exp_month }}/{{ $method->exp_year }}
                                                        @if($method->is_default)
                                                            <span class="ml-2 text-indigo-600">(Default)</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <svg class="w-5 h-5 text-indigo-600 opacity-0 peer-checked:opacity-100 transition-opacity" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                        </div>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                        <x-input-error :messages="$errors->get('payment_method_id')" class="mt-2" />
                    </div>
                </div>

                <div class="mb-6">
                    <a href="{{ route('payment-methods.create') }}" class="text-sm text-indigo-600 hover:text-indigo-800">
                        + Add a new payment method
                    </a>
                </div>

                <div class="flex justify-between items-center pt-4 border-t border-gray-200">
                    <a href="{{ route('wallet.index') }}" class="text-sm text-gray-600 hover:text-gray-900 py-2 flex items-center">
                        ← Back
                    </a>
                    <button 
                        type="submit"
                        class="inline-flex items-center justify-center px-5 py-2.5 bg-gray-900 border border-transparent rounded-md font-semibold text-sm text-white hover:bg-gray-800 focus:bg-gray-800 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                    >
                        Add Funds
                    </button>
                </div>
            </form>
        </div>
    </div>
    </div>
</x-app-layout>

