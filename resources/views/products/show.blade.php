@php
    $variantsData = $product->variants->map(fn ($variant) => [
        'id' => $variant->id,
        'color' => $variant->color,
        'storage' => $variant->storage,
        'price' => (float) $variant->price,
        'stock' => $variant->stock_quantity,
        'sku' => $variant->sku,
    ]);
@endphp

<x-shop-layout :title="$product->name.' - phuonghihi'" :hide-bottom-nav="true">
    <div
        class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 pb-28 sm:pb-8"
        x-data="{
            variants: {{ Illuminate\Support\Js::from($variantsData) }},
            selectedId: {{ $product->variants->first()?->id ?? 'null' }},
            get selected() {
                return this.variants.find(v => v.id === this.selectedId) ?? null;
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
            <span class="text-ink font-medium">{{ $product->name }}</span>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-[1fr_400px] gap-8 lg:gap-10 items-start">
            <!-- Ảnh sản phẩm -->
            <div>
                <div class="relative aspect-square bg-white border border-line rounded-2xl shadow-sm flex items-center justify-center overflow-hidden">
                    <span class="absolute top-3 left-3 z-10 bg-white/90 backdrop-blur text-[11px] font-bold tracking-wide uppercase text-brand px-2.5 py-1 rounded-full shadow-xs">Chính hãng VN/A</span>

                    @if ($product->thumbnail)
                        <img src="{{ $product->thumbnail }}" alt="{{ $product->name }}" fetchpriority="high" decoding="async" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center bg-[repeating-linear-gradient(45deg,theme(colors.line),theme(colors.line)_8px,transparent_8px,transparent_16px)]">
                            <span class="font-mono text-xs text-ink-soft bg-white px-2 py-0.5 rounded shadow-xs">ảnh sản phẩm</span>
                        </div>
                    @endif
                </div>

                @if ($product->images->isNotEmpty())
                    <div class="mt-4 grid grid-cols-5 gap-3">
                        @foreach ($product->images as $image)
                            <div class="aspect-square bg-gray-50 border border-line rounded-xl overflow-hidden">
                                <img src="{{ $image->thumb_url ?? $image->url }}" alt="" loading="lazy" decoding="async" class="w-full h-full object-cover">
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Hộp mua hàng -->
            <div class="lg:sticky lg:top-24">
                <p class="text-xs font-bold tracking-wide uppercase text-brand">{{ $product->series->name }}</p>
                <h1 class="mt-1 font-bold text-2xl sm:text-3xl text-ink leading-tight">{{ $product->name }}</h1>

                <div class="mt-3 flex items-baseline gap-2 flex-wrap">
                    <p class="text-brand text-3xl font-extrabold" x-text="new Intl.NumberFormat('vi-VN').format(selected?.price ?? {{ $product->base_price }}) + 'đ'"></p>
                    <p class="text-xs text-ink-soft">(Đã bao gồm VAT)</p>
                </div>

                <template x-if="selected">
                    <p class="mt-1 text-sm font-medium" :class="selected.stock > 0 ? 'text-emerald-600' : 'text-red-500'" x-text="selected.stock > 0 ? `Còn hàng (${selected.stock} sản phẩm)` : 'Hết hàng'"></p>
                </template>

                <!-- Ưu đãi kèm theo -->
                <div class="mt-5 rounded-xl border border-line bg-paper/70 divide-y divide-line overflow-hidden">
                    <div class="flex items-center gap-2.5 px-3.5 py-2.5">
                        <span class="shrink-0 w-6 h-6 rounded-lg bg-brand/10 text-brand flex items-center justify-center text-[11px] font-bold">✓</span>
                        <span class="text-xs text-ink">Bảo hành chính hãng 12 tháng, 1 đổi 1 trong 30 ngày đầu</span>
                    </div>
                    <div class="flex items-center gap-2.5 px-3.5 py-2.5">
                        <span class="shrink-0 w-6 h-6 rounded-lg bg-brand/10 text-brand flex items-center justify-center text-[11px] font-bold">%</span>
                        <span class="text-xs text-ink">Trả góp 0% lãi suất qua thẻ tín dụng</span>
                    </div>
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

                    <input type="number" inputmode="numeric" name="quantity" value="1" min="1" :max="selected?.stock ?? 1" class="w-20 rounded-xl border-line text-sm focus:border-brand focus:ring-brand">

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

                @error('quantity')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Thông số & mô tả -->
        <div class="mt-12 max-w-3xl">
            @if (! empty($product->specifications))
                <div>
                    <h2 class="font-bold text-lg text-ink mb-3">Thông số kỹ thuật</h2>
                    <dl class="divide-y divide-line rounded-2xl border border-line overflow-hidden bg-white shadow-sm">
                        @foreach ($product->specifications as $label => $value)
                            <div class="flex flex-col sm:flex-row gap-1 sm:gap-4 px-4 py-3 odd:bg-paper/60">
                                <dt class="w-full sm:w-48 shrink-0 text-sm font-medium text-ink">{{ $label }}</dt>
                                <dd class="text-sm text-ink-soft">{{ $value }}</dd>
                            </div>
                        @endforeach
                    </dl>
                </div>
            @endif

            <div class="mt-10 border-t border-line pt-6">
                <h2 class="font-bold text-lg text-ink mb-3">Mô tả sản phẩm</h2>
                <p class="text-sm text-ink-soft whitespace-pre-line leading-relaxed">{{ $product->description }}</p>
            </div>
        </div>

        <!-- Sticky add-to-cart bar (mobile) -->
        <div class="sm:hidden fixed bottom-0 inset-x-0 z-40 bg-white border-t border-line px-3 py-2.5 [padding-bottom:calc(env(safe-area-inset-bottom)+0.625rem)] flex items-center gap-2">
            <a href="{{ route('cart.index') }}" class="shrink-0 p-2 rounded-xl border border-line text-ink-soft">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 1.907-4.925 2.29-7.68l.062-.469a1.125 1.125 0 00-1.115-1.276H6.106M7.5 14.25L5.106 5.272M7.5 14.25L6.6 20.4A.75.75 0 007.35 21h9.3m-7.5-1.5h7.5m-7.5 0a.75.75 0 100 1.5.75.75 0 000-1.5zm7.5 0a.75.75 0 100 1.5.75.75 0 000-1.5z" />
                </svg>
            </a>

            <div class="flex-1 min-w-0">
                <p class="text-[11px] text-ink-soft leading-none">Giá</p>
                <p class="mt-1 text-brand font-extrabold text-base leading-none whitespace-nowrap" x-text="new Intl.NumberFormat('vi-VN').format(selected?.price ?? {{ $product->base_price }}) + 'đ'"></p>
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
