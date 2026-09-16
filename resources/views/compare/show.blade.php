<x-shop-layout title="So sánh sản phẩm - phuonghihi">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
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
                        @foreach ($specLabels as $label)
                            <tr>
                                <td class="px-4 py-3 font-medium text-ink-soft">{{ $label }}</td>
                                @foreach ($products as $product)
                                    <td class="px-4 py-3 text-ink">{{ $product->specifications[$label] ?? '—' }}</td>
                                @endforeach
                            </tr>
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
