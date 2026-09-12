<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ProductVariantController extends Controller
{
    public function store(Request $request, Product $product): RedirectResponse
    {
        $data = $this->validated($request);

        $product->variants()->create($data);

        return back()->with('status', 'Đã thêm biến thể.');
    }

    public function update(Request $request, Product $product, ProductVariant $variant): RedirectResponse
    {
        abort_unless($variant->product_id === $product->id, 404);

        $variant->update($this->validated($request, $variant));

        return back()->with('status', 'Đã cập nhật biến thể.');
    }

    public function destroy(Product $product, ProductVariant $variant): RedirectResponse
    {
        abort_unless($variant->product_id === $product->id, 404);

        $variant->delete();

        return back()->with('status', 'Đã xoá biến thể.');
    }

    private function validated(Request $request, ?ProductVariant $variant = null): array
    {
        return $request->validate([
            'color' => ['required', 'string', 'max:100'],
            'storage' => ['required', 'string', 'max:50'],
            'price' => ['required', 'numeric', 'min:0'],
            'sku' => ['required', 'string', 'max:100', 'unique:product_variants,sku,'.$variant?->id],
            'stock_quantity' => ['required', 'integer', 'min:0'],
        ]);
    }
}
