<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Administrator routes
|--------------------------------------------------------------------------
| Controllers: app/Http/Controllers/Admin
| Views:       resources/views/admin
|
| Every route in this group:
|   - starts with /admin            (prefix)
|   - is named admin.something      (name)
|   - requires a logged-in administrator ("auth" + "admin" middleware, rule R-24)
|
| Planned URLs:
|   GET /admin                 dashboard                       (Q-02)
|   /admin/categories          category management             (Q-03)
|   /admin/products            product management              (Q-04, Q-05, Q-06)
|   /admin/orders              orders and status updates       (Q-07, Q-08)
|   /admin/customers           customers, lock / unlock        (Q-09)
|   /admin/coupons             coupon management               (Q-10)
|   /admin/reports/revenue     revenue report                  (Q-11)
*/

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'admin'])
    ->group(function () {
        // administrator routes
    });
