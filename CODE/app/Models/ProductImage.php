<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * One picture of a product. The file itself is stored in storage/app/public;
 * the "path" column keeps its location, for example products/iphone-15.jpg.
 */
class ProductImage extends Model
{
    protected $fillable = [
        'product_id',
        'path',
        'alt_text',
        'is_primary',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_primary' => 'boolean',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
