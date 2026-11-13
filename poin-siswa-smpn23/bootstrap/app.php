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
        // Saat diakses lewat tunnel (ngrok/cloudflared) di lingkungan lokal, tunnel
        // mengirim X-Forwarded-Proto: https. Tanpa trustProxies Laravel tetap
        // menganggap request http dan menghasilkan URL http:// (mixed content).
        if (\Illuminate\Support\Env::get('APP_ENV', 'production') === 'local') {
            $middleware->trustProxies(at: '*');
        }

        $middleware->alias([
            'role' => \App\Http\Middleware\EnsureRole::class,
            'active' => \App\Http\Middleware\EnsureActive::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
