<x-shop-layout title="So sánh sản phẩm - phuonghihi">
    <div
        class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8"
        x-data="{ hideMatches: false }"
    >
        <nav class="text-sm text-ink-soft mb-4">
            <a href="{{ route('home') }}" class="hover:text-brand transition">Trang chủ</a> /
            <span class="text-ink">So sánh sản phẩm</span>
        </nav>

        <h1 class="font-bold text-3xl text-ink mb-6">So sánh sản phẩm</h1>

        @if ($products->count() < 2)
            <div class="bg-white border border-line rounded-2xl shadow-sm p-10 text-center">
                <p class="text-ink-soft">Chọn ít nhất 2 sản phẩm để so sánh — bấm nút &ldquo;So sánh&rdquo; trên thẻ sản phẩm bất kỳ.</p>
                <a href="{{ route('products.index') }}" class="inline-block mt-4 px-6 py-2.5 rounded-2xl bg-brand hover:bg-brand-dark text-white text-sm font-semibold shadow-sm transition">Khám phá sản phẩm</a>
            </div>
        @else
            @php
                $matchCount = collect($specGroups)->flatMap(fn ($g) => $g['rows'])->filter(fn ($row) => $row['isMatch'])->count();
                $totalRows = collect($specGroups)->flatMap(fn ($g) => $g['rows'])->count() + 2; // + Giá từ, Dung lượng
            @endphp

            @if ($matchCount > 0)
                <div class="flex items-center justify-between gap-3 flex-wrap bg-white border border-line rounded-2xl px-4 py-3 mb-4">
                    <p class="text-sm text-ink-soft">
                        Đã tìm thấy <b class="text-ink font-bold">{{ $matchCount }}</b> điểm giống nhau trong <b class="text-ink font-bold">{{ $totalRows }}</b> thông số
                    </p>
                    <label for="hide-matches" class="inline-flex items-center gap-2.5 cursor-pointer select-none">
                        <span class="text-sm font-semibold text-ink">Ẩn điểm giống nhau</span>
                        <span class="relative inline-block w-9 h-5">
                            <input id="hide-matches" type="checkbox" x-model="hideMatches" class="peer absolute inset-0 opacity-0 cursor-pointer">
                            <span class="pointer-events-none absolute inset-0 rounded-full transition bg-line peer-checked:bg-emerald-600"></span>
                            <span class="pointer-events-none absolute top-0.5 left-0.5 w-4 h-4 bg-white rounded-full shadow-xs transition-transform peer-checked:translate-x-4"></span>
                        </span>
                    </label>
                </div>
            @endif

            <div class="rounded-2xl border border-line overflow-hidden bg-white shadow-sm overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-paper/60">
                            <th class="text-left font-medium text-ink-soft px-4 py-3 w-40">&nbsp;</th>
                            @foreach ($products as $product)
                                <th class="text-left px-4 py-3 min-w-[200px]">
                                    <a href="{{ route('products.show', $product->slug) }}" class="block hover:text-brand transition">
                                        <div class="aspect-square w-20 bg-gray-50 rounded-xl flex items-center justify-center overflow-hidden mb-2">
                                            @if ($product->thumbnail)
                                                <img src="{{ $product->thumbnail_thumb ?? $product->thumbnail }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                                            @endif
                                        </div>
                                        <p class="text-xs font-bold tracking-wide uppercase text-ink-soft">{{ $product->series->name }}</p>
                                        <p class="font-bold text-ink">{{ $product->name }}</p>
                                    </a>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line">
                        <tr>
                            <td class="px-4 py-3 font-medium text-ink-soft">Giá từ</td>
                            @foreach ($products as $product)
                                <td class="px-4 py-3 font-bold text-brand">{{ number_format($product->base_price, 0, ',', '.') }}đ</td>
                            @endforeach
                        </tr>
                        <tr>
                            <td class="px-4 py-3 font-medium text-ink-soft">Dung lượng</td>
                            @foreach ($products as $product)
                                <td class="px-4 py-3 text-ink">{{ $product->storage_options->implode(' / ') ?: '—' }}</td>
                            @endforeach
                        </tr>

                        @foreach ($specGroups as $group)
                            <tr class="bg-paper/60">
                                <td colspan="{{ $products->count() + 1 }}" class="px-4 py-2">
                                    <div class="flex items-center gap-2">
                                        <x-spec-icon :name="$group['icon']" class="h-4 w-4 text-brand shrink-0" />
                                        <span class="text-xs font-bold uppercase tracking-wider text-brand">{{ $group['label'] }}</span>
                                    </div>
                                </td>
                            </tr>
                            @foreach ($group['rows'] as $row)
                                <tr x-show="!(hideMatches && {{ $row['isMatch'] ? 'true' : 'false' }})" x-cloak>
                                    <td class="px-4 py-3 font-medium text-ink-soft align-top">
                                        <span class="flex items-center gap-1.5 flex-wrap">
                                            {{ $row['label'] }}
                                            @if ($row['isMatch'])
                                                <span class="inline-flex items-center gap-1 bg-emerald-50 border border-emerald-200 text-emerald-700 text-[10.5px] font-bold px-1.5 py-0.5 rounded-full whitespace-nowrap">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-2.5 w-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 12l5 5L19 7" />
                                                    </svg>
                                                    Giống nhau
                                                </span>
                                            @endif
                                        </span>
                                    </td>
                                    @foreach ($row['values'] as $index => $value)
                                        <td class="px-4 py-3 align-top {{ $row['winnerIndex'] === $index ? 'bg-brand/5 relative' : '' }}">
                                            @if ($row['winnerIndex'] === $index)
                                                <span class="absolute inset-[2px] border-[1.5px] border-brand/40 rounded-lg pointer-events-none"></span>
                                            @endif
                                            <span class="{{ $value === null ? 'text-ink-soft italic' : 'text-ink' }}">{{ $value ?? '—' }}</span>
                                            @if ($row['winnerIndex'] === $index && $row['delta'])
                                                <span class="flex items-center gap-1 text-[11px] font-bold text-brand mt-1">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.4">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 9l6 6 6-6" />
                                                    </svg>
                                                    {{ $row['delta'] }}
                                                </span>
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        @endforeach

                        <tr>
                            <td class="px-4 py-3 font-medium text-ink-soft">&nbsp;</td>
                            @foreach ($products as $product)
                                <td class="px-4 py-3">
                                    <a href="{{ route('products.show', $product->slug) }}" class="inline-block px-4 py-2 rounded-xl bg-brand hover:bg-brand-dark text-white text-xs font-semibold shadow-sm transition">Xem chi tiết</a>
                                </td>
                            @endforeach
                        </tr>
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</x-shop-layout>
