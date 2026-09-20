<x-shop-layout title="Dịch vụ - phuonghihi"
    description="Các dịch vụ tại phuonghihi: thay pin, kiểm tra máy, hỗ trợ bảo hành, trả góp và thu cũ đổi mới iPhone. Báo giá rõ ràng trước khi làm.">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <nav class="text-sm text-ink-soft mb-6">
            <a href="{{ route('home') }}" class="hover:text-brand transition">Trang chủ</a> /
            <span class="text-ink">Dịch vụ</span>
        </nav>

        <h1 class="font-bold text-3xl text-ink mb-2">Dịch vụ</h1>
        <p class="text-ink-soft mb-8">Không chỉ bán máy — chúng tôi đồng hành cùng bạn từ lúc chọn máy đến suốt quá trình sử dụng.</p>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div class="bg-white border border-line rounded-2xl shadow-sm p-6">
                <div class="w-10 h-10 rounded-xl bg-brand/10 text-brand flex items-center justify-center mb-4 font-bold text-lg">✓</div>
                <h2 class="font-bold text-base text-ink mb-1">Bảo hành chính hãng 12 tháng</h2>
                <p class="text-sm text-ink-soft">1 đổi 1 trong 30 ngày đầu nếu lỗi phần cứng do nhà sản xuất, bảo hành tại trung tâm uỷ quyền trên toàn quốc.</p>
                <a href="{{ route('pages.policies.warranty') }}" class="inline-block mt-3 text-sm font-semibold text-brand hover:text-brand-dark transition">Xem chính sách bảo hành &rarr;</a>
            </div>

            <div class="bg-white border border-line rounded-2xl shadow-sm p-6">
                <div class="w-10 h-10 rounded-xl bg-brand/10 text-brand flex items-center justify-center mb-4 font-bold text-lg">%</div>
                <h2 class="font-bold text-base text-ink mb-1">Trả góp 0% lãi suất</h2>
                <p class="text-sm text-ink-soft">Duyệt hồ sơ nhanh trong ngày qua thẻ tín dụng hoặc công ty tài chính liên kết, không cần chứng minh thu nhập.</p>
                <a href="{{ route('pages.installment') }}" class="inline-block mt-3 text-sm font-semibold text-brand hover:text-brand-dark transition">Tính khoản trả góp &rarr;</a>
            </div>

            <div class="bg-white border border-line rounded-2xl shadow-sm p-6">
                <div class="w-10 h-10 rounded-xl bg-brand/10 text-brand flex items-center justify-center mb-4 font-bold text-lg">⇄</div>
                <h2 class="font-bold text-base text-ink mb-1">Thu cũ lên đời</h2>
                <p class="text-sm text-ink-soft">Định giá máy cũ minh bạch ngay tại cửa hàng, trừ thẳng vào hoá đơn khi lên đời máy mới.</p>
                <a href="{{ route('pages.trade-in') }}" class="inline-block mt-3 text-sm font-semibold text-brand hover:text-brand-dark transition">Ước tính giá thu cũ &rarr;</a>
            </div>

            <div class="bg-white border border-line rounded-2xl shadow-sm p-6">
                <div class="w-10 h-10 rounded-xl bg-brand/10 text-brand flex items-center justify-center mb-4 font-bold text-lg">⚡</div>
                <h2 class="font-bold text-base text-ink mb-1">Giao hàng toàn quốc</h2>
                <p class="text-sm text-ink-soft">Nội thành 2-4 giờ, tỉnh thành 1-3 ngày, đồng kiểm trước khi thanh toán COD.</p>
                <a href="{{ route('pages.policies.shipping') }}" class="inline-block mt-3 text-sm font-semibold text-brand hover:text-brand-dark transition">Xem chính sách vận chuyển &rarr;</a>
            </div>
        </div>
    </div>
</x-shop-layout>
