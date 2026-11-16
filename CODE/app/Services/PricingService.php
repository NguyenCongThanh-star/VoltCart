<?php

namespace App\Services;

use App\Models\Product;
use App\Models\User;

/**
 * The one place that decides which price a visitor pays.
 *
 * Business rules (see DOCUMENTATION/analysis/PHAN_TICH_NGHIEP_VU.md):
 *   R-02  Guests pay the guest price, logged-in members pay the member price.
 *   R-03  The price is always worked out on the server.
 *
 * Every page (list, detail, cart, checkout) must call this class,
 * so that the price is the same everywhere.
 *
 * Example in a controller:
 *   $price = $pricing->priceFor($product, $request->user());
 */
class PricingService
{
    /**
     * Price of one unit, in VND.
     *
     * @param  User|null  $user  the logged-in user, or null for a guest
     */
    public function priceFor(Product $product, ?User $user): int
    {
        return $user ? $product->member_price : $product->guest_price;
    }

    /**
     * Price of several units, in VND.
     */
    public function lineTotal(Product $product, int $quantity, ?User $user): int
    {
        return $this->priceFor($product, $user) * $quantity;
    }
}
