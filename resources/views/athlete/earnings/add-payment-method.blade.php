<x-athlete-dashboard-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Connect Stripe Account
        </h2>
    </x-slot>

    <div class="bg-white rounded-lg shadow-sm p-8">
        <form method="POST" action="{{ route('athlete.earnings.payment-method.store') }}">
            @csrf

            <div class="max-w-2xl space-y-6">
                <!-- Stripe Connect Info -->
                <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-6">
                    <div class="flex items-start">
                        <svg class="w-6 h-6 text-indigo-600 mr-3 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M13.976 9.15c-2.172-.806-3.356-1.426-3.356-2.409 0-.831.683-1.305 1.901-1.305 2.227 0 4.515.858 6.09 1.631l.89-5.494C18.252.975 15.697 0 12.165 0 9.667 0 7.589.654 6.104 1.872 4.56 3.147 3.757 4.992 3.757 7.218c0 2.575 1.464 4.635 3.515 6.005l.891-5.494c.178-.9.808-1.305 1.901-1.305.831 0 1.901.178 2.912.594zm-1.543 9.15c-2.172-.806-3.356-1.426-3.356-2.409 0-.831.683-1.305 1.901-1.305 2.227 0 4.515.858 6.09 1.631l.89-5.494C16.709 10.975 14.154 10 10.622 10c-2.498 0-4.576.654-6.061 1.872C3.017 13.147 2.214 14.992 2.214 17.218c0 2.575 1.464 4.635 3.515 6.005l.891-5.494c.178-.9.808-1.305 1.901-1.305.831 0 1.901.178 2.912.594z"/>
                        </svg>
                        <div>
                            <p class="text-sm font-medium text-indigo-900">Stripe Connect</p>
                            <p class="mt-2 text-sm text-indigo-700">
                                Connect your Stripe account to receive payouts directly. You'll be redirected to Stripe to securely authorize the connection.
                            </p>
                        </div>
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

                <!-- Hidden field for type -->
                <input type="hidden" name="type" value="stripe">

                <!-- Stripe Connect Info (Future Implementation) -->
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-6">
                    <div class="text-center">
                        <p class="text-sm text-gray-600 mb-4">Stripe Connect OAuth will be available soon</p>
                        <button type="button" disabled class="inline-flex items-center px-6 py-3 bg-gray-400 border border-transparent rounded-md font-semibold text-sm text-white cursor-not-allowed opacity-60">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M13.976 9.15c-2.172-.806-3.356-1.426-3.356-2.409 0-.831.683-1.305 1.901-1.305 2.227 0 4.515.858 6.09 1.631l.89-5.494C18.252.975 15.697 0 12.165 0 9.667 0 7.589.654 6.104 1.872 4.56 3.147 3.757 4.992 3.757 7.218c0 2.575 1.464 4.635 3.515 6.005l.891-5.494c.178-.9.808-1.305 1.901-1.305.831 0 1.901.178 2.912.594zm-1.543 9.15c-2.172-.806-3.356-1.426-3.356-2.409 0-.831.683-1.305 1.901-1.305 2.227 0 4.515.858 6.09 1.631l.89-5.494C16.709 10.975 14.154 10 10.622 10c-2.498 0-4.576.654-6.061 1.872C3.017 13.147 2.214 14.992 2.214 17.218c0 2.575 1.464 4.635 3.515 6.005l.891-5.494c.178-.9.808-1.305 1.901-1.305.831 0 1.901.178 2.912.594z"/>
                            </svg>
                            Connect with Stripe (Coming Soon)
                        </button>
                        <p class="mt-4 text-xs text-gray-500">
                            By connecting, you agree to Stripe's <a href="https://stripe.com/connect-account/legal" target="_blank" class="text-indigo-600 hover:text-indigo-800">Terms of Service</a>
                        </p>
                    </div>
                </div>

                <!-- Note: Manual entry for testing -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <p class="text-sm text-blue-800">
                        <strong>For Testing:</strong> Enter your Stripe account ID below. You can get this from your Stripe Dashboard → Settings → Connect → Accounts. The account ID starts with <code class="bg-blue-100 px-1 rounded">acct_</code>.
                    </p>
                </div>

                <!-- Manual Stripe Account ID (for testing) -->
                <div>
                    <label for="provider_account_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Stripe Account ID <span class="text-red-600">*</span>
                    </label>
                    <x-text-input
                        id="provider_account_id"
                        type="text"
                        name="provider_account_id"
                        value="{{ old('provider_account_id') }}"
                        class="block w-full"
                        placeholder="acct_xxxxxxxxxxxxx"
                        required
                    />
                    <p class="mt-2 text-xs text-gray-500">
                        Required to receive payouts. Get this from your Stripe Dashboard → Settings → Connect → Accounts.
                        <br>
                        <strong>For testing:</strong> You can create a test Connect account in Stripe Dashboard or use a test account ID.
                    </p>
                    <x-input-error :messages="$errors->get('provider_account_id')" class="mt-1" />
                </div>

                <!-- Form Actions -->
                <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
                    <a href="{{ route('athlete.earnings.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                        Cancel
                    </a>
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-gray-900 rounded-md hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                        Connect Stripe Account
                    </button>
                </div>
            </div>
        </form>
    </div>
</x-athlete-dashboard-layout>
