<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /** Ruoli disponibili nell'area admin. */
    public const ROLE_SUPERADMIN = 'superadmin';
    public const ROLE_RECEPTION = 'reception';
    public const ROLE_EDITOR = 'editor';

    public const ROLES = [
        self::ROLE_SUPERADMIN => 'Superadmin',
        self::ROLE_RECEPTION => 'Reception',
        self::ROLE_EDITOR => 'Editor',
    ];

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isSuperadmin(): bool
    {
        return $this->role === self::ROLE_SUPERADMIN;
    }

    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }
}
