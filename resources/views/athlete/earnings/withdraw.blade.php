<x-athlete-dashboard-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Withdraw Funds
        </h2>
    </x-slot>

    <div class="bg-white rounded-lg shadow-sm p-8">
        <form method="POST" action="{{ route('athlete.earnings.withdraw.store') }}">
            @csrf

            <div class="max-w-2xl space-y-6">
                <!-- Available Balance Display -->
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Available Balance</p>
                            <p class="text-3xl font-bold text-gray-900 mt-1">${{ number_format($availableBalance, 2) }}</p>
                        </div>
                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>

                @if($errors->any())
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                        <ul class="list-disc list-inside text-sm text-red-800">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Withdrawal Amount -->
                <div>
                    <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">Withdrawal Amount *</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 text-sm">$</span>
                        </div>
                        <x-text-input
                            id="amount"
                            type="number"
                            name="amount"
                            value="{{ old('amount') }}"
                            class="block w-full pl-7"
                            step="0.01"
                            min="10"
                            max="{{ $availableBalance }}"
                            placeholder="0.00"
                            required
                        />
                    </div>
                    <p class="mt-2 text-xs text-gray-500">Minimum withdrawal: $10.00</p>
                    <x-input-error :messages="$errors->get('amount')" class="mt-1" />
                </div>

                <!-- Payment Method Selection -->
                <div>
                    <label for="athlete_payment_method_id" class="block text-sm font-medium text-gray-700 mb-2">Payment Method *</label>
                    <select
                        id="athlete_payment_method_id"
                        name="athlete_payment_method_id"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2.5"
                        required
                    >
                        <option value="">Select a payment method</option>
                        @foreach($paymentMethods as $method)
                            <option value="{{ $method->id }}" {{ old('athlete_payment_method_id') == $method->id ? 'selected' : ($method->is_default ? 'selected' : '') }}>
                                {{ $method->display_name }} {{ $method->is_default ? '(Default)' : '' }}
                            </option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('athlete_payment_method_id')" class="mt-1" />
                    <p class="mt-2 text-xs text-gray-500">
                        <a href="{{ route('athlete.earnings.payment-method.create') }}" class="text-indigo-600 hover:text-indigo-800">Add a new payment method</a>
                    </p>
                </div>

                <!-- Processing Info -->
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <div class="flex">
                        <svg class="w-5 h-5 text-yellow-600 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                        <div>
                            <p class="text-sm font-medium text-yellow-800">Processing Time</p>
                            <p class="mt-1 text-sm text-yellow-700">Withdrawals are typically processed within 1-3 business days. You'll receive a notification once your withdrawal is completed.</p>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
                    <a href="{{ route('athlete.earnings.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-2 text-sm font-medium text-white bg-gray-900 rounded-md hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500" {{ $availableBalance < 10 ? 'disabled' : '' }}>
                        Submit Withdrawal Request
                    </button>
                </div>
            </div>
        </form>
    </div>
</x-athlete-dashboard-layout>

