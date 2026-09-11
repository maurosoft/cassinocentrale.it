<?php

namespace App\Support;

use App\Models\SiteSetting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

/**
 * Accesso comodo alle impostazioni del sito (tabella site_settings),
 * con cache per evitare di interrogare il database a ogni pagina.
 */
class Settings
{
    /** Restituisce tutte le impostazioni come coppie chiave => valore tipizzato. */
    public static function all(): array
    {
        // Se il database non è ancora pronto (prima delle migrazioni), non esplodere.
        if (! self::tableReady()) {
            return [];
        }

        return Cache::rememberForever('site_settings', function () {
            return SiteSetting::all()
                ->mapWithKeys(fn (SiteSetting $s) => [$s->key => $s->typedValue()])
                ->all();
        });
    }

    /** Legge una singola impostazione, con valore di riserva. */
    public static function get(string $key, mixed $default = null): mixed
    {
        return self::all()[$key] ?? $default;
    }

    private static function tableReady(): bool
    {
        try {
            return Schema::hasTable('site_settings');
        } catch (\Throwable $e) {
            return false;
        }
    }
}
