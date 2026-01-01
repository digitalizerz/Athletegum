<?php

use App\Http\Controllers\Admin\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Subdomain Routes
|--------------------------------------------------------------------------
|
| These routes are only accessible on admin.athletegum.com subdomain
| All routes require admin authentication via is_superadmin field
|
*/

// Root redirect to dashboard for authenticated admins
Route::get('/', function () {
    if (Auth::check() && Auth::user()->is_superadmin) {
        return redirect()->route('admin.dashboard');
    }
    return redirect()->route('admin.login');
});

// Admin Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])
        ->name('admin.login');

    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('admin.logout');
});

// Admin Dashboard & Management Routes (require admin middleware)
Route::middleware(['auth', \App\Http\Middleware\EnsureAdmin::class])->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Admin\SuperAdminController::class, 'index'])->name('admin.dashboard');
    
    // User Management
    Route::get('/users', [\App\Http\Controllers\Admin\SuperAdminController::class, 'users'])->name('admin.users.index');
    Route::get('/users/{user}', [\App\Http\Controllers\Admin\SuperAdminController::class, 'showUser'])->name('admin.users.show');
    Route::post('/users/{user}/suspend', [\App\Http\Controllers\Admin\SuperAdminController::class, 'suspendUser'])->name('admin.users.suspend');
    Route::post('/users/{user}/reactivate', [\App\Http\Controllers\Admin\SuperAdminController::class, 'reactivateUser'])->name('admin.users.reactivate');
    Route::post('/users/{user}/impersonate', [\App\Http\Controllers\Admin\SuperAdminController::class, 'impersonateUser'])->name('admin.users.impersonate');
    Route::delete('/users/bulk-delete', [\App\Http\Controllers\Admin\SuperAdminController::class, 'bulkDeleteUsers'])->name('admin.users.bulk-delete');
    
    // Deal Management
    Route::get('/deals', [\App\Http\Controllers\Admin\SuperAdminController::class, 'deals'])->name('admin.deals.index');
    Route::get('/deals/{deal}', [\App\Http\Controllers\Admin\SuperAdminController::class, 'showDeal'])->name('admin.deals.show');
    Route::post('/deals/{deal}/cancel', [\App\Http\Controllers\Admin\SuperAdminController::class, 'cancelDeal'])->name('admin.deals.cancel');
    Route::delete('/deals/bulk-delete', [\App\Http\Controllers\Admin\SuperAdminController::class, 'bulkDeleteDeals'])->name('admin.deals.bulk-delete');
    
    // Deal Messages (Admin - Read-only)
    Route::get('/deals/{deal}/messages', [\App\Http\Controllers\Admin\SuperAdminController::class, 'showDealMessages'])->name('admin.deals.messages');
    
    // Payments
    Route::get('/payments', [\App\Http\Controllers\Admin\SuperAdminController::class, 'payments'])->name('admin.payments.index');
    
    // Athlete Management
    Route::get('/athletes', [\App\Http\Controllers\Admin\SuperAdminController::class, 'athletes'])->name('admin.athletes.index');
    Route::get('/athletes/{athlete}', [\App\Http\Controllers\Admin\SuperAdminController::class, 'showAthlete'])->name('admin.athletes.show');
    Route::get('/athletes/{athlete}/edit', [\App\Http\Controllers\Admin\SuperAdminController::class, 'editAthlete'])->name('admin.athletes.edit');
    Route::put('/athletes/{athlete}', [\App\Http\Controllers\Admin\SuperAdminController::class, 'updateAthlete'])->name('admin.athletes.update');
    Route::delete('/athletes/{athlete}', [\App\Http\Controllers\Admin\SuperAdminController::class, 'deleteAthlete'])->name('admin.athletes.delete');
    Route::post('/athletes/{athlete}/hide', [\App\Http\Controllers\Admin\SuperAdminController::class, 'hideAthleteProfile'])->name('admin.athletes.hide');
    Route::post('/athletes/{athlete}/show', [\App\Http\Controllers\Admin\SuperAdminController::class, 'showAthleteProfile'])->name('admin.athletes.show-profile');
    Route::delete('/athletes/bulk-delete', [\App\Http\Controllers\Admin\SuperAdminController::class, 'bulkDeleteAthletes'])->name('admin.athletes.bulk-delete');
    
    // Business Management
    Route::get('/businesses', [\App\Http\Controllers\Admin\SuperAdminController::class, 'businesses'])->name('admin.businesses.index');
    Route::get('/businesses/{user}', [\App\Http\Controllers\Admin\SuperAdminController::class, 'showBusiness'])->name('admin.businesses.show');
    Route::get('/businesses/{user}/edit', [\App\Http\Controllers\Admin\SuperAdminController::class, 'editBusiness'])->name('admin.businesses.edit');
    Route::put('/businesses/{user}', [\App\Http\Controllers\Admin\SuperAdminController::class, 'updateBusiness'])->name('admin.businesses.update');
    Route::delete('/businesses/{user}', [\App\Http\Controllers\Admin\SuperAdminController::class, 'deleteBusiness'])->name('admin.businesses.delete');
    
    // Audit Logs
    Route::get('/audit-logs', [\App\Http\Controllers\Admin\SuperAdminController::class, 'auditLogs'])->name('admin.audit-logs.index');
    
    // Settings (redirects to Stripe & Fees)
    Route::get('/settings', [SettingsController::class, 'index'])->name('admin.settings');
    Route::post('/settings', [SettingsController::class, 'update'])->name('admin.settings.update');
    
    // Stripe & Fees Management
    Route::get('/stripe-fees', [\App\Http\Controllers\Admin\StripeFeesController::class, 'index'])->name('admin.stripe-fees.index');
    Route::post('/stripe-fees/stripe/verify', [\App\Http\Controllers\Admin\StripeFeesController::class, 'verifyStripe'])->name('admin.stripe-fees.verify-stripe');
    Route::post('/stripe-fees/smb-fee', [\App\Http\Controllers\Admin\StripeFeesController::class, 'updateSMBFee'])->name('admin.stripe-fees.update-smb-fee');
    Route::post('/stripe-fees/athlete-fee', [\App\Http\Controllers\Admin\StripeFeesController::class, 'updateAthleteFee'])->name('admin.stripe-fees.update-athlete-fee');
    
    // Profile Settings (Admin)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('admin.profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('admin.profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('admin.profile.destroy');
});

// Stop impersonating route (accessible outside admin middleware)
Route::post('/stop-impersonating', [\App\Http\Controllers\Admin\SuperAdminController::class, 'stopImpersonating'])->name('admin.stop-impersonating');
