<?php

namespace App\Http\Controllers;

use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WalletController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $transactions = $user->walletTransactions()
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('wallet.index', [
            'walletBalance' => $user->wallet_balance,
            'transactions' => $transactions,
        ]);
    }

    public function showAddFunds()
    {
        $paymentMethods = Auth::user()->paymentMethods()->where('is_active', true)->orderBy('is_default', 'desc')->get();

        if ($paymentMethods->isEmpty()) {
            return redirect()->route('payment-methods.create')->with('info', 'Please add a payment method before adding funds to your wallet.');
        }

        return view('wallet.add-funds', [
            'paymentMethods' => $paymentMethods,
        ]);
    }

    public function addFunds(Request $request)
    {
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:1', 'max:10000'],
            'payment_method_id' => ['required', 'exists:payment_methods,id'],
            'provider' => ['required', 'in:stripe,paypal'],
        ]);

        $paymentMethod = \App\Models\PaymentMethod::findOrFail($validated['payment_method_id']);
        
        // Ensure payment method belongs to user
        if ($paymentMethod->user_id !== Auth::id()) {
            abort(403);
        }

        $amount = (float) $validated['amount'];
        $user = Auth::user();

        try {
            DB::beginTransaction();

            // In production, this would process payment via Stripe/PayPal
            // For now, we'll simulate payment processing
            $providerTransactionId = $validated['provider'] . '_' . uniqid();
            
            // Add funds to wallet
            $transaction = $user->addToWallet($amount, 'deposit', null, [
                'payment_method_id' => $paymentMethod->id,
                'provider' => $validated['provider'],
                'provider_transaction_id' => $providerTransactionId,
            ]);

            // Update transaction with payment details
            $transaction->update([
                'payment_method' => $validated['provider'],
                'payment_provider_transaction_id' => $providerTransactionId,
                'description' => "Wallet deposit via {$validated['provider']}",
            ]);

            DB::commit();

            return redirect()->route('wallet.index')->with('success', 'Funds added to wallet successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Failed to add funds. Please try again.']);
        }
    }
}
