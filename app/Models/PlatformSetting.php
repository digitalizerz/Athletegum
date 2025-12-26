<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlatformSetting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'type',
        'description',
    ];

    /**
     * Get a setting value by key
     */
    public static function get(string $key, $default = null)
    {
        $setting = self::where('key', $key)->first();
        
        if (!$setting) {
            return $default;
        }

        return match($setting->type) {
            'number' => (float) $setting->value,
            'boolean' => (bool) $setting->value,
            'json' => json_decode($setting->value, true),
            default => $setting->value,
        };
    }

    /**
     * Set a setting value by key
     */
    public static function set(string $key, $value, string $type = 'string', ?string $description = null): void
    {
        $setting = self::firstOrNew(['key' => $key]);
        
        $setting->value = match($type) {
            'json' => json_encode($value),
            'boolean' => $value ? '1' : '0',
            default => (string) $value,
        };
        
        $setting->type = $type;
        
        if ($description) {
            $setting->description = $description;
        }
        
        $setting->save();
    }

    /**
     * Get SMB platform fee (fixed at 10% per marketplace rules)
     * Business pays: deal_amount + (deal_amount × 10%)
     */
    public static function getSMBPlatformFee(): array
    {
        return [
            'type' => 'percentage',
            'value' => 10.0, // Fixed 10% business fee - cannot be changed
        ];
    }

    /**
     * Get athlete platform fee percentage (fixed at 5% per marketplace rules)
     * Athlete receives: deal_amount - (deal_amount × 5%)
     */
    public static function getAthletePlatformFeePercentage(): float
    {
        return 5.0; // Fixed 5% athlete fee - cannot be changed
    }

    /**
     * Check if Stripe is connected (via environment variables)
     * Uses config() instead of env() to work with cached config
     */
    public static function isStripeConnected(): bool
    {
        // Use config() which reads from config/services.php (works with cached config)
        // Fallback to env() only if config is not cached
        $key = config('services.stripe.key') ?: env('STRIPE_KEY');
        $secret = config('services.stripe.secret') ?: env('STRIPE_SECRET');
        
        return !empty($key) && !empty($secret);
    }

    /**
     * Get Stripe account ID (from env or default)
     * Uses config() for consistency
     */
    public static function getStripeAccountId(): ?string
    {
        // STRIPE_ACCOUNT_ID is not in config/services.php, so use env() directly
        // But check config cache first
        return env('STRIPE_ACCOUNT_ID');
    }

    /**
     * Get Stripe mode (test or live) based on keys
     * Uses config() instead of env() to work with cached config
     */
    public static function getStripeMode(): string
    {
        // Use config() which reads from config/services.php (works with cached config)
        // Fallback to env() only if config is not cached
        $key = config('services.stripe.key') ?: env('STRIPE_KEY');
        $secret = config('services.stripe.secret') ?: env('STRIPE_SECRET');
        
        if (empty($key) || empty($secret)) {
            return 'not_configured';
        }
        
        // Check if keys are test keys
        if (str_starts_with($key, 'pk_test_') && str_starts_with($secret, 'sk_test_')) {
            return 'test';
        }
        
        if (str_starts_with($key, 'pk_live_') && str_starts_with($secret, 'sk_live_')) {
            return 'live';
        }
        
        return 'invalid';
    }

    /**
     * Get masked Stripe publishable key for display
     * Uses config() instead of env() to work with cached config
     */
    public static function getMaskedStripeKey(): ?string
    {
        // Use config() which reads from config/services.php (works with cached config)
        // Fallback to env() only if config is not cached
        $key = config('services.stripe.key') ?: env('STRIPE_KEY');
        
        if (empty($key)) {
            return null;
        }
        
        // Show first 8 chars and last 4 chars, mask the middle
        if (strlen($key) > 12) {
            return substr($key, 0, 8) . '...' . substr($key, -4);
        }
        
        return $key;
    }
}
