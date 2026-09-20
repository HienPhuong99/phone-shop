<x-shop-layout title="Trả góp 0% - phuonghihi"
    description="Trả góp iPhone qua thẻ tín dụng hoặc công ty tài chính tại phuonghihi: tính trước số tiền mỗi tháng, xem giấy tờ cần chuẩn bị và các khoản phí.">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <nav class="text-sm text-ink-soft mb-6">
            <a href="{{ route('home') }}" class="hover:text-brand transition">Trang chủ</a> /
            <a href="{{ route('pages.services') }}" class="hover:text-brand transition">Dịch vụ</a> /
            <span class="text-ink">Trả góp 0%</span>
        </nav>

        <h1 class="font-bold text-3xl text-ink mb-2">Trả góp 0% lãi suất</h1>
        <p class="text-ink-soft mb-8">Duyệt hồ sơ nhanh trong ngày qua thẻ tín dụng hoặc công ty tài chính liên kết, không cần chứng minh thu nhập. Ước tính khoản trả hàng tháng bên dưới.</p>

        <div
            x-data="{
                price: 20000000,
                term: 12,
                get monthly() {
                    return Math.round((this.price || 0) / this.term);
                },
            }"
            class="bg-white border border-line rounded-2xl shadow-sm p-6"
        >
            <div>
                <label class="text-sm font-semibold text-ink mb-2 block">Giá sản phẩm</label>
                <div class="relative">
                    <input
                        type="number"
                        inputmode="numeric"
                        x-model.number="price"
                        min="0"
                        step="100000"
                        class="w-full rounded-xl border-line text-lg font-semibold focus:border-brand focus:ring-brand pr-12"
                    >
                    <span class="absolute right-4 top-1/2 -translate-y-1/2 text-ink-soft text-sm">đ</span>
                </div>
            </div>

            <div class="mt-5">
                <label class="text-sm font-semibold text-ink mb-2 block">Kỳ hạn trả góp</label>
                <div class="grid grid-cols-3 gap-2">
                    @foreach ([6, 12, 24] as $months)
                        <button
                            type="button"
                            @click="term = {{ $months }}"
                            :class="term === {{ $months }} ? 'border-brand ring-1 ring-brand bg-brand/5 text-brand' : 'border-line text-ink hover:border-brand'"
                            class="rounded-xl border-[1.5px] py-2.5 text-sm font-semibold transition"
                        >{{ $months }} tháng</button>
                    @endforeach
                </div>
            </div>

            <div class="mt-6 rounded-xl bg-paper border border-line p-5 text-center">
                <p class="text-xs font-bold tracking-wide uppercase text-ink-soft">Trả hàng tháng (ước tính)</p>
                <p class="mt-1 text-3xl font-extrabold text-brand" x-text="new Intl.NumberFormat('vi-VN').format(monthly) + 'đ'"></p>
                <p class="mt-1 text-xs text-ink-soft">0% lãi suất — không phát sinh phí ẩn</p>
            </div>

            <a href="{{ route('products.index') }}" class="mt-5 block text-center bg-brand hover:bg-brand-dark text-white font-semibold py-3 rounded-2xl shadow-sm transition">
                Chọn máy để trả góp
            </a>
        </div>

        <div class="mt-8 bg-white border border-line rounded-2xl shadow-sm p-6">
            <h2 class="font-bold text-base text-ink mb-3">Điều kiện trả góp</h2>
            <ul class="space-y-2 text-sm text-ink-soft list-disc list-inside">
                <li>Áp dụng cho đơn hàng từ 3.000.000đ trở lên.</li>
                <li>Duyệt hồ sơ qua thẻ tín dụng Visa/Mastercard hoặc công ty tài chính liên kết.</li>
                <li>Không cần chứng minh thu nhập, xét duyệt trong ngày.</li>
                <li>Số tiền trả hàng tháng trên đây chỉ mang tính tham khảo — số liệu chính xác tuỳ vào đơn vị duyệt hồ sơ.</li>
            </ul>
            <a href="{{ route('pages.contact') }}" class="inline-block mt-4 text-sm font-semibold text-brand hover:text-brand-dark transition">Liên hệ tư vấn trực tiếp &rarr;</a>
        </div>
    </div>
</x-shop-layout>
