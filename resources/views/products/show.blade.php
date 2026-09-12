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
        <nav class="text-sm text-gray-500 mb-6">
            <a href="{{ route('home') }}" class="hover:underline">Trang chủ</a> /
            <a href="{{ route('products.index') }}" class="hover:underline">Sản phẩm</a> /
            <span class="text-gray-700">{{ $product->name }}</span>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
            <!-- Ảnh sản phẩm -->
            <div>
                <div class="aspect-square bg-white border border-gray-200 rounded-lg flex items-center justify-center overflow-hidden">
                    @if ($product->thumbnail)
                        <img src="{{ $product->thumbnail }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                    @else
                        <span class="text-gray-300">Không có ảnh</span>
                    @endif
                </div>

                @if ($product->images->isNotEmpty())
                    <div class="mt-4 grid grid-cols-5 gap-2">
                        @foreach ($product->images as $image)
                            <div class="aspect-square bg-gray-100 rounded-md overflow-hidden">
                                <img src="{{ $image->url }}" alt="" class="w-full h-full object-cover">
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Thông tin -->
            <div>
                <p class="text-sm text-gray-400 uppercase tracking-wide">{{ $product->brand->name }}</p>
                <h1 class="mt-1 text-2xl font-bold text-gray-900">{{ $product->name }}</h1>

                <p class="mt-4 text-3xl font-semibold text-indigo-600" x-text="new Intl.NumberFormat('vi-VN').format(selected?.price ?? {{ $product->base_price }}) + 'đ'"></p>

                <template x-if="selected">
                    <p class="mt-1 text-sm" :class="selected.stock > 0 ? 'text-green-600' : 'text-red-500'" x-text="selected.stock > 0 ? `Còn hàng (${selected.stock} sản phẩm)` : 'Hết hàng'"></p>
                </template>

                <!-- Chọn màu -->
                <div class="mt-6">
                    <p class="text-sm font-medium text-gray-700 mb-2">Màu sắc</p>
                    <div class="flex flex-wrap gap-2">
                        <template x-for="color in colors" :key="color">
                            <button
                                type="button"
                                @click="selectColor(color)"
                                :class="selected?.color === color ? 'border-indigo-600 text-indigo-600' : 'border-gray-300 text-gray-600'"
                                class="px-4 py-2 text-sm rounded-md border hover:border-indigo-400"
                                x-text="color"
                            ></button>
                        </template>
                    </div>
                </div>

                <!-- Chọn dung lượng -->
                <div class="mt-4">
                    <p class="text-sm font-medium text-gray-700 mb-2">Dung lượng</p>
                    <div class="flex flex-wrap gap-2">
                        <template x-for="storage in storagesForColor" :key="storage">
                            <button
                                type="button"
                                @click="selectStorage(storage)"
                                :class="selected?.storage === storage ? 'border-indigo-600 text-indigo-600' : 'border-gray-300 text-gray-600'"
                                class="px-4 py-2 text-sm rounded-md border hover:border-indigo-400"
                                x-text="storage"
                            ></button>
                        </template>
                    </div>
                </div>

                <button
                    type="button"
                    :disabled="!selected || selected.stock <= 0"
                    class="mt-8 w-full sm:w-auto px-8 py-3 rounded-md text-white font-medium disabled:bg-gray-300 disabled:cursor-not-allowed bg-indigo-600 hover:bg-indigo-700"
                >
                    Thêm vào giỏ hàng
                </button>

                <div class="mt-10 border-t border-gray-100 pt-6">
                    <h2 class="text-sm font-semibold text-gray-900 mb-2">Mô tả sản phẩm</h2>
                    <p class="text-sm text-gray-600 whitespace-pre-line">{{ $product->description }}</p>
                </div>
            </div>
        </div>
    </div>
</x-shop-layout>
