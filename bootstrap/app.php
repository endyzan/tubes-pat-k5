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
        $middleware->appendToGroup('verify-token', [
            \App\Http\Middleware\EnsureTokenIsValid::class,
        ]);
        $middleware->appendToGroup('admin-level', [
            \App\Http\Middleware\EnsureTokenIsValid::class,
            \App\Http\Middleware\AdminLevel::class,
        ]);
        $middleware->appendToGroup('volunteer-level', [
            \App\Http\Middleware\EnsureTokenIsValid::class,
            \App\Http\Middleware\VolunteerLevel::class,
        ]);
        $middleware->appendToGroup('user-level', [
            \App\Http\Middleware\EnsureTokenIsValid::class,
            \App\Http\Middleware\UserLevel::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
