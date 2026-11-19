<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * One item for sale. There are no variants: each configuration
 * (for example 8GB/256GB and 16GB/512GB) is a separate product.
 *
 * Prices are whole numbers in VND. To get the price a visitor should pay,
 * use App\Services\PricingService instead of reading the columns directly.
 */
class Product extends Model
{
    // Allowed values of the "status" column.
    public const STATUS_ACTIVE = 'active';   // shown in the shop
    public const STATUS_HIDDEN = 'hidden';   // kept for old orders, not shown in the shop

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'brand',
        'ram_gb',
        'storage_gb',
        'screen_size',
        'guest_price',
        'member_price',
        'stock',
        'description',
        'specifications',
        'is_featured',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'guest_price' => 'integer',
            'member_price' => 'integer',
            'stock' => 'integer',
            'is_featured' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    /**
     * Order lines that contain this product. A product with order lines cannot be deleted.
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Only products that customers may see.
     * Usage: Product::visible()->get()
     */
    public function scopeVisible(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function isInStock(): bool
    {
        return $this->stock > 0;
    }
}
