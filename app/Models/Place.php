<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Place extends Model
{
    /** @use HasFactory<\Database\Factories\PlaceFactory> */
    use HasFactory;

    // Categorie dei luoghi (per la sezione "Scopri Cassino").
    public const CATEGORIES = [
        'storia' => 'Storia',
        'arte' => 'Arte',
        'natura' => 'Natura',
        'guerra' => 'Seconda Guerra Mondiale',
        'religione' => 'Luoghi di culto',
        'servizi' => 'Servizi utili',
    ];

    // Tipi di attività locali (negozi, ristoranti, ecc.).
    public const TYPES = [
        'negozio' => 'Negozio',
        'ristorante' => 'Ristorante',
        'bar' => 'Bar / Caffetteria',
        'artigianato' => 'Artigianato',
        'servizi' => 'Servizi',
    ];

    protected $fillable = [
        'name',
        'slug',
        'description',
        'address',
        'category',
        'type',
        'distance_walking',
        'distance_car',
        'distance_bus',
        'lat',
        'lng',
        'link',
        'image',
        'is_convention',
        'convention_description',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'lat' => 'float',
            'lng' => 'float',
            'is_convention' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Place $place): void {
            if (blank($place->slug)) {
                $place->slug = Str::slug($place->name);
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function categoryLabel(): string
    {
        return self::CATEGORIES[$this->category] ?? ucfirst((string) $this->category);
    }

    public function typeLabel(): string
    {
        return self::TYPES[$this->type] ?? ucfirst((string) $this->type);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    public function scopeConventions($query)
    {
        return $query->where('is_convention', true);
    }

    public function scopeAttractions($query)
    {
        return $query->where('is_convention', false);
    }
}
