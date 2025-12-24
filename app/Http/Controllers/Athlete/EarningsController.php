<?php

namespace App\Http\Controllers\Athlete;

use App\Http\Controllers\Controller;
use App\Models\AthletePaymentMethod;
use App\Models\AthleteWithdrawal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EarningsController extends Controller
{
    /**
     * Show earnings dashboard
     */
    public function index()
    {
        $athlete = Auth::guard('athlete')->user();
        
        // Total earnings from all released deals (net payout after fees)
        $totalEarnings = $athlete->releasedDeals()
            ->get()
            ->sum(function($deal) {
                return $deal->athlete_net_payout ?? 
                       ($deal->escrow_amount - ($deal->escrow_amount * ($deal->athlete_fee_percentage ?? 0) / 100));
            });
        
        $availableBalance = $athlete->available_balance;
        $escrowBalance = $athlete->escrow_balance; // Funds still in escrow
        $pendingWithdrawals = $athlete->withdrawals()
            ->whereIn('status', ['pending', 'processing'])
            ->sum('amount');
        
        $paymentMethods = $athlete->paymentMethods()->where('is_active', true)->orderBy('is_default', 'desc')->get();
        $recentWithdrawals = $athlete->withdrawals()->with('paymentMethod')->orderBy('created_at', 'desc')->take(10)->get();
        
        return view('athlete.earnings.index', compact(
            'athlete',
            'totalEarnings',
            'availableBalance',
            'escrowBalance',
            'pendingWithdrawals',
            'paymentMethods',
            'recentWithdrawals'
        ));
    }

    /**
     * Show form to add payment method
     */
    public function createPaymentMethod()
    {
        return view('athlete.earnings.add-payment-method');
    }

    /**
     * Store new payment method
     */
    public function storePaymentMethod(Request $request)
    {
        $validated = $request->validate([
            'type' => ['required', 'in:stripe'],
            'provider_account_id' => ['nullable', 'string', 'max:255'],
        ]);

        $athlete = Auth::guard('athlete')->user();

        // If this is the first payment method, make it default
        $isFirst = $athlete->paymentMethods()->count() === 0;

        // If setting as default, unset other defaults
        if ($isFirst || $request->has('is_default')) {
            $athlete->paymentMethods()->update(['is_default' => false]);
        }

        $paymentMethod = AthletePaymentMethod::create([
            'athlete_id' => $athlete->id,
            'type' => 'stripe',
            'provider' => 'stripe',
            'provider_account_id' => $validated['provider_account_id'] ?? null,
            'is_default' => $isFirst,
            'is_active' => true,
        ]);

        return redirect()->route('athlete.earnings.index')
            ->with('success', 'Payment method added successfully.');
    }

    /**
     * Show form to withdraw funds
     */
    public function createWithdrawal()
    {
        $athlete = Auth::guard('athlete')->user();
        $availableBalance = $athlete->available_balance;
        $paymentMethods = $athlete->paymentMethods()->where('is_active', true)->orderBy('is_default', 'desc')->get();

        if ($paymentMethods->isEmpty()) {
            return redirect()->route('athlete.earnings.payment-method.create')
                ->with('error', 'Please add a payment method before withdrawing funds.');
        }

        return view('athlete.earnings.withdraw', compact('athlete', 'availableBalance', 'paymentMethods'));
    }

    /**
     * Process withdrawal request
     */
    public function storeWithdrawal(Request $request)
    {
        $athlete = Auth::guard('athlete')->user();
        $availableBalance = $athlete->available_balance;

        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:10', 'max:' . $availableBalance],
            'athlete_payment_method_id' => ['required', 'exists:athlete_payment_methods,id'],
        ]);

        // Ensure payment method belongs to athlete
        $paymentMethod = AthletePaymentMethod::where('id', $validated['athlete_payment_method_id'])
            ->where('athlete_id', $athlete->id)
            ->where('is_active', true)
            ->firstOrFail();

        // Check available balance
        if ($validated['amount'] > $availableBalance) {
            return redirect()->back()
                ->withErrors(['amount' => 'Insufficient available balance.'])
                ->withInput();
        }

        try {
            DB::beginTransaction();

            // Create withdrawal record
            $withdrawal = AthleteWithdrawal::create([
                'athlete_id' => $athlete->id,
                'athlete_payment_method_id' => $paymentMethod->id,
                'amount' => $validated['amount'],
                'currency' => 'USD',
                'status' => 'pending',
            ]);

            // In production, here you would:
            // 1. Call Stripe/PayPal API to initiate transfer
            // 2. Update withdrawal with provider_transaction_id
            // 3. Update status based on API response

            DB::commit();

            return redirect()->route('athlete.earnings.index')
                ->with('success', 'Withdrawal request submitted successfully. It will be processed within 1-3 business days.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withErrors(['error' => 'Failed to process withdrawal. Please try again.'])
                ->withInput();
        }
    }

    /**
     * Delete payment method
     */
    public function destroyPaymentMethod(AthletePaymentMethod $paymentMethod)
    {
        $athlete = Auth::guard('athlete')->user();

        // Ensure payment method belongs to athlete
        if ($paymentMethod->athlete_id !== $athlete->id) {
            abort(403);
        }

        // Check if there are pending withdrawals using this method
        $pendingWithdrawals = $paymentMethod->withdrawals()
            ->whereIn('status', ['pending', 'processing'])
            ->count();

        if ($pendingWithdrawals > 0) {
            return redirect()->back()
                ->withErrors(['error' => 'Cannot delete payment method with pending withdrawals.']);
        }

        $paymentMethod->update(['is_active' => false]);

        return redirect()->back()->with('success', 'Payment method removed successfully.');
    }

    /**
     * Set default payment method
     */
    public function setDefaultPaymentMethod(AthletePaymentMethod $paymentMethod)
    {
        $athlete = Auth::guard('athlete')->user();

        // Ensure payment method belongs to athlete
        if ($paymentMethod->athlete_id !== $athlete->id) {
            abort(403);
        }

        // Unset other defaults
        $athlete->paymentMethods()->update(['is_default' => false]);

        // Set this as default
        $paymentMethod->update(['is_default' => true]);

        return redirect()->back()->with('success', 'Default payment method updated.');
    }
}
