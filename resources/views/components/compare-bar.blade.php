{{-- Floating bar for the cross-page "so sánh" selection (Alpine.store('compare') in app.js). --}}
<div
    x-data
    x-show="$store.compare.items.length > 0"
    x-cloak
    x-transition
    class="fixed bottom-16 sm:bottom-4 inset-x-0 z-40 px-4"
>
    <div class="max-w-2xl mx-auto bg-ink text-white rounded-2xl shadow-lg px-4 py-3 flex items-center gap-3">
        <div class="flex -space-x-2 shrink-0">
            <template x-for="item in $store.compare.items" :key="item.slug">
                <span class="w-9 h-9 rounded-lg bg-white/10 border-2 border-ink flex items-center justify-center overflow-hidden">
                    <img :src="item.thumbnail" :alt="item.name" class="w-full h-full object-contain p-1">
                </span>
            </template>
        </div>

        <p class="flex-1 min-w-0 text-sm font-medium">
            Đã chọn <span x-text="$store.compare.items.length"></span>/<span x-text="$store.compare.max"></span> sản phẩm để so sánh
        </p>

        <button type="button" @click="$store.compare.clear()" class="shrink-0 text-xs font-semibold text-white/70 hover:text-white transition">Xoá</button>

        <a
            :href="`{{ route('compare.show') }}?slugs=${$store.compare.slugsQuery}`"
            :class="$store.compare.items.length < 2 ? 'pointer-events-none opacity-40' : ''"
            class="shrink-0 px-4 py-2 rounded-xl bg-white text-ink text-sm font-semibold transition"
        >
            So sánh ngay
        </a>
    </div>
</div>
