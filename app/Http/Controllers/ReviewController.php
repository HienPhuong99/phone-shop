<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request, Product $product): RedirectResponse
    {
        $user = $request->user();

        if ($user->hasReviewed($product)) {
            return back()->with('error', 'Bạn đã đánh giá sản phẩm này rồi.');
        }

        $orderItem = $user->purchasedOrderItemFor($product);

        if (! $orderItem) {
            return back()->with('error', 'Bạn cần mua và nhận hàng sản phẩm này trước khi có thể đánh giá.');
        }

        $data = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'content' => ['nullable', 'string', 'max:1000'],
        ]);

        Review::create([
            'product_id' => $product->id,
            'user_id' => $user->id,
            'order_item_id' => $orderItem->id,
            'rating' => $data['rating'],
            'content' => $data['content'] ?? null,
        ]);

        return back()->with('status', 'Cảm ơn bạn đã đánh giá sản phẩm!');
    }
}
