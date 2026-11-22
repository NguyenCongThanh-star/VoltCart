<?php

namespace App\Services;

use App\Exceptions\BusinessRuleException;
use App\Models\Coupon;

/**
 * Checks discount coupons and works out the discount.
 *
 * Business rules:
 *   R-11  One coupon per order (a cart holds at most one coupon_id).
 *   R-12  A coupon is valid when it exists, is active, and the subtotal
 *         reaches its minimum order amount (VOLT200K: 200,000 VND off
 *         orders from 3,000,000 VND).
 *   R-13  If the subtotal drops below the minimum, the coupon is removed
 *         (done by CartService::summary() using isStillValid()).
 */
class CouponService
{
    /**
     * Finds an active coupon that can be used with this subtotal.
     *
     * @throws BusinessRuleException COUPON_INVALID or COUPON_MIN_NOT_MET
     */
    public function findUsable(string $code, int $subtotal): Coupon
    {
        $code = strtoupper(trim($code));

        $coupon = Coupon::active()->where('code', $code)->first();

        if (! $coupon) {
            throw new BusinessRuleException(
                'COUPON_INVALID',
                'This coupon code does not exist or is no longer active.',
                'code',
            );
        }

        if ($subtotal < $coupon->min_order_amount) {
            throw new BusinessRuleException(
                'COUPON_MIN_NOT_MET',
                sprintf(
                    '%s needs a subtotal of at least %s VND. Add %s VND more to use it.',
                    $coupon->code,
                    self::vnd($coupon->min_order_amount),
                    self::vnd($coupon->min_order_amount - $subtotal),
                ),
                'code',
            );
        }

        return $coupon;
    }

    /**
     * Whether a coupon already on a cart may stay there (R-13).
     */
    public function isStillValid(Coupon $coupon, int $subtotal): bool
    {
        return $coupon->is_active && $subtotal >= $coupon->min_order_amount;
    }

    /**
     * Amount taken off, in VND. Never more than the subtotal.
     */
    public function discountFor(Coupon $coupon, int $subtotal): int
    {
        return min($coupon->discount_amount, $subtotal);
    }

    /** 3000000 -> "3,000,000" */
    public static function vnd(int $amount): string
    {
        return number_format($amount, 0, '.', ',');
    }
}
