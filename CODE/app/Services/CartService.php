<?php

namespace App\Services;

use App\Exceptions\BusinessRuleException;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Everything about the shopping cart.
 *
 * Where the cart is stored:
 *   Tables carts and cart_items. A member's cart is found by user_id, a guest's cart by
 *   the browser session id. Prices are never stored in the cart: they are recalculated.
 *
 * Business rules:
 *   R-02/R-03  Prices come from PricingService (member or guest price), never from the browser.
 *   R-05  Only visible, in-stock products can be added.
 *   R-06  Quantity of each product: from 1 up to the stock.
 *   R-13  A coupon whose minimum is no longer met is removed (in summary()).
 *   R-14  Delivery fee 30,000 VND (0 while the cart has nothing to pay for).
 *   R-15  Total = subtotal - discount + delivery fee.
 *   R-31  When a guest logs in, the guest cart is merged into the member cart.
 *
 * The JSON shape of summary() is fixed in DOCUMENTATION/guides/CART_API.md.
 */
class CartService
{
    // Values of the "problem" field of an item (CART_API.md, decision J).
    public const PROBLEM_UNAVAILABLE = 'unavailable';        // product hidden by the admin
    public const PROBLEM_OUT_OF_STOCK = 'out_of_stock';      // stock is 0
    public const PROBLEM_NOT_ENOUGH = 'not_enough_stock';    // quantity > stock

    public function __construct(
        private readonly PricingService $pricing,
        private readonly CouponService $coupons,
    ) {
    }

    // ------------------------------------------------------------------
    // Finding the cart of the current visitor
    // ------------------------------------------------------------------

    /** The visitor's cart, or null if they have never added anything. */
    public function find(?User $user, string $sessionId): ?Cart
    {
        return $user
            ? Cart::where('user_id', $user->id)->first()
            : Cart::where('session_id', $sessionId)->first();
    }

    /** The visitor's cart, created on the first "Add to cart". */
    public function findOrCreate(?User $user, string $sessionId): Cart
    {
        // createOrFirst: safe when two requests create the cart at the same moment.
        return $user
            ? Cart::createOrFirst(['user_id' => $user->id])
            : Cart::createOrFirst(['session_id' => $sessionId]);
    }

    /** Number shown on the cart icon in the header: total quantity. */
    public function count(?User $user, string $sessionId): int
    {
        $cart = $this->find($user, $sessionId);

        return $cart ? (int) $cart->items()->sum('quantity') : 0;
    }

    // ------------------------------------------------------------------
    // Changing the cart
    // ------------------------------------------------------------------

    /**
     * Adds a product. If it is already in the cart, the quantities are added up.
     *
     * @throws BusinessRuleException PRODUCT_UNAVAILABLE, NOT_ENOUGH_STOCK
     */
    public function add(Cart $cart, int $productId, int $quantity): void
    {
        DB::transaction(function () use ($cart, $productId, $quantity) {
            $this->lock($cart);

            $product = $this->sellableProduct($productId);           // R-05
            $item = $cart->items()->where('product_id', $productId)->first();
            $newQuantity = ($item?->quantity ?? 0) + $quantity;

            $this->ensureEnoughStock($product, $newQuantity);          // R-06

            if ($item) {
                $item->update(['quantity' => $newQuantity]);
            } else {
                $cart->items()->create(['product_id' => $productId, 'quantity' => $newQuantity]);
            }

            $cart->touch();
        });
    }

    /**
     * Sets the quantity of a product already in the cart.
     *
     * @throws BusinessRuleException NOT_IN_CART, PRODUCT_UNAVAILABLE, NOT_ENOUGH_STOCK
     */
    public function update(Cart $cart, int $productId, int $quantity): void
    {
        DB::transaction(function () use ($cart, $productId, $quantity) {
            $this->lock($cart);

            $item = $this->itemOrFail($cart, $productId);
            $product = $this->sellableProduct($productId);
            $this->ensureEnoughStock($product, $quantity);

            $item->update(['quantity' => $quantity]);
            $cart->touch();
        });
    }

    /**
     * @throws BusinessRuleException NOT_IN_CART
     */
    public function remove(Cart $cart, int $productId): void
    {
        $this->itemOrFail($cart, $productId)->delete();
        $cart->touch();
    }

    /**
     * Puts a coupon on the cart, replacing any other one (R-11).
     *
     * @throws BusinessRuleException COUPON_INVALID, COUPON_MIN_NOT_MET
     */
    public function applyCoupon(Cart $cart, string $code, ?User $user): void
    {
        $subtotal = $this->summary($cart, $user)['cart']['subtotal'];
        $coupon = $this->coupons->findUsable($code, $subtotal);

        $cart->update(['coupon_id' => $coupon->id]);
    }

    public function removeCoupon(Cart $cart): void
    {
        $cart->update(['coupon_id' => null]);
    }

    /** Empties the cart after an order is placed. */
    public function clear(Cart $cart): void
    {
        $cart->delete(); // its items are deleted with it (cascade)
    }

    /**
     * R-31: moves the guest cart into the member's cart right after login.
     * Quantities are added up but never above the stock.
     * The guest session id must be read BEFORE Auth::attempt(), which gives the session a new id.
     *
     * @return bool true if something was moved
     */
    public function mergeGuestCart(string $guestSessionId, User $user): bool
    {
        return DB::transaction(function () use ($guestSessionId, $user) {
            $guest = Cart::where('session_id', $guestSessionId)->lockForUpdate()->first();

            if (! $guest) {
                return false;
            }

            $guest->load('items.product');

            if ($guest->items->isEmpty()) {
                $guest->delete();

                return false;
            }

            $member = Cart::where('user_id', $user->id)->lockForUpdate()->first();

            // No member cart yet: the guest cart simply becomes the member's cart.
            if (! $member) {
                $guest->update(['user_id' => $user->id, 'session_id' => null]);

                return true;
            }

            $member->load('items');

            foreach ($guest->items as $guestItem) {
                $existing = $member->items->firstWhere('product_id', $guestItem->product_id);
                $wanted = ($existing?->quantity ?? 0) + $guestItem->quantity;
                // Not above the stock; at least 1 (an out-of-stock line is then shown as a problem).
                $quantity = max(1, min($wanted, $guestItem->product->stock));

                if ($existing) {
                    $existing->update(['quantity' => $quantity]);
                } else {
                    $member->items()->create(['product_id' => $guestItem->product_id, 'quantity' => $quantity]);
                }
            }

            if (! $member->coupon_id && $guest->coupon_id) {
                $member->update(['coupon_id' => $guest->coupon_id]);
            }

            $guest->delete();
            $member->touch();

            return true;
        });
    }

    // ------------------------------------------------------------------
    // Reading the cart (the "cart" object of the JSON answers)
    // ------------------------------------------------------------------

    /**
     * Works out prices and totals for the page or the JSON answer.
     * Also removes a coupon that is no longer valid (R-13) and says so in "notices".
     *
     * @return array{cart: array<string, mixed>, notices: list<array{code: string, message: string}>}
     */
    public function summary(?Cart $cart, ?User $user): array
    {
        $notices = [];
        $items = [];
        $count = 0;
        $subtotal = 0;
        $hasProblem = false;

        if ($cart) {
            $cart->load(['items' => fn ($q) => $q->orderBy('id'), 'items.product.images', 'coupon']);

            foreach ($cart->items as $item) {
                $product = $item->product;
                $unitPrice = $this->pricing->priceFor($product, $user);
                $problem = $this->problemOf($item);

                $items[] = [
                    'product_id' => $product->id,
                    'name' => $product->name,
                    'url' => url('/products/'.$product->slug),
                    'image' => $this->imageUrl($product),
                    'unit_price' => $unitPrice,
                    'quantity' => $item->quantity,
                    'line_total' => $unitPrice * $item->quantity,
                    'stock' => $product->stock,
                    'problem' => $problem,
                ];

                $count += $item->quantity;

                // Lines with a problem are shown but not paid for (decision J).
                if ($problem === null) {
                    $subtotal += $unitPrice * $item->quantity;
                } else {
                    $hasProblem = true;
                }
            }
        }

        $coupon = $cart?->coupon;

        if ($coupon && ! $this->coupons->isStillValid($coupon, $subtotal)) {
            $cart->update(['coupon_id' => null]);
            $cart->setRelation('coupon', null);
            $notices[] = [
                'code' => 'COUPON_REMOVED',
                'message' => $coupon->is_active
                    ? sprintf('Coupon %s was removed because the subtotal is below %s VND.', $coupon->code, CouponService::vnd($coupon->min_order_amount))
                    : sprintf('Coupon %s was removed because it is no longer active.', $coupon->code),
            ];
            $coupon = null;
        }

        $discount = $coupon ? $this->coupons->discountFor($coupon, $subtotal) : 0;
        $shippingFee = $subtotal > 0 ? OrderService::SHIPPING_FEE : 0;

        return [
            'cart' => [
                'items' => $items,
                'count' => $count,
                'subtotal' => $subtotal,
                'discount' => $discount,
                'shipping_fee' => $shippingFee,
                'total' => $subtotal - $discount + $shippingFee,
                'coupon' => $coupon ? [
                    'code' => $coupon->code,
                    'discount_amount' => $coupon->discount_amount,
                    'min_order_amount' => $coupon->min_order_amount,
                ] : null,
                'price_type' => $user ? 'member' : 'guest',
                'can_checkout' => $subtotal > 0 && ! $hasProblem,
            ],
            'notices' => $notices,
        ];
    }

    // ------------------------------------------------------------------
    // Helpers
    // ------------------------------------------------------------------

    /** Locks the cart row so two clicks at the same time cannot both write. */
    private function lock(Cart $cart): void
    {
        Cart::whereKey($cart->id)->lockForUpdate()->first();
    }

    private function itemOrFail(Cart $cart, int $productId): CartItem
    {
        $item = $cart->items()->where('product_id', $productId)->first();

        if (! $item) {
            throw new BusinessRuleException('NOT_IN_CART', 'This product is not in your cart.', 'product_id', 404);
        }

        return $item;
    }

    /** R-05: the product exists, is shown in the shop and is in stock. */
    private function sellableProduct(int $productId): Product
    {
        $product = Product::find($productId);

        if (! $product || $product->status !== Product::STATUS_ACTIVE) {
            throw new BusinessRuleException('PRODUCT_UNAVAILABLE', 'This product is no longer available.', 'product_id');
        }

        if (! $product->isInStock()) {
            throw new BusinessRuleException('PRODUCT_UNAVAILABLE', $product->name.' is out of stock.', 'product_id');
        }

        return $product;
    }

    /** R-06: the quantity is not above the stock. */
    private function ensureEnoughStock(Product $product, int $quantity): void
    {
        if ($quantity > $product->stock) {
            throw new BusinessRuleException(
                'NOT_ENOUGH_STOCK',
                sprintf('Only %d of %s left in stock. The maximum quantity is %d.', $product->stock, $product->name, $product->stock),
                'quantity',
            );
        }
    }

    private function problemOf(CartItem $item): ?string
    {
        $product = $item->product;

        return match (true) {
            $product->status !== Product::STATUS_ACTIVE => self::PROBLEM_UNAVAILABLE,
            $product->stock === 0 => self::PROBLEM_OUT_OF_STOCK,
            $item->quantity > $product->stock => self::PROBLEM_NOT_ENOUGH,
            default => null,
        };
    }

    private function imageUrl(Product $product): ?string
    {
        $image = $product->images->firstWhere('is_primary', true) ?? $product->images->first();

        return $image ? asset('storage/'.$image->path) : null;
    }
}
