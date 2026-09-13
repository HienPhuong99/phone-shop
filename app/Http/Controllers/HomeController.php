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

        $categories = Category::whereNull('parent_id')->orderBy('name')->get();
        $series = ProductSeries::orderBy('sort_order')->get();

        return view('home', compact('featuredProducts', 'categories', 'series'));
    }
}
