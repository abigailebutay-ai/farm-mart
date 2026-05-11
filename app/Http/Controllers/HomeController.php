<?php

namespace App\Http\Controllers;

use App\Models\Product;

class HomeController extends Controller
{
    /**
     * Show the home page.
     */
    public function index()
    {
        $featuredProducts = Product::inRandomOrder()->limit(8)->get();
        return view('home', ['featuredProducts' => $featuredProducts]);
    }
}
