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

        // Applica la configurazione SMTP impostata da admin (se attiva).
        $this->configureMailFromSettings();
    }

    /** Sovrascrive la configurazione mail con i valori impostati in admin. */
    private function configureMailFromSettings(): void
    {
        $s = Settings::all();

        if (empty($s['smtp.enabled']) || empty($s['smtp.host'])) {
            return;
        }

        config([
            'mail.default' => 'smtp',
            'mail.mailers.smtp.host' => $s['smtp.host'],
            'mail.mailers.smtp.port' => (int) ($s['smtp.port'] ?? 587),
            'mail.mailers.smtp.username' => $s['smtp.username'] ?? null,
            'mail.mailers.smtp.password' => $s['smtp.password'] ?? null,
            'mail.mailers.smtp.encryption' => $s['smtp.encryption'] ?? 'tls',
            'mail.from.address' => $s['smtp.from_email'] ?? config('mail.from.address'),
            'mail.from.name' => $s['smtp.from_name'] ?? config('mail.from.name'),
        ]);
    }
}
