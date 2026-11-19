<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * An order placed by a member or a guest.
 *
 * The subtotal and the total are not stored (BCNF): they are worked out from the items.
 * Load the items first to avoid one query per order: Order::with('items')->get()
 *
 * Placing an order and changing its status belong to App\Services\OrderService.
 */
class Order extends Model
{
    // Allowed values of the "status" column (R-16 to R-19).
    public const STATUS_PENDING = 'pending';
    public const STATUS_CONFIRMED = 'confirmed';
    public const STATUS_SHIPPING = 'shipping';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';

    // Allowed values of the "payment_method" column.
    public const PAYMENT_COD = 'cod';
    public const PAYMENT_BANK_TRANSFER = 'bank_transfer';

    protected $fillable = [
        'code',
        'user_id',
        'coupon_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'shipping_address',
        'payment_method',
        'discount',
        'shipping_fee',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'discount' => 'integer',
            'shipping_fee' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Sum of the line totals, in VND.
     */
    public function subtotal(): int
    {
        return $this->items->sum(fn (OrderItem $item) => $item->lineTotal());
    }

    /**
     * R-15: total = subtotal - discount + delivery fee, in VND.
     */
    public function total(): int
    {
        return $this->subtotal() - $this->discount + $this->shipping_fee;
    }

    public function isGuestOrder(): bool
    {
        return $this->user_id === null;
    }

    /**
     * Only completed orders count as revenue (R-21).
     * Usage: Order::completed()->whereDate('created_at', today())->get()
     */
    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }
}
