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

<x-shop-layout :title="$product->name.' - phuonghihi'">
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

                <form method="POST" action="{{ route('cart.store') }}" class="mt-8 flex items-center gap-3">
                    @csrf
                    <input type="hidden" name="variant_id" :value="selectedId">

                    <input type="number" name="quantity" value="1" min="1" :max="selected?.stock ?? 1" class="w-20 rounded-xl border-line text-sm focus:border-brand focus:ring-brand">

                    <button
                        type="submit"
                        data-testid="add-to-cart-button"
                        :disabled="!selected || selected.stock <= 0"
                        class="flex-1 px-8 py-3 rounded-2xl text-white font-semibold disabled:bg-gray-300 disabled:cursor-not-allowed bg-brand hover:bg-brand-dark shadow-sm transition"
                    >
                        Thêm vào giỏ hàng
                    </button>

                    <button
                        type="button"
                        data-testid="buy-now-button"
                        x-on:click="$dispatch('open-modal', 'buy-now')"
                        :disabled="!selected || selected.stock <= 0"
                        class="flex-1 px-8 py-3 rounded-2xl font-semibold border-[1.5px] border-brand text-brand disabled:border-gray-300 disabled:text-gray-300 disabled:cursor-not-allowed hover:bg-brand/5 transition"
                    >
                        Mua ngay
                    </button>
                </form>

                @error('quantity')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror

                <x-modal name="buy-now" :show="$errors->buyNow->isNotEmpty()" focusable>
                    <form method="POST" action="{{ route('buy-now.store') }}" class="p-6">
                        @csrf
                        <input type="hidden" name="variant_id" :value="selectedId">

                        <h2 class="text-lg font-bold text-ink">Mua ngay</h2>
                        <p class="mt-1 text-sm text-ink-soft">Điền thông tin bên dưới, không cần đăng ký tài khoản — đặt hàng chỉ với một bước.</p>

                        <div class="mt-5 space-y-4">
                            <div>
                                <x-input-label for="buy_now_recipient_name" value="Họ tên người nhận" />
                                <x-text-input id="buy_now_recipient_name" name="recipient_name" class="block mt-1 w-full rounded-xl border-line text-sm focus:border-brand focus:ring-brand" :value="old('recipient_name')" required />
                                <x-input-error :messages="$errors->buyNow->get('recipient_name')" class="mt-1" />
                            </div>

                            <div>
                                <x-input-label for="buy_now_phone" value="Số điện thoại" />
                                <x-text-input id="buy_now_phone" name="phone" class="block mt-1 w-full rounded-xl border-line text-sm focus:border-brand focus:ring-brand" :value="old('phone')" required />
                                <x-input-error :messages="$errors->buyNow->get('phone')" class="mt-1" />
                            </div>

                            <div>
                                <x-input-label for="buy_now_address_line" value="Địa chỉ giao hàng" />
                                <x-text-input id="buy_now_address_line" name="address_line" class="block mt-1 w-full rounded-xl border-line text-sm focus:border-brand focus:ring-brand" :value="old('address_line')" required />
                                <x-input-error :messages="$errors->buyNow->get('address_line')" class="mt-1" />
                            </div>

                            <div>
                                <x-input-label value="Số lượng" />
                                <input type="number" name="quantity" value="1" min="1" :max="selected?.stock ?? 1" class="block mt-1 w-24 rounded-xl border-line text-sm focus:border-brand focus:ring-brand">
                            </div>

                            <div>
                                <x-input-label value="Phương thức thanh toán" />
                                <div class="mt-1 space-y-2">
                                    <label class="flex items-center gap-3 p-3 border-[1.5px] border-brand bg-brand/5 rounded-xl cursor-pointer">
                                        <input type="radio" name="payment_method" value="cod" checked class="text-brand focus:ring-brand">
                                        <span class="text-sm font-medium text-ink">Thanh toán khi nhận hàng (COD)</span>
                                    </label>
                                    <label class="flex items-center gap-3 p-3 border border-line rounded-xl cursor-pointer hover:border-brand/50 transition">
                                        <input type="radio" name="payment_method" value="vnpay" class="text-brand focus:ring-brand">
                                        <span class="text-sm font-medium text-ink">Thanh toán qua VNPay</span>
                                    </label>
                                </div>
                                <x-input-error :messages="$errors->buyNow->get('quantity')" class="mt-1" />
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end gap-3">
                            <x-secondary-button type="button" x-on:click="$dispatch('close')">Huỷ</x-secondary-button>
                            <button type="submit" class="px-6 py-2.5 rounded-xl text-white font-semibold bg-brand hover:bg-brand-dark shadow-sm transition">
                                Xác nhận đặt hàng
                            </button>
                        </div>
                    </form>
                </x-modal>

                <div class="mt-10 border-t border-line pt-6">
                    <h2 class="text-sm font-semibold text-ink mb-2">Mô tả sản phẩm</h2>
                    <p class="text-sm text-ink-soft whitespace-pre-line">{{ $product->description }}</p>
                </div>
            </div>
        </div>
    </div>
</x-shop-layout>
