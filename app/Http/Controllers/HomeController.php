<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductSeries;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $featuredProducts = Product::query()
            ->where('status', 'active')
            ->with(['series', 'variants'])
            ->latest()
            ->take(8)
            ->get();

        $categories = Category::navList();
        $series = ProductSeries::navList();

        return view('home', compact('featuredProducts', 'categories', 'series'));
    }
}
