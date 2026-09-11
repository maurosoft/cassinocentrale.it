<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SiteSetting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
    ];

    /** Svuota la cache delle impostazioni quando qualcosa cambia. */
    protected static function booted(): void
    {
        $forget = fn () => Cache::forget('site_settings');
        static::saved($forget);
        static::deleted($forget);
    }

    /** Converte il valore grezzo nel tipo corretto (testo, numero, json, ecc.). */
    public function typedValue(): mixed
    {
        return match ($this->type) {
            'json', 'array' => json_decode((string) $this->value, true),
            'boolean', 'bool' => filter_var($this->value, FILTER_VALIDATE_BOOLEAN),
            'integer', 'int' => (int) $this->value,
            'float', 'decimal' => (float) $this->value,
            default => $this->value,
        };
    }
}
