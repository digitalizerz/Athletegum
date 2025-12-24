<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Athlete;
use App\Models\AuditLog;
use App\Models\Deal;
use App\Models\User;
use App\Models\WalletTransaction;
use App\Models\AthleteWithdrawal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SuperAdminController extends Controller
{
    // Middleware is handled in routes

    /**
     * Super Admin Dashboard
     */
    public function index()
    {
        // Platform Stats
        $stats = [
            'total_users' => User::where('is_superadmin', false)->count(),
            'total_athletes' => Athlete::count(),
            'total_deals' => Deal::count(),
            'active_deals' => Deal::whereIn('status', ['pending', 'sent', 'active'])->count(),
            'completed_deals' => Deal::where('status', 'completed')->count(),
            'total_platform_fees' => Deal::where('payment_status', 'paid')->sum('platform_fee_amount'),
            'total_escrow' => Deal::where('payment_status', 'paid')->whereNull('released_at')->sum('escrow_amount'),
            'pending_withdrawals' => AthleteWithdrawal::whereIn('status', ['pending', 'processing'])->sum('amount'),
        ];

        // Recent Activity
        $recentLogs = AuditLog::with('admin')->latest()->take(10)->get();
        $recentDeals = Deal::with(['user', 'athlete'])->latest()->take(10)->get();

        return view('admin.superadmin.dashboard', compact('stats', 'recentLogs', 'recentDeals'));
    }

    /**
     * User Management
     */
    public function users(Request $request)
    {
        $query = User::where('is_superadmin', false);

        // Filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            // Add status filter if you implement soft deletes or active status
        }

        $users = $query->withCount(['deals', 'walletTransactions'])->latest()->paginate(50);

        return view('admin.superadmin.users.index', compact('users'));
    }

    /**
     * View user details
     */
    public function showUser(User $user)
    {
        if ($user->is_superadmin) {
            abort(403);
        }

        $user->loadCount(['deals', 'walletTransactions']);
        $deals = $user->deals()->with('athlete')->latest()->paginate(20);
        $transactions = $user->walletTransactions()->latest()->paginate(20);

        return view('admin.superadmin.users.show', compact('user', 'deals', 'transactions'));
    }

    /**
     * Suspend user
     */
    public function suspendUser(Request $request, User $user)
    {
        if ($user->is_superadmin) {
            abort(403);
        }

        $user->update(['email_verified_at' => null]); // Simple suspension method

        AuditLog::log(
            'user.suspended',
            User::class,
            $user->id,
            "User {$user->email} was suspended",
            ['reason' => $request->reason ?? null]
        );

        return redirect()->back()->with('success', 'User suspended successfully.');
    }

    /**
     * Reactivate user
     */
    public function reactivateUser(User $user)
    {
        if ($user->is_superadmin) {
            abort(403);
        }

        try {
            $user->email_verified_at = now();
            $user->save();
            
            // Refresh from database to ensure we have the latest data
            $user->refresh();
            
            // Verify the update worked
            if (!$user->email_verified_at) {
                return redirect()->back()->withErrors(['error' => 'Failed to reactivate user. Please try again.']);
            }

            AuditLog::log(
                'user.reactivated',
                User::class,
                $user->id,
                "User {$user->email} was reactivated"
            );

            return redirect()->route('admin.users.index')->with('success', 'User reactivated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Failed to reactivate user: ' . $e->getMessage()]);
        }
    }

    /**
     * Impersonate user
     */
    public function impersonateUser(User $user)
    {
        if ($user->is_superadmin) {
            abort(403);
        }

        session()->put('impersonating', $user->id);
        session()->put('original_admin_id', Auth::id());

        AuditLog::log(
            'user.impersonated',
            User::class,
            $user->id,
            "Admin impersonated user {$user->email}"
        );

        Auth::login($user);

        return redirect()->route('dashboard')->with('success', 'Impersonating user. Use admin panel to stop.');
    }

    /**
     * Bulk delete users
     */
    public function bulkDeleteUsers(Request $request)
    {
        $request->validate([
            'user_ids' => ['required', 'array'],
            'user_ids.*' => ['required', 'exists:users,id'],
        ]);

        $userIds = $request->user_ids;
        $users = User::whereIn('id', $userIds)
            ->where('is_superadmin', false)
            ->get();

        if ($users->count() !== count($userIds)) {
            return redirect()->back()
                ->withErrors(['error' => 'Some users could not be found or are super admins.']);
        }

        $count = $users->count();
        $users->each(function($user) {
            AuditLog::log(
                'user.deleted',
                User::class,
                $user->id,
                "User {$user->email} was deleted by admin"
            );
        });

        User::whereIn('id', $userIds)->delete();

        return redirect()->route('admin.users.index')
            ->with('success', "{$count} user(s) deleted successfully.");
    }

    /**
     * Stop impersonating
     */
    public function stopImpersonating()
    {
        $originalAdminId = session()->get('original_admin_id');
        
        if (!$originalAdminId) {
            // If no original admin ID, check if current user is super admin
            if (Auth::check() && Auth::user()->is_superadmin) {
                return redirect()->route('admin.dashboard');
            }
            return redirect()->route('dashboard');
        }

        $admin = User::findOrFail($originalAdminId);
        
        // Logout current user (whether it's a regular user or athlete)
        Auth::logout();
        if (Auth::guard('athlete')->check()) {
            Auth::guard('athlete')->logout();
        }
        
        // Login as the original admin
        Auth::login($admin);

        session()->forget(['impersonating', 'original_admin_id']);

        return redirect()->route('admin.dashboard')->with('success', 'Stopped impersonating.');
    }

    /**
     * Deal Management
     */
    public function deals(Request $request)
    {
        $query = Deal::with(['user', 'athlete']);

        // Filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('deal_type', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%")
                  ->orWhereHas('user', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $deals = $query->latest()->paginate(50);

        return view('admin.superadmin.deals.index', compact('deals'));
    }

    /**
     * View deal details
     */
    public function showDeal(Deal $deal)
    {
        $deal->load(['user', 'athlete']);
        return view('admin.superadmin.deals.show', compact('deal'));
    }

    /**
     * Show deal messages (Super Admin - Read-only)
     */
    public function showDealMessages(Deal $deal)
    {
        $deal->load(['user', 'athlete']);
        $messages = $deal->messages()->with(['sender', 'athleteSender'])->get();
        
        return view('admin.superadmin.deals.messages', [
            'deal' => $deal,
            'messages' => $messages,
        ]);
    }

    /**
     * Cancel deal and return escrowed funds to SMB
     */
    public function cancelDeal(Request $request, Deal $deal)
    {
        if (in_array($deal->status, ['completed', 'cancelled'])) {
            return redirect()->back()->withErrors(['error' => 'Cannot cancel a deal that is already completed or cancelled.']);
        }

        try {
            DB::beginTransaction();

            // If deal has escrowed funds, return them to SMB
            if ($deal->payment_status === 'paid' && $deal->released_at === null && $deal->escrow_amount > 0) {
                $escrowAmount = (float) $deal->escrow_amount;
                $platformFeeAmount = (float) ($deal->platform_fee_amount ?? 0);
                $totalToReturn = $escrowAmount + $platformFeeAmount; // Return both escrow and platform fee

                // Return funds to SMB wallet
                $deal->user->addToWallet($totalToReturn, 'refund', null, [
                    'deal_id' => $deal->id,
                    'escrow_amount' => $escrowAmount,
                    'platform_fee_amount' => $platformFeeAmount,
                    'reason' => 'Deal cancelled - escrow returned',
                ]);

                AuditLog::log(
                    'deal.escrow_returned',
                    Deal::class,
                    $deal->id,
                    "Escrowed funds returned to SMB for cancelled deal #{$deal->id}",
                    [
                        'escrow_amount' => $escrowAmount,
                        'platform_fee_amount' => $platformFeeAmount,
                        'total_returned' => $totalToReturn,
                    ]
                );
            }

            $deal->update(['status' => 'cancelled']);

            AuditLog::log(
                'deal.cancelled',
                Deal::class,
                $deal->id,
                "Deal #{$deal->id} was cancelled by admin",
                ['reason' => $request->reason ?? null]
            );

            DB::commit();

            return redirect()->back()->with('success', 'Deal cancelled successfully. Escrowed funds have been returned to the SMB.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Failed to cancel deal. Please try again.']);
        }
    }

    /**
     * Bulk delete deals
     */
    public function bulkDeleteDeals(Request $request)
    {
        $request->validate([
            'deal_ids' => ['required', 'array'],
            'deal_ids.*' => ['required', 'exists:deals,id'],
        ]);

        $dealIds = $request->deal_ids;
        $count = Deal::whereIn('id', $dealIds)->count();

        Deal::whereIn('id', $dealIds)->delete();

        AuditLog::log(
            'deals.bulk_deleted',
            Deal::class,
            null,
            "{$count} deal(s) were bulk deleted by admin",
            ['deal_ids' => $dealIds]
        );

        return redirect()->route('admin.deals.index')
            ->with('success', "{$count} deal(s) deleted successfully.");
    }

    /**
     * Payments & Financial Oversight
     */
    public function payments(Request $request)
    {
        $query = WalletTransaction::with('user');

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $transactions = $query->latest()->paginate(50);

        // Financial summary
        $summary = [
            'total_platform_fees' => Deal::where('payment_status', 'paid')->sum('platform_fee_amount'),
            'total_escrow' => Deal::where('payment_status', 'paid')->whereNull('released_at')->sum('escrow_amount'),
            'total_payouts' => AthleteWithdrawal::where('status', 'completed')->sum('amount'),
            'pending_withdrawals' => AthleteWithdrawal::whereIn('status', ['pending', 'processing'])->sum('amount'),
        ];

        return view('admin.superadmin.payments.index', compact('transactions', 'summary'));
    }

    /**
     * Athlete Profile Management
     */
    public function athletes(Request $request)
    {
        $query = Athlete::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        $athletes = $query->withCount('deals')->latest()->paginate(50);

        return view('admin.superadmin.athletes.index', compact('athletes'));
    }

    /**
     * View athlete details
     */
    public function showAthlete(Athlete $athlete)
    {
        $athlete->loadCount('deals');
        $deals = $athlete->deals()->with('user')->latest()->paginate(20);

        return view('admin.superadmin.athletes.show', compact('athlete', 'deals'));
    }

    /**
     * Edit athlete
     */
    public function editAthlete(Athlete $athlete)
    {
        return view('admin.superadmin.athletes.edit', compact('athlete'));
    }

    /**
     * Update athlete
     */
    public function updateAthlete(Request $request, Athlete $athlete)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:athletes,email,' . $athlete->id],
            'sport' => ['nullable', 'string', 'max:255'],
            'school' => ['nullable', 'string', 'max:255'],
            'athlete_level' => ['nullable', 'in:pro,college,highschool'],
            'instagram_handle' => ['nullable', 'string', 'max:255'],
            'tiktok_handle' => ['nullable', 'string', 'max:255'],
            'twitter_handle' => ['nullable', 'string', 'max:255'],
            'youtube_handle' => ['nullable', 'string', 'max:255'],
        ]);

        $athlete->update($validated);

        AuditLog::log(
            'athlete.updated',
            Athlete::class,
            $athlete->id,
            "Athlete {$athlete->email} was updated by admin"
        );

        return redirect()->route('admin.athletes.index')
            ->with('success', 'Athlete updated successfully.');
    }

    /**
     * Delete athlete
     */
    public function deleteAthlete(Athlete $athlete)
    {
        AuditLog::log(
            'athlete.deleted',
            Athlete::class,
            $athlete->id,
            "Athlete {$athlete->email} was deleted by admin"
        );

        $athlete->delete();

        return redirect()->route('admin.athletes.index')
            ->with('success', 'Athlete deleted successfully.');
    }

    /**
     * Hide/disable athlete profile
     */
    public function hideAthleteProfile(Athlete $athlete)
    {
        $athlete->is_active = false;
        $athlete->save();
        $athlete->refresh();

        // Verify the update worked
        if ($athlete->is_active) {
            return redirect()->back()->withErrors(['error' => 'Failed to hide athlete profile. Please try again.']);
        }

        AuditLog::log(
            'athlete.profile.hidden',
            Athlete::class,
            $athlete->id,
            "Athlete profile for {$athlete->email} was hidden"
        );

        return redirect()->route('admin.athletes.index')->with('success', 'Athlete profile hidden.');
    }

    /**
     * Show/enable athlete profile
     */
    public function showAthleteProfile(Athlete $athlete)
    {
        try {
            $athlete->is_active = true;
            $athlete->save();
            $athlete->refresh();

            // Verify the update worked
            if (!$athlete->is_active) {
                return redirect()->back()->withErrors(['error' => 'Failed to show athlete profile. Please try again.']);
            }

            AuditLog::log(
                'athlete.profile.shown',
                Athlete::class,
                $athlete->id,
                "Athlete profile for {$athlete->email} was made visible"
            );

            return redirect()->route('admin.athletes.index')->with('success', 'Athlete profile made visible.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Failed to show athlete profile: ' . $e->getMessage()]);
        }
    }

    /**
     * Bulk delete athletes
     */
    public function bulkDeleteAthletes(Request $request)
    {
        $request->validate([
            'athlete_ids' => ['required', 'array'],
            'athlete_ids.*' => ['required', 'exists:athletes,id'],
        ]);

        $athleteIds = $request->athlete_ids;
        $count = Athlete::whereIn('id', $athleteIds)->count();

        Athlete::whereIn('id', $athleteIds)->each(function($athlete) {
            AuditLog::log(
                'athlete.deleted',
                Athlete::class,
                $athlete->id,
                "Athlete {$athlete->email} was deleted by admin"
            );
        });

        Athlete::whereIn('id', $athleteIds)->delete();

        return redirect()->route('admin.athletes.index')
            ->with('success', "{$count} athlete(s) deleted successfully.");
    }

    /**
     * Business Management (SMBs)
     */
    public function businesses(Request $request)
    {
        $query = User::where('is_superadmin', false);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('business_name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->whereNotNull('email_verified_at');
            } elseif ($request->status === 'suspended') {
                $query->whereNull('email_verified_at');
            }
        }

        $businesses = $query->withCount('deals')->latest()->paginate(50);

        return view('admin.superadmin.businesses.index', compact('businesses'));
    }

    /**
     * View business details
     */
    public function showBusiness(User $user)
    {
        if ($user->is_superadmin) {
            abort(403);
        }

        $user->loadCount('deals');
        $deals = $user->deals()->with('athlete')->latest()->paginate(20);
        $transactions = $user->walletTransactions()->latest()->paginate(20);

        return view('admin.superadmin.businesses.show', compact('user', 'deals', 'transactions'));
    }

    /**
     * Edit business
     */
    public function editBusiness(User $user)
    {
        if ($user->is_superadmin) {
            abort(403);
        }

        return view('admin.superadmin.businesses.edit', compact('user'));
    }

    /**
     * Update business
     */
    public function updateBusiness(Request $request, User $user)
    {
        if ($user->is_superadmin) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'business_name' => ['nullable', 'string', 'max:255'],
            'business_information' => ['nullable', 'string', 'max:2000'],
            'phone' => ['nullable', 'string', 'max:20'],
            'owner_principal' => ['nullable', 'string', 'max:255'],
        ]);

        $user->update($validated);

        AuditLog::log(
            'business.updated',
            User::class,
            $user->id,
            "Business {$user->email} was updated by admin"
        );

        return redirect()->route('admin.businesses.index')
            ->with('success', 'Business updated successfully.');
    }

    /**
     * Delete business
     */
    public function deleteBusiness(User $user)
    {
        if ($user->is_superadmin) {
            abort(403);
        }

        AuditLog::log(
            'business.deleted',
            User::class,
            $user->id,
            "Business {$user->email} was deleted by admin"
        );

        $user->delete();

        return redirect()->route('admin.businesses.index')
            ->with('success', 'Business deleted successfully.');
    }

    /**
     * Audit Logs
     */
    public function auditLogs(Request $request)
    {
        $query = AuditLog::with('admin');

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('entity_type')) {
            $query->where('entity_type', $request->entity_type);
        }

        $logs = $query->latest()->paginate(100);

        return view('admin.superadmin.audit-logs.index', compact('logs'));
    }
}
