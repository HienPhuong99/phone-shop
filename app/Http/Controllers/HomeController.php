<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use App\Models\Product;
use App\Models\ProductSeries;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $heroProducts = Product::query()
            ->active()
            ->where('is_featured', true)
            ->with(['series', 'variants'])
            ->latest()
            ->take(6)
            ->get();

        $featuredProducts = Product::query()
            ->active()
            ->with(['series', 'variants'])
            ->withRatingStats()
            ->latest()
            ->take(8)
            ->get();

        $categories = Category::navList();
        $series = ProductSeries::navList();

        $latestPosts = Post::query()->published()->latestPublished()->take(3)->get();

        return view('home', compact('heroProducts', 'featuredProducts', 'categories', 'series', 'latestPosts'));
    }
}
