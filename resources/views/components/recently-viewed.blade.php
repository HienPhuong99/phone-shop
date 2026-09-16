@props(['product'])

{{--
    Purely client-side: snapshots {slug, name, thumbnail, price, series} to
    localStorage on each product visit (capped at 8, newest first), then
    renders every OTHER stored snapshot. No server round trip, so it works
    for guests and never touches the DB.
--}}
<div
    x-data="{
        items: [],
        init() {
            let stored = [];
            try { stored = JSON.parse(localStorage.getItem('recentlyViewed') || '[]'); } catch (e) {}

            const current = {
                slug: {{ Illuminate\Support\Js::from($product->slug) }},
                name: {{ Illuminate\Support\Js::from($product->name) }},
                thumbnail: {{ Illuminate\Support\Js::from($product->thumbnail_thumb ?? $product->thumbnail) }},
                price: {{ (float) $product->base_price }},
                series: {{ Illuminate\Support\Js::from($product->series->name) }},
                url: {{ Illuminate\Support\Js::from(route('products.show', $product->slug)) }},
            };

            stored = [current, ...stored.filter((p) => p.slug !== current.slug)].slice(0, 8);
            try { localStorage.setItem('recentlyViewed', JSON.stringify(stored)); } catch (e) {}

            this.items = stored.filter((p) => p.slug !== current.slug);
        },
    }"
    x-show="items.length > 0"
    x-cloak
    class="mt-12 pt-8 border-t border-line"
>
    <h2 class="font-bold text-lg text-ink mb-4">Đã xem gần đây</h2>
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <template x-for="item in items" :key="item.slug">
            <a :href="item.url" class="block bg-white border border-line rounded-2xl shadow-sm hover:shadow-md transition overflow-hidden">
                <div class="aspect-square bg-gray-50 flex items-center justify-center overflow-hidden">
                    <img :src="item.thumbnail" :alt="item.name" loading="lazy" class="w-full h-full object-cover">
                </div>
                <div class="p-3">
                    <p class="text-[11px] font-bold tracking-wide uppercase text-ink-soft truncate" x-text="item.series"></p>
                    <h3 class="mt-0.5 text-sm font-semibold text-ink truncate" x-text="item.name"></h3>
                    <p class="mt-1 text-brand font-bold text-sm" x-text="new Intl.NumberFormat('vi-VN').format(item.price) + 'đ'"></p>
                </div>
            </a>
        </template>
    </div>
</div>
