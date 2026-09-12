<x-shop-layout title="Sản phẩm - Phone Shop">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">Sản phẩm</h1>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <!-- Bộ lọc -->
            <aside class="lg:col-span-1">
                <form method="GET" action="{{ route('products.index') }}" class="bg-white border border-gray-200 rounded-lg p-4 space-y-6">
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900 mb-2">Danh mục</h3>
                        <div class="space-y-1">
                            @foreach ($categories as $category)
                                <label class="flex items-center gap-2 text-sm text-gray-600">
                                    <input type="radio" name="category" value="{{ $category->slug }}" {{ request('category') === $category->slug ? 'checked' : '' }} onchange="this.form.submit()">
                                    {{ $category->name }}
                                </label>
                            @endforeach
                            @if (request('category'))
                                <a href="{{ route('products.index', request()->except('category', 'page')) }}" class="text-xs text-indigo-600 hover:underline">Bỏ lọc danh mục</a>
                            @endif
                        </div>
                    </div>

                    <div>
                        <h3 class="text-sm font-semibold text-gray-900 mb-2">Thương hiệu</h3>
                        <div class="space-y-1">
                            @foreach ($brands as $brand)
                                <label class="flex items-center gap-2 text-sm text-gray-600">
                                    <input type="radio" name="brand" value="{{ $brand->slug }}" {{ request('brand') === $brand->slug ? 'checked' : '' }} onchange="this.form.submit()">
                                    {{ $brand->name }}
                                </label>
                            @endforeach
                            @if (request('brand'))
                                <a href="{{ route('products.index', request()->except('brand', 'page')) }}" class="text-xs text-indigo-600 hover:underline">Bỏ lọc thương hiệu</a>
                            @endif
                        </div>
                    </div>

                    <div>
                        <h3 class="text-sm font-semibold text-gray-900 mb-2">Khoảng giá</h3>
                        <div class="flex items-center gap-2">
                            <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="Từ" class="w-full rounded-md border-gray-300 text-sm">
                            <span class="text-gray-400">-</span>
                            <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="Đến" class="w-full rounded-md border-gray-300 text-sm">
                        </div>
                        <button type="submit" class="mt-2 w-full text-sm bg-indigo-600 text-white rounded-md py-1.5 hover:bg-indigo-700">Áp dụng</button>
                    </div>

                    <input type="hidden" name="sort" value="{{ request('sort') }}">
                </form>
            </aside>

            <!-- Danh sách -->
            <div class="lg:col-span-3">
                <div class="flex items-center justify-between mb-4">
                    <p class="text-sm text-gray-500">{{ $products->total() }} sản phẩm</p>

                    <form method="GET" action="{{ route('products.index') }}">
                        @foreach (request()->except('sort', 'page') as $key => $value)
                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                        @endforeach
                        <select name="sort" onchange="this.form.submit()" class="text-sm rounded-md border-gray-300">
                            <option value="" {{ request('sort') === null || request('sort') === '' ? 'selected' : '' }}>Mới nhất</option>
                            <option value="price_asc" {{ request('sort') === 'price_asc' ? 'selected' : '' }}>Giá tăng dần</option>
                            <option value="price_desc" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>Giá giảm dần</option>
                        </select>
                    </form>
                </div>

                @if ($products->isEmpty())
                    <div class="bg-white border border-gray-200 rounded-lg p-10 text-center text-gray-500">
                        Không tìm thấy sản phẩm phù hợp.
                    </div>
                @else
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                        @foreach ($products as $product)
                            <x-product-card :product="$product" />
                        @endforeach
                    </div>

                    <div class="mt-6">
                        {{ $products->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-shop-layout>
