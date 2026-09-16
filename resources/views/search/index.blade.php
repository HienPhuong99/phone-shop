<x-shop-layout :title="($term !== '' ? 'Tìm kiếm: '.$term : 'Tìm kiếm').' - phuonghihi'">
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
                    <div class="bg-white border border-line rounded-2xl shadow-sm overflow-hidden">
                        <input type="checkbox" id="search-filter-toggle" class="peer hidden">
                        <label for="search-filter-toggle" class="lg:hidden flex items-center justify-between gap-2 p-4 cursor-pointer text-sm font-semibold text-ink select-none">
                            <span>Bộ lọc</span>
                            <svg class="h-4 w-4 text-ink-soft" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </label>

                        <form method="GET" action="{{ route('search') }}" class="hidden peer-checked:block lg:!block p-5 space-y-6">
                            <input type="hidden" name="q" value="{{ $term }}">

                            <div>
                                <h3 class="text-xs font-bold tracking-wide uppercase text-ink-soft mb-2">Danh mục</h3>
                                <div class="space-y-1">
                                    @foreach ($categories as $category)
                                        <label class="flex items-center gap-2 py-2 -mx-1 px-1 min-h-[44px] text-sm text-ink cursor-pointer">
                                            <input type="radio" name="category" value="{{ $category->slug }}" {{ request('category') === $category->slug ? 'checked' : '' }} onchange="this.form.submit()" class="h-4 w-4 text-brand focus:ring-brand">
                                            {{ $category->name }}
                                        </label>
                                    @endforeach
                                    @if (request('category'))
                                        <a href="{{ route('search', request()->except('category', 'page')) }}" class="text-xs text-brand hover:text-brand-dark font-medium inline-block mt-1">Bỏ lọc danh mục</a>
                                    @endif
                                </div>
                            </div>

                            <div>
                                <h3 class="text-xs font-bold tracking-wide uppercase text-ink-soft mb-2">Dòng sản phẩm</h3>
                                <div class="space-y-1">
                                    @foreach ($allSeries as $item)
                                        <label class="flex items-center gap-2 py-2 -mx-1 px-1 min-h-[44px] text-sm text-ink cursor-pointer">
                                            <input type="radio" name="series" value="{{ $item->slug }}" {{ request('series') === $item->slug ? 'checked' : '' }} onchange="this.form.submit()" class="h-4 w-4 text-brand focus:ring-brand">
                                            {{ $item->name }}
                                        </label>
                                    @endforeach
                                    @if (request('series'))
                                        <a href="{{ route('search', request()->except('series', 'page')) }}" class="text-xs text-brand hover:text-brand-dark font-medium inline-block mt-1">Bỏ lọc dòng sản phẩm</a>
                                    @endif
                                </div>
                            </div>

                            <div>
                                <h3 class="text-xs font-bold tracking-wide uppercase text-ink-soft mb-2">Khoảng giá</h3>
                                <div class="flex items-center gap-2">
                                    <input type="number" inputmode="numeric" name="min_price" value="{{ request('min_price') }}" placeholder="Từ" class="w-full rounded-xl border-line text-sm focus:border-brand focus:ring-brand">
                                    <span class="text-ink-soft">-</span>
                                    <input type="number" inputmode="numeric" name="max_price" value="{{ request('max_price') }}" placeholder="Đến" class="w-full rounded-xl border-line text-sm focus:border-brand focus:ring-brand">
                                </div>
                                <button type="submit" class="mt-3 w-full text-sm font-semibold bg-brand text-white rounded-xl py-2 hover:bg-brand-dark shadow-sm transition">Áp dụng</button>
                            </div>

                            <input type="hidden" name="sort" value="{{ request('sort') }}">
                        </form>
                    </div>
                </aside>

                <!-- Danh sách -->
                <div class="lg:col-span-3">
                    <div class="flex items-center justify-end mb-4">
                        <form method="GET" action="{{ route('search') }}">
                            @foreach (request()->except('sort', 'page') as $key => $value)
                                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                            @endforeach
                            <select name="sort" onchange="this.form.submit()" class="text-sm border-line rounded-xl focus:border-brand focus:ring-brand">
                                <option value="" {{ request('sort') === null || request('sort') === '' ? 'selected' : '' }}>Liên quan nhất</option>
                                <option value="price_asc" {{ request('sort') === 'price_asc' ? 'selected' : '' }}>Giá tăng dần</option>
                                <option value="price_desc" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>Giá giảm dần</option>
                            </select>
                        </form>
                    </div>

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
