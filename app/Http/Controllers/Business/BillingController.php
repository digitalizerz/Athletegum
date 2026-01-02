<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Services\StripeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Stripe\Subscription as StripeSubscription;

class BillingController extends Controller
{
    protected StripeService $stripeService;

    public function __construct(StripeService $stripeService)
    {
        $this->stripeService = $stripeService;
    }

    /**
     * Display the business billing page
     */
    public function index()
    {
        $user = Auth::user();

        // Get subscription data from user model
        $subscriptionData = [
            'plan' => $user->subscription_plan ?? 'free',
            'status' => $user->subscription_status ?? null,
            'stripe_subscription_id' => $user->stripe_subscription_id ?? null,
            'stripe_customer_id' => $user->stripe_customer_id ?? null,
        ];

        // Defensive sync: If user has stripe_customer_id but DB shows free/null, check Stripe
        if ($user->stripe_customer_id && 
            (!$subscriptionData['status'] || $subscriptionData['plan'] === 'free')) {
            
            try {
                if ($this->stripeService->isConfigured()) {
                    // Fetch active subscriptions from Stripe for this customer
                    $subscriptions = StripeSubscription::all([
                        'customer' => $user->stripe_customer_id,
                        'status' => 'active',
                        'limit' => 1,
                    ]);

                    if (count($subscriptions->data) > 0) {
                        $stripeSubscription = $subscriptions->data[0];
                        
                        // Determine plan from subscription
                        $plan = $this->determinePlanFromSubscription($stripeSubscription);
                        
                        // Update DB to match Stripe
                        $user->update([
                            'subscription_plan' => $plan,
                            'subscription_status' => $stripeSubscription->status,
                            'stripe_subscription_id' => $stripeSubscription->id,
                        ]);

                        Log::info('Defensive sync: Updated user subscription from Stripe', [
                            'user_id' => $user->id,
                            'plan' => $plan,
                            'status' => $stripeSubscription->status,
                            'subscription_id' => $stripeSubscription->id,
                        ]);

                        // Refresh subscription data
                        $user->refresh();
                        $subscriptionData = [
                            'plan' => $user->subscription_plan ?? 'free',
                            'status' => $user->subscription_status ?? null,
                            'stripe_subscription_id' => $user->stripe_subscription_id ?? null,
                            'stripe_customer_id' => $user->stripe_customer_id ?? null,
                            'pending_subscription_plan' => $user->pending_subscription_plan ?? null,
                        ];
                    }
                }
            } catch (\Exception $e) {
                // Log but don't fail - page can still show DB data
                Log::warning('Defensive sync failed on billing page', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // Fetch subscription end date if cancelling
        $subscriptionEndDate = null;
        if ($subscriptionData['status'] === 'cancelling' && $subscriptionData['stripe_subscription_id']) {
            try {
                if ($this->stripeService->isConfigured()) {
                    $stripeSubscription = $this->stripeService->retrieveSubscription($subscriptionData['stripe_subscription_id']);
                    if ($stripeSubscription->cancel_at) {
                        $subscriptionEndDate = \Carbon\Carbon::createFromTimestamp($stripeSubscription->cancel_at);
                    } elseif ($stripeSubscription->current_period_end) {
                        $subscriptionEndDate = \Carbon\Carbon::createFromTimestamp($stripeSubscription->current_period_end);
                    }
                }
            } catch (\Exception $e) {
                Log::warning('Failed to fetch subscription end date', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // Include pending plan and end date in subscription data
        $subscriptionData['pending_subscription_plan'] = $user->pending_subscription_plan ?? null;
        $subscriptionData['end_date'] = $subscriptionEndDate;

        // Calculate usage metrics for upgrade nudges
        // Count active deals only (pending, accepted, active)
        $activeDealsCount = \App\Models\Deal::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'accepted', 'active'])
            ->count();
        
        $maxActiveDeals = \App\Support\PlanFeatures::maxActiveDeals($user);
        $hasReachedDealLimit = $maxActiveDeals !== null && $activeDealsCount >= $maxActiveDeals;

        return view('business.billing.index', [
            'subscriptionData' => $subscriptionData,
            'activeDealsCount' => $activeDealsCount,
            'hasReachedDealLimit' => $hasReachedDealLimit,
            'maxActiveDeals' => $maxActiveDeals,
        ]);
    }

    /**
     * Determine plan name from subscription (copied from StripeWebhookController)
     */
    protected function determinePlanFromSubscription($subscription): string
    {
        // Try metadata first
        if (isset($subscription->metadata) && isset($subscription->metadata->plan)) {
            $plan = $subscription->metadata->plan;
            if (in_array($plan, ['pro', 'growth', 'free'])) {
                return $plan;
            }
        }

        // Try to determine from price ID
        if (isset($subscription->items->data[0]->price->id)) {
            $priceId = $subscription->items->data[0]->price->id;
            
            $proPriceId = env('STRIPE_PRICE_PRO');
            $growthPriceId = env('STRIPE_PRICE_GROWTH');
            
            if ($priceId === $proPriceId) {
                return 'pro';
            }
            if ($priceId === $growthPriceId) {
                return 'growth';
            }
        }

        // Fallback: try to determine from amount
        if (isset($subscription->items->data[0]->price->unit_amount)) {
            $amount = $subscription->items->data[0]->price->unit_amount;
            if ($amount === 4900) {
                return 'pro';
            }
            if ($amount === 9900) {
                return 'growth';
            }
        }

        return 'free';
    }

    /**
     * Cancel subscription at period end
     */
    public function cancel(Request $request)
    {
        $user = Auth::user();

        if (!$user->stripe_subscription_id) {
            return redirect()->route('business.billing.index')
                ->withErrors(['error' => 'No active subscription found.']);
        }

        if (!$this->stripeService->isConfigured()) {
            return redirect()->route('business.billing.index')
                ->withErrors(['error' => 'Billing service is not available. Please contact support.']);
        }

        try {
            // Update Stripe subscription to cancel at period end
            $this->stripeService->cancelSubscriptionAtPeriodEnd($user->stripe_subscription_id);

            // Update DB status to 'cancelling' (webhook will confirm final state)
            $user->update([
                'subscription_status' => 'cancelling',
                // Keep subscription_plan unchanged
            ]);

            Log::info('Subscription cancellation requested', [
                'user_id' => $user->id,
                'subscription_id' => $user->stripe_subscription_id,
            ]);

            return redirect()->route('business.billing.index')
                ->with('success', 'Your subscription will be cancelled at the end of the current billing period.');
        } catch (\Exception $e) {
            Log::error('Failed to cancel subscription', [
                'user_id' => $user->id,
                'subscription_id' => $user->stripe_subscription_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->route('business.billing.index')
                ->withErrors(['error' => 'Failed to cancel subscription. Please try again or contact support.']);
        }
    }

    /**
     * Change subscription plan (downgrade/upgrade)
     */
    public function changePlan(Request $request, string $plan)
    {
        $user = Auth::user();

        // Validate plan
        if (!in_array($plan, ['pro', 'growth', 'free'])) {
            return redirect()->route('business.billing.index')
                ->withErrors(['error' => 'Invalid subscription plan.']);
        }

        // Cannot change to same plan
        if ($user->subscription_plan === $plan) {
            return redirect()->route('business.billing.index')
                ->withErrors(['error' => 'You are already on the ' . ucfirst($plan) . ' plan.']);
        }

        if (!$user->stripe_subscription_id) {
            return redirect()->route('business.billing.index')
                ->withErrors(['error' => 'No active subscription found.']);
        }

        if (!$this->stripeService->isConfigured()) {
            return redirect()->route('business.billing.index')
                ->withErrors(['error' => 'Billing service is not available. Please contact support.']);
        }

        try {
            // Get price ID for the new plan
            $priceId = $this->getPriceIdForPlan($plan);
            if (!$priceId) {
                return redirect()->route('business.billing.index')
                    ->withErrors(['error' => 'Price ID not configured for plan: ' . $plan]);
            }

            // Update Stripe subscription with new plan (no proration, unchanged billing cycle)
            $this->stripeService->changeSubscriptionPlan($user->stripe_subscription_id, $priceId);

            // Update DB with pending plan (webhook will confirm when period ends)
            $user->update([
                'pending_subscription_plan' => $plan,
                // Keep current subscription_plan until webhook confirms change
            ]);

            Log::info('Subscription plan change requested', [
                'user_id' => $user->id,
                'subscription_id' => $user->stripe_subscription_id,
                'current_plan' => $user->subscription_plan,
                'new_plan' => $plan,
            ]);

            return redirect()->route('business.billing.index')
                ->with('success', 'Your plan change will take effect at the start of your next billing cycle.');
        } catch (\Exception $e) {
            Log::error('Failed to change subscription plan', [
                'user_id' => $user->id,
                'subscription_id' => $user->stripe_subscription_id,
                'new_plan' => $plan,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->route('business.billing.index')
                ->withErrors(['error' => 'Failed to change plan. Please try again or contact support.']);
        }
    }

    /**
     * Get Stripe Price ID for a given plan
     */
    protected function getPriceIdForPlan(string $plan): ?string
    {
        $envKey = 'STRIPE_PRICE_' . strtoupper($plan);
        return env($envKey);
    }

    /**
     * Create Stripe Billing Portal session and redirect
     */
    public function portal(Request $request)
    {
        $user = Auth::user();

        if (!$user->stripe_customer_id) {
            return redirect()->route('business.billing.index')
                ->withErrors(['error' => 'No billing account found. Please contact support.']);
        }

        if (!$this->stripeService->isConfigured()) {
            return redirect()->route('business.billing.index')
                ->withErrors(['error' => 'Billing portal is not available. Please contact support.']);
        }

        try {
            $portalSession = $this->stripeService->createBillingPortalSession(
                $user->stripe_customer_id,
                route('business.billing.index')
            );

            return redirect()->away($portalSession->url);
        } catch (\Exception $e) {
            Log::error('Failed to create Stripe billing portal session', [
                'user_id' => $user->id,
                'customer_id' => $user->stripe_customer_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->route('business.billing.index')
                ->withErrors(['error' => 'Failed to open billing portal. Please try again or contact support.']);
        }
    }
}
