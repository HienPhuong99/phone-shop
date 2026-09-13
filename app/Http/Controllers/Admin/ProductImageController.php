<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductImage;
use App\Services\ImageUploadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ProductImageController extends Controller
{
    public function __construct(private readonly ImageUploadService $imageUploadService) {}

    public function store(Request $request, Product $product): RedirectResponse
    {
        $request->validate([
            'image' => ['required', 'image', 'max:4096'],
        ]);

        $url = $this->imageUploadService->store($request->file('image'), 'products');

        $product->images()->create([
            'url' => $url,
            'sort_order' => $product->images()->max('sort_order') + 1,
        ]);

        return back()->with('status', 'Đã thêm ảnh.');
    }

    public function destroy(Product $product, ProductImage $image): RedirectResponse
    {
        abort_unless($image->product_id === $product->id, 404);

        $image->delete();

        return back()->with('status', 'Đã xoá ảnh.');
    }
}
