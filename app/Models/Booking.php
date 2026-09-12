<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Booking extends Model
{
    /** @use HasFactory<\Database\Factories\BookingFactory> */
    use HasFactory;

    // Stati della prenotazione.
    public const STATUS_DRAFT = 'draft';
    public const STATUS_CONFIRMED = 'confirmed';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_COMPLETED = 'completed';

    public const STATUSES = [
        self::STATUS_DRAFT => 'Bozza',
        self::STATUS_CONFIRMED => 'Confermata',
        self::STATUS_CANCELLED => 'Annullata',
        self::STATUS_COMPLETED => 'Completata',
    ];

    // Stati del pagamento.
    public const PAYMENT_UNPAID = 'unpaid';
    public const PAYMENT_PAID = 'paid';
    public const PAYMENT_REFUNDED = 'refunded';

    protected $fillable = [
        'customer_id',
        'reference',
        'guest_name',
        'guest_email',
        'guest_phone',
        'check_in',
        'check_out',
        'number_of_guests',
        'notes',
        'internal_notes',
        'status',
        'total_price',
        'discount_percent',
        'payment_status',
        'stripe_payment_intent_id',
    ];

    protected function casts(): array
    {
        return [
            'check_in' => 'date',
            'check_out' => 'date',
            'total_price' => 'decimal:2',
            'discount_percent' => 'decimal:2',
        ];
    }

    public function rooms(): HasMany
    {
        return $this->hasMany(BookingRoom::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /** Numero di notti del soggiorno. */
    public function nights(): int
    {
        if (! $this->check_in || ! $this->check_out) {
            return 0;
        }

        return (int) $this->check_in->diffInDays($this->check_out);
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }
}
