<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WishlistController extends Controller
{
    public function index(Request $request): View
    {
        $products = Product::query()
            ->whereHas('wishlists', fn ($q) => $q->where('user_id', $request->user()->id))
            ->with(['series', 'category', 'variants'])
            ->withRatingStats()
            ->latest('updated_at')
            ->paginate(12);

        return view('wishlist.index', compact('products'));
    }

    public function store(Request $request, Product $product): RedirectResponse
    {
        $request->user()->wishlists()->firstOrCreate(['product_id' => $product->id]);

        return back()->with('status', 'Đã thêm vào danh sách yêu thích.');
    }

    public function destroy(Request $request, Product $product): RedirectResponse
    {
        $request->user()->wishlists()->where('product_id', $product->id)->delete();

        return back()->with('status', 'Đã bỏ khỏi danh sách yêu thích.');
    }
}
