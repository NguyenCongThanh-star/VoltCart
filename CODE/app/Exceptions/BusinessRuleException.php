<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * Thrown by the services when a business rule says no, for example
 * "not enough stock" or "this coupon needs a bigger order".
 *
 * It carries everything the controller needs to answer:
 *   - errorCode  fixed text for JavaScript, e.g. NOT_ENOUGH_STOCK (see DOCUMENTATION/guides/CART_API.md)
 *   - message    sentence shown to the customer
 *   - field      form field the error belongs to, e.g. "quantity" (or null)
 *   - status     HTTP status, 422 unless said otherwise
 */
class BusinessRuleException extends RuntimeException
{
    public function __construct(
        public readonly string $errorCode,
        string $message,
        public readonly ?string $field = null,
        public readonly int $status = 422,
    ) {
        parent::__construct($message);
    }
}
