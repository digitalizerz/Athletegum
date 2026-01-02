<?php

namespace App\Http\Controllers;

use App\Services\StripeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SubscriptionController extends Controller
{
    protected StripeService $stripeService;

    public function __construct(StripeService $stripeService)
    {
        $this->stripeService = $stripeService;
    }

    /**
     * Create checkout session for subscription
     */
    public function checkout(Request $request, string $plan)
    {
        // Validate plan from route parameter
        if (!in_array($plan, ['pro', 'growth'])) {
            return redirect()->route('dashboard')->withErrors(['error' => 'Invalid subscription plan.']);
        }

        $user = Auth::user();

        // Only block checkout if user has an ACTIVE subscription
        // Allow checkout for: free, cancelled, trialing, past_due, or null status
        if ($user->subscription_status === 'active') {
            return redirect()->route('dashboard')->with('info', 'You already have an active subscription.');
        }

        if (!$this->stripeService->isConfigured()) {
            return redirect()->back()->withErrors(['error' => 'Stripe is not configured. Please contact support.']);
        }

        try {
            $checkoutSession = $this->stripeService->createSubscriptionCheckoutSession(
                $user->id,
                $user->email,
                $user->name,
                $plan
            );

            return redirect($checkoutSession->url);
        } catch (\Exception $e) {
            Log::error('Failed to create subscription checkout session', [
                'user_id' => $user->id,
                'plan' => $plan,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()->withErrors(['error' => 'Failed to create checkout session. Please try again.']);
        }
    }

    /**
     * Handle successful subscription checkout
     */
    public function success(Request $request)
    {
        $sessionId = $request->get('session_id');

        if (!$sessionId) {
            return redirect()->route('dashboard')->withErrors(['error' => 'Invalid checkout session.']);
        }

        try {
            // The webhook will handle updating the subscription
            // This page just shows success
            return view('subscriptions.success');
        } catch (\Exception $e) {
            Log::error('Failed to process subscription success', [
                'session_id' => $sessionId,
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('dashboard')->withErrors(['error' => 'Subscription processing error.']);
        }
    }

    /**
     * Handle cancelled subscription checkout
     */
    public function cancel(Request $request)
    {
        return redirect()->route('dashboard')->with('info', 'Subscription checkout was cancelled.');
    }
}

