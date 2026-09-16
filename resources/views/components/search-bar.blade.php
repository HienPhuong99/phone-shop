@php
    $quickSeries = \App\Models\ProductSeries::navList()->take(4);
@endphp

<div
    x-data="{
        open: false,
        mobileOpen: false,
        q: '',
        results: [],
        total: 0,
        loading: false,
        highlighted: -1,
        controller: null,
        popular: ['iPhone 16 Pro Max', 'iPhone 15', 'iPhone 14', 'iPhone 13'],
        recent: (() => {
            try { return JSON.parse(localStorage.getItem('recentSearches') || '[]'); }
            catch (e) { return []; }
        })(),
        async search() {
            const term = this.q.trim();

            if (term.length < 2) {
                this.results = [];
                this.total = 0;
                this.highlighted = -1;
                return;
            }

            this.loading = true;
            if (this.controller) this.controller.abort();
            this.controller = new AbortController();

            try {
                const res = await fetch(`{{ route('search.suggest') }}?q=${encodeURIComponent(term)}`, { signal: this.controller.signal });
                if (!res.ok) throw new Error('search request failed');
                const data = await res.json();
                this.results = data.products;
                this.total = data.total;
                this.highlighted = -1;
            } catch (e) {
                if (e.name !== 'AbortError') {
                    this.results = [];
                    this.total = 0;
                }
            } finally {
                this.loading = false;
            }
        },
        moveHighlight(delta) {
            if (!this.results.length) return;
            this.highlighted = (this.highlighted + delta + this.results.length) % this.results.length;
        },
        addRecent(term) {
            const t = term.trim();
            if (!t) return;
            this.recent = [t, ...this.recent.filter(r => r !== t)].slice(0, 5);
            try { localStorage.setItem('recentSearches', JSON.stringify(this.recent)); } catch (e) {}
        },
        removeRecent(term) {
            this.recent = this.recent.filter(r => r !== term);
            try { localStorage.setItem('recentSearches', JSON.stringify(this.recent)); } catch (e) {}
        },
        goTo(url, term) {
            this.addRecent(term);
            window.location.href = url;
        },
        submit() {
            const term = this.q.trim();
            if (!term) return;
            this.goTo(`{{ route('search') }}?q=${encodeURIComponent(term)}`, term);
        },
        openHighlighted() {
            if (this.highlighted >= 0 && this.results[this.highlighted]) {
                this.goTo(this.results[this.highlighted].url, this.q);
            } else {
                this.submit();
            }
        },
        openMobile() {
            this.mobileOpen = true;
            this.open = true;
            this.$nextTick(() => this.$refs.mobileInput?.focus());
        },
        closeMobile() {
            this.mobileOpen = false;
            this.open = false;
        },
    }"
    @keydown.escape="open = false; closeMobile()"
    class="relative flex-1 sm:max-w-xl"
>
    {{-- Desktop bar --}}
    <div class="hidden sm:block relative">
        <div class="flex items-center gap-2.5 h-[46px] px-4 bg-white rounded-2xl shadow-[0_0_0_2px_rgba(255,255,255,0.28)] focus-within:shadow-[0_0_0_2px_rgba(255,255,255,0.6)] transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-[19px] w-[19px] shrink-0 text-brand" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <circle cx="11" cy="11" r="7" />
                <path stroke-linecap="round" d="M20 20l-3.5-3.5" />
            </svg>
            <input
                type="text"
                name="q"
                x-model="q"
                @input.debounce.250ms="search()"
                @focus="open = true"
                @keydown.down.prevent="moveHighlight(1)"
                @keydown.up.prevent="moveHighlight(-1)"
                @keydown.enter.prevent="openHighlighted()"
                placeholder="Tìm iPhone theo tên, dòng máy..."
                autocomplete="off"
                class="flex-1 min-w-0 border-0 p-0 text-sm text-ink placeholder:text-ink-soft/70 focus:ring-0"
            >
        </div>

        <div
            x-show="open"
            x-cloak
            @click.outside="open = false"
            x-transition:enter="transition ease-out duration-150"
            x-transition:enter-start="opacity-0 -translate-y-1"
            x-transition:enter-end="opacity-100 translate-y-0"
            class="absolute z-50 mt-2 w-full min-w-[420px] bg-white border border-line rounded-2xl shadow-lg overflow-hidden"
        >
            <template x-if="q.trim().length < 2">
                <div class="p-4">
                    <p class="text-xs font-bold tracking-wide uppercase text-ink-soft mb-2">Từ khoá được tìm nhiều</p>
                    <div class="flex flex-wrap gap-2">
                        <template x-for="item in popular" :key="item">
                            <a
                                :href="`{{ route('search') }}?q=${encodeURIComponent(item)}`"
                                @click="addRecent(item)"
                                class="px-3.5 py-1.5 rounded-full border border-line text-sm font-medium text-ink hover:border-brand hover:text-brand transition"
                                x-text="item"
                            ></a>
                        </template>
                    </div>
                </div>
            </template>

            <template x-if="q.trim().length >= 2 && !loading && results.length > 0">
                <div>
                    <p class="px-4 pt-3 pb-1 text-xs font-bold tracking-wide uppercase text-ink-soft">Sản phẩm phù hợp</p>
                    <template x-for="(item, index) in results" :key="item.slug">
                        <a
                            :href="item.url"
                            @click="addRecent(q)"
                            @mouseenter="highlighted = index"
                            class="flex items-center gap-3.5 px-4 py-2.5 transition"
                            :class="highlighted === index ? 'bg-paper' : ''"
                        >
                            <span class="w-11 h-14 rounded-xl bg-paper flex items-center justify-center p-1.5 shrink-0 overflow-hidden">
                                <img :src="item.thumbnail" :alt="item.name" class="max-w-full max-h-full object-contain">
                            </span>
                            <span class="min-w-0 flex-1">
                                <span class="block text-sm font-semibold text-ink truncate" x-text="item.name"></span>
                                <span class="block text-xs text-ink-soft mt-0.5" x-text="item.series"></span>
                            </span>
                            <span class="text-right shrink-0">
                                <span class="block text-sm font-bold text-brand" x-text="new Intl.NumberFormat('vi-VN').format(item.price) + 'đ'"></span>
                                <span
                                    class="block text-[11px] font-semibold mt-0.5"
                                    :class="item.in_stock ? 'text-emerald-600' : 'text-red-500'"
                                    x-text="item.in_stock ? 'Còn hàng' : 'Hết hàng'"
                                ></span>
                            </span>
                        </a>
                    </template>
                    <button
                        type="button"
                        @click="submit()"
                        class="w-full flex items-center gap-2 px-4 py-3 bg-paper border-t border-line text-sm font-semibold text-brand hover:text-brand-dark transition"
                    >
                        <span x-text="`Xem tất cả ${total} kết quả cho “${q}”`"></span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 ml-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 6l6 6-6 6M5 12h13" />
                        </svg>
                    </button>
                </div>
            </template>

            <template x-if="q.trim().length >= 2 && !loading && results.length === 0">
                <div class="px-4 py-6 text-center text-sm text-ink-soft">
                    Không tìm thấy sản phẩm nào cho &ldquo;<span x-text="q" class="font-medium text-ink"></span>&rdquo;.
                </div>
            </template>
        </div>
    </div>

    {{-- Mobile trigger --}}
    <button type="button" @click="openMobile()" class="sm:hidden text-white/80 hover:text-white transition" aria-label="Tìm kiếm">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <circle cx="11" cy="11" r="7" />
            <path stroke-linecap="round" d="M20 20l-3.5-3.5" />
        </svg>
    </button>

    {{-- Mobile overlay --}}
    <div
        x-show="mobileOpen"
        x-cloak
        class="sm:hidden fixed inset-0 z-50 bg-white flex flex-col [padding-top:env(safe-area-inset-top)]"
    >
        <div class="bg-brand px-3 pt-2.5 pb-3 flex items-center gap-2.5 shrink-0">
            <button type="button" @click="closeMobile()" class="p-1 text-white shrink-0" aria-label="Đóng tìm kiếm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
            </button>
            <div class="flex-1 flex items-center gap-2 h-11 px-3.5 bg-white rounded-xl">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-[18px] w-[18px] shrink-0 text-brand" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="7" />
                    <path stroke-linecap="round" d="M20 20l-3.5-3.5" />
                </svg>
                <input
                    type="text"
                    x-ref="mobileInput"
                    x-model="q"
                    @input.debounce.250ms="search()"
                    @keydown.enter.prevent="openHighlighted()"
                    placeholder="Tìm iPhone theo tên, dòng máy..."
                    autocomplete="off"
                    class="flex-1 min-w-0 border-0 p-0 text-sm text-ink focus:ring-0"
                >
                <button type="button" x-show="q.length > 0" x-cloak @click="q = ''; results = []; total = 0" class="text-ink-soft text-lg leading-none px-1">&times;</button>
            </div>
        </div>

        <div class="flex-1 overflow-y-auto">
            <template x-if="q.trim().length < 2">
                <div>
                    <template x-if="recent.length > 0">
                        <div class="px-4 pt-4 pb-1">
                            <div class="flex items-center justify-between">
                                <p class="text-xs font-bold tracking-wide uppercase text-ink-soft">Tìm gần đây</p>
                                <button
                                    type="button"
                                    @click="recent = []; try { localStorage.removeItem('recentSearches'); } catch (e) {}"
                                    class="text-xs font-semibold text-brand"
                                >Xoá</button>
                            </div>
                            <div class="flex flex-wrap gap-2 mt-2.5">
                                <template x-for="item in recent" :key="item">
                                    <span class="inline-flex items-center gap-1.5 min-h-[36px] pl-3.5 pr-2.5 rounded-full border border-line text-sm font-medium text-ink">
                                        <a :href="`{{ route('search') }}?q=${encodeURIComponent(item)}`" x-text="item"></a>
                                        <button type="button" @click="removeRecent(item)" class="text-ink-soft" aria-label="Xoá từ khoá">&times;</button>
                                    </span>
                                </template>
                            </div>
                        </div>
                    </template>

                    <div class="px-4 pt-4 pb-2">
                        <p class="text-xs font-bold tracking-wide uppercase text-ink-soft">Từ khoá được tìm nhiều</p>
                        <div class="flex flex-wrap gap-2 mt-2.5">
                            <template x-for="item in popular" :key="item">
                                <a
                                    :href="`{{ route('search') }}?q=${encodeURIComponent(item)}`"
                                    @click="addRecent(item)"
                                    class="min-h-[36px] flex items-center px-3.5 rounded-full border border-line text-sm font-medium text-ink"
                                    x-text="item"
                                ></a>
                            </template>
                        </div>
                    </div>

                    @if ($quickSeries->isNotEmpty())
                        <div class="mt-2 px-4 pt-4 pb-6 border-t border-line">
                            <p class="text-xs font-bold tracking-wide uppercase text-ink-soft mb-2.5">Hoặc chọn theo dòng máy</p>
                            <div class="grid grid-cols-2 gap-2">
                                @foreach ($quickSeries as $item)
                                    <a
                                        href="{{ route('products.index', ['series' => $item->slug]) }}"
                                        @click="closeMobile()"
                                        class="flex items-center justify-center text-center min-h-[44px] px-3 rounded-2xl border border-line text-sm font-medium text-ink"
                                    >{{ $item->name }}</a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </template>

            <template x-if="q.trim().length >= 2 && !loading && results.length > 0">
                <div>
                    <p class="px-4 pt-4 pb-1 text-xs font-bold tracking-wide uppercase text-ink-soft">Sản phẩm phù hợp</p>
                    <template x-for="item in results" :key="item.slug">
                        <a :href="item.url" @click="addRecent(q)" class="flex items-center gap-3 px-4 min-h-[60px] py-3 border-b border-line">
                            <span class="w-11 h-14 rounded-xl bg-paper flex items-center justify-center p-1.5 shrink-0 overflow-hidden">
                                <img :src="item.thumbnail" :alt="item.name" class="max-w-full max-h-full object-contain">
                            </span>
                            <span class="min-w-0 flex-1">
                                <span class="block text-sm font-semibold text-ink truncate" x-text="item.name"></span>
                                <span class="block text-xs text-ink-soft mt-0.5" x-text="item.series"></span>
                            </span>
                            <span class="text-sm font-bold text-brand shrink-0" x-text="new Intl.NumberFormat('vi-VN').format(item.price) + 'đ'"></span>
                        </a>
                    </template>
                    <div class="p-4">
                        <button type="button" @click="submit()" class="w-full flex items-center justify-center gap-2 h-12 rounded-2xl border-[1.5px] border-line text-sm font-semibold text-brand">
                            <span x-text="`Xem tất cả ${total} kết quả`"></span>
                        </button>
                    </div>
                </div>
            </template>

            <template x-if="q.trim().length >= 2 && !loading && results.length === 0">
                <div class="px-4 py-8 text-center text-sm text-ink-soft">
                    Không tìm thấy sản phẩm nào cho &ldquo;<span x-text="q" class="font-medium text-ink"></span>&rdquo;.
                </div>
            </template>
        </div>
    </div>
</div>
