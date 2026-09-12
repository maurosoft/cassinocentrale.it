<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    /** @use HasFactory<\Database\Factories\CustomerFactory> */
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'notes',
    ];

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function fullName(): string
    {
        return trim($this->first_name.' '.$this->last_name);
    }

    /**
     * Trova un cliente esistente tramite email oppure ne crea uno nuovo,
     * aggiornando i dati con quelli forniti.
     */
    public static function upsertFrom(array $data): self
    {
        $email = $data['email'] ?? null;

        $customer = $email
            ? static::firstOrNew(['email' => $email])
            : new static();

        $customer->fill([
            'first_name' => $data['first_name'] ?? $customer->first_name,
            'last_name' => $data['last_name'] ?? $customer->last_name,
            'email' => $email ?: $customer->email,
            'phone' => $data['phone'] ?? $customer->phone,
        ]);
        $customer->save();

        return $customer;
    }
}
