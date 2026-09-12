<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $featuredProducts = Product::query()
            ->where('status', 'active')
            ->with(['brand', 'variants'])
            ->latest()
            ->take(8)
            ->get();

        $categories = Category::whereNull('parent_id')->orderBy('name')->get();
        $brands = Brand::orderBy('name')->get();

        return view('home', compact('featuredProducts', 'categories', 'brands'));
    }
}
