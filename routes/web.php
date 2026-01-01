<?php

use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Athlete\AuthController as AthleteAuthController;
use App\Http\Controllers\Athlete\DashboardController as AthleteDashboardController;
use App\Http\Controllers\Athlete\EarningsController as AthleteEarningsController;
use App\Http\Controllers\Athlete\ProfileController as AthleteProfileController;
use App\Http\Controllers\DealController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PaymentMethodController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WalletController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    if (Auth::guard('athlete')->check()) {
        return redirect()->route('athlete.dashboard');
    }
    return view('welcome');
})->name('welcome');

// Static Pages
Route::prefix('pages')->name('pages.')->group(function () {
    Route::get('/about', function () {
        return view('pages.about');
    })->name('about');
    
    Route::get('/terms', function () {
        return view('pages.terms');
    })->name('terms');
    
    Route::get('/privacy', function () {
        return view('pages.privacy');
    })->name('privacy');
    
    Route::get('/contact', function () {
        return view('pages.contact');
    })->name('contact');
});

// Legacy routes for direct access
Route::get('/about', function () {
    return redirect()->route('pages.about');
});

Route::get('/terms', function () {
    return redirect()->route('pages.terms');
});

Route::get('/privacy', function () {
    return redirect()->route('pages.privacy');
});

Route::get('/contact', function () {
    return redirect()->route('pages.contact');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Deals
    Route::get('/deals', [DealController::class, 'index'])->name('deals.index');
    Route::delete('/deals/bulk-delete', [DealController::class, 'bulkDelete'])->name('deals.bulk-delete');
    Route::get('/deals/{deal}/edit', [DealController::class, 'edit'])->name('deals.edit');
    Route::patch('/deals/{deal}', [DealController::class, 'update'])->name('deals.update');
    Route::delete('/deals/{deal}', [DealController::class, 'destroy'])->name('deals.destroy');
    Route::get('/deals/create', [DealController::class, 'create'])->name('deals.create');
    Route::post('/deals/create/type', [DealController::class, 'storeType'])->name('deals.create.type');
    Route::get('/deals/create/platforms', [DealController::class, 'createPlatforms'])->name('deals.create.platforms');
    Route::post('/deals/create/platforms', [DealController::class, 'storePlatforms'])->name('deals.create.platforms.store');
    Route::get('/deals/create/compensation', [DealController::class, 'createCompensation'])->name('deals.create.compensation');
    Route::post('/deals/create/compensation', [DealController::class, 'storeCompensation'])->name('deals.create.compensation.store');
    Route::get('/deals/create/deadline', [DealController::class, 'createDeadline'])->name('deals.create.deadline');
    Route::post('/deals/create/deadline', [DealController::class, 'storeDeadline'])->name('deals.create.deadline.store');
    Route::get('/deals/create/notes', [DealController::class, 'createNotes'])->name('deals.create.notes');
    Route::post('/deals/create/notes', [DealController::class, 'storeNotes'])->name('deals.create.notes.store');
    Route::get('/deals/create/contract', [DealController::class, 'createContract'])->name('deals.create.contract');
    Route::post('/deals/create/contract', [DealController::class, 'storeContract'])->name('deals.create.contract.store');
    Route::get('/deals/create/payment', [DealController::class, 'createPayment'])->name('deals.create.payment');
    Route::post('/deals/create/payment', [PaymentController::class, 'processDealPayment'])->name('deals.create.payment.store');
    Route::get('/deals/review', [DealController::class, 'review'])->name('deals.review');
    Route::post('/deals', [DealController::class, 'store'])->name('deals.store');
    Route::post('/deals/save-draft', [DealController::class, 'saveDraft'])->name('deals.save-draft');
    Route::get('/deals/{deal}/resume', [DealController::class, 'resumeDraft'])->name('deals.resume-draft');
    Route::get('/deals/{deal}/success', [DealController::class, 'success'])->name('deals.success');
    Route::post('/deals/{deal}/approve', [\App\Http\Controllers\DealApprovalController::class, 'approve'])->name('deals.approve');
    Route::post('/deals/{deal}/request-revisions', [\App\Http\Controllers\DealApprovalController::class, 'requestRevisions'])->name('deals.request-revisions');
    Route::post('/deals/{deal}/cancel', [\App\Http\Controllers\DealApprovalController::class, 'cancel'])->name('deals.cancel');
    Route::post('/deals/{deal}/release-payment', [PaymentController::class, 'releasePayment'])->name('deals.release-payment');

    // Deal Messages (SMB)
    Route::get('/messages', [\App\Http\Controllers\DealMessageController::class, 'index'])->name('messages.index');
    Route::get('/deals/{deal}/messages', [\App\Http\Controllers\DealMessageController::class, 'show'])->name('deals.messages');
    Route::post('/deals/{deal}/messages', [\App\Http\Controllers\DealMessageController::class, 'store'])->name('deals.messages.store');

    // Notifications (SMB)
    Route::get('/notifications', [\App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification}/read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
});

// Public route for viewing deals by token
Route::get('/deal/{token}', [DealController::class, 'showByToken'])->name('deals.show.token');

// Athlete public routes (no auth required for accepting deals by token)
Route::prefix('athlete')->name('athlete.')->group(function () {
    Route::post('/deals/{token}/accept', [\App\Http\Controllers\Athlete\DealController::class, 'accept'])->name('deals.accept');
});

// Wallet, payments, admin, etc. (unchanged)
Route::middleware('auth')->group(function () {

    Route::get('/wallet', [WalletController::class, 'index'])->name('wallet.index');
    Route::get('/wallet/add-funds', [WalletController::class, 'showAddFunds'])->name('wallet.add-funds');
    Route::post('/wallet/add-funds', [WalletController::class, 'addFunds'])->name('wallet.add-funds.store');

    Route::get('/payment-methods', [PaymentMethodController::class, 'index'])->name('payment-methods.index');
    Route::get('/payment-methods/create', [PaymentMethodController::class, 'create'])->name('payment-methods.create');
    Route::post('/payment-methods', [PaymentMethodController::class, 'store'])->name('payment-methods.store');
    Route::post('/payment-methods/{paymentMethod}/set-default', [PaymentMethodController::class, 'setDefault'])->name('payment-methods.set-default');
    Route::delete('/payment-methods/{paymentMethod}', [PaymentMethodController::class, 'destroy'])->name('payment-methods.destroy');
});

// Public athlete profile
Route::get('/a/{identifier}', [AthleteProfileController::class, 'showPublic'])->name('athlete.profile');

// Athlete auth routes
Route::prefix('athlete')->name('athlete.')->group(function () {
    Route::get('/register', [AthleteAuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AthleteAuthController::class, 'register'])->name('register.store');
    Route::get('/login', [AthleteAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AthleteAuthController::class, 'login'])->name('login.store');
    Route::post('/logout', [AthleteAuthController::class, 'logout'])->name('logout');

    // Athlete password reset routes
    Route::middleware('guest:athlete')->group(function () {
        Route::get('/forgot-password', [\App\Http\Controllers\Athlete\Auth\PasswordResetLinkController::class, 'create'])->name('password.request');
        Route::post('/forgot-password', [\App\Http\Controllers\Athlete\Auth\PasswordResetLinkController::class, 'store'])->name('password.email');
        Route::get('/reset-password/{token}', [\App\Http\Controllers\Athlete\Auth\NewPasswordController::class, 'create'])->name('password.reset');
        Route::post('/reset-password', [\App\Http\Controllers\Athlete\Auth\NewPasswordController::class, 'store'])->name('password.store');
    });

    Route::middleware('auth:athlete')->group(function () {
        Route::get('/dashboard', [AthleteDashboardController::class, 'index'])->name('dashboard');
        
        // Profile routes
        Route::get('/profile/edit', [AthleteProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [AthleteProfileController::class, 'update'])->name('profile.update');
        
        // Deals routes
        Route::get('/deals', [\App\Http\Controllers\Athlete\DealController::class, 'index'])->name('deals.index');
        Route::get('/deals/{deal}', [\App\Http\Controllers\Athlete\DealController::class, 'show'])->name('deals.show');
        Route::get('/deals/{deal}/submit', [\App\Http\Controllers\Athlete\DealController::class, 'showSubmit'])->name('deals.submit.show');
        Route::post('/deals/{deal}/submit', [\App\Http\Controllers\Athlete\DealController::class, 'submitDeliverables'])->name('deals.submit.store');
        Route::get('/deals/{deal}/cancel', [\App\Http\Controllers\Athlete\DealController::class, 'showCancel'])->name('deals.cancel');
        Route::post('/deals/{deal}/cancel', [\App\Http\Controllers\Athlete\DealController::class, 'cancel'])->name('deals.cancel.store');
        
        // Messages routes
        Route::get('/messages', [\App\Http\Controllers\Athlete\DealMessageController::class, 'index'])->name('messages.index');
        Route::get('/deals/{deal}/messages', [\App\Http\Controllers\Athlete\DealMessageController::class, 'show'])->name('deals.messages');
        Route::post('/deals/{deal}/messages', [\App\Http\Controllers\Athlete\DealMessageController::class, 'store'])->name('deals.messages.store');
        
        // Earnings routes
        Route::get('/earnings', [\App\Http\Controllers\Athlete\EarningsController::class, 'index'])->name('earnings.index');
        Route::get('/earnings/withdraw', [\App\Http\Controllers\Athlete\EarningsController::class, 'createWithdrawal'])->name('earnings.withdraw');
        Route::post('/earnings/withdraw', [\App\Http\Controllers\Athlete\EarningsController::class, 'storeWithdrawal'])->name('earnings.withdraw.store');
        Route::get('/earnings/payment-method/create', [\App\Http\Controllers\Athlete\EarningsController::class, 'createPaymentMethod'])->name('earnings.payment-method.create');
        Route::post('/earnings/payment-method', [\App\Http\Controllers\Athlete\EarningsController::class, 'storePaymentMethod'])->name('earnings.payment-method.store');
        Route::delete('/earnings/payment-method/{paymentMethodId}', [\App\Http\Controllers\Athlete\EarningsController::class, 'destroyPaymentMethod'])->name('earnings.payment-method.destroy')->where('paymentMethodId', '[0-9]+');
        Route::post('/earnings/payment-method/{paymentMethod}/default', [\App\Http\Controllers\Athlete\EarningsController::class, 'setDefaultPaymentMethod'])->name('earnings.payment-method.default');
        
        // Stripe Connect OAuth routes
        Route::get('/earnings/stripe-connect/initiate', [\App\Http\Controllers\Athlete\EarningsController::class, 'initiateStripeConnect'])->name('earnings.stripe-connect.initiate');
        Route::get('/earnings/stripe-connect/callback/{athlete}', [\App\Http\Controllers\Athlete\EarningsController::class, 'handleStripeConnectCallback'])->name('earnings.stripe-connect.callback');
        Route::get('/earnings/stripe-connect/refresh/{athlete}', [\App\Http\Controllers\Athlete\EarningsController::class, 'handleStripeConnectRefresh'])->name('earnings.stripe-connect.refresh');
        
        // Notifications routes
        Route::get('/notifications', [\App\Http\Controllers\Athlete\NotificationController::class, 'index'])->name('notifications.index');
        Route::post('/notifications/{notification}/read', [\App\Http\Controllers\Athlete\NotificationController::class, 'markAsRead'])->name('notifications.read');
        Route::post('/notifications/read-all', [\App\Http\Controllers\Athlete\NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
    });
});

require __DIR__.'/auth.php';

// Admin routes (super admin only)
require __DIR__.'/admin.php';
