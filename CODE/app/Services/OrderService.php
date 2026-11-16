<?php

namespace App\Services;

/**
 * Places orders and changes their status. Not implemented yet (planned for week 5).
 *
 * Business rules to implement here:
 *   R-04  Copy each product's name and unit price into the order.
 *   R-07  Check the stock one last time before creating the order.
 *   R-08  Take the quantities out of the stock when the order is created.
 *   R-09  Put the quantities back when the order is cancelled.
 *   R-10  Create the order, its items and the stock change in ONE database transaction.
 *   R-14  Fixed delivery fee: 30,000 VND.
 *   R-15  Total = subtotal - discount + delivery fee.
 *   R-16  A new order starts as Pending.
 *   R-17  Allowed steps: Pending -> Confirmed -> Shipping -> Completed.
 *   R-18  Cancelling is allowed from Pending or Confirmed only.
 *   R-19  Completed and Cancelled are final.
 *
 * Planned methods:
 *   place(array $customer, string $paymentMethod, ?User $user)   returns the new order
 *   changeStatus(Order $order, string $newStatus)
 */
class OrderService
{
    public const SHIPPING_FEE = 30000; // VND (R-14)
}
