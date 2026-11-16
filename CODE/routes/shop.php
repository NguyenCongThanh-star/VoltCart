<?php

use App\Http\Controllers\Shop\HomeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Shop routes (guests and members)
|--------------------------------------------------------------------------
| Controllers: app/Http/Controllers/Shop
| Views:       resources/views/shop
|
| Planned URLs:
|   GET  /                    home page                       (C-01)
|   GET  /products            list, search, filter, sort      (C-02, C-03, C-04)
|   GET  /products/{slug}     product detail                  (C-05)
|   GET  /cart                cart page                       (C-10)
|   POST /cart/items          add / update / remove (AJAX)    (C-10, C-11)
|   POST /cart/coupon         apply a coupon (AJAX)           (C-13)
|   GET  /checkout            checkout form                   (C-12)
|   POST /checkout            place the order                 (C-15, C-16)
*/

Route::get('/', [HomeController::class, 'index'])->name('home');
