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
            ->withRatingStats()
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

    public function show(Request $request, Product $product): View
    {
        $product->load(['series', 'category', 'variants', 'images', 'reviews' => fn ($q) => $q->with('user')->latest()]);
        $product->loadCount('reviews')->loadAvg('reviews', 'rating');

        $relatedProducts = Product::query()
            ->active()
            ->where('series_id', $product->series_id)
            ->where('id', '!=', $product->id)
            ->with(['series', 'variants'])
            ->withRatingStats()
            ->take(4)
            ->get();

        // "So sánh nhanh" picks whichever other active product sits closest
        // in price — usually the previous generation of the same tier.
        $comparisonProduct = Product::query()
            ->active()
            ->where('id', '!=', $product->id)
            ->with(['series', 'variants'])
            ->orderByRaw('ABS(base_price - ?)', [$product->base_price])
            ->first();

        $user = $request->user();
        $canReview = $user && ! $user->hasReviewed($product) && $user->purchasedOrderItemFor($product) !== null;
        $hasReviewed = $user && $user->hasReviewed($product);
        $isWishlisted = $user && $product->wishlists()->where('user_id', $user->id)->exists();

        return view('products.show', compact(
            'product', 'relatedProducts', 'comparisonProduct', 'canReview', 'hasReviewed', 'isWishlisted'
        ));
    }
}
