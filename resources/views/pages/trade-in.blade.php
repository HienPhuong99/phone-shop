@php
    $productOptions = $products->map(fn ($p) => ['id' => $p->id, 'name' => $p->name, 'price' => (float) $p->base_price]);
@endphp

<x-shop-layout title="Thu cũ đổi mới - phuonghihi"
    description="Thu cũ đổi mới iPhone tại phuonghihi: ước tính giá máy cũ ngay trên web, trừ thẳng vào giá máy mới, phần chênh lệch có thể trả góp.">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <nav class="text-sm text-ink-soft mb-6">
            <a href="{{ route('home') }}" class="hover:text-brand transition">Trang chủ</a> /
            <a href="{{ route('pages.services') }}" class="hover:text-brand transition">Dịch vụ</a> /
            <span class="text-ink">Thu cũ đổi mới</span>
        </nav>

        <h1 class="font-bold text-3xl text-ink mb-2">Thu cũ lên đời</h1>
        <p class="text-ink-soft mb-8">Định giá máy cũ minh bạch, trừ thẳng vào hoá đơn khi lên đời máy mới. Chọn model và tình trạng máy bên dưới để xem giá ước tính.</p>

        <div
            x-data="{
                products: {{ Illuminate\Support\Js::from($productOptions) }},
                productId: {{ $productOptions->first()['id'] ?? 'null' }},
                condition: 'good',
                conditions: {
                    like_new: { label: 'Như mới', desc: 'Không trầy xước, pin trên 90%, đầy đủ phụ kiện', rate: 0.7 },
                    good: { label: 'Tốt', desc: 'Trầy xước nhẹ, còn hoạt động bình thường', rate: 0.55 },
                    fair: { label: 'Trung bình', desc: 'Trầy xước nhiều, pin yếu hoặc lỗi nhẹ', rate: 0.4 },
                },
                get selectedProduct() {
                    return this.products.find(p => p.id === this.productId) ?? null;
                },
                get estimate() {
                    if (!this.selectedProduct) return 0;
                    return Math.round(this.selectedProduct.price * this.conditions[this.condition].rate);
                },
            }"
            class="bg-white border border-line rounded-2xl shadow-sm p-6"
        >
            <div>
                <label class="text-sm font-semibold text-ink mb-2 block">Máy bạn đang dùng</label>
                <select x-model.number="productId" class="w-full rounded-xl border-line text-sm focus:border-brand focus:ring-brand">
                    <template x-for="p in products" :key="p.id">
                        <option :value="p.id" x-text="p.name"></option>
                    </template>
                </select>
            </div>

            <div class="mt-5">
                <label class="text-sm font-semibold text-ink mb-2 block">Tình trạng máy</label>
                <div class="space-y-2">
                    <template x-for="key in ['like_new', 'good', 'fair']" :key="key">
                        <label
                            @click="condition = key"
                            :class="condition === key ? 'border-brand ring-1 ring-brand bg-brand/5' : 'border-line'"
                            class="flex items-start gap-3 p-3 rounded-xl border-[1.5px] cursor-pointer transition"
                        >
                            <input type="radio" :checked="condition === key" class="mt-1 text-brand focus:ring-brand" readonly>
                            <span>
                                <span class="block text-sm font-semibold text-ink" x-text="conditions[key].label"></span>
                                <span class="block text-xs text-ink-soft" x-text="conditions[key].desc"></span>
                            </span>
                        </label>
                    </template>
                </div>
            </div>

            <div class="mt-6 rounded-xl bg-paper border border-line p-5 text-center">
                <p class="text-xs font-bold tracking-wide uppercase text-ink-soft">Giá thu ước tính</p>
                <p class="mt-1 text-3xl font-extrabold text-brand" x-text="new Intl.NumberFormat('vi-VN').format(estimate) + 'đ'"></p>
                <p class="mt-1 text-xs text-ink-soft">Giá tham khảo — giá cuối cùng được kiểm tra và chốt trực tiếp tại cửa hàng</p>
            </div>

            <a href="{{ route('pages.contact') }}" class="mt-5 block text-center bg-brand hover:bg-brand-dark text-white font-semibold py-3 rounded-2xl shadow-sm transition">
                Đặt lịch định giá tại cửa hàng
            </a>
        </div>

        <div class="mt-8 bg-white border border-line rounded-2xl shadow-sm p-6">
            <h2 class="font-bold text-base text-ink mb-3">Quy trình thu cũ</h2>
            <ol class="space-y-2 text-sm text-ink-soft list-decimal list-inside">
                <li>Ước tính giá trên trang này để tham khảo trước.</li>
                <li>Mang máy đến cửa hàng để kiểm tra và định giá chính xác.</li>
                <li>Đồng ý mức giá — trừ thẳng vào hoá đơn mua máy mới, hoặc nhận tiền mặt.</li>
            </ol>
        </div>
    </div>
</x-shop-layout>
