<?php

namespace App\Providers;

use App\Support\Settings;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // In produzione forziamo i link in HTTPS (utile dietro il proxy di HestiaCP).
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        // Rende disponibili in TUTTE le pagine Blade:
        //  - $settings  = impostazioni modificabili da admin (tabella site_settings)
        //  - $bnb       = dati fissi del B&B (config/bnb.php)
        View::share('settings', Settings::all());
        View::share('bnb', config('bnb'));
    }
}
