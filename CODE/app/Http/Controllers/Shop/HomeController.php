<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Home page (C-01).
     */
    public function index(): View
    {
        return view('shop.home');
    }
}
