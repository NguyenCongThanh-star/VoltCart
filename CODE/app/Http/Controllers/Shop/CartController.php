<?php

namespace App\Http\Controllers\Shop;

use App\Exceptions\BusinessRuleException;
use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Services\CartService;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

/**
 * Cart page and the cart AJAX API (C-10, C-11, C-13). Follows sequence diagram SQ-03.
 * Every JSON answer has the shape fixed in DOCUMENTATION/guides/CART_API.md:
 *   success: { ok: true,  message, cart, notices }
 *   error:   { ok: false, code, message, errors, cart, notices }   with HTTP 422 or 404
 *
 * The page view resources/views/shop/cart.blade.php is written by Mẫn.
 * It receives $cart and $notices (the same arrays as the JSON).
 */
class CartController extends Controller
{
    public function __construct(private readonly CartService $carts)
    {
    }

    /** GET /cart  (page, or JSON when the request asks for JSON) */
    public function index(Request $request): View|JsonResponse
    {
        $summary = $this->carts->summary($this->currentCart($request), $request->user());

        if ($request->wantsJson()) {
            return $this->ok($request, '', null, $summary);
        }

        return view('shop.cart', $summary);
    }

    /** POST /cart/items  { product_id, quantity }  adds to the quantity already in the cart */
    public function store(Request $request): JsonResponse
    {
        return $this->handle($request, [
            'product_id' => ['required', 'integer', 'min:1'],
            'quantity' => ['required', 'integer', 'min:1', 'max:999'],
        ], 'Added to cart.', true, function (Cart $cart, array $data) {
            $this->carts->add($cart, (int) $data['product_id'], (int) $data['quantity']);
        });
    }

    /** PATCH /cart/items/{productId}  { quantity } */
    public function update(Request $request, int $productId): JsonResponse
    {
        return $this->handle($request, [
            'quantity' => ['required', 'integer', 'min:1', 'max:999'],
        ], 'Cart updated.', false, function (Cart $cart, array $data) use ($productId) {
            $this->carts->update($cart, $productId, (int) $data['quantity']);
        });
    }

    /** DELETE /cart/items/{productId} */
    public function destroy(Request $request, int $productId): JsonResponse
    {
        return $this->handle($request, [], 'Removed from cart.', false, function (Cart $cart) use ($productId) {
            $this->carts->remove($cart, $productId);
        });
    }

    /** POST /cart/coupon  { code } */
    public function applyCoupon(Request $request): JsonResponse
    {
        return $this->handle($request, [
            'code' => ['required', 'string', 'max:30'],
        ], 'Coupon applied.', true, function (Cart $cart, array $data) use ($request) {
            $this->carts->applyCoupon($cart, $data['code'], $request->user());
        });
    }

    /** DELETE /cart/coupon */
    public function removeCoupon(Request $request): JsonResponse
    {
        return $this->handle($request, [], 'Coupon removed.', false, function (Cart $cart) {
            $this->carts->removeCoupon($cart);
        });
    }

    // ------------------------------------------------------------------

    /**
     * The same steps for every change: validate, find the cart, run the change,
     * then answer with the new cart, or with the error and the unchanged cart.
     */
    private function handle(Request $request, array $rules, string $message, bool $createCart, Closure $change): JsonResponse
    {
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->error($request, 'VALIDATION', $validator->errors()->first(), $validator->errors()->toArray(), 422);
        }

        $cart = $createCart
            ? $this->carts->findOrCreate($request->user(), $request->session()->getId())
            : $this->currentCart($request);

        try {
            if (! $cart) {
                throw new BusinessRuleException('NOT_IN_CART', 'Your cart is empty.', null, 404);
            }
            $change($cart, $validator->validated());
        } catch (BusinessRuleException $e) {
            $errors = $e->field ? [$e->field => [$e->getMessage()]] : [];

            return $this->error($request, $e->errorCode, $e->getMessage(), $errors, $e->status);
        }

        return $this->ok($request, $message, $cart->fresh());
    }

    private function currentCart(Request $request): ?Cart
    {
        return $this->carts->find($request->user(), $request->session()->getId());
    }

    private function ok(Request $request, string $message, ?Cart $cart, ?array $summary = null): JsonResponse
    {
        $summary ??= $this->carts->summary($cart, $request->user());

        return response()->json([
            'ok' => true,
            'message' => $message,
            'cart' => $summary['cart'],
            'notices' => $summary['notices'],
        ]);
    }

    private function error(Request $request, string $code, string $message, array $errors, int $status): JsonResponse
    {
        // The cart as it is now, so the page can show the real quantities again (decision G).
        $summary = $this->carts->summary($this->currentCart($request), $request->user());

        return response()->json([
            'ok' => false,
            'code' => $code,
            'message' => $message,
            'errors' => (object) $errors,
            'cart' => $summary['cart'],
            'notices' => $summary['notices'],
        ], $status);
    }
}
