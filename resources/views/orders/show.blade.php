<x-shop-layout title="Đơn hàng #{{ $order->order_code }} - Phone Shop">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-green-50 border border-green-200 text-green-800 rounded-md px-4 py-3 mb-6">
            Đặt hàng thành công! Cảm ơn bạn đã mua sắm tại Phone Shop.
        </div>

        <div class="bg-white border border-gray-200 rounded-lg p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h1 class="text-xl font-bold text-gray-900">Đơn hàng #{{ $order->order_code }}</h1>
                    <p class="text-sm text-gray-500">{{ $order->created_at->format('d/m/Y H:i') }}</p>
                </div>
                <span class="px-3 py-1 rounded-full text-sm bg-indigo-50 text-indigo-600">{{ $order->status_label }}</span>
            </div>

            @if ($order->address)
                <div class="mb-4 text-sm">
                    <p class="font-medium text-gray-900">Giao đến</p>
                    <p class="text-gray-600">{{ $order->address->recipient_name }} — {{ $order->address->phone }}</p>
                    <p class="text-gray-600">{{ $order->address->address_line }}</p>
                </div>
            @endif

            <div class="divide-y divide-gray-100 border-t border-gray-100">
                @foreach ($order->items as $item)
                    <div class="flex justify-between py-3 text-sm">
                        <span class="text-gray-600">{{ $item->product_name_snapshot }} ({{ $item->variant_label_snapshot }}) x{{ $item->quantity }}</span>
                        <span class="text-gray-900">{{ number_format($item->subtotal, 0, ',', '.') }}đ</span>
                    </div>
                @endforeach
            </div>

            <div class="border-t border-gray-100 pt-3 mt-3 space-y-1 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-600">Phí vận chuyển</span>
                    <span class="text-gray-900">{{ number_format($order->shipping_fee, 0, ',', '.') }}đ</span>
                </div>
                <div class="flex justify-between font-semibold text-base">
                    <span>Tổng cộng</span>
                    <span class="text-indigo-600">{{ number_format($order->total_amount, 0, ',', '.') }}đ</span>
                </div>
            </div>

            <p class="mt-4 text-sm text-gray-500">
                Phương thức thanh toán: {{ $order->payment_method === 'cod' ? 'Thanh toán khi nhận hàng (COD)' : $order->payment_method }}
            </p>
        </div>

        <a href="{{ route('orders.index') }}" class="inline-block mt-6 text-indigo-600 hover:underline text-sm">&larr; Xem tất cả đơn hàng</a>
    </div>
</x-shop-layout>
