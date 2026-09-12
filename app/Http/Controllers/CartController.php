<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddToCartRequest;
use App\Models\CartItem;
use App\Models\ProductVariant;
use App\Services\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    public function __construct(private readonly CartService $cartService) {}

    public function index(Request $request): View
    {
        $cart = $this->cartService->currentCart($request);
        $cart->load(['items.variant.product']);

        return view('cart.index', compact('cart'));
    }

    public function store(AddToCartRequest $request): RedirectResponse
    {
        $variant = ProductVariant::findOrFail($request->integer('variant_id'));
        $cart = $this->cartService->currentCart($request);

        $item = $cart->items()->where('variant_id', $variant->id)->first();
        $newQuantity = ($item?->quantity ?? 0) + $request->integer('quantity');

        if ($newQuantity > $variant->stock_quantity) {
            return back()->with('error', "Chỉ còn {$variant->stock_quantity} sản phẩm trong kho.");
        }

        if ($item) {
            $item->update(['quantity' => $newQuantity]);
        } else {
            $cart->items()->create([
                'variant_id' => $variant->id,
                'quantity' => $request->integer('quantity'),
            ]);
        }

        return back()->with('status', 'Đã thêm vào giỏ hàng.');
    }

    public function update(Request $request, CartItem $cartItem): RedirectResponse
    {
        $this->authorizeCartItem($request, $cartItem);

        $quantity = $request->integer('quantity');

        if ($quantity < 1) {
            return back()->with('error', 'Số lượng không hợp lệ.');
        }

        if ($quantity > $cartItem->variant->stock_quantity) {
            return back()->with('error', "Chỉ còn {$cartItem->variant->stock_quantity} sản phẩm trong kho.");
        }

        $cartItem->update(['quantity' => $quantity]);

        return back()->with('status', 'Đã cập nhật giỏ hàng.');
    }

    public function destroy(Request $request, CartItem $cartItem): RedirectResponse
    {
        $this->authorizeCartItem($request, $cartItem);

        $cartItem->delete();

        return back()->with('status', 'Đã xoá sản phẩm khỏi giỏ hàng.');
    }

    private function authorizeCartItem(Request $request, CartItem $cartItem): void
    {
        $cart = $this->cartService->currentCart($request);

        abort_unless($cartItem->cart_id === $cart->id, 403);
    }
}
