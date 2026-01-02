<?php

namespace App\Support;

use App\Models\User;

class PlanFeatures
{
    /**
     * Plan capabilities (SOURCE OF TRUTH)
     */
    protected static array $capabilities = [
        'free' => [
            'athlete_search' => 'limited',
            'athlete_filters' => false,
            'max_active_deals' => 3,
            'revenue_dashboard' => false,
            'priority_placement' => false,
            'advanced_analytics' => false,
        ],
        'pro' => [
            'athlete_search' => true,
            'athlete_filters' => true,
            'max_active_deals' => null, // unlimited
            'revenue_dashboard' => true,
            'priority_placement' => false,
            'advanced_analytics' => false,
        ],
        'growth' => [
            'athlete_search' => true,
            'athlete_filters' => true,
            'max_active_deals' => null, // unlimited
            'revenue_dashboard' => true,
            'priority_placement' => true,
            'advanced_analytics' => true,
        ],
    ];

    /**
     * Check if user can use a specific feature
     * 
     * @param User $user
     * @param string $feature
     * @return bool|string Returns true if fully enabled, 'limited' if limited access, false if disabled
     */
    public static function canUseFeature(User $user, string $feature): bool|string
    {
        $plan = self::getUserPlan($user);
        $capabilities = self::getCapabilities($plan);
        
        return $capabilities[$feature] ?? false;
    }

    /**
     * Get max active deals for user
     * 
     * @param User $user
     * @return int|null Returns max deals (null = unlimited)
     */
    public static function maxActiveDeals(User $user): ?int
    {
        $plan = self::getUserPlan($user);
        $capabilities = self::getCapabilities($plan);
        
        return $capabilities['max_active_deals'];
    }

    /**
     * Get user's plan (defaults to 'free')
     * 
     * @param User $user
     * @return string
     */
    protected static function getUserPlan(User $user): string
    {
        $plan = $user->subscription_plan ?? 'free';
        
        // Only consider active subscriptions
        if ($plan !== 'free' && $user->subscription_status !== 'active') {
            return 'free';
        }
        
        return $plan;
    }

    /**
     * Get capabilities for a plan
     * 
     * @param string $plan
     * @return array
     */
    protected static function getCapabilities(string $plan): array
    {
        return self::$capabilities[$plan] ?? self::$capabilities['free'];
    }

    /**
     * Check if user has full access to a feature (not limited)
     * 
     * @param User $user
     * @param string $feature
     * @return bool
     */
    public static function hasFullAccess(User $user, string $feature): bool
    {
        $access = self::canUseFeature($user, $feature);
        return $access === true;
    }

    /**
     * Check if user has any access (full or limited) to a feature
     * 
     * @param User $user
     * @param string $feature
     * @return bool
     */
    public static function hasAccess(User $user, string $feature): bool
    {
        $access = self::canUseFeature($user, $feature);
        return $access !== false;
    }
}

