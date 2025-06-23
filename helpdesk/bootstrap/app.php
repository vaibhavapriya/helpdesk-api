<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'guest.api' => \App\Http\Middleware\GuestApi::class,
            'lang' => \App\Http\Middleware\SetLocale::class,

        ]);
            // Add LocaleMiddleware to the web group
        $middleware->group('web', [
            EncryptCookies::class,
            AddQueuedCookiesToResponse::class,
            StartSession::class,
            ShareErrorsFromSession::class,
            VerifyCsrfToken::class,
            SubstituteBindings::class,
            'lang', // your SetLocale middleware
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->report(function (Throwable $e) {
            // Avoid logging certain exceptions
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                return;
            }

            try {
                \App\Models\ErrorLog::create([
                    'error_message' => $e->getMessage(),
                    'stack_trace' => $e->getTraceAsString(),
                    'user_id' => auth()->id() ?? null,
                    'method' => request()->method(),
                    'route' => request()->path(),
                ]);
            } catch (\Throwable $inner) {
                // Prevent cascading failures — optionally log to file as backup
                logger()->error('Failed to log error to DB: ' . $inner->getMessage());
            }
        });
    })
    ->create();
