@props(['product'])

@php
    $stockToneClasses = match ($product->stock_status) {
        'out_of_stock' => 'text-red-500',
        'low_stock' => 'text-amber-600',
        default => 'text-emerald-600',
    };
@endphp

<div class="group relative bg-white border border-line rounded-2xl shadow-sm hover:shadow-md transition overflow-hidden">
    <a href="{{ route('products.show', $product->slug) }}" class="block">
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

            @if ($product->reviews_count)
                <div class="mt-1.5 flex items-center gap-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-amber-500" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 3.5l2.6 5.3 5.9.85-4.25 4.15 1 5.85L12 16.9l-5.25 2.75 1-5.85L3.5 9.65l5.9-.85z" />
                    </svg>
                    <span class="text-xs font-semibold text-ink">{{ $product->average_rating }}</span>
                    <span class="text-xs text-ink-soft">({{ $product->reviews_count }})</span>
                </div>
            @endif

            <p class="mt-1.5 text-xs font-semibold {{ $stockToneClasses }}">{{ $product->stock_label }}</p>
        </div>
    </a>

    <button
        type="button"
        x-data
        @click="$store.compare.toggle({
            slug: {{ Illuminate\Support\Js::from($product->slug) }},
            name: {{ Illuminate\Support\Js::from($product->name) }},
            thumbnail: {{ Illuminate\Support\Js::from($product->thumbnail_thumb ?? $product->thumbnail) }},
        })"
        :class="$store.compare.has({{ Illuminate\Support\Js::from($product->slug) }}) ? 'border-brand bg-brand/5 text-brand' : 'border-line bg-white text-ink-soft'"
        class="absolute bottom-4 right-4 flex items-center gap-1.5 text-[11px] font-semibold border rounded-lg px-2 py-1 transition"
    >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <rect x="4" y="4" width="16" height="16" rx="4" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M8.5 12l2.25 2.25L15.5 9.5" :class="$store.compare.has({{ Illuminate\Support\Js::from($product->slug) }}) ? 'opacity-100' : 'opacity-0'" class="transition-opacity" />
        </svg>
        So sánh
    </button>
</div>
