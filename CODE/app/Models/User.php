<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * A person with an account: a member (customer) or an administrator.
 * Guests have no row in this table.
 */
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    // Allowed values of the "role" column. Use these instead of typing the text by hand.
    public const ROLE_CUSTOMER = 'customer';
    public const ROLE_ADMIN = 'admin';

    // Allowed values of the "status" column.
    public const STATUS_ACTIVE = 'active';
    public const STATUS_LOCKED = 'locked';

    /**
     * Columns that may be filled from a form.
     * "role" and "status" are left out on purpose so that a visitor cannot
     * make themselves an administrator by adding a field to the form.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'address',
    ];

    /**
     * Columns that are never sent to the browser.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed', // stored encrypted, never as plain text (R-22)
        ];
    }

    /**
     * Orders placed while logged in (R-20, R-25).
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * A member has at most one cart (R-31).
     */
    public function cart(): HasOne
    {
        return $this->hasOne(Cart::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    /**
     * A locked account cannot log in (R-23).
     */
    public function isLocked(): bool
    {
        return $this->status === self::STATUS_LOCKED;
    }
}
