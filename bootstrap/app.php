<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role'                 => \App\Http\Middleware\RequireRole::class,
            'active_subscription'  => \App\Http\Middleware\RequireActiveSubscription::class,
            'superadmin'           => \App\Http\Middleware\RequireSuperAdmin::class,
            'apikey'               => \App\Http\Middleware\AuthenticateApiKey::class,
        ]);

        // Log all mutating API requests to audit_logs
        $middleware->appendToGroup('api', \App\Http\Middleware\AuditActivity::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );
    })
    ->booted(function () {
        // Auth endpoints: 10 attempts / minute per IP
        RateLimiter::for('api-auth', fn (Request $req) =>
            Limit::perMinute(10)->by($req->ip())
        );

        // General API: 120 requests / minute per authenticated user (or IP)
        RateLimiter::for('api', fn (Request $req) =>
            $req->user()
                ? Limit::perMinute(120)->by($req->user()->id)
                : Limit::perMinute(30)->by($req->ip())
        );

        // PDF/export endpoints: 20 / minute (heavy)
        RateLimiter::for('api-export', fn (Request $req) =>
            Limit::perMinute(20)->by($req->user()?->id ?? $req->ip())
        );
    })
    ->create();
