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
        $products = Product::query()
            ->active()
            ->with(['series', 'category', 'variants'])
            ->filter($request->all())
            ->sorted($request->string('sort')->toString())
            ->paginate(12)
            ->withQueryString();

        $allSeries = ProductSeries::navList();
        $categories = Category::navList();
        $storageOptions = Product::availableStorages();
        $colorOptions = Product::availableColors();

        return view('products.index', compact('products', 'allSeries', 'categories', 'storageOptions', 'colorOptions'));
    }

    public function show(Product $product): View
    {
        $product->load(['series', 'category', 'variants', 'images']);

        return view('products.show', compact('product'));
    }
}
