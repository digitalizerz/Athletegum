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

                @if ($errors->any())
                    <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-md">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800">
                                    There was an error adding your payment method
                                </h3>
                                <div class="mt-2 text-sm text-red-700">
                                    <ul class="list-disc list-inside space-y-1">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @if (session('success'))
                    <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-md">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                @if(empty($stripeKey))
                    <div class="mb-4 p-4 bg-yellow-50 border border-yellow-200 rounded-md">
                        <p class="text-sm text-yellow-800">
                            Stripe is not configured. Please contact support.
                        </p>
                    </div>
                @else
                    <form id="payment-form" method="POST" action="{{ route('payment-methods.store') }}">
                        @csrf
                        <input type="hidden" name="payment_method_id" id="payment_method_id" />

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
                                <label class="block text-sm font-medium text-gray-700 mb-2">Card Number</label>
                                <div id="card-element" class="p-3 border border-gray-300 rounded-md focus-within:border-gray-900 focus-within:ring-1 focus-within:ring-gray-900 min-h-[42px] bg-white">
                                    <!-- Stripe Elements will mount here -->
                                </div>
                                <div id="card-errors" class="mt-1 text-sm text-red-600 min-h-[20px]" role="alert"></div>
                                <p class="mt-1 text-xs text-gray-500">Enter your card details securely</p>
                            </div>
                        </div>

                        <div class="flex justify-between items-center pt-4 border-t border-gray-200">
                            <a href="{{ route('payment-methods.index') }}" class="text-sm text-gray-600 hover:text-gray-900 py-2 flex items-center">
                                ← Back
                            </a>
                            <button 
                                type="submit"
                                id="submit-button"
                                class="inline-flex items-center justify-center px-5 py-2.5 bg-gray-900 border border-transparent rounded-md font-semibold text-sm text-white hover:bg-gray-800 focus:bg-gray-800 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                <span id="button-text">Add Payment Method</span>
                                <span id="spinner" class="hidden ml-2">
                                    <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                </span>
                            </button>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>

    @if(!empty($stripeKey))
        <style>
            #card-element {
                min-height: 42px;
            }
            .StripeElement {
                box-sizing: border-box;
                height: 40px;
                padding: 10px 12px;
                border: 1px solid transparent;
                border-radius: 4px;
                background-color: white;
                box-shadow: 0 1px 3px 0 #e6ebf1;
                transition: box-shadow 150ms ease;
            }
            .StripeElement--focus {
                box-shadow: 0 1px 3px 0 #cfd7df;
            }
            .StripeElement--invalid {
                border-color: #fa755a;
            }
        </style>
        
        <script src="https://js.stripe.com/v3/"></script>
        <script>
            window.addEventListener('DOMContentLoaded', function() {
                const stripeKey = '{{ $stripeKey }}';
                
                if (!stripeKey) {
                    document.getElementById('card-element').innerHTML = '<p class="text-red-600 text-sm">Stripe is not configured.</p>';
                    return;
                }

                // Wait for Stripe to be available
                let checkCount = 0;
                const checkStripe = setInterval(function() {
                    checkCount++;
                    if (typeof Stripe !== 'undefined') {
                        clearInterval(checkStripe);
                        initializeStripe(stripeKey);
                    } else if (checkCount > 50) {
                        clearInterval(checkStripe);
                        document.getElementById('card-element').innerHTML = '<p class="text-red-600 text-sm">Failed to load Stripe. Please refresh.</p>';
                    }
                }, 100);
            });

            function initializeStripe(stripeKey) {
                try {
                    const stripe = Stripe(stripeKey);
                    const elements = stripe.elements();
                    
                    const cardElement = elements.create('card', {
                        style: {
                            base: {
                                fontSize: '16px',
                                color: '#111827',
                                fontFamily: 'system-ui, -apple-system, sans-serif',
                                '::placeholder': {
                                    color: '#9CA3AF',
                                },
                            },
                            invalid: {
                                color: '#EF4444',
                            },
                        },
                    });

                    cardElement.mount('#card-element');

                    cardElement.on('change', function(event) {
                        const displayError = document.getElementById('card-errors');
                        if (displayError) {
                            if (event.error) {
                                displayError.textContent = event.error.message;
                            } else {
                                displayError.textContent = '';
                            }
                        }
                    });

                    const form = document.getElementById('payment-form');
                    const submitButton = document.getElementById('submit-button');
                    const buttonText = document.getElementById('button-text');
                    const spinner = document.getElementById('spinner');

                    form.addEventListener('submit', async function(event) {
                        event.preventDefault();

                        submitButton.disabled = true;
                        buttonText.textContent = 'Processing...';
                        spinner.classList.remove('hidden');

                        const displayError = document.getElementById('card-errors');
                        if (displayError) {
                            displayError.textContent = '';
                        }

                        try {
                            const {paymentMethod, error} = await stripe.createPaymentMethod({
                                type: 'card',
                                card: cardElement,
                                billing_details: {
                                    name: document.getElementById('cardholder_name').value,
                                },
                            });

                            if (error) {
                                if (displayError) {
                                    displayError.textContent = error.message;
                                }
                                submitButton.disabled = false;
                                buttonText.textContent = 'Add Payment Method';
                                spinner.classList.add('hidden');
                            } else {
                                document.getElementById('payment_method_id').value = paymentMethod.id;
                                form.submit();
                            }
                        } catch (err) {
                            console.error('Error:', err);
                            if (displayError) {
                                displayError.textContent = 'An error occurred. Please try again.';
                            }
                            submitButton.disabled = false;
                            buttonText.textContent = 'Add Payment Method';
                            spinner.classList.add('hidden');
                        }
                    });
                } catch (err) {
                    console.error('Stripe initialization error:', err);
                    document.getElementById('card-element').innerHTML = '<p class="text-red-600 text-sm">Error initializing payment form.</p>';
                }
            }
        </script>
    @endif
</x-app-layout>
