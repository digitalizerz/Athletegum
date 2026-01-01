<?php

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
            // Load admin subdomain routes with domain constraint
            Route::domain('admin.athletegum.com')
                ->middleware('web')
                ->group(function () {
                    require base_path('routes/admin.php');
                });
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Register admin middleware aliases
        $middleware->alias([
            'admin' => \App\Http\Middleware\EnsureAdmin::class,
            'admin.subdomain' => \App\Http\Middleware\EnsureAdminSubdomain::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
