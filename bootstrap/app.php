<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        // api: __DIR__.'/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        then: function ($router) {
            Route::prefix('api')
                ->middleware('api')
                ->namespace('App\\Http\\Controllers')
                ->group(base_path('routes/api.php'));
        }
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->api(append: [
            'throttle:api',
        ]);
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->withEvents(discover: [
        __DIR__ . '/../app/Listeners',
    ])
    ->create();
