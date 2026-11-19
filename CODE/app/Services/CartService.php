<?php

namespace App\Services;

/**
 * Everything about the shopping cart. Not implemented yet (planned for week 5).
 *
 * Where the cart is stored:
 *   Tables carts and cart_items. A member's cart is found by user_id, a guest's cart by
 *   the browser session id. Prices are never stored in the cart: they are recalculated.
 *
 * Business rules to implement here:
 *   R-05  Only visible, in-stock products can be added.
 *   R-06  Quantity of each product: from 1 up to the stock.
 *   R-03  Prices come from PricingService, never from the browser.
 *   R-31  When a guest logs in, the guest cart is merged into the member cart.
 *
 * Planned methods:
 *   add(Product $product, int $quantity)
 *   update(int $productId, int $quantity)
 *   remove(int $productId)
 *   items()      products in the cart with quantity and line total
 *   subtotal()   sum of the line totals
 *   clear()      empty the cart after an order is placed
 *   mergeGuestCart(User $user)   called right after login (R-31)
 */
class CartService
{
    //
}
