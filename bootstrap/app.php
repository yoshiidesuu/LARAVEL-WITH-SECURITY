<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\SecurityHeaders;
use App\Http\Middleware\WebApplicationFirewall;
use App\Http\Middleware\AntiSqlInjection;
use App\Http\Middleware\AntiXss;
use App\Http\Middleware\SecureFileUpload;
use App\Http\Middleware\WebhookReplayProtection;
use App\Http\Middleware\SecurityLogger;
use Spatie\Csp\AddCspHeaders;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Global middleware - applied to all requests
        $middleware->append([
            SecurityLogger::class,           // Log all security events
            SecurityHeaders::class,          // Add security headers
            AddCspHeaders::class,            // Content Security Policy
            WebApplicationFirewall::class,   // WAF protection
            AntiSqlInjection::class,        // SQL injection protection
            AntiXss::class,                 // XSS protection
        ]);

        // Web middleware group - applied to web routes
        $middleware->web(append: [
            \Illuminate\Cookie\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ]);

        // Middleware aliases for route-specific use
        $middleware->alias([
            'secure.upload' => SecureFileUpload::class,
            'webhook.protection' => WebhookReplayProtection::class,
        ]);

        // Encrypt cookies
        $middleware->encryptCookies(except: [
            // Add any cookies that shouldn't be encrypted here
        ]);

        // CSRF token verification
        $middleware->validateCsrfTokens(except: [
            '/webhook/*',  // Exclude webhook routes from CSRF
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
