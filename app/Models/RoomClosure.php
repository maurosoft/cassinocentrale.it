<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoomClosure extends Model
{
    /** @use HasFactory<\Database\Factories\RoomClosureFactory> */
    use HasFactory;

    protected $fillable = [
        'room_id',
        'start_date',
        'end_date',
        'reason',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    /** Chiusura dell'intero B&B (nessuna camera specifica). */
    public function isGlobal(): bool
    {
        return $this->room_id === null;
    }
}
