<x-shop-layout title="Sản phẩm - phuonghihi">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <h1 class="font-bold text-3xl text-ink mb-6">Sản phẩm</h1>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <!-- Bộ lọc -->
            <aside class="lg:col-span-1">
                <x-product-filters
                    :action="route('products.index')"
                    :categories="$categories"
                    :all-series="$allSeries"
                    :storage-options="$storageOptions"
                    :color-options="$colorOptions"
                />
            </aside>

            <!-- Danh sách -->
            <div class="lg:col-span-3">
                <div class="flex items-center justify-between mb-4">
                    <p class="text-sm text-ink-soft">{{ $products->total() }} sản phẩm</p>

                    <form method="GET" action="{{ route('products.index') }}">
                        @foreach (request()->except('sort', 'page') as $key => $value)
                            @if (is_array($value))
                                @foreach ($value as $item)
                                    <input type="hidden" name="{{ $key }}[]" value="{{ $item }}">
                                @endforeach
                            @else
                                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                            @endif
                        @endforeach
                        <select name="sort" onchange="this.form.submit()" class="text-sm border-line rounded-xl focus:border-brand focus:ring-brand">
                            <option value="" {{ request('sort') === null || request('sort') === '' ? 'selected' : '' }}>Mới nhất</option>
                            <option value="best_selling" {{ request('sort') === 'best_selling' ? 'selected' : '' }}>Bán chạy</option>
                            <option value="discount" {{ request('sort') === 'discount' ? 'selected' : '' }}>Giảm giá nhiều nhất</option>
                            <option value="price_asc" {{ request('sort') === 'price_asc' ? 'selected' : '' }}>Giá tăng dần</option>
                            <option value="price_desc" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>Giá giảm dần</option>
                        </select>
                    </form>
                </div>

                <x-active-filter-chips :action="route('products.index')" :categories="$categories" :all-series="$allSeries" />

                @if ($products->isEmpty())
                    <div class="bg-white border border-line rounded-2xl shadow-sm p-10 text-center text-ink-soft">
                        Không tìm thấy sản phẩm phù hợp.
                    </div>
                @else
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-6">
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
