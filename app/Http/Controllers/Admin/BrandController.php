<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BrandController extends Controller
{
    public function index(): View
    {
        $brands = Brand::orderBy('name')->paginate(20);

        return view('admin.brands.index', compact('brands'));
    }

    public function create(): View
    {
        return view('admin.brands.create');
    }

    public function store(Request $request): RedirectResponse
    {
        Brand::create($this->validated($request));

        return redirect()->route('admin.brands.index')->with('status', 'Đã tạo thương hiệu.');
    }

    public function edit(Brand $brand): View
    {
        return view('admin.brands.edit', compact('brand'));
    }

    public function update(Request $request, Brand $brand): RedirectResponse
    {
        $brand->update($this->validated($request));

        return redirect()->route('admin.brands.index')->with('status', 'Đã cập nhật thương hiệu.');
    }

    public function destroy(Brand $brand): RedirectResponse
    {
        try {
            $brand->delete();
        } catch (QueryException) {
            return back()->with('error', 'Không thể xoá thương hiệu vì vẫn còn sản phẩm thuộc thương hiệu này.');
        }

        return back()->with('status', 'Đã xoá thương hiệu.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $data['slug'] = Str::slug($data['name']);

        return $data;
    }
}
