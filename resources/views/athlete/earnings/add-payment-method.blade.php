<x-athlete-dashboard-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Payment Methods
        </h2>
    </x-slot>

    <div class="bg-white rounded-lg shadow-sm p-8">
        <div class="max-w-2xl space-y-6">
            <!-- Deferred Onboarding Message -->
            <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-6">
                <div class="flex items-start">
                    <svg class="w-6 h-6 text-indigo-600 mr-3 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div>
                        <p class="text-sm font-medium text-indigo-900">Stripe Connect Setup</p>
                        <p class="mt-2 text-sm text-indigo-700">
                            <strong>You'll only need to connect Stripe when you're ready to withdraw your earnings.</strong>
                        </p>
                        <p class="mt-2 text-sm text-indigo-600">
                            You can create your account, accept deals, complete work, and accumulate earnings without connecting Stripe. When you're ready to withdraw, we'll guide you through a quick Stripe Connect setup process.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Current Payment Methods -->
            @php
                $athlete = Auth::guard('athlete')->user();
                $paymentMethods = $athlete->paymentMethods()->where('is_active', true)->orderBy('is_default', 'desc')->get();
            @endphp

            @if($paymentMethods->isEmpty())
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-6 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                    </svg>
                    <p class="mt-4 text-sm text-gray-600">No payment methods connected yet.</p>
                    <p class="mt-2 text-xs text-gray-500">You'll be prompted to connect Stripe when you try to withdraw earnings.</p>
                </div>
            @else
                <div class="space-y-3">
                    <h3 class="text-sm font-medium text-gray-700">Connected Payment Methods</h3>
                    @foreach($paymentMethods as $method)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-200">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-6 bg-indigo-600 rounded flex items-center justify-center text-white font-bold text-xs">
                                    Stripe
                                </div>
                                <div>
                                    <div class="text-sm font-medium">
                                        {{ $method->provider_account_id ? substr($method->provider_account_id, 0, 20) . '...' : 'Connected Account' }}
                                    </div>
                                    <div class="text-xs text-gray-500">Stripe Connect Express</div>
                                </div>
                            </div>
                            @if($method->is_default)
                                <span class="badge badge-primary">Default</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- Action Buttons -->
            <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
                <a href="{{ route('athlete.earnings.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                    Back to Earnings
                </a>
            </div>
        </div>
    </div>
</x-athlete-dashboard-layout>
