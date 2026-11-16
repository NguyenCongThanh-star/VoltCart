<?php

namespace App\Services;

/**
 * Everything about the shopping cart. Not implemented yet (planned for week 5).
 *
 * Business rules to implement here:
 *   R-05  Only visible, in-stock products can be added.
 *   R-06  Quantity of each product: from 1 up to the stock.
 *   R-03  Prices come from PricingService, never from the browser.
 *
 * Planned methods:
 *   add(Product $product, int $quantity)
 *   update(int $productId, int $quantity)
 *   remove(int $productId)
 *   items()      products in the cart with quantity and line total
 *   subtotal()   sum of the line totals
 *   clear()      empty the cart after an order is placed
 */
class CartService
{
    //
}
