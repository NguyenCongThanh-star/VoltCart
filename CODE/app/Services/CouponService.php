<?php

namespace App\Services;

/**
 * Checks and applies discount coupons. Not implemented yet (planned for week 6).
 *
 * Business rules to implement here:
 *   R-11  One coupon per order.
 *   R-12  A coupon is valid when it exists, is active, and the subtotal
 *         reaches its minimum order amount (VOLT200K: 200,000 VND off
 *         orders from 3,000,000 VND).
 *   R-13  If the subtotal drops below the minimum, the coupon is removed.
 *
 * Planned methods:
 *   validate(string $code, int $subtotal)   returns the coupon or an error message
 *   discountFor(Coupon $coupon, int $subtotal)
 */
class CouponService
{
    //
}
