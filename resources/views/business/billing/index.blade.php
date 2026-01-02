<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Billing & Subscription
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="space-y-6">
                <!-- Current Plan Card -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4">Current Plan</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <p class="text-sm text-gray-600 mb-1">Plan</p>
                                <p class="text-2xl font-bold">
                                    @if($subscriptionData['plan'] === 'free')
                                        Free
                                    @elseif($subscriptionData['plan'] === 'pro')
                                        Pro
                                    @elseif($subscriptionData['plan'] === 'growth')
                                        Growth
                                    @else
                                        {{ ucfirst($subscriptionData['plan']) }}
                                    @endif
                                    @if($subscriptionData['pending_subscription_plan'])
                                        <span class="text-sm font-normal text-gray-500">
                                            → {{ ucfirst($subscriptionData['pending_subscription_plan']) }}
                                        </span>
                                    @endif
                                </p>
                            </div>
                            
                            <div>
                                <p class="text-sm text-gray-600 mb-1">Price</p>
                                <p class="text-2xl font-bold">
                                    @if($subscriptionData['plan'] === 'free')
                                        $0/month
                                    @elseif($subscriptionData['plan'] === 'pro')
                                        $49/month
                                    @elseif($subscriptionData['plan'] === 'growth')
                                        $99/month
                                    @else
                                        —
                                    @endif
                                </p>
                            </div>
                            
                            <div>
                                <p class="text-sm text-gray-600 mb-1">Status</p>
                                <p class="text-lg font-semibold">
                                    @if($subscriptionData['status'] === 'active')
                                        <span class="text-green-600">Active</span>
                                    @elseif($subscriptionData['status'] === 'cancelling')
                                        <span class="text-yellow-600">Cancelling</span>
                                    @elseif($subscriptionData['status'] === 'past_due')
                                        <span class="text-yellow-600">Past Due</span>
                                    @elseif($subscriptionData['status'] === 'inactive')
                                        <span class="text-gray-600">Inactive</span>
                                    @else
                                        <span class="text-gray-400">—</span>
                                    @endif
                                </p>
                            </div>
                        </div>

                        <!-- Show end date if cancelling -->
                        @if($subscriptionData['status'] === 'cancelling' && $subscriptionData['end_date'])
                            <div class="mb-4 p-3 bg-yellow-50 border border-yellow-200 rounded">
                                <p class="text-sm text-yellow-800">
                                    <strong>Subscription ends on:</strong> {{ $subscriptionData['end_date']->format('F j, Y') }}
                                </p>
                            </div>
                        @endif

                        <div class="border-t pt-4 mt-4">
                            <p class="text-sm text-gray-600 mb-4">
                                Billing managed securely by Stripe
                            </p>
                            
                            <!-- Action buttons based on status -->
                            @if($subscriptionData['status'] === 'active')
                                <div class="flex gap-3">
                                    <button type="button"
                                            x-data=""
                                            x-on:click.prevent="$dispatch('open-modal', 'confirm-cancel-subscription')"
                                            class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition">
                                        Cancel Subscription
                                    </button>
                                    
                                    @if($subscriptionData['plan'] === 'pro')
                                        <button type="button"
                                                x-data=""
                                                x-on:click.prevent="$dispatch('open-modal', 'confirm-upgrade-growth')"
                                                class="inline-flex items-center px-4 py-2 bg-black border border-black rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-black transition">
                                            Upgrade to Growth
                                        </button>
                                    @elseif($subscriptionData['plan'] === 'growth')
                                        <button type="button"
                                                x-data=""
                                                x-on:click.prevent="$dispatch('open-modal', 'confirm-downgrade-pro')"
                                                class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition">
                                            Downgrade to Pro
                                        </button>
                                    @endif
                                    
                                    @if($subscriptionData['stripe_customer_id'])
                                        <a href="{{ route('business.billing.portal') }}" 
                                           class="inline-flex items-center px-4 py-2 bg-black border border-black rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-black transition">
                                            Manage Billing
                                        </a>
                                    @endif
                                </div>
                            @elseif($subscriptionData['status'] === 'cancelling')
                                <!-- Already cancelling - no cancel button, but show manage billing -->
                                @if($subscriptionData['stripe_customer_id'])
                                    <a href="{{ route('business.billing.portal') }}" 
                                       class="inline-flex items-center px-4 py-2 bg-black border border-black rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-black transition">
                                        Manage Billing
                                    </a>
                                @endif
                            @elseif($subscriptionData['stripe_customer_id'])
                                <!-- Inactive but has customer ID -->
                                <a href="{{ route('business.billing.portal') }}" 
                                   class="inline-flex items-center px-4 py-2 bg-black border border-black rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-black transition">
                                    Manage Billing
                                </a>
                            @else
                                <p class="text-sm text-gray-500 italic">No billing account found</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Upgrade Options (if inactive/free) -->
                @if($subscriptionData['status'] === 'inactive' || $subscriptionData['status'] === null || $subscriptionData['plan'] === 'free')
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold mb-4">Upgrade Your Plan</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Pro Plan -->
                                <div class="border border-gray-200 rounded-lg p-6">
                                    <h4 class="text-xl font-bold mb-2">Pro</h4>
                                    <p class="text-3xl font-bold mb-4">$49<span class="text-lg font-normal text-gray-600">/month</span></p>
                                    <ul class="space-y-2 mb-6 text-sm text-gray-600">
                                        <li>✓ Athlete search & filters</li>
                                        <li>✓ Unlimited deals</li>
                                        <li>✓ Revenue analytics</li>
                                    </ul>
                                    <a href="{{ route('subscriptions.checkout', 'pro') }}" 
                                       class="block w-full text-center px-4 py-2 bg-black border border-black rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-black transition">
                                        Upgrade to Pro
                                    </a>
                                </div>
                                
                                <!-- Growth Plan -->
                                <div class="border border-gray-200 rounded-lg p-6">
                                    <h4 class="text-xl font-bold mb-2">Growth</h4>
                                    <p class="text-3xl font-bold mb-4">$99<span class="text-lg font-normal text-gray-600">/month</span></p>
                                    <ul class="space-y-2 mb-6 text-sm text-gray-600">
                                        <li>✓ Everything in Pro</li>
                                        <li>✓ Priority placement</li>
                                        <li>✓ Advanced analytics</li>
                                    </ul>
                                    <a href="{{ route('subscriptions.checkout', 'growth') }}" 
                                       class="block w-full text-center px-4 py-2 bg-black border border-black rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-black transition">
                                        Upgrade to Growth
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- What's Included (if on paid plan) -->
                @if($subscriptionData['plan'] === 'pro' || $subscriptionData['plan'] === 'growth')
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold mb-4">What's Included</h3>
                            
                            @if($subscriptionData['plan'] === 'pro')
                                <ul class="space-y-2 text-gray-700">
                                    <li>✓ Athlete search & filters</li>
                                    <li>✓ Unlimited deals</li>
                                    <li>✓ Revenue analytics</li>
                                </ul>
                            @elseif($subscriptionData['plan'] === 'growth')
                                <ul class="space-y-2 text-gray-700">
                                    <li>✓ Everything in Pro</li>
                                    <li>✓ Priority placement</li>
                                    <li>✓ Advanced analytics</li>
                                </ul>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Cancel Subscription Modal -->
    <x-modal name="confirm-cancel-subscription" focusable>
        <form method="POST" action="{{ route('business.billing.cancel') }}" class="p-6">
            @csrf
            
            <h3 class="font-bold text-lg text-gray-900 mb-4">
                Cancel Subscription
            </h3>

            <div class="mb-6">
                <p class="text-sm text-gray-700 mb-4">
                    Are you sure you want to cancel your subscription?
                </p>
                
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h4 class="text-sm font-medium text-yellow-800 mb-2">
                                Important Information
                            </h4>
                            <ul class="text-sm text-yellow-700 space-y-1 list-disc list-inside">
                                <li>Your subscription will remain active until the end of your current billing period</li>
                                <li>You'll continue to have full access to all features until then</li>
                                <li>No refunds will be issued for the remaining billing period</li>
                                <li>You can reactivate your subscription at any time</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <p class="text-sm text-gray-600">
                    Your subscription will end on <strong>{{ $subscriptionData['end_date'] ? $subscriptionData['end_date']->format('F j, Y') : 'the end of your current billing period' }}</strong>. After that date, you'll lose access to premium features and will be moved to the free plan.
                </p>
            </div>

            <div class="flex justify-end gap-3">
                <button type="button" 
                        class="px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition"
                        x-on:click="$dispatch('close-modal', 'confirm-cancel-subscription')">
                    Keep Subscription
                </button>
                <button type="submit" 
                        class="px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition">
                    Yes, Cancel Subscription
                </button>
            </div>
        </form>
    </x-modal>

    <!-- Upgrade to Growth Modal -->
    <x-modal name="confirm-upgrade-growth" focusable>
        <form method="POST" action="{{ route('business.billing.change-plan', 'growth') }}" class="p-6">
            @csrf
            
            <h3 class="font-bold text-lg text-gray-900 mb-4">
                Upgrade to Growth Plan
            </h3>

            <div class="mb-6">
                <p class="text-sm text-gray-700 mb-4">
                    You're about to upgrade from <strong>Pro</strong> to <strong>Growth</strong> plan.
                </p>
                
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h4 class="text-sm font-medium text-blue-800 mb-2">
                                What You'll Get
                            </h4>
                            <ul class="text-sm text-blue-700 space-y-1">
                                <li>✓ Everything in Pro</li>
                                <li>✓ Priority placement</li>
                                <li>✓ Advanced analytics</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="border-t border-gray-200 pt-4">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm text-gray-600">Current Plan (Pro)</span>
                        <span class="text-sm font-semibold text-gray-900">$49/month</span>
                    </div>
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm text-gray-600">New Plan (Growth)</span>
                        <span class="text-sm font-semibold text-gray-900">$99/month</span>
                    </div>
                    <div class="flex justify-between items-center pt-2 border-t border-gray-200">
                        <span class="text-sm font-semibold text-gray-900">Price Difference</span>
                        <span class="text-sm font-semibold text-gray-900">+$50/month</span>
                    </div>
                </div>

                <p class="text-sm text-gray-600 mt-4">
                    The change will take effect at the start of your next billing cycle. You'll continue to have access to your current Pro plan features until then.
                </p>
            </div>

            <div class="flex justify-end gap-3">
                <button type="button" 
                        class="px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition"
                        x-on:click="$dispatch('close-modal', 'confirm-upgrade-growth')">
                    Cancel
                </button>
                <button type="submit" 
                        class="px-4 py-2 bg-black border border-black rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-black transition">
                    Confirm Upgrade
                </button>
            </div>
        </form>
    </x-modal>

    <!-- Downgrade to Pro Modal -->
    <x-modal name="confirm-downgrade-pro" focusable>
        <form method="POST" action="{{ route('business.billing.change-plan', 'pro') }}" class="p-6">
            @csrf
            
            <h3 class="font-bold text-lg text-gray-900 mb-4">
                Downgrade to Pro Plan
            </h3>

            <div class="mb-6">
                <p class="text-sm text-gray-700 mb-4">
                    You're about to downgrade from <strong>Growth</strong> to <strong>Pro</strong> plan.
                </p>
                
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h4 class="text-sm font-medium text-yellow-800 mb-2">
                                Features You'll Lose
                            </h4>
                            <ul class="text-sm text-yellow-700 space-y-1">
                                <li>✗ Priority placement</li>
                                <li>✗ Advanced analytics</li>
                            </ul>
                            <p class="text-sm text-yellow-700 mt-2">
                                You'll still have access to all Pro plan features including athlete search, unlimited deals, and revenue analytics.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="border-t border-gray-200 pt-4">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm text-gray-600">Current Plan (Growth)</span>
                        <span class="text-sm font-semibold text-gray-900">$99/month</span>
                    </div>
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm text-gray-600">New Plan (Pro)</span>
                        <span class="text-sm font-semibold text-gray-900">$49/month</span>
                    </div>
                    <div class="flex justify-between items-center pt-2 border-t border-gray-200">
                        <span class="text-sm font-semibold text-gray-900">Monthly Savings</span>
                        <span class="text-sm font-semibold text-green-600">-$50/month</span>
                    </div>
                </div>

                <p class="text-sm text-gray-600 mt-4">
                    The change will take effect at the start of your next billing cycle. You'll continue to have access to your current Growth plan features until then.
                </p>
            </div>

            <div class="flex justify-end gap-3">
                <button type="button" 
                        class="px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition"
                        x-on:click="$dispatch('close-modal', 'confirm-downgrade-pro')">
                    Keep Growth Plan
                </button>
                <button type="submit" 
                        class="px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition">
                    Confirm Downgrade
                </button>
            </div>
        </form>
    </x-modal>
</x-app-layout>
