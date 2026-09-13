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

<x-shop-layout :title="$product->name.' - Phone Shop'">
    <div
        class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8"
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
            <a href="{{ route('home') }}" class="hover:text-ink">Trang chủ</a> /
            <a href="{{ route('products.index') }}" class="hover:text-ink">Sản phẩm</a> /
            <span class="text-ink">{{ $product->name }}</span>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
            <!-- Ảnh sản phẩm -->
            <div>
                <div class="aspect-square bg-white border border-line rounded-[2px] flex items-center justify-center overflow-hidden">
                    @if ($product->thumbnail)
                        <img src="{{ $product->thumbnail }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center bg-[repeating-linear-gradient(45deg,theme(colors.line),theme(colors.line)_8px,transparent_8px,transparent_16px)]">
                            <span class="font-mono text-xs text-ink-soft bg-white px-1">ảnh sản phẩm</span>
                        </div>
                    @endif
                </div>

                @if ($product->images->isNotEmpty())
                    <div class="mt-4 grid grid-cols-5 gap-2">
                        @foreach ($product->images as $image)
                            <div class="aspect-square bg-gray-100 rounded-[2px] overflow-hidden">
                                <img src="{{ $image->url }}" alt="" class="w-full h-full object-cover">
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Thông tin -->
            <div>
                <p class="text-xs font-bold tracking-wide uppercase text-ink-soft">{{ $product->brand->name }}</p>
                <h1 class="mt-1 font-serif text-[32px] font-medium text-ink">{{ $product->name }}</h1>

                <p class="mt-4 text-accent text-[32px] font-bold" x-text="new Intl.NumberFormat('vi-VN').format(selected?.price ?? {{ $product->base_price }}) + 'đ'"></p>

                <template x-if="selected">
                    <p class="mt-1 text-sm" :class="selected.stock > 0 ? 'text-[oklch(52%_0.12_150)]' : 'text-red-500'" x-text="selected.stock > 0 ? `Còn hàng (${selected.stock} sản phẩm)` : 'Hết hàng'"></p>
                </template>

                <!-- Chọn màu -->
                <div class="mt-6">
                    <p class="text-sm font-medium text-ink mb-2">Màu sắc</p>
                    <div class="flex flex-wrap gap-2">
                        <template x-for="color in colors" :key="color">
                            <button
                                type="button"
                                @click="selectColor(color)"
                                :class="selected?.color === color ? 'border-accent text-accent' : 'border-line text-ink'"
                                class="px-4 py-2 text-sm rounded-[2px] border-[1.5px] hover:border-accent"
                                x-text="color"
                            ></button>
                        </template>
                    </div>
                </div>

                <!-- Chọn dung lượng -->
                <div class="mt-4">
                    <p class="text-sm font-medium text-ink mb-2">Dung lượng</p>
                    <div class="flex flex-wrap gap-2">
                        <template x-for="storage in storagesForColor" :key="storage">
                            <button
                                type="button"
                                @click="selectStorage(storage)"
                                :class="selected?.storage === storage ? 'border-accent text-accent' : 'border-line text-ink'"
                                class="px-4 py-2 text-sm rounded-[2px] border-[1.5px] hover:border-accent"
                                x-text="storage"
                            ></button>
                        </template>
                    </div>
                </div>

                <form method="POST" action="{{ route('cart.store') }}" class="mt-8 flex items-center gap-3">
                    @csrf
                    <input type="hidden" name="variant_id" :value="selectedId">

                    <input type="number" name="quantity" value="1" min="1" :max="selected?.stock ?? 1" class="w-20 rounded-[2px] border-line text-sm">

                    <button
                        type="submit"
                        :disabled="!selected || selected.stock <= 0"
                        class="flex-1 px-8 py-3 rounded-[2px] text-white font-medium disabled:bg-gray-300 disabled:cursor-not-allowed bg-ink hover:bg-ink/90"
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
    </div>
</x-shop-layout>
