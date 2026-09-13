<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductSeries;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $query = Product::query()
            ->where('status', 'active')
            ->with(['series', 'category', 'variants']);

        if ($request->filled('series')) {
            $query->whereHas('series', fn ($q) => $q->where('slug', $request->string('series')));
        }

        if ($request->filled('category')) {
            $query->whereHas('category', fn ($q) => $q->where('slug', $request->string('category')));
        }

        if ($request->filled('min_price')) {
            $query->where('base_price', '>=', (float) $request->input('min_price'));
        }

        if ($request->filled('max_price')) {
            $query->where('base_price', '<=', (float) $request->input('max_price'));
        }

        match ($request->string('sort')->toString()) {
            'price_asc' => $query->orderBy('base_price'),
            'price_desc' => $query->orderByDesc('base_price'),
            default => $query->latest(),
        };

        $products = $query->paginate(12)->withQueryString();

        $allSeries = ProductSeries::navList();
        $categories = Category::navList();

        return view('products.index', compact('products', 'allSeries', 'categories'));
    }

    public function show(Product $product): View
    {
        $product->load(['series', 'category', 'variants', 'images']);

        return view('products.show', compact('product'));
    }
}
