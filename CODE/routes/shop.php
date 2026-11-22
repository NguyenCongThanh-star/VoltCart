<?php

use App\Http\Controllers\Shop\CartController;
use App\Http\Controllers\Shop\CheckoutController;
use App\Http\Controllers\Shop\HomeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Shop routes (guests and members)
|--------------------------------------------------------------------------
| Controllers: app/Http/Controllers/Shop
| Views:       resources/views/shop
|
| URLs:
|   GET    /                        home page                       (C-01)
|   GET    /products                list, search, filter, sort      (C-02, C-03, C-04)  planned
|   GET    /products/{slug}         product detail                  (C-05)              planned
|   GET    /cart                    cart page (JSON if asked)       (C-10)
|   POST   /cart/items              add to cart (AJAX)              (C-10, C-11)
|   PATCH  /cart/items/{productId}  change the quantity (AJAX)      (C-10, C-11)
|   DELETE /cart/items/{productId}  remove from cart (AJAX)         (C-10)
|   POST   /cart/coupon             apply a coupon (AJAX)           (C-13)
|   DELETE /cart/coupon             remove the coupon (AJAX)        (C-13)
|   GET    /checkout                checkout form                   (C-12, C-14)
|   POST   /checkout                place the order                 (C-15)
|   GET    /checkout/confirmation   order placed page               (C-16)
|
| The JSON of the cart API is described in DOCUMENTATION/guides/CART_API.md.
*/

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::prefix('cart')->name('cart.')->controller(CartController::class)->group(function () {
    Route::get('/', 'index')->name('index');
    Route::post('/items', 'store')->name('items.store');
    Route::patch('/items/{productId}', 'update')->whereNumber('productId')->name('items.update');
    Route::delete('/items/{productId}', 'destroy')->whereNumber('productId')->name('items.destroy');
    Route::post('/coupon', 'applyCoupon')->name('coupon.store');
    Route::delete('/coupon', 'removeCoupon')->name('coupon.destroy');
});

Route::prefix('checkout')->name('checkout.')->controller(CheckoutController::class)->group(function () {
    Route::get('/', 'create')->name('create');
    Route::post('/', 'store')->name('store');
    Route::get('/confirmation', 'confirmation')->name('confirmation');
});
