<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductSeries;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProductSeriesController extends Controller
{
    public function index(): View
    {
        $productSeries = ProductSeries::withCount('products')
            ->orderBy('sort_order')
            ->paginate(20);

        return view('admin.product-series.index', compact('productSeries'));
    }

    public function create(): View
    {
        return view('admin.product-series.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        if (! isset($data['sort_order']) || $data['sort_order'] === null) {
            $data['sort_order'] = ((int) ProductSeries::max('sort_order')) + 1;
        }

        ProductSeries::create($data);

        return redirect()->route('admin.product-series.index')->with('status', 'Đã tạo dòng sản phẩm.');
    }

    public function edit(ProductSeries $productSeries): View
    {
        return view('admin.product-series.edit', compact('productSeries'));
    }

    public function update(Request $request, ProductSeries $productSeries): RedirectResponse
    {
        $data = $this->validated($request);

        if (! isset($data['sort_order']) || $data['sort_order'] === null) {
            $data['sort_order'] = $productSeries->sort_order ?? 0;
        }

        $productSeries->update($data);

        return redirect()->route('admin.product-series.index')->with('status', 'Đã cập nhật dòng sản phẩm.');
    }

    public function destroy(ProductSeries $productSeries): RedirectResponse
    {
        try {
            $productSeries->delete();
        } catch (QueryException) {
            return back()->with('error', 'Không thể xoá dòng sản phẩm vì vẫn còn sản phẩm thuộc dòng này.');
        }

        return back()->with('status', 'Đã xoá dòng sản phẩm.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $data['slug'] = Str::slug($data['name']);

        return $data;
    }
}
