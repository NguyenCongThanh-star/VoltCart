<?php

namespace App\Services;

use App\Exceptions\BusinessRuleException;
use App\Models\Cart;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;

/**
 * Places orders and changes their status. Follows sequence diagrams SQ-04 and SQ-05.
 *
 * Business rules:
 *   R-04  Copy each product's name and unit price into the order.
 *   R-07  Check the stock one last time before creating the order.
 *   R-08  Take the quantities out of the stock when the order is created.
 *   R-09  Put the quantities back when the order is cancelled.
 *   R-10  Create the order, its items and the stock change in ONE database transaction.
 *   R-14  Fixed delivery fee: 30,000 VND.
 *   R-15  Total = subtotal - discount + delivery fee (Order::total()).
 *   R-16  A new order starts as Pending.
 *   R-17  Allowed steps: Pending -> Confirmed -> Shipping -> Completed.
 *   R-18  Cancelling is allowed from Pending or Confirmed only.
 *   R-19  Completed and Cancelled are final.
 *
 * Admin controllers (Khôi) must call changeStatus() and never change "status" or the stock themselves.
 */
class OrderService
{
    public const SHIPPING_FEE = 30000; // VND (R-14)

    /** Which status can follow which (R-17, R-18, R-19). */
    public const TRANSITIONS = [
        Order::STATUS_PENDING => [Order::STATUS_CONFIRMED, Order::STATUS_CANCELLED],
        Order::STATUS_CONFIRMED => [Order::STATUS_SHIPPING, Order::STATUS_CANCELLED],
        Order::STATUS_SHIPPING => [Order::STATUS_COMPLETED],
        Order::STATUS_COMPLETED => [],
        Order::STATUS_CANCELLED => [],
    ];

    public function __construct(
        private readonly CartService $carts,
        private readonly PricingService $pricing,
        private readonly CouponService $coupons,
    ) {
    }

    /**
     * Turns the cart into an order.
     *
     * @param  array{customer_name: string, customer_email: string, customer_phone: string, shipping_address: string}  $customer
     *
     * @throws BusinessRuleException CART_EMPTY, CART_HAS_PROBLEMS, COUPON_REMOVED, NOT_ENOUGH_STOCK
     */
    public function place(?Cart $cart, array $customer, string $paymentMethod, ?User $user): Order
    {
        // First look, outside the transaction: this also removes a coupon that is no longer
        // valid (R-13), and that removal must be kept even though no order is created.
        $summary = $this->carts->summary($cart, $user);

        if (! $cart || $summary['cart']['count'] === 0) {
            throw new BusinessRuleException('CART_EMPTY', 'Your cart is empty.');
        }
        if ($summary['notices'] !== []) {
            throw new BusinessRuleException('COUPON_REMOVED', $summary['notices'][0]['message'].' Please check your order again.');
        }
        if (! $summary['cart']['can_checkout']) {
            throw new BusinessRuleException('CART_HAS_PROBLEMS', 'Some products in your cart are no longer available in that quantity. Please update your cart.');
        }

        // The order code is unique; if two orders get the same number at the same moment,
        // the second one simply tries again with the next number.
        for ($attempt = 1; ; $attempt++) {
            try {
                return DB::transaction(fn () => $this->createOrder($cart, $customer, $paymentMethod, $user));
            } catch (UniqueConstraintViolationException $e) {
                if ($attempt >= 3) {
                    throw $e;
                }
            }
        }
    }

    /**
     * Everything here happens in one transaction (R-10): if anything fails, nothing is saved.
     */
    private function createOrder(Cart $cart, array $customer, string $paymentMethod, ?User $user): Order
    {
        $cart = Cart::with(['items', 'coupon'])->lockForUpdate()->findOrFail($cart->id);

        // R-07: lock the products and check the stock one last time.
        // lockForUpdate makes a second buyer wait until this order is finished.
        $products = Product::whereIn('id', $cart->items->pluck('product_id'))
            ->lockForUpdate()
            ->get()
            ->keyBy('id');

        $missing = [];
        foreach ($cart->items as $item) {
            $product = $products->get($item->product_id);
            if (! $product || $product->status !== Product::STATUS_ACTIVE || $product->stock < $item->quantity) {
                $missing[] = sprintf('%s (%d left)', $product?->name ?? 'a removed product', $product?->stock ?? 0);
            }
        }
        if ($missing !== []) {
            throw new BusinessRuleException('NOT_ENOUGH_STOCK', 'Not enough stock for: '.implode(', ', $missing).'. Please update your cart.');
        }

        // Prices worked out again on the server (R-03).
        $lines = [];
        $subtotal = 0;
        foreach ($cart->items as $item) {
            $product = $products->get($item->product_id);
            $unitPrice = $this->pricing->priceFor($product, $user);
            $lines[] = [$product, $unitPrice, $item->quantity];
            $subtotal += $unitPrice * $item->quantity;
        }

        $coupon = $cart->coupon;
        if ($coupon && ! $this->coupons->isStillValid($coupon, $subtotal)) {
            throw new BusinessRuleException('COUPON_REMOVED', "Coupon {$coupon->code} can no longer be used. Please check your order again.");
        }

        $order = Order::create([
            'code' => $this->nextCode(),
            'user_id' => $user?->id,                    // null for a guest (R-20)
            'coupon_id' => $coupon?->id,
            'customer_name' => $customer['customer_name'],
            'customer_email' => $customer['customer_email'],
            'customer_phone' => $customer['customer_phone'],
            'shipping_address' => $customer['shipping_address'],
            'payment_method' => $paymentMethod,
            'discount' => $coupon ? $this->coupons->discountFor($coupon, $subtotal) : 0,
            'shipping_fee' => self::SHIPPING_FEE,
            'status' => Order::STATUS_PENDING,           // R-16
        ]);

        foreach ($lines as [$product, $unitPrice, $quantity]) {
            // R-04: name and price are copied, so later changes do not touch this order.
            $order->items()->create([
                'product_id' => $product->id,
                'product_name' => $product->name,
                'unit_price' => $unitPrice,
                'quantity' => $quantity,
            ]);

            // R-08: take the quantity out of the stock now.
            $product->decrement('stock', $quantity);
        }

        $this->carts->clear($cart);

        return $order->load('items');
    }

    /**
     * Next order code of today: VC-YYYYMMDD-XXXX, e.g. VC-20261125-0007 (7th order of that day).
     * The date is in the shop's timezone (APP_TIMEZONE = Asia/Ho_Chi_Minh).
     */
    private function nextCode(): string
    {
        $prefix = 'VC-'.now()->format('Ymd').'-';

        $last = Order::where('code', 'like', $prefix.'%')
            ->orderByDesc('code')
            ->lockForUpdate()
            ->value('code');

        $number = $last ? (int) substr($last, strlen($prefix)) + 1 : 1;

        return $prefix.str_pad((string) $number, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Statuses the admin may choose next for this order, e.g. to show only the allowed buttons.
     *
     * @return list<string>
     */
    public function allowedNextStatuses(Order $order): array
    {
        return self::TRANSITIONS[$order->status] ?? [];
    }

    /**
     * Moves an order to a new status. Cancelling puts the stock back (R-09).
     *
     * @throws BusinessRuleException INVALID_STATUS_CHANGE
     */
    public function changeStatus(Order $order, string $newStatus): Order
    {
        return DB::transaction(function () use ($order, $newStatus) {
            // Locked, so two admins clicking "Cancel" together cannot put the stock back twice.
            $order = Order::with('items')->lockForUpdate()->findOrFail($order->id);

            if (! in_array($newStatus, $this->allowedNextStatuses($order), true)) {
                throw new BusinessRuleException(
                    'INVALID_STATUS_CHANGE',
                    sprintf('An order that is %s cannot be changed to %s.', $order->status, $newStatus),
                    'status',
                );
            }

            if ($newStatus === Order::STATUS_CANCELLED) {
                foreach ($order->items as $item) {
                    Product::whereKey($item->product_id)->increment('stock', $item->quantity);
                }
            }

            $order->update(['status' => $newStatus]);

            return $order;
        });
    }
}
