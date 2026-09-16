@props(['product'])

@php
    $stockToneClasses = match ($product->stock_status) {
        'out_of_stock' => 'text-red-500',
        'low_stock' => 'text-amber-600',
        default => 'text-emerald-600',
    };
@endphp

<a href="{{ route('products.show', $product->slug) }}" class="group block bg-white border border-line rounded-2xl shadow-sm hover:shadow-md transition overflow-hidden">
    <div class="relative aspect-square bg-gray-50 flex items-center justify-center overflow-hidden">
        @if ($product->discount_percent)
            <span class="absolute top-2.5 left-2.5 z-10 px-2 py-0.5 rounded-lg bg-[#FF6B4A] text-white text-[11px] font-bold">Giảm {{ $product->discount_percent }}%</span>
        @endif

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

        @if ($product->storage_options->count() > 1)
            <div class="mt-2 flex flex-wrap gap-1">
                @foreach ($product->storage_options as $storage)
                    <span class="text-[11px] font-medium text-ink-soft border border-line rounded-md px-1.5 py-0.5">{{ $storage }}</span>
                @endforeach
            </div>
        @endif

        <div class="mt-2 flex items-baseline gap-1.5 flex-wrap">
            <p class="text-brand font-bold">{{ number_format($product->base_price, 0, ',', '.') }}đ</p>
            @if ($product->discount_percent)
                <p class="text-xs text-ink-soft/70 line-through">{{ number_format($product->compare_at_price, 0, ',', '.') }}đ</p>
            @endif
        </div>

        <p class="mt-1.5 text-xs font-semibold {{ $stockToneClasses }}">{{ $product->stock_label }}</p>
    </div>
</a>
