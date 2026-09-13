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
        }"
    >
        <nav class="text-sm text-ink-soft mb-6">
            <a href="{{ route('home') }}" class="hover:text-brand transition">Trang chủ</a> /
            <a href="{{ route('products.index') }}" class="hover:text-brand transition">Sản phẩm</a> /
            <span class="text-ink font-medium">{{ $product->name }}</span>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
            <!-- Ảnh sản phẩm -->
            <div>
                <div class="aspect-square bg-white border border-line rounded-2xl shadow-sm flex items-center justify-center overflow-hidden">
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
                                <img src="{{ $image->url }}" alt="" loading="lazy" decoding="async" class="w-full h-full object-cover">
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Thông tin -->
            <div>
                <p class="text-xs font-bold tracking-wide uppercase text-brand">{{ $product->series->name }}</p>
                <h1 class="mt-1 font-bold text-3xl text-ink">{{ $product->name }}</h1>

                <p class="mt-4 text-brand text-3xl font-extrabold" x-text="new Intl.NumberFormat('vi-VN').format(selected?.price ?? {{ $product->base_price }}) + 'đ'"></p>

                <template x-if="selected">
                    <p class="mt-1 text-sm font-medium" :class="selected.stock > 0 ? 'text-emerald-600' : 'text-red-500'" x-text="selected.stock > 0 ? `Còn hàng (${selected.stock} sản phẩm)` : 'Hết hàng'"></p>
                </template>

                <!-- Chọn màu -->
                <div class="mt-6">
                    <p class="text-sm font-semibold text-ink mb-2">Màu sắc</p>
                    <div class="flex flex-wrap gap-2">
                        <template x-for="color in colors" :key="color">
                            <button
                                type="button"
                                data-testid="variant-color-button"
                                @click="selectColor(color)"
                                :class="selected?.color === color ? 'border-brand text-brand bg-brand/5' : 'border-line text-ink hover:border-brand hover:text-brand'"
                                class="px-4 py-2 text-sm rounded-xl border-[1.5px] font-medium transition"
                                x-text="color"
                            ></button>
                        </template>
                    </div>
                </div>

                <!-- Chọn dung lượng -->
                <div class="mt-4">
                    <p class="text-sm font-semibold text-ink mb-2">Dung lượng</p>
                    <div class="flex flex-wrap gap-2">
                        <template x-for="storage in storagesForColor" :key="storage">
                            <button
                                type="button"
                                @click="selectStorage(storage)"
                                :class="selected?.storage === storage ? 'border-brand text-brand bg-brand/5' : 'border-line text-ink hover:border-brand hover:text-brand'"
                                class="px-4 py-2 text-sm rounded-xl border-[1.5px] font-medium transition"
                                x-text="storage"
                            ></button>
                        </template>
                    </div>
                </div>

                <form method="POST" action="{{ route('cart.store') }}" class="hidden sm:flex mt-8 items-center gap-3">
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

                @error('quantity')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror

                <div class="mt-10 border-t border-line pt-6">
                    <h2 class="text-sm font-semibold text-ink mb-2">Mô tả sản phẩm</h2>
                    <p class="text-sm text-ink-soft whitespace-pre-line">{{ $product->description }}</p>
                </div>
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
