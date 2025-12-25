<?php

namespace App\Http\Controllers;

use App\Models\PaymentMethod;
use App\Services\StripeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PaymentMethodController extends Controller
{
    protected StripeService $stripeService;

    public function __construct(StripeService $stripeService)
    {
        $this->stripeService = $stripeService;
    }
    public function index()
    {
        $paymentMethods = Auth::user()->paymentMethods()->orderBy('is_default', 'desc')->orderBy('created_at', 'desc')->get();

        return view('payment-methods.index', [
            'paymentMethods' => $paymentMethods,
        ]);
    }

    public function create()
    {
        $stripeKey = $this->stripeService->getPublishableKey();
        
        return view('payment-methods.create', [
            'stripeKey' => $stripeKey,
        ]);
    }

    public function store(Request $request)
    {
        // Validate that we have a payment method ID from Stripe Elements
        $validated = $request->validate([
            'payment_method_id' => ['required', 'string', 'starts_with:pm_'],
            'cardholder_name' => ['required', 'string', 'max:255'],
        ], [
            'payment_method_id.required' => 'Payment method is required. Please try again.',
            'payment_method_id.starts_with' => 'Invalid payment method. Please try again.',
        ]);

        try {
            // Check if Stripe is configured
            if (!$this->stripeService->isConfigured()) {
                Log::error('Stripe not configured when trying to add payment method', [
                    'user_id' => Auth::id(),
                ]);
                return redirect()->back()->withErrors([
                    'error' => 'Stripe is not configured. Please contact support.'
                ])->withInput();
            }

            $user = Auth::user();

            Log::info('Creating payment method for user', [
                'user_id' => $user->id,
                'email' => $user->email,
            ]);

            // Create or get Stripe customer
            try {
                $customerId = $this->stripeService->getOrCreateCustomer(
                    $user->id,
                    $user->email,
                    $user->name
                );
                Log::info('Stripe customer retrieved/created', [
                    'user_id' => $user->id,
                    'customer_id' => $customerId,
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to get/create Stripe customer', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage(),
                ]);
                return redirect()->back()->withErrors([
                    'error' => 'Failed to set up payment account: ' . $e->getMessage()
                ])->withInput();
            }

            // Retrieve the payment method from Stripe (already created by Stripe Elements on client)
            try {
                $stripePaymentMethod = \Stripe\PaymentMethod::retrieve($validated['payment_method_id']);
                Log::info('Stripe payment method retrieved', [
                    'user_id' => $user->id,
                    'payment_method_id' => $stripePaymentMethod->id,
                ]);
            } catch (\Stripe\Exception\InvalidRequestException $e) {
                Log::error('Stripe payment method not found', [
                    'user_id' => $user->id,
                    'payment_method_id' => $validated['payment_method_id'],
                    'error' => $e->getMessage(),
                ]);
                return redirect()->back()->withErrors([
                    'error' => 'Payment method not found. Please try again.'
                ])->withInput();
            } catch (\Exception $e) {
                Log::error('Failed to retrieve Stripe payment method', [
                    'user_id' => $user->id,
                    'payment_method_id' => $validated['payment_method_id'],
                    'error' => $e->getMessage(),
                ]);
                return redirect()->back()->withErrors([
                    'error' => 'Failed to retrieve payment method: ' . $e->getMessage()
                ])->withInput();
            }

            // Attach to customer
            try {
                $this->stripeService->attachPaymentMethodToCustomer(
                    $stripePaymentMethod->id,
                    $customerId
                );
                Log::info('Payment method attached to customer', [
                    'user_id' => $user->id,
                    'payment_method_id' => $stripePaymentMethod->id,
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to attach payment method to customer', [
                    'user_id' => $user->id,
                    'payment_method_id' => $stripePaymentMethod->id,
                    'error' => $e->getMessage(),
                ]);
                // Try to clean up the payment method
                try {
                    $stripePaymentMethod->detach();
                } catch (\Exception $detachError) {
                    Log::warning('Failed to detach payment method after attach failure', [
                        'error' => $detachError->getMessage(),
                    ]);
                }
                return redirect()->back()->withErrors([
                    'error' => 'Failed to link payment method to account: ' . $e->getMessage()
                ])->withInput();
            }

            // Store in database
            $isFirst = Auth::user()->paymentMethods()->count() === 0;
            
            $paymentMethod = PaymentMethod::create([
                'user_id' => $user->id,
                'type' => 'card',
                'provider' => 'stripe',
                'provider_payment_method_id' => $stripePaymentMethod->id,
                'last_four' => $stripePaymentMethod->card->last4,
                'brand' => $stripePaymentMethod->card->brand,
                'exp_month' => str_pad($stripePaymentMethod->card->exp_month, 2, '0', STR_PAD_LEFT),
                'exp_year' => (string) $stripePaymentMethod->card->exp_year,
                'is_default' => $isFirst,
                'is_active' => true,
            ]);

            // If this is the first payment method, make it default
            if ($isFirst) {
                Auth::user()->paymentMethods()->where('id', '!=', $paymentMethod->id)->update(['is_default' => false]);
            }

            Log::info('Payment method created via Stripe', [
                'user_id' => $user->id,
                'payment_method_id' => $paymentMethod->id,
                'stripe_pm_id' => $stripePaymentMethod->id,
            ]);

            return redirect()->route('payment-methods.index')->with('success', 'Payment method added successfully.');
        } catch (\Stripe\Exception\CardException $e) {
            Log::error('Stripe card error', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
            ]);
            return redirect()->back()->withErrors([
                'error' => 'Card error: ' . $e->getError()->message
            ])->withInput();
        } catch (\Exception $e) {
            Log::error('Payment method creation failed', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
            ]);
            return redirect()->back()->withErrors([
                'error' => 'Failed to add payment method: ' . $e->getMessage()
            ])->withInput();
        }
    }

    public function setDefault(PaymentMethod $paymentMethod)
    {
        // Ensure payment method belongs to user
        if ($paymentMethod->user_id !== Auth::id()) {
            abort(403);
        }

        // Remove default from all payment methods
        Auth::user()->paymentMethods()->update(['is_default' => false]);

        // Set this one as default
        $paymentMethod->update(['is_default' => true]);

        return redirect()->route('payment-methods.index')->with('success', 'Default payment method updated.');
    }

    public function destroy(PaymentMethod $paymentMethod)
    {
        // Ensure payment method belongs to user
        if ($paymentMethod->user_id !== Auth::id()) {
            abort(403);
        }

        // Don't allow deleting if it's the only payment method
        if (Auth::user()->paymentMethods()->count() === 1) {
            return redirect()->route('payment-methods.index')->with('error', 'Cannot delete your only payment method.');
        }

        try {
            // Detach from Stripe if it's a real Stripe payment method
            if ($this->stripeService->isConfigured() && 
                str_starts_with($paymentMethod->provider_payment_method_id, 'pm_')) {
                $stripePaymentMethod = \Stripe\PaymentMethod::retrieve($paymentMethod->provider_payment_method_id);
                $stripePaymentMethod->detach();
            }
        } catch (\Exception $e) {
            Log::warning('Failed to detach Stripe payment method', [
                'error' => $e->getMessage(),
                'payment_method_id' => $paymentMethod->id,
            ]);
            // Continue with deletion even if Stripe detach fails
        }

        $paymentMethod->delete();

        return redirect()->route('payment-methods.index')->with('success', 'Payment method deleted.');
    }
}
