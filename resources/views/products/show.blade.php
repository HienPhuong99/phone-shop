@php
    $variantsData = $product->variants->map(fn ($variant) => [
        'id' => $variant->id,
        'color' => $variant->color,
        'storage' => $variant->storage,
        'price' => (float) $variant->price,
        'stock' => $variant->stock_quantity,
        'sku' => $variant->sku,
    ]);

    $galleryImages = collect()
        ->when($product->thumbnail, fn ($c) => $c->push([
            'url' => $product->thumbnail,
            'thumb' => $product->thumbnail_thumb ?? $product->thumbnail,
        ]))
        ->merge($product->images->map(fn ($image) => [
            'url' => $image->url,
            'thumb' => $image->thumb_url ?? $image->url,
        ]))
        ->values();

    // Only specs both products actually have, so the table never shows a
    // blank cell.
    $comparisonSpecLabels = $comparisonProduct
        ? collect($product->specifications ?? [])
            ->keys()
            ->intersect(collect($comparisonProduct->specifications ?? [])->keys())
            ->take(6)
        : collect();

    $metaDescription = $product->description
        ? Illuminate\Support\Str::limit(preg_replace('/\s+/', ' ', trim($product->description)), 155)
        : "{$product->name} chính hãng, giá ".number_format((float) $product->base_price, 0, ',', '.')."đ tại phuonghihi. Bảo hành 12 tháng, giao toàn quốc.";
@endphp

<x-shop-layout
    :title="$product->name.' - phuonghihi'"
    :hide-bottom-nav="true"
    :description="$metaDescription"
    :og-image="$galleryImages->first()['url'] ?? null"
>
    <x-slot:head>
        {{--
            Plain json_encode(), not Js::from() — Js::from() wraps its
            output in a JSON.parse(...) JS expression (meant for a JS
            variable assignment), but application/ld+json needs the
            script tag's textContent to parse as JSON on its own.
            json_encode()'s default slash-escaping keeps a stray
            "</script>" in any field from breaking out of the tag.
        --}}
        <script type="application/ld+json">{!! json_encode([
                '@@context' => 'https://schema.org',
                '@type' => 'Product',
                'name' => $product->name,
                'description' => $metaDescription,
                'image' => $galleryImages->pluck('url')->values()->all(),
                'brand' => ['@type' => 'Brand', 'name' => 'Apple'],
                'offers' => [
                    '@type' => 'Offer',
                    'url' => route('products.show', $product->slug),
                    'priceCurrency' => 'VND',
                    'price' => (string) $product->base_price,
                    'availability' => $product->total_stock > 0
                        ? 'https://schema.org/InStock'
                        : 'https://schema.org/OutOfStock',
                ],
            ], JSON_UNESCAPED_UNICODE) !!}</script>
    </x-slot:head>
    <div
        class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 pb-28 sm:pb-8"
        x-data="{
            variants: {{ Illuminate\Support\Js::from($variantsData) }},
            gallery: {{ Illuminate\Support\Js::from($galleryImages) }},
            compareAtPrice: {{ $product->compare_at_price ? (float) $product->compare_at_price : 'null' }},
            activeImage: 0,
            fading: false,
            selectedId: {{ $product->variants->first()?->id ?? 'null' }},
            quantity: 1,
            pulse: false,
            init() {
                this.$watch('selectedId', () => {
                    if (this.selected && this.quantity > this.selected.stock) {
                        this.quantity = Math.max(1, this.selected.stock);
                    }
                    this.pulse = true;
                    setTimeout(() => { this.pulse = false; }, 220);
                });
            },
            switchImage(index) {
                if (index === this.activeImage) return;
                this.fading = true;
                setTimeout(() => {
                    this.activeImage = index;
                    this.fading = false;
                }, 150);
            },
            get selected() {
                return this.variants.find(v => v.id === this.selectedId) ?? null;
            },
            get currentPrice() {
                return this.selected?.price ?? {{ (float) $product->base_price }};
            },
            get savings() {
                if (!this.compareAtPrice || this.compareAtPrice <= this.currentPrice) return null;
                return this.compareAtPrice - this.currentPrice;
            },
            get monthlyInstallment() {
                return Math.round(this.currentPrice / 12);
            },
            get colors() {
                return [...new Set(this.variants.map(v => v.color))];
            },
            get storagesForColor() {
                return this.variants.filter(v => v.color === this.selected?.color).map(v => v.storage);
            },
            selectColor(color) {
                const match = this.variants.find(v => v.color === color);
                if (match) this.selectedId = match.id;
            },
            selectStorage(storage) {
                const match = this.variants.find(v => v.color === this.selected?.color && v.storage === storage);
                if (match) this.selectedId = match.id;
            },
            priceForStorage(storage) {
                const match = this.variants.find(v => v.color === this.selected?.color && v.storage === storage);
                return match?.price ?? 0;
            },
            colorDot(color) {
                const c = (color || '').toLowerCase();
                const map = [
                    [['titan sa mạc', 'desert'], '#c5a68a'],
                    [['titan tự nhiên'], '#9c9490'],
                    [['titan xanh'], '#3d4a4d'],
                    [['titan trắng'], '#e6e2da'],
                    [['titan đen'], '#3a3a3c'],
                    [['midnight'], '#1d1d20'],
                    [['starlight'], '#f0e6d5'],
                    [['space black'], '#2b2a29'],
                    [['deep purple', 'tím'], '#5f5470'],
                    [['sierra blue'], '#a6c2d8'],
                    [['pacific blue'], '#37535e'],
                    [['than chì'], '#54524f'],
                    [['xanh mòng két', 'teal'], '#adc8bf'],
                    [['xanh lưu ly', 'ultramarine'], '#4a5ba6'],
                    [['xanh lục bảo'], '#0f6b4f'],
                    [['xanh dương'], '#4a6fa5'],
                    [['xanh lá'], '#6a7d5c'],
                    [['xanh'], '#5a7d9a'],
                    [['hồng pastel'], '#e8c6c6'],
                    [['hồng'], '#e0b8bf'],
                    [['đỏ'], '#a53030'],
                    [['vàng'], '#e8d9a0'],
                    [['bạc'], '#d6d6d6'],
                    [['đen'], '#1c1c1e'],
                    [['trắng'], '#f5f5f0'],
                ];
                for (const [keywords, hex] of map) {
                    if (keywords.some(k => c.includes(k))) return hex;
                }
                return '#a3a3a3';
            },
        }"
    >
        <nav class="text-sm text-ink-soft mb-6">
            <a href="{{ route('home') }}" class="hover:text-brand transition">Trang chủ</a> /
            <a href="{{ route('products.index') }}" class="hover:text-brand transition">Sản phẩm</a> /
            <a href="{{ route('products.index', ['series' => $product->series->slug]) }}" class="hover:text-brand transition">{{ $product->series->name }}</a> /
            <span class="text-ink font-medium">{{ $product->name }}</span>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-[1fr_400px] gap-8 lg:gap-10 items-start">
            <!-- Ảnh sản phẩm -->
            <div>
                <div class="relative aspect-square bg-white border border-line rounded-2xl shadow-sm overflow-hidden">
                    <span class="absolute top-3 left-3 z-10 bg-white/90 backdrop-blur text-[11px] font-bold tracking-wide uppercase text-brand px-2.5 py-1 rounded-full shadow-xs">Chính hãng VN/A</span>

                    @if ($galleryImages->isNotEmpty())
                        <img
                            src="{{ $galleryImages->first()['url'] }}"
                            :src="gallery[activeImage]?.url"
                            alt="{{ $product->name }}"
                            fetchpriority="high"
                            decoding="async"
                            class="w-full h-full object-cover transition-opacity duration-150"
                            :class="fading ? 'opacity-0' : 'opacity-100'"
                        >
                    @else
                        <div class="w-full h-full flex items-center justify-center bg-[repeating-linear-gradient(45deg,theme(colors.line),theme(colors.line)_8px,transparent_8px,transparent_16px)]">
                            <span class="font-mono text-xs text-ink-soft bg-white px-2 py-0.5 rounded shadow-xs">ảnh sản phẩm</span>
                        </div>
                    @endif
                </div>

                @if ($galleryImages->count() > 1)
                    <div class="mt-4 grid grid-cols-5 gap-3">
                        @foreach ($galleryImages as $index => $image)
                            <button
                                type="button"
                                @click="switchImage({{ $index }})"
                                :class="activeImage === {{ $index }} ? 'border-brand ring-1 ring-brand' : 'border-line hover:border-brand/60'"
                                class="aspect-square bg-gray-50 border-[1.5px] rounded-xl overflow-hidden transition"
                            >
                                <img src="{{ $image['thumb'] }}" alt="" loading="lazy" decoding="async" class="w-full h-full object-cover">
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Hộp mua hàng -->
            <div class="lg:sticky lg:top-24">
                <p class="text-xs font-bold tracking-wide uppercase text-brand">{{ $product->series->name }}</p>
                <h1 class="mt-1 font-bold text-2xl sm:text-3xl text-ink leading-tight">{{ $product->name }}</h1>

                <div class="mt-3 flex items-baseline gap-2 flex-wrap">
                    <p
                        class="text-brand text-3xl font-extrabold transition-transform duration-200 ease-out"
                        :class="pulse ? 'scale-105' : 'scale-100'"
                        x-text="new Intl.NumberFormat('vi-VN').format(currentPrice) + 'đ'"
                    ></p>
                    <template x-if="savings">
                        <p class="text-sm text-ink-soft/70 line-through" x-text="new Intl.NumberFormat('vi-VN').format(compareAtPrice) + 'đ'"></p>
                    </template>
                    <p class="text-xs text-ink-soft">(Đã bao gồm VAT)</p>
                </div>

                <template x-if="savings">
                    <p class="mt-1 text-xs font-semibold text-[#c2410c]" x-text="`Tiết kiệm ${new Intl.NumberFormat('vi-VN').format(savings)}đ`"></p>
                </template>

                <template x-if="selected">
                    <p
                        class="mt-1 text-sm font-medium transition-opacity duration-150"
                        :class="[selected.stock > 0 ? 'text-emerald-600' : 'text-red-500', pulse ? 'opacity-60' : 'opacity-100']"
                        x-text="selected.stock > 0 ? `Còn hàng (${selected.stock} sản phẩm)` : 'Hết hàng'"
                    ></p>
                </template>

                <!-- Ưu đãi kèm theo -->
                <div class="mt-5 rounded-xl border border-line bg-paper/70 divide-y divide-line overflow-hidden">
                    <div class="flex items-center gap-2.5 px-3.5 py-2.5">
                        <span class="shrink-0 w-6 h-6 rounded-lg bg-brand/10 text-brand flex items-center justify-center text-[11px] font-bold">✓</span>
                        <span class="text-xs text-ink">Bảo hành chính hãng 12 tháng, 1 đổi 1 trong 30 ngày đầu</span>
                    </div>
                    <a href="{{ route('pages.services') }}" class="flex items-center gap-2.5 px-3.5 py-2.5 hover:bg-white transition">
                        <span class="shrink-0 w-6 h-6 rounded-lg bg-brand/10 text-brand flex items-center justify-center text-[11px] font-bold">%</span>
                        <span class="text-xs text-ink">
                            Trả góp 0% — từ <span class="font-semibold" x-text="new Intl.NumberFormat('vi-VN').format(monthlyInstallment) + 'đ'"></span>/tháng (12 tháng)
                        </span>
                    </a>
                    <div class="flex items-center gap-2.5 px-3.5 py-2.5">
                        <span class="shrink-0 w-6 h-6 rounded-lg bg-brand/10 text-brand flex items-center justify-center text-[11px] font-bold">⇄</span>
                        <span class="text-xs text-ink">Thu cũ lên đời, trừ thẳng vào hoá đơn</span>
                    </div>
                    <div class="flex items-center gap-2.5 px-3.5 py-2.5">
                        <span class="shrink-0 w-6 h-6 rounded-lg bg-brand/10 text-brand flex items-center justify-center text-[11px] font-bold">⚡</span>
                        <span class="text-xs text-ink">Giao hàng toàn quốc, nội thành 2-4 giờ</span>
                    </div>
                </div>

                <!-- Chọn dung lượng -->
                <div class="mt-6">
                    <p class="text-sm font-semibold text-ink mb-2">Dung lượng</p>
                    <div class="grid grid-cols-3 gap-2">
                        <template x-for="storage in storagesForColor" :key="storage">
                            <button
                                type="button"
                                @click="selectStorage(storage)"
                                :class="selected?.storage === storage ? 'border-brand ring-1 ring-brand bg-brand/5' : 'border-line hover:border-brand'"
                                class="flex flex-col items-center justify-center gap-0.5 rounded-xl border-[1.5px] px-2 py-2.5 text-center transition"
                            >
                                <span class="text-sm font-semibold text-ink" x-text="storage"></span>
                                <span class="text-[11px] text-ink-soft" x-text="new Intl.NumberFormat('vi-VN').format(priceForStorage(storage)) + 'đ'"></span>
                            </button>
                        </template>
                    </div>
                </div>

                <!-- Chọn màu -->
                <div class="mt-4">
                    <p class="text-sm font-semibold text-ink mb-2">
                        Màu sắc: <span class="font-normal text-ink-soft" x-text="selected?.color"></span>
                    </p>
                    <div class="flex flex-wrap gap-2">
                        <template x-for="color in colors" :key="color">
                            <button
                                type="button"
                                data-testid="variant-color-button"
                                @click="selectColor(color)"
                                :class="selected?.color === color ? 'border-brand text-brand bg-brand/5' : 'border-line text-ink hover:border-brand hover:text-brand'"
                                class="flex items-center gap-2 pl-2 pr-3.5 py-1.5 text-sm rounded-full border-[1.5px] font-medium transition"
                            >
                                <span class="w-4 h-4 rounded-full border border-black/10 shrink-0" :style="`background-color: ${colorDot(color)}`"></span>
                                <span x-text="color"></span>
                            </button>
                        </template>
                    </div>
                </div>

                <!-- Mua hàng (desktop) -->
                <form method="POST" action="{{ route('cart.store') }}" class="hidden sm:flex mt-6 items-center gap-3">
                    @csrf
                    <input type="hidden" name="variant_id" :value="selectedId">

                    <div class="flex items-center border-[1.5px] border-line rounded-xl overflow-hidden shrink-0">
                        <button
                            type="button"
                            @click="quantity = Math.max(1, quantity - 1)"
                            class="w-9 h-11 flex items-center justify-center text-ink-soft hover:bg-paper active:scale-95 transition"
                        >−</button>
                        <input
                            type="number"
                            inputmode="numeric"
                            name="quantity"
                            x-model.number="quantity"
                            @change="quantity = Math.min(Math.max(1, quantity || 1), selected?.stock ?? 1)"
                            min="1"
                            :max="selected?.stock ?? 1"
                            class="w-12 h-11 border-0 border-x-[1.5px] border-line text-center text-sm focus:border-brand focus:ring-0"
                        >
                        <button
                            type="button"
                            @click="quantity = Math.min(selected?.stock ?? 1, quantity + 1)"
                            class="w-9 h-11 flex items-center justify-center text-ink-soft hover:bg-paper active:scale-95 transition"
                        >+</button>
                    </div>

                    <button
                        type="submit"
                        data-testid="add-to-cart-button"
                        :disabled="!selected || selected.stock <= 0"
                        class="flex-1 px-8 py-3 rounded-2xl text-white font-semibold disabled:bg-gray-300 disabled:cursor-not-allowed bg-brand hover:bg-brand-dark shadow-sm transition"
                    >
                        Thêm vào giỏ hàng
                    </button>
                </form>

                <a href="tel:0900300300" class="hidden sm:flex mt-3 items-center justify-center gap-2 w-full px-8 py-2.5 rounded-2xl border-[1.5px] border-line text-ink font-semibold hover:border-brand hover:text-brand transition text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" />
                    </svg>
                    Gọi đặt mua: 0900 300 300
                </a>

                <div class="mt-3 flex items-center gap-2">
                    @auth
                        <form method="POST" action="{{ $isWishlisted ? route('wishlist.destroy', $product) : route('wishlist.store', $product) }}" class="flex-1">
                            @csrf
                            @if ($isWishlisted) @method('DELETE') @endif
                            <button type="submit" class="w-full flex items-center justify-center gap-2 px-4 py-2.5 rounded-2xl border-[1.5px] {{ $isWishlisted ? 'border-brand text-brand bg-brand/5' : 'border-line text-ink hover:border-brand hover:text-brand' }} font-semibold text-sm transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="{{ $isWishlisted ? 'currentColor' : 'none' }}" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 20s-7-4.4-7-9.2A4 4 0 0112 8a4 4 0 017 2.8C19 15.6 12 20 12 20z" />
                                </svg>
                                {{ $isWishlisted ? 'Đã yêu thích' : 'Yêu thích' }}
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="flex-1 flex items-center justify-center gap-2 px-4 py-2.5 rounded-2xl border-[1.5px] border-line text-ink hover:border-brand hover:text-brand font-semibold text-sm transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 20s-7-4.4-7-9.2A4 4 0 0112 8a4 4 0 017 2.8C19 15.6 12 20 12 20z" />
                            </svg>
                            Yêu thích
                        </a>
                    @endauth

                    <button
                        type="button"
                        x-data
                        @click="$store.compare.toggle({
                            slug: {{ Illuminate\Support\Js::from($product->slug) }},
                            name: {{ Illuminate\Support\Js::from($product->name) }},
                            thumbnail: {{ Illuminate\Support\Js::from($product->thumbnail_thumb ?? $product->thumbnail) }},
                        })"
                        :class="$store.compare.has({{ Illuminate\Support\Js::from($product->slug) }}) ? 'border-brand text-brand bg-brand/5' : 'border-line text-ink hover:border-brand hover:text-brand'"
                        class="flex-1 flex items-center justify-center gap-2 px-4 py-2.5 rounded-2xl border-[1.5px] font-semibold text-sm transition"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 3v18m6-18v18M3 8h4m10 0h4M3 16h4m10 0h4" />
                        </svg>
                        <span x-text="$store.compare.has({{ Illuminate\Support\Js::from($product->slug) }}) ? 'Đã thêm so sánh' : 'So sánh'"></span>
                    </button>
                </div>

                @error('quantity')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Điều hướng nhanh giữa các khối nội dung --}}
        <div class="mt-12 sticky top-0 z-30 -mx-4 sm:-mx-6 lg:-mx-8 px-4 sm:px-6 lg:px-8 bg-paper/93 backdrop-blur border-y border-line">
            <nav class="flex gap-1 overflow-x-auto [scrollbar-width:none] [&::-webkit-scrollbar]:hidden" aria-label="Nội dung sản phẩm">
                @php
                    $sectionLinks = array_filter([
                        ['id' => 'mo-ta', 'label' => 'Tổng quan', 'show' => $product->description !== null],
                        ['id' => 'thong-so', 'label' => 'Thông số kỹ thuật', 'show' => $specGroups !== []],
                        ['id' => 'danh-gia', 'label' => 'Đánh giá', 'count' => $product->reviews_count, 'show' => true],
                        ['id' => 'so-sanh', 'label' => 'So sánh', 'show' => $comparisonProduct && $comparisonSpecLabels->isNotEmpty()],
                    ], fn ($link) => $link['show']);
                @endphp

                @foreach ($sectionLinks as $link)
                    <a
                        href="#{{ $link['id'] }}"
                        class="whitespace-nowrap px-4 py-3.5 text-sm font-semibold text-ink-soft hover:text-brand border-b-[2.5px] border-transparent hover:border-brand/40 transition"
                    >
                        {{ $link['label'] }}@if (! empty($link['count']))
                            <span class="font-medium text-ink-soft/80">({{ $link['count'] }})</span>
                        @endif
                    </a>
                @endforeach
            </nav>
        </div>

        {{-- Thông số nổi bật --}}
        @if (! empty($product->spec_highlights))
            <div class="mt-8">
                <h2 class="font-bold text-lg text-ink mb-4">Thông số nổi bật</h2>
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
                    @php
                        $highlightIcons = [
                            'Màn hình' => 'display', 'Chip' => 'chip', 'RAM' => 'ram',
                            'Bộ nhớ' => 'storage', 'Camera sau' => 'camera', 'Pin' => 'battery',
                        ];
                    @endphp

                    @foreach ($product->spec_highlights as $label => $value)
                        <div class="bg-white border border-line rounded-2xl p-3.5 flex flex-col gap-2 min-w-0">
                            <x-spec-icon :name="$highlightIcons[$label] ?? 'dots'" class="h-5 w-5 text-brand shrink-0" />
                            <p class="text-[10.5px] font-bold uppercase tracking-wider text-ink-soft">{{ $label }}</p>
                            <p class="text-sm font-bold text-ink leading-tight break-words">{{ $value }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Mô tả sản phẩm --}}
        @if ($product->description)
            <div id="mo-ta" class="mt-10 scroll-mt-16">
                <h2 class="font-bold text-lg text-ink mb-4">Mô tả sản phẩm</h2>

                <div class="grid grid-cols-1 lg:grid-cols-[minmax(0,1fr)_260px] gap-8 items-start">
                    <div x-data="{ expanded: false }">
                        <div
                            class="relative overflow-hidden transition-[max-height] duration-300"
                            :class="expanded ? 'max-h-none' : 'max-h-[365px]'"
                        >
                            @foreach ($product->description_blocks as $block)
                                @if ($block['type'] === 'lead')
                                    <p class="text-[16.5px] leading-[1.72] text-ink font-medium max-w-[66ch]">{{ $block['text'] }}</p>
                                @elseif ($block['type'] === 'chapter')
                                    <h3 class="mt-6 pl-3.5 border-l-[3px] border-brand font-bold text-base text-brand leading-snug max-w-[66ch]">{{ $block['text'] }}</h3>
                                @elseif ($block['type'] === 'heading')
                                    <h4 class="mt-5 font-bold text-[14.5px] text-ink max-w-[66ch]">{{ $block['text'] }}</h4>
                                @else
                                    <p class="mt-2 text-[14.5px] leading-[1.78] text-ink-soft max-w-[66ch]">{{ $block['text'] }}</p>
                                @endif
                            @endforeach

                            <div
                                x-show="! expanded"
                                x-cloak
                                class="absolute inset-x-0 bottom-0 h-24 bg-gradient-to-b from-transparent to-paper pointer-events-none"
                            ></div>
                        </div>

                        <button
                            type="button"
                            @click="expanded = ! expanded"
                            class="mt-3.5 inline-flex items-center gap-2 px-5 py-2 rounded-full bg-white border-[1.5px] border-line hover:border-brand text-sm font-semibold text-brand transition"
                        >
                            <span x-text="expanded ? 'Thu gọn mô tả' : 'Xem thêm mô tả'">Xem thêm mô tả</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 transition-transform duration-200" :class="expanded && 'rotate-180'" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 9l6 6 6-6" />
                            </svg>
                        </button>
                    </div>

                    {{-- Tóm tắt các chỉ số chốt đơn, giữ trong tầm mắt khi đọc mô tả dài --}}
                    @if (! empty($product->spec_highlights))
                        <aside class="lg:sticky lg:top-16 bg-white border border-line rounded-2xl overflow-hidden">
                            <p class="px-4 py-2.5 bg-paper border-b border-line text-[11px] font-bold uppercase tracking-wider text-ink-soft">Tóm tắt máy</p>
                            <dl class="py-1">
                                @foreach ($product->spec_highlights as $label => $value)
                                    <div class="flex gap-3 px-4 py-1.5 text-[12.5px] items-baseline">
                                        <dt class="w-[72px] shrink-0 text-ink-soft">{{ $label }}</dt>
                                        <dd class="font-semibold text-ink min-w-0 break-words">{{ $value }}</dd>
                                    </div>
                                @endforeach
                            </dl>
                            @if ($specGroups !== [])
                                <a href="#thong-so" class="block px-4 py-2.5 border-t border-line text-center text-[12.5px] font-bold text-brand hover:bg-paper transition">
                                    Xem thông số đầy đủ &rarr;
                                </a>
                            @endif
                        </aside>
                    @endif
                </div>
            </div>
        @endif

        {{-- Thông số kỹ thuật, gom theo nhóm --}}
        @if ($specGroups !== [])
            <div id="thong-so" class="mt-12 max-w-4xl scroll-mt-16">
                <h2 class="font-bold text-lg text-ink mb-4">Thông số kỹ thuật</h2>

                <div class="border border-line rounded-2xl overflow-hidden bg-white shadow-sm divide-y divide-line">
                    @foreach ($specGroups as $group)
                        <div>
                            <div class="flex items-center gap-2.5 px-4 py-2.5 bg-paper border-b border-line">
                                <x-spec-icon :name="$group['icon']" class="h-4 w-4 text-brand shrink-0" />
                                <h3 class="text-xs font-bold uppercase tracking-wider text-brand">{{ $group['label'] }}</h3>
                                <span class="ml-auto text-[11px] text-ink-soft tabular-nums">{{ count($group['specs']) }}</span>
                            </div>
                            <dl class="divide-y divide-line">
                                @foreach ($group['specs'] as $label => $value)
                                    <div class="flex flex-col sm:flex-row gap-1 sm:gap-4 px-4 py-2.5">
                                        <dt class="w-full sm:w-44 shrink-0 text-sm text-ink-soft font-medium">{{ $label }}</dt>
                                        <dd class="text-sm text-ink leading-relaxed min-w-0">{{ $value }}</dd>
                                    </div>
                                @endforeach
                            </dl>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Đánh giá -->
        @php
            $ratingCounts = $product->reviews->countBy('rating');
        @endphp
        <div id="danh-gia" class="mt-12 max-w-3xl scroll-mt-16">
            <h2 class="font-bold text-lg text-ink mb-3">Đánh giá từ khách hàng</h2>

            <div class="rounded-2xl border border-line bg-white shadow-sm p-6">
                @if ($product->reviews_count)
                    <div class="flex items-center gap-6">
                        <div class="text-center shrink-0">
                            <p class="text-4xl font-extrabold text-brand leading-none">{{ $product->average_rating }}</p>
                            <p class="mt-1.5 text-xs text-ink-soft">{{ $product->reviews_count }} đánh giá</p>
                        </div>
                        <div class="flex-1 space-y-1.5">
                            @for ($star = 5; $star >= 1; $star--)
                                @php $count = $ratingCounts[$star] ?? 0; @endphp
                                <div class="flex items-center gap-2">
                                    <span class="text-xs text-ink-soft w-10 shrink-0">{{ $star }} sao</span>
                                    <span class="flex-1 h-1.5 rounded-full bg-line overflow-hidden">
                                        <span class="block h-full bg-amber-400" style="width: {{ $product->reviews_count ? round($count / $product->reviews_count * 100) : 0 }}%"></span>
                                    </span>
                                    <span class="text-xs text-ink-soft w-5 text-right shrink-0">{{ $count }}</span>
                                </div>
                            @endfor
                        </div>
                    </div>
                @else
                    <p class="text-sm text-ink-soft">Chưa có đánh giá nào cho sản phẩm này.</p>
                @endif

                <!-- Form đánh giá -->
                <div class="mt-6 pt-6 border-t border-line">
                    @if ($canReview)
                        <form method="POST" action="{{ route('reviews.store', $product) }}" x-data="{ rating: 5 }">
                            @csrf
                            <p class="text-sm font-semibold text-ink mb-2">Đánh giá của bạn</p>
                            <div class="flex items-center gap-1 mb-3">
                                <template x-for="star in [1, 2, 3, 4, 5]" :key="star">
                                    <button type="button" @click="rating = star" class="p-0.5">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" :class="star <= rating ? 'text-amber-400' : 'text-line'" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 3.5l2.6 5.3 5.9.85-4.25 4.15 1 5.85L12 16.9l-5.25 2.75 1-5.85L3.5 9.65l5.9-.85z" />
                                        </svg>
                                    </button>
                                </template>
                            </div>
                            <input type="hidden" name="rating" :value="rating">
                            <textarea name="content" rows="3" maxlength="1000" placeholder="Chia sẻ trải nghiệm của bạn về sản phẩm (không bắt buộc)..." class="w-full rounded-xl border-line text-sm focus:border-brand focus:ring-brand">{{ old('content') }}</textarea>
                            @error('content')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                            <button type="submit" class="mt-3 px-5 py-2 rounded-xl bg-brand hover:bg-brand-dark text-white text-sm font-semibold shadow-sm transition">Gửi đánh giá</button>
                        </form>
                    @elseif ($hasReviewed)
                        <p class="text-sm text-ink-soft">Bạn đã đánh giá sản phẩm này. Cảm ơn bạn!</p>
                    @elseif (auth()->check())
                        <p class="text-sm text-ink-soft">Bạn cần mua và nhận hàng sản phẩm này trước khi có thể đánh giá.</p>
                    @else
                        <p class="text-sm text-ink-soft">
                            <a href="{{ route('login') }}" class="font-semibold text-brand hover:text-brand-dark transition">Đăng nhập</a>
                            để đánh giá sản phẩm này.
                        </p>
                    @endif
                </div>

                <!-- Danh sách đánh giá -->
                @if ($product->reviews->isNotEmpty())
                    <div class="mt-6 pt-6 border-t border-line divide-y divide-line">
                        @foreach ($product->reviews->take(10) as $review)
                            <div class="py-4 first:pt-0 last:pb-0">
                                <div class="flex items-center gap-3">
                                    <span class="w-9 h-9 rounded-full bg-brand/10 text-brand flex items-center justify-center text-sm font-bold shrink-0">
                                        {{ Illuminate\Support\Str::of($review->user->name)->substr(0, 1)->upper() }}
                                    </span>
                                    <div class="min-w-0">
                                        <p class="text-sm font-semibold text-ink truncate">{{ $review->user->name }}</p>
                                        <div class="flex items-center gap-1 mt-0.5">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 {{ $i <= $review->rating ? 'text-amber-400' : 'text-line' }}" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M12 3.5l2.6 5.3 5.9.85-4.25 4.15 1 5.85L12 16.9l-5.25 2.75 1-5.85L3.5 9.65l5.9-.85z" />
                                                </svg>
                                            @endfor
                                            <span class="text-xs text-ink-soft ml-1">{{ $review->created_at->format('d/m/Y') }}</span>
                                        </div>
                                    </div>
                                </div>
                                @if ($review->content)
                                    <p class="mt-2.5 text-sm text-ink-soft leading-relaxed">{{ $review->content }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <!-- So sánh nhanh -->
        @if ($comparisonProduct && $comparisonSpecLabels->isNotEmpty())
            <div id="so-sanh" class="mt-12 max-w-3xl scroll-mt-16">
                <h2 class="font-bold text-lg text-ink mb-3">So sánh nhanh trong tầm giá</h2>
                <div class="rounded-2xl border border-line overflow-hidden bg-white shadow-sm overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-paper/60">
                                <th class="text-left font-medium text-ink-soft px-4 py-3 w-1/3">Tiêu chí</th>
                                <th class="text-left font-bold text-brand px-4 py-3 bg-brand/5">{{ $product->name }}</th>
                                <th class="text-left font-bold text-ink px-4 py-3">{{ $comparisonProduct->name }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-line">
                            <tr>
                                <td class="px-4 py-3 font-medium text-ink-soft">Giá từ</td>
                                <td class="px-4 py-3 font-bold text-brand bg-brand/5">{{ number_format($product->base_price, 0, ',', '.') }}đ</td>
                                <td class="px-4 py-3 text-ink">{{ number_format($comparisonProduct->base_price, 0, ',', '.') }}đ</td>
                            </tr>
                            @foreach ($comparisonSpecLabels as $label)
                                <tr>
                                    <td class="px-4 py-3 font-medium text-ink-soft">{{ $label }}</td>
                                    <td class="px-4 py-3 text-ink bg-brand/5">{{ $product->specifications[$label] }}</td>
                                    <td class="px-4 py-3 text-ink">{{ $comparisonProduct->specifications[$label] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <!-- Cùng dòng sản phẩm -->
        @if ($relatedProducts->isNotEmpty())
            <div class="mt-12">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="font-bold text-lg text-ink">Cùng dòng {{ $product->series->name }}</h2>
                    <a href="{{ route('products.index', ['series' => $product->series->slug]) }}" class="text-sm font-semibold text-brand hover:text-brand-dark transition">Xem tất cả &rarr;</a>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    @foreach ($relatedProducts as $related)
                        <x-product-card :product="$related" />
                    @endforeach
                </div>
            </div>
        @endif

        <x-recently-viewed :product="$product" />

        <!-- Sticky add-to-cart bar (mobile) -->
        <div class="sm:hidden fixed bottom-0 inset-x-0 z-40 bg-white border-t border-line px-3 py-2.5 [padding-bottom:calc(env(safe-area-inset-bottom)+0.625rem)] flex items-center gap-2">
            <a href="{{ route('cart.index') }}" class="shrink-0 p-2 rounded-xl border border-line text-ink-soft">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 1.907-4.925 2.29-7.68l.062-.469a1.125 1.125 0 00-1.115-1.276H6.106M7.5 14.25L5.106 5.272M7.5 14.25L6.6 20.4A.75.75 0 007.35 21h9.3m-7.5-1.5h7.5m-7.5 0a.75.75 0 100 1.5.75.75 0 000-1.5zm7.5 0a.75.75 0 100 1.5.75.75 0 000-1.5z" />
                </svg>
            </a>

            <div class="flex-1 min-w-0">
                <p class="text-[11px] text-ink-soft leading-none">Giá</p>
                <p class="mt-1 text-brand font-extrabold text-base leading-none whitespace-nowrap" x-text="new Intl.NumberFormat('vi-VN').format(currentPrice) + 'đ'"></p>
            </div>

            <form method="POST" action="{{ route('cart.store') }}" class="shrink-0">
                @csrf
                <input type="hidden" name="variant_id" :value="selectedId">
                <input type="hidden" name="quantity" value="1">
                <button
                    type="submit"
                    data-testid="add-to-cart-button-mobile"
                    :disabled="!selected || selected.stock <= 0"
                    class="px-5 py-3 rounded-2xl text-white text-sm font-semibold disabled:bg-gray-300 disabled:cursor-not-allowed bg-brand hover:bg-brand-dark shadow-sm transition whitespace-nowrap"
                >
                    Thêm vào giỏ
                </button>
            </form>
        </div>
    </div>
</x-shop-layout>
