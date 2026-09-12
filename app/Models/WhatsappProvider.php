<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsappProvider extends Model
{
    // Tipi di provider supportati (SpotWab usa lo stesso contratto di Hooki).
    public const PROVIDERS = [
        'uozap' => 'UoZap',
        'hooki' => 'Hooki / SpotWab',
    ];

    protected $fillable = [
        'name',
        'provider',
        'base_url',
        'token',
        'instance_id',
        'sender',
        'timeout',
        'order_fallback',
        'is_active',
    ];

    protected $hidden = ['token'];

    protected function casts(): array
    {
        return [
            'token' => 'encrypted',   // cifrato nel database
            'is_active' => 'boolean',
            'timeout' => 'integer',
            'order_fallback' => 'integer',
        ];
    }

    public function providerLabel(): string
    {
        return self::PROVIDERS[$this->provider] ?? $this->provider;
    }
}
