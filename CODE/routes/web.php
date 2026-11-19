<?php

/*
|--------------------------------------------------------------------------
| Web routes
|--------------------------------------------------------------------------
| The routes are split by area so that each member works in a separate file:
|
|   shop.php     customer-facing store (home, products, cart, checkout, chat)
|   account.php  register, login, profile, order history
|   admin.php    administrator area, everything under /admin
|
| Add a new route to the file of its area, not here.
*/

require __DIR__.'/shop.php';
require __DIR__.'/account.php';
require __DIR__.'/admin.php';
