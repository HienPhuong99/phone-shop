@props(['product'])

<a href="{{ route('products.show', $product->slug) }}" class="group block bg-white border border-line rounded-[2px] overflow-hidden hover:shadow-sm transition">
    <div class="aspect-square bg-gray-100 flex items-center justify-center overflow-hidden">
        @if ($product->thumbnail)
            <img src="{{ $product->thumbnail }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-105 transition" />
        @else
            <div class="w-full h-full flex items-center justify-center bg-[repeating-linear-gradient(45deg,theme(colors.line),theme(colors.line)_8px,transparent_8px,transparent_16px)]">
                <span class="font-mono text-xs text-ink-soft bg-white px-1">ảnh sản phẩm</span>
            </div>
        @endif
    </div>
    <div class="p-4">
        <p class="text-xs font-bold tracking-wide uppercase text-ink-soft">{{ $product->brand->name }}</p>
        <h3 class="mt-1 text-sm font-semibold text-ink truncate">{{ $product->name }}</h3>
        <p class="mt-2 text-accent font-bold">{{ number_format($product->base_price, 0, ',', '.') }}đ</p>
    </div>
</a>
