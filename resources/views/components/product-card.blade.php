@props(['product'])

<a href="{{ route('products.show', $product->slug) }}" class="group block bg-white border border-line rounded-2xl shadow-sm hover:shadow-md transition overflow-hidden">
    <div class="aspect-square bg-gray-50 flex items-center justify-center overflow-hidden">
        @if ($product->thumbnail)
            <img src="{{ $product->thumbnail_thumb ?? $product->thumbnail }}" alt="{{ $product->name }}" loading="lazy" decoding="async" class="w-full h-full object-cover group-hover:scale-105 transition" />
        @else
            <div class="w-full h-full flex items-center justify-center bg-[repeating-linear-gradient(45deg,theme(colors.line),theme(colors.line)_8px,transparent_8px,transparent_16px)]">
                <span class="font-mono text-xs text-ink-soft bg-white px-2 py-0.5 rounded shadow-xs">ảnh sản phẩm</span>
            </div>
        @endif
    </div>
    <div class="p-4">
        <p class="text-xs font-bold tracking-wide uppercase text-ink-soft">{{ $product->series->name }}</p>
        <h3 class="mt-1 text-sm font-semibold text-ink group-hover:text-brand transition truncate">{{ $product->name }}</h3>
        <p class="mt-2 text-brand font-bold">{{ number_format($product->base_price, 0, ',', '.') }}đ</p>
    </div>
</a>
