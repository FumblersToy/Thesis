<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminAuth::class,
            'checkDisabled' => \App\Http\Middleware\CheckAccountDisabled::class,
        ]);
        
        // Update last_seen_at for authenticated users
        $middleware->append(\App\Http\Middleware\UpdateLastSeen::class);
        
        // Check if account is disabled
        $middleware->append(\App\Http\Middleware\CheckAccountDisabled::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
