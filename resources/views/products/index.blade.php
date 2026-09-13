<x-shop-layout title="Sản phẩm - phuonghihi">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <h1 class="font-bold text-3xl text-ink mb-6">Sản phẩm</h1>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <!-- Bộ lọc -->
            <aside class="lg:col-span-1">
                <form method="GET" action="{{ route('products.index') }}" class="bg-white border border-line rounded-2xl shadow-sm p-5 space-y-6">
                    <div>
                        <h3 class="text-xs font-bold tracking-wide uppercase text-ink-soft mb-2">Danh mục</h3>
                        <div class="space-y-1">
                            @foreach ($categories as $category)
                                <label class="flex items-center gap-2 text-sm text-ink cursor-pointer">
                                    <input type="radio" name="category" value="{{ $category->slug }}" {{ request('category') === $category->slug ? 'checked' : '' }} onchange="this.form.submit()" class="text-brand focus:ring-brand">
                                    {{ $category->name }}
                                </label>
                            @endforeach
                            @if (request('category'))
                                <a href="{{ route('products.index', request()->except('category', 'page')) }}" class="text-xs text-brand hover:text-brand-dark font-medium inline-block mt-1">Bỏ lọc danh mục</a>
                            @endif
                        </div>
                    </div>

                    <div>
                        <h3 class="text-xs font-bold tracking-wide uppercase text-ink-soft mb-2">Dòng sản phẩm</h3>
                        <div class="space-y-1">
                            @foreach ($allSeries as $item)
                                <label class="flex items-center gap-2 text-sm text-ink cursor-pointer">
                                    <input type="radio" name="series" value="{{ $item->slug }}" {{ request('series') === $item->slug ? 'checked' : '' }} onchange="this.form.submit()" class="text-brand focus:ring-brand">
                                    {{ $item->name }}
                                </label>
                            @endforeach
                            @if (request('series'))
                                <a href="{{ route('products.index', request()->except('series', 'page')) }}" class="text-xs text-brand hover:text-brand-dark font-medium inline-block mt-1">Bỏ lọc dòng sản phẩm</a>
                            @endif
                        </div>
                    </div>

                    <div>
                        <h3 class="text-xs font-bold tracking-wide uppercase text-ink-soft mb-2">Khoảng giá</h3>
                        <div class="flex items-center gap-2">
                            <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="Từ" class="w-full rounded-xl border-line text-sm focus:border-brand focus:ring-brand">
                            <span class="text-ink-soft">-</span>
                            <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="Đến" class="w-full rounded-xl border-line text-sm focus:border-brand focus:ring-brand">
                        </div>
                        <button type="submit" class="mt-3 w-full text-sm font-semibold bg-brand text-white rounded-xl py-2 hover:bg-brand-dark shadow-sm transition">Áp dụng</button>
                    </div>

                    <input type="hidden" name="sort" value="{{ request('sort') }}">
                </form>
            </aside>

            <!-- Danh sách -->
            <div class="lg:col-span-3">
                <div class="flex items-center justify-between mb-4">
                    <p class="text-sm text-ink-soft">{{ $products->total() }} sản phẩm</p>

                    <form method="GET" action="{{ route('products.index') }}">
                        @foreach (request()->except('sort', 'page') as $key => $value)
                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                        @endforeach
                        <select name="sort" onchange="this.form.submit()" class="text-sm border-line rounded-xl focus:border-brand focus:ring-brand">
                            <option value="" {{ request('sort') === null || request('sort') === '' ? 'selected' : '' }}>Mới nhất</option>
                            <option value="price_asc" {{ request('sort') === 'price_asc' ? 'selected' : '' }}>Giá tăng dần</option>
                            <option value="price_desc" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>Giá giảm dần</option>
                        </select>
                    </form>
                </div>

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
