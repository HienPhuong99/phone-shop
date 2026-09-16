@props(['products'])

<div x-data="{ active: 0, count: {{ $products->count() }} }" class="aspect-square bg-white border border-line rounded-2xl shadow-sm relative overflow-hidden">
    @if ($products->count() > 1)
        <span class="absolute top-5 right-6 z-10 text-xs font-semibold text-ink-soft" x-text="(active + 1) + ' / ' + count"></span>
    @endif

    @foreach ($products as $index => $product)
        <a
            href="{{ route('products.show', $product->slug) }}"
            x-show="active === {{ $index }}"
            @if ($index > 0) x-cloak @endif
            class="absolute inset-0 flex flex-col items-center justify-center text-center p-8 group"
        >
            <div class="h-56 sm:h-64 w-full flex items-center justify-center">
                @if ($product->thumbnail)
                    <img src="{{ $product->thumbnail_thumb ?? $product->thumbnail }}" alt="{{ $product->name }}" class="max-h-full max-w-[70%] object-contain group-hover:scale-105 transition" />
                @else
                    <div class="w-40 h-full rounded-2xl bg-[repeating-linear-gradient(45deg,theme(colors.line),theme(colors.line)_8px,transparent_8px,transparent_16px)] flex items-end justify-center pb-3">
                        <span class="font-mono text-xs text-ink-soft bg-white px-2 py-0.5 rounded shadow-xs">ảnh sản phẩm</span>
                    </div>
                @endif
            </div>

            <p class="mt-5 text-xs font-bold tracking-wide uppercase text-ink-soft">{{ $product->series->name }}</p>
            <h3 class="mt-1 text-lg font-bold text-ink group-hover:text-brand transition">{{ $product->name }}</h3>

            @if ($product->featured_tagline)
                <p class="mt-2 text-sm text-ink-soft italic line-clamp-2 max-w-sm">&ldquo;{{ $product->featured_tagline }}&rdquo;</p>
            @endif

            <p class="mt-3 text-2xl font-extrabold text-brand">{{ number_format($product->base_price, 0, ',', '.') }}đ</p>
        </a>
    @endforeach

    @if ($products->count() > 1)
        <button
            type="button"
            @click.prevent="active = (active - 1 + count) % count"
            class="absolute left-4 top-1/2 -translate-y-1/2 w-9 h-9 rounded-full bg-white border border-line shadow-sm flex items-center justify-center text-ink hover:border-brand hover:text-brand transition"
            aria-label="Sản phẩm trước"
        >
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 6l-6 6 6 6" /></svg>
        </button>
        <button
            type="button"
            @click.prevent="active = (active + 1) % count"
            class="absolute right-4 top-1/2 -translate-y-1/2 w-9 h-9 rounded-full bg-white border border-line shadow-sm flex items-center justify-center text-ink hover:border-brand hover:text-brand transition"
            aria-label="Sản phẩm tiếp theo"
        >
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 6l6 6-6 6" /></svg>
        </button>

        <div class="absolute bottom-5 inset-x-0 flex items-center justify-center gap-1.5">
            @foreach ($products as $index => $product)
                <button
                    type="button"
                    @click.prevent="active = {{ $index }}"
                    :class="active === {{ $index }} ? 'w-5 bg-brand' : 'w-1.5 bg-line'"
                    class="h-1.5 rounded-full transition-all"
                    aria-label="Xem sản phẩm {{ $index + 1 }}"
                ></button>
            @endforeach
        </div>
    @endif
</div>
