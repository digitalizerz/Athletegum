<?php

use App\Http\Controllers\Admin\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| These routes are accessible at /admin/* paths
| All routes require admin authentication via is_superadmin field
|
*/

Route::prefix('admin')->name('admin.')->group(function () {
    // Admin Authentication Routes
    Route::middleware('guest')->group(function () {
        Route::get('/login', [AuthenticatedSessionController::class, 'create'])
            ->name('login');

        Route::post('/login', [AuthenticatedSessionController::class, 'store']);
    });

    Route::middleware('auth')->group(function () {
        Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
            ->name('logout');
    });

    // Admin Dashboard & Management Routes (require admin middleware)
    Route::middleware(['auth', \App\Http\Middleware\EnsureAdmin::class])->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Admin\SuperAdminController::class, 'index'])->name('dashboard');
        
        // User Management
        Route::get('/users', [\App\Http\Controllers\Admin\SuperAdminController::class, 'users'])->name('users.index');
        Route::get('/users/{user}', [\App\Http\Controllers\Admin\SuperAdminController::class, 'showUser'])->name('users.show');
        Route::post('/users/{user}/suspend', [\App\Http\Controllers\Admin\SuperAdminController::class, 'suspendUser'])->name('users.suspend');
        Route::post('/users/{user}/reactivate', [\App\Http\Controllers\Admin\SuperAdminController::class, 'reactivateUser'])->name('users.reactivate');
        Route::post('/users/{user}/impersonate', [\App\Http\Controllers\Admin\SuperAdminController::class, 'impersonateUser'])->name('users.impersonate');
        Route::delete('/users/bulk-delete', [\App\Http\Controllers\Admin\SuperAdminController::class, 'bulkDeleteUsers'])->name('users.bulk-delete');
        
        // Deal Management
        Route::get('/deals', [\App\Http\Controllers\Admin\SuperAdminController::class, 'deals'])->name('deals.index');
        Route::get('/deals/{deal}', [\App\Http\Controllers\Admin\SuperAdminController::class, 'showDeal'])->name('deals.show');
        Route::post('/deals/{deal}/cancel', [\App\Http\Controllers\Admin\SuperAdminController::class, 'cancelDeal'])->name('deals.cancel');
        Route::delete('/deals/bulk-delete', [\App\Http\Controllers\Admin\SuperAdminController::class, 'bulkDeleteDeals'])->name('deals.bulk-delete');
        
        // Deal Messages (Admin - Read-only)
        Route::get('/deals/{deal}/messages', [\App\Http\Controllers\Admin\SuperAdminController::class, 'showDealMessages'])->name('deals.messages');
        
        // Payments
        Route::get('/payments', [\App\Http\Controllers\Admin\SuperAdminController::class, 'payments'])->name('payments.index');
        
        // Athlete Management
        Route::get('/athletes', [\App\Http\Controllers\Admin\SuperAdminController::class, 'athletes'])->name('athletes.index');
        Route::get('/athletes/{athlete}', [\App\Http\Controllers\Admin\SuperAdminController::class, 'showAthlete'])->name('athletes.show');
        Route::get('/athletes/{athlete}/edit', [\App\Http\Controllers\Admin\SuperAdminController::class, 'editAthlete'])->name('athletes.edit');
        Route::put('/athletes/{athlete}', [\App\Http\Controllers\Admin\SuperAdminController::class, 'updateAthlete'])->name('athletes.update');
        Route::delete('/athletes/{athlete}', [\App\Http\Controllers\Admin\SuperAdminController::class, 'deleteAthlete'])->name('athletes.delete');
        Route::post('/athletes/{athlete}/hide', [\App\Http\Controllers\Admin\SuperAdminController::class, 'hideAthleteProfile'])->name('athletes.hide');
        Route::post('/athletes/{athlete}/show', [\App\Http\Controllers\Admin\SuperAdminController::class, 'showAthleteProfile'])->name('athletes.show-profile');
        Route::delete('/athletes/bulk-delete', [\App\Http\Controllers\Admin\SuperAdminController::class, 'bulkDeleteAthletes'])->name('athletes.bulk-delete');
        
        // Business Management
        Route::get('/businesses', [\App\Http\Controllers\Admin\SuperAdminController::class, 'businesses'])->name('businesses.index');
        Route::get('/businesses/{user}', [\App\Http\Controllers\Admin\SuperAdminController::class, 'showBusiness'])->name('businesses.show');
        Route::get('/businesses/{user}/edit', [\App\Http\Controllers\Admin\SuperAdminController::class, 'editBusiness'])->name('businesses.edit');
        Route::put('/businesses/{user}', [\App\Http\Controllers\Admin\SuperAdminController::class, 'updateBusiness'])->name('businesses.update');
        Route::delete('/businesses/{user}', [\App\Http\Controllers\Admin\SuperAdminController::class, 'deleteBusiness'])->name('businesses.delete');
        
        // Audit Logs
        Route::get('/audit-logs', [\App\Http\Controllers\Admin\SuperAdminController::class, 'auditLogs'])->name('audit-logs.index');
        
        // Settings (redirects to Stripe & Fees)
        Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
        Route::post('/settings', [SettingsController::class, 'update'])->name('settings.update');
        
        // Stripe & Fees Management
        Route::get('/stripe-fees', [\App\Http\Controllers\Admin\StripeFeesController::class, 'index'])->name('stripe-fees.index');
        Route::post('/stripe-fees/stripe/verify', [\App\Http\Controllers\Admin\StripeFeesController::class, 'verifyStripe'])->name('stripe-fees.verify-stripe');
        Route::post('/stripe-fees/smb-fee', [\App\Http\Controllers\Admin\StripeFeesController::class, 'updateSMBFee'])->name('stripe-fees.update-smb-fee');
        Route::post('/stripe-fees/athlete-fee', [\App\Http\Controllers\Admin\StripeFeesController::class, 'updateAthleteFee'])->name('stripe-fees.update-athlete-fee');
        
        // Profile Settings (Admin)
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });

    // Stop impersonating route (accessible outside admin middleware)
    Route::post('/stop-impersonating', [\App\Http\Controllers\Admin\SuperAdminController::class, 'stopImpersonating'])->name('stop-impersonating');
});
