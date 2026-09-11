<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingRoom extends Model
{
    /** @use HasFactory<\Database\Factories\BookingRoomFactory> */
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'room_id',
        'price_per_night',
        'nights',
        'subtotal',
    ];

    protected function casts(): array
    {
        return [
            'price_per_night' => 'decimal:2',
            'subtotal' => 'decimal:2',
            'nights' => 'integer',
        ];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }
}
