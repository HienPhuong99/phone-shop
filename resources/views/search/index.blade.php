<x-shop-layout
    :title="($term !== '' ? 'Tìm kiếm: '.$term : 'Tìm kiếm').' - phuonghihi'"
    :noindex="true"
>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <nav class="text-sm text-ink-soft mb-4">
            <a href="{{ route('home') }}" class="hover:text-brand transition">Trang chủ</a> /
            <span class="text-ink">Tìm kiếm</span>
        </nav>

        @if ($term !== '')
            <h1 class="font-bold text-3xl text-ink mb-2">Kết quả cho &ldquo;{{ $term }}&rdquo;</h1>
            <p class="text-ink-soft mb-6">{{ $products->total() }} sản phẩm phù hợp</p>
        @else
            <h1 class="font-bold text-3xl text-ink mb-6">Tìm kiếm sản phẩm</h1>
        @endif

        @if ($products->isEmpty())
            <div class="bg-white border border-line rounded-2xl shadow-sm p-8 sm:p-10">
                @if ($term === '')
                    <p class="text-center text-ink-soft">Nhập từ khoá ở ô tìm kiếm phía trên để bắt đầu.</p>
                @else
                    <div class="max-w-lg mx-auto text-center">
                        <p class="text-ink font-semibold">Không có sản phẩm nào khớp với &ldquo;{{ $term }}&rdquo;.</p>

                        @if ($suggestion)
                            <p class="mt-2 text-ink-soft">
                                Có phải bạn muốn tìm
                                <a href="{{ route('search', ['q' => $suggestion['name']]) }}" class="font-semibold text-brand hover:text-brand-dark transition">{{ $suggestion['name'] }}</a>?
                            </p>
                        @endif

                        <p class="mt-6 text-sm text-ink-soft">
                            Thử tên ngắn hơn, hoặc gõ không dấu cũng tìm được. Một số đời máy cũ đang tạm ẩn khỏi gian hàng —
                            gọi <a href="tel:0900300300" class="font-semibold text-ink hover:text-brand transition">0900 300 300</a> để hỏi tình trạng hàng về.
                        </p>

                        @if ($allSeries->isNotEmpty())
                            <div class="mt-6 flex flex-wrap justify-center gap-2">
                                @foreach ($allSeries->take(6) as $item)
                                    <a href="{{ route('products.index', ['series' => $item->slug]) }}" class="px-3.5 py-1.5 rounded-full border border-line text-sm font-medium text-ink hover:border-brand hover:text-brand transition">
                                        {{ $item->name }}
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        @else
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                <!-- Bộ lọc -->
                <aside class="lg:col-span-1">
                    <x-product-filters
                        :action="route('search')"
                        :categories="$categories"
                        :all-series="$allSeries"
                        :storage-options="$storageOptions"
                        :color-options="$colorOptions"
                    >
                        <x-slot:hiddenFields>
                            <input type="hidden" name="q" value="{{ $term }}">
                        </x-slot:hiddenFields>
                    </x-product-filters>
                </aside>

                <!-- Danh sách -->
                <div class="lg:col-span-3">
                    <div class="flex items-center justify-end mb-4">
                        <form method="GET" action="{{ route('search') }}">
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
                                <option value="" {{ request('sort') === null || request('sort') === '' ? 'selected' : '' }}>Liên quan nhất</option>
                                <option value="best_selling" {{ request('sort') === 'best_selling' ? 'selected' : '' }}>Bán chạy</option>
                                <option value="discount" {{ request('sort') === 'discount' ? 'selected' : '' }}>Giảm giá nhiều nhất</option>
                                <option value="price_asc" {{ request('sort') === 'price_asc' ? 'selected' : '' }}>Giá tăng dần</option>
                                <option value="price_desc" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>Giá giảm dần</option>
                            </select>
                        </form>
                    </div>

                    <x-active-filter-chips :action="route('search')" :categories="$categories" :all-series="$allSeries" />

                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-6">
                        @foreach ($products as $product)
                            <x-product-card :product="$product" />
                        @endforeach
                    </div>

                    <div class="mt-6">
                        {{ $products->links() }}
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-shop-layout>
