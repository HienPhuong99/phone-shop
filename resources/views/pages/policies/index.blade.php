<x-shop-layout title="Chính sách - Phone Shop">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <nav class="text-sm text-ink-soft mb-6">
            <a href="{{ route('home') }}" class="hover:text-brand transition">Trang chủ</a> /
            <span class="text-ink">Chính sách</span>
        </nav>

        <h1 class="font-bold text-3xl text-ink mb-2">Chính sách</h1>
        <p class="text-ink-soft mb-8">Mọi chính sách được công khai rõ ràng để bạn yên tâm khi mua hàng.</p>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <a href="{{ route('pages.policies.warranty') }}" class="block bg-white border border-line rounded-2xl shadow-sm hover:shadow-md hover:border-brand transition p-6">
                <h2 class="font-bold text-base text-ink mb-1">Chính sách bảo hành</h2>
                <p class="text-sm text-ink-soft">Thời hạn 12 tháng, điều kiện bảo hành và quy trình đổi máy 1 đổi 1.</p>
            </a>
            <a href="{{ route('pages.policies.returns') }}" class="block bg-white border border-line rounded-2xl shadow-sm hover:shadow-md hover:border-brand transition p-6">
                <h2 class="font-bold text-base text-ink mb-1">Đổi trả & hoàn tiền</h2>
                <p class="text-sm text-ink-soft">Đổi trả trong 7 ngày, các trường hợp được hoàn tiền 100%.</p>
            </a>
            <a href="{{ route('pages.policies.shipping') }}" class="block bg-white border border-line rounded-2xl shadow-sm hover:shadow-md hover:border-brand transition p-6">
                <h2 class="font-bold text-base text-ink mb-1">Vận chuyển & thanh toán</h2>
                <p class="text-sm text-ink-soft">Phí ship, thời gian giao hàng và các phương thức thanh toán hỗ trợ.</p>
            </a>
            <a href="{{ route('pages.policies.privacy') }}" class="block bg-white border border-line rounded-2xl shadow-sm hover:shadow-md hover:border-brand transition p-6">
                <h2 class="font-bold text-base text-ink mb-1">Bảo mật thông tin</h2>
                <p class="text-sm text-ink-soft">Thông tin nào được thu thập, dùng để làm gì và ai được truy cập.</p>
            </a>
        </div>
    </div>
</x-shop-layout>
