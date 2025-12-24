<?php

namespace App\Http\Controllers;

use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentMethodController extends Controller
{
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
        // For now, we'll create a simple payment method
        // In production, this would integrate with Stripe to create a payment method
        $validated = $request->validate([
            'card_number' => ['required', 'string', 'size:16'],
            'exp_month' => ['required', 'string', 'size:2'],
            'exp_year' => ['required', 'string', 'size:4'],
            'cvc' => ['required', 'string', 'size:3'],
            'cardholder_name' => ['required', 'string', 'max:255'],
        ]);

        // Extract last 4 digits
        $lastFour = substr($validated['card_number'], -4);
        
        // Determine brand from first digit (simplified)
        $firstDigit = substr($validated['card_number'], 0, 1);
        $brand = match($firstDigit) {
            '4' => 'visa',
            '5' => 'mastercard',
            '3' => 'amex',
            default => 'unknown',
        };

        // In production, this would create a payment method via Stripe API
        // For now, we'll store a mock payment method
        $paymentMethod = PaymentMethod::create([
            'user_id' => Auth::id(),
            'type' => 'card',
            'provider' => 'stripe',
            'provider_payment_method_id' => 'pm_mock_' . uniqid(), // Mock ID
            'last_four' => $lastFour,
            'brand' => $brand,
            'exp_month' => $validated['exp_month'],
            'exp_year' => $validated['exp_year'],
            'is_default' => Auth::user()->paymentMethods()->count() === 0, // First one is default
            'is_active' => true,
        ]);

        return redirect()->route('payment-methods.index')->with('success', 'Payment method added successfully.');
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

        $paymentMethod->delete();

        return redirect()->route('payment-methods.index')->with('success', 'Payment method deleted.');
    }
}
