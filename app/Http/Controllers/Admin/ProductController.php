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
            $image = $this->imageUploadService->store($request->file('thumbnail'), 'products');
            $data['thumbnail'] = $image->url;
            $data['thumbnail_thumb'] = $image->thumbUrl;
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
            $image = $this->imageUploadService->store($request->file('thumbnail'), 'products');
            $data['thumbnail'] = $image->url;
            $data['thumbnail_thumb'] = $image->thumbUrl;
        }

        $product->update($data);

        return redirect()->route('admin.products.edit', $product)->with('status', 'Đã cập nhật sản phẩm.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $product->delete();

        return redirect()->route('admin.products.index')->with('status', 'Đã xoá sản phẩm.');
    }

    public function toggleStatus(Product $product): RedirectResponse
    {
        $product->update(['status' => $product->status === 'active' ? 'inactive' : 'active']);

        $message = $product->status === 'active' ? 'Đã hiện sản phẩm.' : 'Đã ẩn sản phẩm.';

        return back()->with('status', $message);
    }

    public function toggleFeatured(Product $product): RedirectResponse
    {
        $product->update(['is_featured' => ! $product->is_featured]);

        $message = $product->is_featured ? 'Đã thêm vào sản phẩm nổi bật.' : 'Đã bỏ khỏi sản phẩm nổi bật.';

        return back()->with('status', $message);
    }

    private function validated(Request $request, ?Product $product = null): array
    {
        $data = $request->validate([
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'brand_id' => ['required', 'integer', 'exists:brands,id'],
            'series_id' => ['required', 'integer', 'exists:product_series,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'specifications' => ['nullable', 'string'],
            'base_price' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'in:active,inactive'],
            'thumbnail' => ['nullable', 'image', 'max:4096'],
            'is_featured' => ['sometimes', 'boolean'],
            'featured_tagline' => ['nullable', 'string', 'max:160'],
        ]);

        unset($data['thumbnail']);
        $data['is_featured'] = $request->boolean('is_featured');
        $data['specifications'] = $this->parseSpecifications($data['specifications'] ?? null);
        $data['slug'] = $product?->slug ?? Str::slug($data['name']).'-'.Str::random(4);

        return $data;
    }

    /**
     * Parse "Label: value" lines from the admin textarea into an ordered map.
     *
     * @return array<string, string>|null
     */
    private function parseSpecifications(?string $raw): ?array
    {
        if (blank($raw)) {
            return null;
        }

        $specifications = [];

        foreach (preg_split('/\r\n|\r|\n/', $raw) as $line) {
            if (blank($line) || ! str_contains($line, ':')) {
                continue;
            }

            [$label, $value] = explode(':', $line, 2);
            $label = trim($label);
            $value = trim($value);

            if ($label !== '' && $value !== '') {
                $specifications[$label] = $value;
            }
        }

        return $specifications === [] ? null : $specifications;
    }
}
