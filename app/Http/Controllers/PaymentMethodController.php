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
        return view('payment-methods.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'card_number' => ['required', 'string', 'size:16'],
            'exp_month' => ['required', 'string', 'size:2'],
            'exp_year' => ['required', 'string', 'size:4'],
            'cvc' => ['required', 'string', 'size:3'],
            'cardholder_name' => ['required', 'string', 'max:255'],
        ]);

        try {
            // Check if Stripe is configured
            if (!$this->stripeService->isConfigured()) {
                return redirect()->back()->withErrors([
                    'error' => 'Stripe is not configured. Please contact support.'
                ])->withInput();
            }

            $user = Auth::user();

            // Create or get Stripe customer
            $customerId = $this->stripeService->getOrCreateCustomer(
                $user->id,
                $user->email,
                $user->name
            );

            // Create payment method in Stripe
            $stripePaymentMethod = $this->stripeService->createPaymentMethod([
                'number' => $validated['card_number'],
                'exp_month' => (int) $validated['exp_month'],
                'exp_year' => (int) $validated['exp_year'],
                'cvc' => $validated['cvc'],
            ]);

            // Attach to customer
            $this->stripeService->attachPaymentMethodToCustomer(
                $stripePaymentMethod->id,
                $customerId
            );

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
