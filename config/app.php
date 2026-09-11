<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Nome dell'applicazione
    |--------------------------------------------------------------------------
    */
    'name' => env('APP_NAME', 'B&B Cassino Centrale'),

    /*
    |--------------------------------------------------------------------------
    | Ambiente
    |--------------------------------------------------------------------------
    */
    'env' => env('APP_ENV', 'production'),

    'debug' => (bool) env('APP_DEBUG', false),

    'url' => env('APP_URL', 'http://localhost'),

    /*
    |--------------------------------------------------------------------------
    | Fuso orario e lingua
    |--------------------------------------------------------------------------
    */
    'timezone' => env('APP_TIMEZONE', 'Europe/Rome'),

    'locale' => env('APP_LOCALE', 'it'),

    'fallback_locale' => env('APP_FALLBACK_LOCALE', 'it'),

    'faker_locale' => env('APP_FAKER_LOCALE', 'it_IT'),

    /*
    |--------------------------------------------------------------------------
    | Chiave di cifratura
    |--------------------------------------------------------------------------
    */
    'cipher' => 'AES-256-CBC',

    'key' => env('APP_KEY'),

    'previous_keys' => [
        ...array_filter(
            explode(',', (string) env('APP_PREVIOUS_KEYS', ''))
        ),
    ],

    /*
    |--------------------------------------------------------------------------
    | Modalità manutenzione
    |--------------------------------------------------------------------------
    */
    'maintenance' => [
        'driver' => env('APP_MAINTENANCE_DRIVER', 'file'),
        'store' => env('APP_MAINTENANCE_STORE', 'database'),
    ],

];
