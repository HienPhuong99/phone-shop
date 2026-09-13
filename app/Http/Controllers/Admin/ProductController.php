<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductSeries;
use App\Services\ImageUploadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function __construct(private readonly ImageUploadService $imageUploadService) {}

    public function index(Request $request): View
    {
        $products = Product::query()
            ->with(['brand', 'category', 'series'])
            ->when($request->filled('search'), fn ($q) => $q->where('name', 'like', '%'.$request->string('search').'%'))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.products.index', compact('products'));
    }

    public function create(): View
    {
        $categories = Category::orderBy('name')->get();
        $brands = Brand::orderBy('name')->get();
        $allSeries = ProductSeries::orderBy('sort_order')->get();

        return view('admin.products.create', compact('categories', 'brands', 'allSeries'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $this->imageUploadService->store($request->file('thumbnail'), 'products');
        }

        $product = Product::create($data);

        return redirect()->route('admin.products.edit', $product)->with('status', 'Đã tạo sản phẩm. Thêm biến thể bên dưới.');
    }

    public function edit(Product $product): View
    {
        $product->load(['variants', 'images']);
        $categories = Category::orderBy('name')->get();
        $brands = Brand::orderBy('name')->get();
        $allSeries = ProductSeries::orderBy('sort_order')->get();

        return view('admin.products.edit', compact('product', 'categories', 'brands', 'allSeries'));
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $data = $this->validated($request, $product);

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $this->imageUploadService->store($request->file('thumbnail'), 'products');
        }

        $product->update($data);

        return redirect()->route('admin.products.edit', $product)->with('status', 'Đã cập nhật sản phẩm.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $product->delete();

        return redirect()->route('admin.products.index')->with('status', 'Đã xoá sản phẩm.');
    }

    private function validated(Request $request, ?Product $product = null): array
    {
        $data = $request->validate([
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'brand_id' => ['required', 'integer', 'exists:brands,id'],
            'series_id' => ['required', 'integer', 'exists:product_series,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'base_price' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'in:active,inactive'],
            'thumbnail' => ['nullable', 'image', 'max:4096'],
        ]);

        unset($data['thumbnail']);
        $data['slug'] = $product?->slug ?? Str::slug($data['name']).'-'.Str::random(4);

        return $data;
    }
}
