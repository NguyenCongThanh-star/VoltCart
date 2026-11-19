<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * A discount code such as VOLT200K: a fixed amount off when the subtotal reaches a minimum.
 * Checking whether a code can be used belongs to App\Services\CouponService.
 */
class Coupon extends Model
{
    protected $fillable = [
        'code',
        'discount_amount',
        'min_order_amount',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'discount_amount' => 'integer',
            'min_order_amount' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Usage: Coupon::active()->where('code', $code)->first()
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
