<x-shop-layout title="Kết quả thanh toán - Phone Shop">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-16 text-center">
        @if (! $isValid)
            <div class="bg-red-50 border border-red-200 text-red-800 rounded-md px-6 py-8">
                <h1 class="text-xl font-semibold mb-2">Không thể xác thực giao dịch</h1>
                <p class="text-sm">Chữ ký không hợp lệ. Vui lòng liên hệ hỗ trợ nếu tiền đã bị trừ.</p>
            </div>
        @elseif ($success)
            <div class="bg-green-50 border border-green-200 text-green-800 rounded-md px-6 py-8">
                <h1 class="text-xl font-semibold mb-2">Thanh toán thành công!</h1>
                <p class="text-sm">Đơn hàng của bạn đang được xử lý và xác nhận.</p>
            </div>
        @else
            <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 rounded-md px-6 py-8">
                <h1 class="text-xl font-semibold mb-2">Thanh toán chưa hoàn tất</h1>
                <p class="text-sm">Giao dịch bị huỷ hoặc thất bại. Bạn có thể thử lại.</p>
            </div>
        @endif

        @if ($order)
            <a href="{{ route('orders.show', $order) }}" class="inline-block mt-6 text-indigo-600 hover:underline">
                Xem chi tiết đơn hàng #{{ $order->order_code }}
            </a>
        @else
            <a href="{{ route('orders.index') }}" class="inline-block mt-6 text-indigo-600 hover:underline">
                Xem đơn hàng của tôi
            </a>
        @endif
    </div>
</x-shop-layout>
