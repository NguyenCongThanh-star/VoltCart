<?php

use App\Http\Controllers\Account\LoginController;
use App\Http\Controllers\Account\RegisterController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Account routes
|--------------------------------------------------------------------------
| Controllers: app/Http/Controllers/Account
| Views:       resources/views/account
|
| Planned URLs:
|   GET/POST /register            create an account            (C-06)
|   GET/POST /login, POST /logout                              (C-07)
|   GET/PUT  /account/profile     edit the profile             (C-08)
|   GET      /account/orders      order history and details    (C-17)
|
| Pages for visitors only go in the "guest" group.
| Pages that need a logged-in member go in the "auth" group.
*/

Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store']);

    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

    // profile and order-history routes
});
