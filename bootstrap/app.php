<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->append(\App\Http\Middleware\CheckAdminUserRole::class);
        $middleware->append(\App\Http\Middleware\CheckSchedulerUserRole::class);
        $middleware->append(\App\Http\Middleware\CheckClientUserRole::class);
        $middleware->append(\App\Http\Middleware\CheckServiceProviderUserRole::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
