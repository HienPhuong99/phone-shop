<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $query = Product::query()
            ->where('status', 'active')
            ->with(['brand', 'category', 'variants']);

        if ($request->filled('brand')) {
            $query->whereHas('brand', fn ($q) => $q->where('slug', $request->string('brand')));
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

        $brands = Brand::orderBy('name')->get();
        $categories = Category::whereNull('parent_id')->orderBy('name')->get();

        return view('products.index', compact('products', 'brands', 'categories'));
    }

    public function show(Product $product): View
    {
        $product->load(['brand', 'category', 'variants', 'images']);

        return view('products.show', compact('product'));
    }
}
