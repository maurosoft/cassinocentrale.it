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
        // Middleware globali e alias verranno aggiunti nelle fasi successive
        // (es. controllo ruoli admin in Fase 2).
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
