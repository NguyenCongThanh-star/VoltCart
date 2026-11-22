<?php

namespace App\Http\Controllers\Shop;

use App\Exceptions\BusinessRuleException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Shop\CheckoutRequest;
use App\Models\Order;
use App\Services\CartService;
use App\Services\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Checkout and order confirmation (C-12, C-14, C-15, C-16). Follows sequence diagram SQ-04.
 *
 * Views written by Mẫn:
 *   shop/checkout.blade.php            receives $cart, $notices, $prefill (old() wins over $prefill)
 *   shop/order-confirmation.blade.php  receives $order (with items) and $bankTransfer
 */
class CheckoutController extends Controller
{
    // Session key that remembers the order just placed, so only its buyer sees the confirmation page.
    private const PLACED_ORDER = 'placed_order_id';

    public function __construct(
        private readonly CartService $carts,
        private readonly OrderService $orders,
    ) {
    }

    /** GET /checkout */
    public function create(Request $request): View|RedirectResponse
    {
        $user = $request->user();
        $summary = $this->carts->summary($this->carts->find($user, $request->session()->getId()), $user);

        if (! $summary['cart']['can_checkout']) {
            return redirect()->route('cart.index')
                ->with('error', 'Your cart is empty or has products that are no longer available.');
        }

        // C-12: a member gets the form filled in from the profile.
        $prefill = [
            'customer_name' => $user?->name,
            'customer_email' => $user?->email,
            'customer_phone' => $user?->phone,
            'shipping_address' => $user?->address,
        ];

        return view('shop.checkout', $summary + ['prefill' => $prefill]);
    }

    /** POST /checkout */
    public function store(CheckoutRequest $request): RedirectResponse
    {
        $user = $request->user();
        $cart = $this->carts->find($user, $request->session()->getId());

        try {
            $order = $this->orders->place($cart, $request->customer(), $request->validated('payment_method'), $user);
        } catch (BusinessRuleException $e) {
            // R-07: back to the cart and say what is wrong; no order was created.
            return redirect()->route('cart.index')->with('error', $e->getMessage());
        }

        $request->session()->put(self::PLACED_ORDER, $order->id);

        return redirect()->route('checkout.confirmation');
    }

    /** GET /checkout/confirmation  (C-16) */
    public function confirmation(Request $request): View|RedirectResponse
    {
        $order = Order::with('items')->find($request->session()->get(self::PLACED_ORDER));

        if (! $order) {
            return redirect()->route('home');
        }

        return view('shop.order-confirmation', [
            'order' => $order,
            // Shown only for a bank transfer. The payment is simulated: no money moves.
            'bankTransfer' => config('shop.bank_transfer'),
        ]);
    }
}
