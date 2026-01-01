<?php

use App\Http\Controllers\StripeWebhookController;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            // Stripe webhook route - outside web middleware group (no sessions, cookies, CSRF)
            // Routes defined in 'then' callback do not use web middleware group
            Route::post('/stripe/webhook', [StripeWebhookController::class, 'handle']);
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Register admin middleware aliases
        $middleware->alias([
            'admin' => \App\Http\Middleware\EnsureAdmin::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
