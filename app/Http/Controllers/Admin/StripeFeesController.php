<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PlatformSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StripeFeesController extends Controller
{
    /**
     * Show Stripe & Fees configuration page
     */
    public function index()
    {
        $stripeConnected = PlatformSetting::isStripeConnected();
        $stripeMode = PlatformSetting::getStripeMode();
        $stripeAccountId = PlatformSetting::getStripeAccountId();
        $maskedKey = PlatformSetting::getMaskedStripeKey();
        
        $smbFee = PlatformSetting::getSMBPlatformFee();
        $athleteFee = PlatformSetting::getAthletePlatformFeePercentage();

        // Get fee summary statistics
        $feeStats = $this->getFeeStatistics();

        return view('admin.superadmin.stripe-fees.index', compact(
            'stripeConnected',
            'stripeMode',
            'stripeAccountId',
            'maskedKey',
            'smbFee',
            'athleteFee',
            'feeStats'
        ));
    }

    /**
     * Verify Stripe connection (validates keys from .env)
     */
    public function verifyStripe()
    {
        // Use config() which reads from config/services.php (works with cached config)
        // Fallback to env() only if config is not cached
        $key = config('services.stripe.key') ?: env('STRIPE_KEY');
        $secret = config('services.stripe.secret') ?: env('STRIPE_SECRET');
        $isLocalOrDevelopment = in_array(config('app.env'), ['local', 'development']);
        
        $errors = [];
        
        if (empty($key)) {
            $errors[] = 'STRIPE_KEY is not set in .env file';
        }
        
        if (empty($secret)) {
            $errors[] = 'STRIPE_SECRET is not set in .env file';
        }
        
        // In local/development environments, enforce test keys only for safety
        if ($isLocalOrDevelopment) {
            if (!empty($key) && !str_starts_with($key, 'pk_test_')) {
                $errors[] = 'STRIPE_KEY must be a test key (pk_test_...). Live keys are not allowed in local/development environments.';
            }
            
            if (!empty($secret) && !str_starts_with($secret, 'sk_test_')) {
                $errors[] = 'STRIPE_SECRET must be a test key (sk_test_...). Live keys are not allowed in local/development environments.';
            }
        } else {
            // In production, validate that keys are properly formatted (either test or live)
            if (!empty($key) && !str_starts_with($key, 'pk_test_') && !str_starts_with($key, 'pk_live_')) {
                $errors[] = 'STRIPE_KEY must be a valid Stripe key (pk_test_... or pk_live_...).';
            }
            
            if (!empty($secret) && !str_starts_with($secret, 'sk_test_') && !str_starts_with($secret, 'sk_live_')) {
                $errors[] = 'STRIPE_SECRET must be a valid Stripe key (sk_test_... or sk_live_...).';
            }
        }
        
        if (!empty($errors)) {
            return redirect()->route('admin.stripe-fees.index')
                ->with('error', implode(' ', $errors));
        }
        
        // Try to make a test API call to verify keys work
        try {
            // Check if Stripe SDK is available
            if (!class_exists(\Stripe\Stripe::class)) {
                return redirect()->route('admin.stripe-fees.index')
                    ->with('error', 'Stripe PHP SDK is not installed. Please run: composer require stripe/stripe-php');
            }
            
            \Stripe\Stripe::setApiKey($secret);
            \Stripe\Account::retrieve(); // Simple API call to verify
        } catch (\Stripe\Exception\AuthenticationException $e) {
            return redirect()->route('admin.stripe-fees.index')
                ->with('error', 'Stripe authentication failed. Please check your keys are correct.');
        } catch (\Exception $e) {
            return redirect()->route('admin.stripe-fees.index')
                ->with('error', 'Stripe verification failed: ' . $e->getMessage());
        }
        
        $mode = str_starts_with($key, 'pk_test_') ? 'Test Mode' : 'Live Mode';
        $logsLocation = str_starts_with($key, 'pk_test_') 
            ? 'Stripe Dashboard → Test Mode → Logs' 
            : 'Stripe Dashboard → Live Mode → Logs';
        
        return redirect()->route('admin.stripe-fees.index')
            ->with('success', "Stripe connection verified successfully! Keys are valid and working in {$mode}. API calls will appear in {$logsLocation}.");
    }

    /**
     * Update SMB platform fee
     */
    public function updateSMBFee(Request $request)
    {
        $request->validate([
            'fee_type' => ['required', 'in:percentage,fixed'],
            'fee_value' => ['required', 'numeric', 'min:0'],
        ]);

        PlatformSetting::set('smb_platform_fee_type', $request->fee_type);
        PlatformSetting::set('smb_platform_fee_value', $request->fee_value, 'number');

        return redirect()->route('admin.stripe-fees.index')
            ->with('success', 'SMB platform fee updated successfully.');
    }

    /**
     * Update athlete platform fee
     */
    public function updateAthleteFee(Request $request)
    {
        $request->validate([
            'fee_percentage' => ['required', 'numeric', 'min:0', 'max:100'],
        ]);

        PlatformSetting::set('athlete_platform_fee_percentage', $request->fee_percentage, 'number');

        return redirect()->route('admin.stripe-fees.index')
            ->with('success', 'Athlete platform fee updated successfully.');
    }

    /**
     * Get fee statistics for reporting
     */
    private function getFeeStatistics()
    {
        // This would query actual deal/payment data
        // For now, return placeholder structure
        return [
            'total_platform_fees' => DB::table('deals')
                ->where('payment_status', 'paid')
                ->sum('platform_fee_amount') ?? 0,
            'total_smb_fees' => DB::table('deals')
                ->where('payment_status', 'paid')
                ->sum('platform_fee_amount') ?? 0,
            'total_athlete_fees' => DB::table('deals')
                ->where('payment_status', 'paid')
                ->whereNotNull('released_at')
                ->sum(DB::raw('(escrow_amount * athlete_fee_percentage / 100)')) ?? 0,
            'total_athlete_payouts_gross' => DB::table('deals')
                ->where('payment_status', 'paid')
                ->whereNotNull('released_at')
                ->sum('escrow_amount') ?? 0,
            'total_athlete_payouts_net' => DB::table('deals')
                ->where('payment_status', 'paid')
                ->whereNotNull('released_at')
                ->sum(DB::raw('escrow_amount - (escrow_amount * athlete_fee_percentage / 100)')) ?? 0,
            'total_smb_charges' => DB::table('deals')
                ->where('payment_status', 'paid')
                ->sum(DB::raw('compensation_amount + platform_fee_amount')) ?? 0,
        ];
    }
}
