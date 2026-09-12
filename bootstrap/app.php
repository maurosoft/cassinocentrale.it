<?php

use App\Http\Middleware\MaintenanceMode;
use App\Http\Middleware\RoleMiddleware;
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
        // Alias del controllo ruoli, usabile nelle rotte come 'role:superadmin'.
        $middleware->alias([
            'role' => RoleMiddleware::class,
        ]);

        // Modalità manutenzione morbida (blocca i visitatori, non lo staff).
        $middleware->web(append: [
            MaintenanceMode::class,
        ]);

        // Gli utenti non autenticati vengono mandati alla pagina di login admin.
        $middleware->redirectGuestsTo(fn () => route('admin.login'));

        // Il webhook di Stripe non invia il token CSRF: va escluso.
        $middleware->validateCsrfTokens(except: ['stripe/webhook']);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
