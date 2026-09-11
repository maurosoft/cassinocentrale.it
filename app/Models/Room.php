<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Room extends Model
{
    /** @use HasFactory<\Database\Factories\RoomFactory> */
    use HasFactory;

    protected $fillable = [
        'number_name',
        'name',
        'slug',
        'short_description',
        'description',
        'rules',
        'base_price',
        'max_guests',
        'has_kitchenette',
        'images',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'base_price' => 'decimal:2',
            'has_kitchenette' => 'boolean',
            'is_active' => 'boolean',
            'images' => 'array',
        ];
    }

    /** Usa lo "slug" (testo leggibile) negli URL invece dell'id. */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    protected static function booted(): void
    {
        static::saving(function (Room $room): void {
            if (blank($room->slug)) {
                $room->slug = Str::slug($room->number_name.'-'.$room->name);
            }
        });
    }

    /** Servizi collegati alla camera (con eventuale costo extra specifico). */
    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class)
            ->withPivot('extra_cost')
            ->withTimestamps();
    }

    public function bookingRooms(): HasMany
    {
        return $this->hasMany(BookingRoom::class);
    }

    /** Prima immagine disponibile, o un segnaposto. */
    public function coverImage(): string
    {
        $first = is_array($this->images) ? ($this->images[0] ?? null) : null;

        return $first ?: 'images/placeholders/room.svg';
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('number_name');
    }
}
