@props(['action', 'categories', 'allSeries', 'storageOptions', 'colorOptions'])

@php
    $priceRanges = [
        ['label' => 'Dưới 10 triệu', 'min' => null, 'max' => 10000000],
        ['label' => '10 - 15 triệu', 'min' => 10000000, 'max' => 15000000],
        ['label' => '15 - 25 triệu', 'min' => 15000000, 'max' => 25000000],
        ['label' => 'Trên 25 triệu', 'min' => 25000000, 'max' => null],
    ];

    $currentMin = request('min_price');
    $currentMax = request('max_price');
    $selectedSeries = (array) request('series', []);
    $selectedStorages = (array) request('storage', []);
    $selectedColors = (array) request('color', []);
@endphp

<div class="bg-white border border-line rounded-2xl shadow-sm overflow-hidden" x-data="{ colorsExpanded: false }">
    <input type="checkbox" id="filter-toggle" class="peer hidden">
    <label for="filter-toggle" class="lg:hidden flex items-center justify-between gap-2 p-4 cursor-pointer text-sm font-semibold text-ink select-none">
        <span>Bộ lọc</span>
        <svg class="h-4 w-4 text-ink-soft" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
        </svg>
    </label>

    <form method="GET" action="{{ $action }}" class="hidden peer-checked:block lg:!block p-5 space-y-6">
        {{ $hiddenFields ?? '' }}
        <input type="hidden" name="sort" value="{{ request('sort') }}">

        <div>
            <h3 class="text-xs font-bold tracking-wide uppercase text-ink-soft mb-2">Danh mục</h3>
            <div class="space-y-1">
                @foreach ($categories as $category)
                    <label class="flex items-center gap-2 py-2 -mx-1 px-1 min-h-[44px] text-sm text-ink cursor-pointer">
                        <input type="radio" name="category" value="{{ $category->slug }}" {{ request('category') === $category->slug ? 'checked' : '' }} class="h-4 w-4 text-brand focus:ring-brand">
                        {{ $category->name }}
                    </label>
                @endforeach
            </div>
        </div>

        <div>
            <h3 class="text-xs font-bold tracking-wide uppercase text-ink-soft mb-2">Dòng sản phẩm</h3>
            <div class="space-y-1">
                @foreach ($allSeries as $item)
                    <label class="flex items-center gap-2 py-2 -mx-1 px-1 min-h-[44px] text-sm text-ink cursor-pointer">
                        <input type="checkbox" name="series[]" value="{{ $item->slug }}" {{ in_array($item->slug, $selectedSeries, true) ? 'checked' : '' }} class="h-4 w-4 rounded text-brand focus:ring-brand">
                        {{ $item->name }}
                    </label>
                @endforeach
            </div>
        </div>

        <div>
            <h3 class="text-xs font-bold tracking-wide uppercase text-ink-soft mb-2">Khoảng giá</h3>
            <div class="flex flex-wrap gap-1.5 mb-3">
                @foreach ($priceRanges as $range)
                    @php
                        $isActive = (string) $currentMin === (string) ($range['min'] ?? '') && (string) $currentMax === (string) ($range['max'] ?? '');
                    @endphp
                    <a
                        href="{{ $action }}?{{ http_build_query(array_merge(request()->except(['min_price', 'max_price', 'page']), array_filter(['min_price' => $range['min'], 'max_price' => $range['max']]))) }}"
                        class="px-2.5 py-1 rounded-full border text-xs font-medium transition {{ $isActive ? 'border-brand bg-brand/5 text-brand' : 'border-line text-ink-soft hover:border-brand hover:text-brand' }}"
                    >{{ $range['label'] }}</a>
                @endforeach
            </div>
            <div class="flex items-center gap-2">
                <input type="number" inputmode="numeric" name="min_price" value="{{ $currentMin }}" placeholder="Từ" class="w-full rounded-xl border-line text-sm focus:border-brand focus:ring-brand">
                <span class="text-ink-soft">-</span>
                <input type="number" inputmode="numeric" name="max_price" value="{{ $currentMax }}" placeholder="Đến" class="w-full rounded-xl border-line text-sm focus:border-brand focus:ring-brand">
            </div>
        </div>

        @if ($storageOptions->isNotEmpty())
            <div>
                <h3 class="text-xs font-bold tracking-wide uppercase text-ink-soft mb-2">Dung lượng</h3>
                <div class="flex flex-wrap gap-2">
                    @foreach ($storageOptions as $storage)
                        <label class="flex items-center gap-1.5 min-h-[36px] px-3 rounded-full border {{ in_array($storage, $selectedStorages, true) ? 'border-brand bg-brand/5' : 'border-line' }} text-sm text-ink cursor-pointer">
                            <input type="checkbox" name="storage[]" value="{{ $storage }}" {{ in_array($storage, $selectedStorages, true) ? 'checked' : '' }} class="h-3.5 w-3.5 rounded text-brand focus:ring-brand">
                            {{ $storage }}
                        </label>
                    @endforeach
                </div>
            </div>
        @endif

        @if ($colorOptions->isNotEmpty())
            <div>
                <h3 class="text-xs font-bold tracking-wide uppercase text-ink-soft mb-2">Màu sắc</h3>
                <div class="flex flex-wrap gap-2">
                    @foreach ($colorOptions as $index => $color)
                        <label
                            @if ($index >= 8) x-show="colorsExpanded" x-cloak @endif
                            class="flex items-center gap-1.5 min-h-[36px] px-3 rounded-full border {{ in_array($color, $selectedColors, true) ? 'border-brand bg-brand/5' : 'border-line' }} text-sm text-ink cursor-pointer"
                        >
                            <input type="checkbox" name="color[]" value="{{ $color }}" {{ in_array($color, $selectedColors, true) ? 'checked' : '' }} class="h-3.5 w-3.5 rounded text-brand focus:ring-brand">
                            {{ $color }}
                        </label>
                    @endforeach
                </div>
                @if ($colorOptions->count() > 8)
                    <button type="button" @click="colorsExpanded = !colorsExpanded" class="mt-2 text-xs font-semibold text-brand hover:text-brand-dark transition">
                        <span x-show="!colorsExpanded">Xem thêm màu</span>
                        <span x-show="colorsExpanded" x-cloak>Thu gọn</span>
                    </button>
                @endif
            </div>
        @endif

        <div>
            <label class="flex items-center gap-2 py-2 -mx-1 px-1 min-h-[44px] text-sm text-ink cursor-pointer">
                <input type="checkbox" name="in_stock" value="1" {{ request()->boolean('in_stock') ? 'checked' : '' }} class="h-4 w-4 rounded text-brand focus:ring-brand">
                Chỉ hiện máy còn hàng
            </label>
        </div>

        <div class="border-t border-line pt-4 space-y-2">
            <button type="submit" class="w-full text-sm font-semibold bg-brand text-white rounded-xl py-2.5 hover:bg-brand-dark shadow-sm transition">Áp dụng</button>
            @if (count(request()->except(['sort', 'page', 'q'])) > 0)
                <a href="{{ $action }}{{ request()->has('q') ? '?q='.urlencode(request('q')) : '' }}" class="block text-center text-xs font-semibold text-ink-soft hover:text-brand transition">Xoá tất cả bộ lọc</a>
            @endif
        </div>
    </form>
</div>
