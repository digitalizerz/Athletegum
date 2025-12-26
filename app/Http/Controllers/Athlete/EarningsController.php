<?php

namespace App\Http\Controllers\Athlete;

use App\Http\Controllers\Controller;
use App\Models\AthletePaymentMethod;
use App\Models\AthleteWithdrawal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Stripe\Account as StripeAccount;
use Stripe\Exception\InvalidRequestException;

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
        $stripeService = app(\App\Services\StripeService::class);
        $isStripeConfigured = $stripeService->isConfigured();
        
        return view('athlete.earnings.add-payment-method', [
            'isStripeConfigured' => $isStripeConfigured,
            'stripePublishableKey' => $isStripeConfigured ? $stripeService->getPublishableKey() : null,
        ]);
    }

    /**
     * Initiate Stripe Connect OAuth flow
     */
    public function initiateStripeConnect(Request $request)
    {
        $athlete = Auth::guard('athlete')->user();
        $stripeService = app(\App\Services\StripeService::class);

        if (!$stripeService->isConfigured()) {
            return redirect()->back()->withErrors([
                'error' => 'Stripe is not configured. Please contact support.'
            ]);
        }

        try {
            // Create or retrieve Stripe Connect Express account
            $stripeAccountId = $athlete->stripe_account_id;
            
            if (!$stripeAccountId) {
                // Create new Express account
                $account = $stripeService->createExpressAccount(
                    $athlete->email,
                    'US' // Default to US, can be made configurable
                );
                $stripeAccountId = $account->id;
                
                // Save account ID to athlete
                $athlete->update(['stripe_account_id' => $stripeAccountId]);
            } else {
                // Verify account exists
                $stripeService->retrieveAccount($stripeAccountId);
            }

            // Create account link for onboarding/connecting
            $returnUrl = route('athlete.earnings.stripe-connect.callback', ['athlete' => $athlete->id]);
            $refreshUrl = route('athlete.earnings.stripe-connect.refresh', ['athlete' => $athlete->id]);
            
            $accountLink = $stripeService->createAccountLink($stripeAccountId, $returnUrl, $refreshUrl);

            // Redirect to Stripe
            return redirect($accountLink->url);
        } catch (\Exception $e) {
            Log::error('Failed to initiate Stripe Connect', [
                'athlete_id' => $athlete->id,
                'error' => $e->getMessage(),
            ]);
            
            return redirect()->back()->withErrors([
                'error' => 'Failed to initiate Stripe connection: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Handle Stripe Connect OAuth callback
     */
    public function handleStripeConnectCallback(Request $request, $athleteId)
    {
        $athlete = Auth::guard('athlete')->user();
        
        // Verify athlete ID matches logged-in athlete
        if ($athlete->id != $athleteId) {
            abort(403, 'Unauthorized');
        }

        $stripeService = app(\App\Services\StripeService::class);
        $stripeAccountId = $athlete->stripe_account_id;

        if (!$stripeAccountId) {
            return redirect()->route('athlete.earnings.index')
                ->withErrors(['error' => 'No Stripe account found. Please try connecting again.']);
        }

        try {
            // Retrieve account to check status
            $account = $stripeService->retrieveAccount($stripeAccountId);
            
            // Check account status
            $chargesEnabled = $account->charges_enabled ?? false;
            $detailsSubmitted = $account->details_submitted ?? false;
            $payoutsEnabled = $account->payouts_enabled ?? false;

            // Save payment method record (even if verification isn't complete)
            // The account is connected, even if it needs additional verification
            $paymentMethod = AthletePaymentMethod::updateOrCreate(
                [
                    'athlete_id' => $athlete->id,
                    'provider_account_id' => $stripeAccountId,
                ],
                [
                    'type' => 'stripe',
                    'provider' => 'stripe',
                    'is_active' => true,
                ]
            );

            // If this is the first active payment method, make it default
            $activePaymentMethodsCount = $athlete->paymentMethods()
                ->where('is_active', true)
                ->where('id', '!=', $paymentMethod->id)
                ->count();
            
            if ($activePaymentMethodsCount === 0) {
                // This is the first (or only) active payment method, make it default
                // Also unset other defaults
                $athlete->paymentMethods()->update(['is_default' => false]);
                $paymentMethod->update(['is_default' => true]);
            }

            Log::info('Stripe Connect callback processed', [
                'athlete_id' => $athlete->id,
                'stripe_account_id' => $stripeAccountId,
                'payment_method_id' => $paymentMethod->id,
                'charges_enabled' => $chargesEnabled,
                'details_submitted' => $detailsSubmitted,
                'payouts_enabled' => $payoutsEnabled,
            ]);

            // Determine success message based on account status
            if ($chargesEnabled && $detailsSubmitted && $payoutsEnabled) {
                // Fully verified and ready
                return redirect()->route('athlete.earnings.index')
                    ->with('success', 'Stripe account connected successfully! You can now receive payouts.');
            } elseif ($detailsSubmitted) {
                // Onboarding completed but may need additional verification
                return redirect()->route('athlete.earnings.index')
                    ->with('success', 'Stripe account connected! Some verification steps may still be pending. You\'ll be notified when your account is fully activated.');
            } else {
                // Still in onboarding
                return redirect()->route('athlete.earnings.index')
                    ->with('warning', 'Stripe account connection started. Please complete the verification process in your Stripe Dashboard to enable payouts.');
            }
        } catch (\Exception $e) {
            Log::error('Failed to handle Stripe Connect callback', [
                'athlete_id' => $athlete->id,
                'error' => $e->getMessage(),
            ]);
            
            return redirect()->route('athlete.earnings.index')
                ->withErrors(['error' => 'Failed to complete Stripe connection: ' . $e->getMessage()]);
        }
    }

    /**
     * Handle Stripe Connect refresh (when user needs to complete onboarding)
     */
    public function handleStripeConnectRefresh(Request $request, $athleteId)
    {
        // Same as callback - redirect back to initiate connection
        return $this->initiateStripeConnect($request);
    }

    /**
     * Store new payment method
     */
    public function storePaymentMethod(Request $request)
    {
        $validated = $request->validate([
            'type' => ['required', 'in:stripe'],
            'provider_account_id' => ['nullable', 'string', 'max:255', 'starts_with:acct_'],
        ], [
            'provider_account_id.starts_with' => 'Stripe Account ID must start with "acct_".',
        ]);

        $athlete = Auth::guard('athlete')->user();
        $stripeAccountId = $validated['provider_account_id'] ?? null;

        // If no account ID provided, check if athlete already has one from OAuth
        if (!$stripeAccountId) {
            $stripeAccountId = $athlete->stripe_account_id;
        }

        // Still need an account ID
        if (!$stripeAccountId) {
            return redirect()->back()
                ->withErrors(['provider_account_id' => 'Either connect via OAuth above or enter a Stripe Account ID.'])
                ->withInput();
        }

        // Validate that the Stripe account ID actually exists and is accessible
        try {
            $stripeService = app(\App\Services\StripeService::class);
            if ($stripeService->isConfigured()) {
                // Verify the account exists by trying to retrieve it
                $account = StripeAccount::retrieve($stripeAccountId);
                
                // Check if it's the platform's own account (shouldn't be)
                $platformAccount = StripeAccount::retrieve();
                if ($account->id === $platformAccount->id) {
                    return redirect()->back()
                        ->withErrors(['provider_account_id' => 'You cannot use the platform\'s Stripe account. Please use your own Stripe Connect account ID from your Stripe Dashboard.'])
                        ->withInput();
                }
            }
        } catch (InvalidRequestException $e) {
            // Account doesn't exist or is invalid
            $errorMessage = $e->getMessage();
            if (str_contains($errorMessage, 'No such account')) {
                $errorMessage = 'The Stripe account ID does not exist. Please verify you entered the correct account ID from your Stripe Dashboard → Settings → Connect → Accounts.';
            }
            return redirect()->back()
                ->withErrors(['provider_account_id' => $errorMessage])
                ->withInput();
        } catch (\Exception $e) {
            // Log but allow to continue (might be a connectivity issue)
            Log::warning('Could not validate Stripe account ID', [
                'account_id' => $stripeAccountId,
                'error' => $e->getMessage(),
            ]);
        }

        // If this is the first payment method, make it default
        $isFirst = $athlete->paymentMethods()->count() === 0;

        // If setting as default, unset other defaults
        if ($isFirst || $request->has('is_default')) {
            $athlete->paymentMethods()->update(['is_default' => false]);
        }
        
        // Save Stripe account ID to the athlete record
        if ($stripeAccountId) {
            $athlete->update(['stripe_account_id' => $stripeAccountId]);
        }

        $paymentMethod = AthletePaymentMethod::create([
            'athlete_id' => $athlete->id,
            'type' => 'stripe',
            'provider' => 'stripe',
            'provider_account_id' => $stripeAccountId,
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
     * Delete payment method (disconnect Stripe account)
     */
    public function destroyPaymentMethod(Request $request, $paymentMethodId)
    {
        // Log immediately when method is called
        Log::info('=== destroyPaymentMethod CALLED ===', [
            'payment_method_id' => $paymentMethodId,
            'request_method' => $request->method(),
            'request_url' => $request->fullUrl(),
            'request_headers' => $request->headers->all(),
        ]);
        
        try {
            $athlete = Auth::guard('athlete')->user();
            
            if (!$athlete) {
                Log::error('No authenticated athlete found');
                return redirect()->route('athlete.login')
                    ->withErrors(['error' => 'You must be logged in to perform this action.']);
            }

            Log::info('Attempting to delete athlete payment method', [
                'athlete_id' => $athlete->id,
                'payment_method_id' => $paymentMethodId,
                'request_method' => $request->method(),
                'is_ajax' => $request->ajax(),
                'user_agent' => $request->userAgent(),
                'ip' => $request->ip(),
                'csrf_token' => $request->header('X-CSRF-TOKEN') ? 'present' : 'missing',
            ]);

            // Find the payment method and ensure it belongs to athlete
            $paymentMethod = AthletePaymentMethod::where('id', $paymentMethodId)
                ->where('athlete_id', $athlete->id)
                ->first();

            if (!$paymentMethod) {
                Log::warning('Payment method not found or does not belong to athlete', [
                    'athlete_id' => $athlete->id,
                    'payment_method_id' => $paymentMethodId,
                ]);
                return redirect()->back()->withErrors([
                    'error' => 'Payment method not found or you do not have permission to delete it.'
                ]);
            }

            // Check if there are pending withdrawals using this method
            $pendingWithdrawals = $paymentMethod->withdrawals()
                ->whereIn('status', ['pending', 'processing'])
                ->count();

            if ($pendingWithdrawals > 0) {
                Log::warning('Cannot delete payment method - pending withdrawals exist', [
                    'payment_method_id' => $paymentMethodId,
                    'pending_withdrawals' => $pendingWithdrawals,
                ]);
                return redirect()->back()
                    ->withErrors(['error' => 'Cannot delete payment method with pending withdrawals.']);
            }

            // Store account ID before deletion for comparison
            $accountIdToDelete = $paymentMethod->provider_account_id;

            // CRITICAL: The foreign key constraint uses 'restrict', which means we MUST delete
            // ALL related withdrawals before deleting the payment method
            // Otherwise, the database will prevent deletion
            $allWithdrawals = $paymentMethod->withdrawals()->get();
            $withdrawalCount = $allWithdrawals->count();
            
            if ($withdrawalCount > 0) {
                Log::info('Found withdrawals associated with payment method, deleting them before deletion', [
                    'payment_method_id' => $paymentMethodId,
                    'withdrawal_count' => $withdrawalCount,
                    'withdrawal_statuses' => $allWithdrawals->pluck('status')->toArray(),
                ]);
                
                try {
                    // Delete ALL withdrawals (not just completed/failed) since constraint is 'restrict'
                    // We already checked for pending/processing above, so these should be safe to delete
                    // Use DB facade to ensure direct deletion
                    $deletedCount = DB::table('athlete_withdrawals')
                        ->where('athlete_payment_method_id', $paymentMethodId)
                        ->delete();
                    
                    Log::info('Deleted all withdrawals associated with payment method', [
                        'payment_method_id' => $paymentMethodId,
                        'deleted_count' => $deletedCount,
                    ]);
                    
                    // Verify deletion was successful
                    $remainingWithdrawals = DB::table('athlete_withdrawals')
                        ->where('athlete_payment_method_id', $paymentMethodId)
                        ->count();
                    
                    if ($remainingWithdrawals > 0) {
                        Log::error('Still have withdrawals after cleanup - cannot delete payment method', [
                            'payment_method_id' => $paymentMethodId,
                            'remaining_count' => $remainingWithdrawals,
                        ]);
                        return redirect()->back()
                            ->withErrors(['error' => 'Cannot delete payment method. Please contact support to resolve withdrawal records.']);
                    }
                } catch (\Exception $e) {
                    Log::error('Failed to clean up withdrawals before deletion', [
                        'payment_method_id' => $paymentMethodId,
                        'error' => $e->getMessage(),
                        'error_code' => $e->getCode(),
                        'sql_state' => $e->getCode(),
                    ]);
                    return redirect()->back()
                        ->withErrors(['error' => 'Failed to delete payment method due to database constraints: ' . $e->getMessage()]);
                }
            }

            // Actually delete the payment method record
            // Use DB facade for direct deletion to avoid any model-level issues or caching
            try {
                $deleted = DB::table('athlete_payment_methods')
                    ->where('id', $paymentMethodId)
                    ->where('athlete_id', $athlete->id)
                    ->delete();
                
                if ($deleted === 0) {
                    Log::warning('Payment method deletion returned 0 rows affected', [
                        'payment_method_id' => $paymentMethodId,
                        'athlete_id' => $athlete->id,
                    ]);
                    return redirect()->back()
                        ->withErrors(['error' => 'Payment method could not be deleted. It may have already been deleted or does not exist.']);
                }
                
                Log::info('Payment method record deleted from database', [
                    'payment_method_id' => $paymentMethodId,
                    'rows_deleted' => $deleted,
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to delete payment method record', [
                    'payment_method_id' => $paymentMethodId,
                    'athlete_id' => $athlete->id,
                    'error' => $e->getMessage(),
                    'error_code' => $e->getCode(),
                    'error_trace' => substr($e->getTraceAsString(), 0, 500), // Limit trace length
                ]);
                
                // Check if it's a foreign key constraint error
                if (str_contains($e->getMessage(), 'foreign key') || 
                    str_contains($e->getMessage(), '1451') || 
                    str_contains($e->getMessage(), '23000') ||
                    $e->getCode() === 23000) {
                    return redirect()->back()
                        ->withErrors(['error' => 'Cannot delete payment method because it is still referenced by withdrawal records. Please contact support.']);
                }
                
                throw $e; // Re-throw to be caught by outer catch
            }

            // Refresh athlete to get latest data
            $athlete->refresh();

            // If this payment method's account ID matches the athlete's stripe_account_id, clear it
            // Also check if this was the only active payment method - if so, clear athlete's stripe_account_id
            if ($accountIdToDelete && $athlete->stripe_account_id === $accountIdToDelete) {
                // Check if there are any other active payment methods using DB facade
                $hasOtherActiveMethods = DB::table('athlete_payment_methods')
                    ->where('athlete_id', $athlete->id)
                    ->where('is_active', true)
                    ->where('id', '!=', $paymentMethodId) // Exclude the one we just deleted
                    ->exists();
                
                if (!$hasOtherActiveMethods) {
                    // No other active payment methods, clear the athlete's stripe_account_id
                    DB::table('athletes')
                        ->where('id', $athlete->id)
                        ->update(['stripe_account_id' => null]);
                    $athlete->refresh();
                } else {
                    // Get the account ID from another active payment method
                    $otherPaymentMethod = DB::table('athlete_payment_methods')
                        ->where('athlete_id', $athlete->id)
                        ->where('is_active', true)
                        ->where('id', '!=', $paymentMethodId)
                        ->whereNotNull('provider_account_id')
                        ->orderBy('is_default', 'desc')
                        ->orderBy('created_at', 'desc')
                        ->first();
                    
                    if ($otherPaymentMethod && $otherPaymentMethod->provider_account_id) {
                        DB::table('athletes')
                            ->where('id', $athlete->id)
                            ->update(['stripe_account_id' => $otherPaymentMethod->provider_account_id]);
                        $athlete->refresh();
                    } else {
                        DB::table('athletes')
                            ->where('id', $athlete->id)
                            ->update(['stripe_account_id' => null]);
                        $athlete->refresh();
                    }
                }
            }

            Log::info('Payment method deleted successfully', [
                'athlete_id' => $athlete->id,
                'payment_method_id' => $paymentMethodId,
                'stripe_account_id' => $accountIdToDelete,
            ]);

            return redirect()->route('athlete.earnings.index')
                ->with('success', 'Stripe account deleted successfully.');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('Payment method not found for deletion', [
                'payment_method_id' => $paymentMethodId,
                'error' => $e->getMessage(),
            ]);
            return redirect()->back()->withErrors([
                'error' => 'Payment method not found.'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to delete payment method', [
                'payment_method_id' => $paymentMethodId,
                'athlete_id' => $athlete->id ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->back()->withErrors([
                'error' => 'Failed to delete payment method: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Delete payment method via GET (fallback method for production issues)
     * This is a simpler approach that avoids form submission issues
     */
    public function destroyPaymentMethodGet(Request $request, $paymentMethodId)
    {
        // Require confirmation token to prevent accidental deletions
        if (!$request->has('confirm') || $request->get('confirm') !== 'yes') {
            return redirect()->route('athlete.earnings.index')
                ->withErrors(['error' => 'Deletion requires confirmation. Please use the delete button in the modal.']);
        }

        // Use the same logic as the POST method
        return $this->destroyPaymentMethod($request, $paymentMethodId);
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
