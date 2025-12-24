<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Add Payment Method
            </h2>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto">
            <div class="bg-white rounded-lg shadow-sm p-6 sm:p-8">
                <h3 class="text-lg font-semibold text-gray-900 mb-5">Card Information</h3>

                <form method="POST" action="{{ route('payment-methods.store') }}">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div class="md:col-span-2">
                            <label for="cardholder_name" class="block text-sm font-medium text-gray-700 mb-2">Cardholder Name</label>
                            <x-text-input
                                id="cardholder_name"
                                type="text"
                                name="cardholder_name"
                                value="{{ old('cardholder_name') }}"
                                class="block w-full"
                                placeholder="John Doe"
                                required
                                autofocus
                            />
                            <x-input-error :messages="$errors->get('cardholder_name')" class="mt-1" />
                        </div>

                        <div class="md:col-span-2">
                            <label for="card_number" class="block text-sm font-medium text-gray-700 mb-2">Card Number</label>
                            <x-text-input
                                id="card_number"
                                type="text"
                                name="card_number"
                                value="{{ old('card_number') }}"
                                class="block w-full"
                                placeholder="1234567890123456"
                                maxlength="16"
                                pattern="[0-9]{16}"
                                required
                            />
                            <x-input-error :messages="$errors->get('card_number')" class="mt-1" />
                            <p class="mt-1 text-xs text-gray-500">Enter 16-digit card number (no spaces)</p>
                        </div>

                        <div>
                            <label for="exp_month" class="block text-sm font-medium text-gray-700 mb-2">Expiration Month</label>
                            <x-text-input
                                id="exp_month"
                                type="text"
                                name="exp_month"
                                value="{{ old('exp_month') }}"
                                class="block w-full"
                                placeholder="MM"
                                maxlength="2"
                                pattern="[0-9]{2}"
                                required
                            />
                            <x-input-error :messages="$errors->get('exp_month')" class="mt-1" />
                        </div>

                        <div>
                            <label for="exp_year" class="block text-sm font-medium text-gray-700 mb-2">Expiration Year</label>
                            <x-text-input
                                id="exp_year"
                                type="text"
                                name="exp_year"
                                value="{{ old('exp_year') }}"
                                class="block w-full"
                                placeholder="YYYY"
                                maxlength="4"
                                pattern="[0-9]{4}"
                                required
                            />
                            <x-input-error :messages="$errors->get('exp_year')" class="mt-1" />
                        </div>

                        <div>
                            <label for="cvc" class="block text-sm font-medium text-gray-700 mb-2">CVC</label>
                            <x-text-input
                                id="cvc"
                                type="text"
                                name="cvc"
                                value="{{ old('cvc') }}"
                                class="block w-full"
                                placeholder="123"
                                maxlength="3"
                                pattern="[0-9]{3}"
                                required
                            />
                            <x-input-error :messages="$errors->get('cvc')" class="mt-1" />
                        </div>
                    </div>

                    <div class="flex justify-between items-center pt-4 border-t border-gray-200">
                        <a href="{{ route('payment-methods.index') }}" class="text-sm text-gray-600 hover:text-gray-900 py-2 flex items-center">
                            ← Back
                        </a>
                        <button 
                            type="submit"
                            class="inline-flex items-center justify-center px-5 py-2.5 bg-gray-900 border border-transparent rounded-md font-semibold text-sm text-white hover:bg-gray-800 focus:bg-gray-800 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                        >
                            Add Payment Method
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

