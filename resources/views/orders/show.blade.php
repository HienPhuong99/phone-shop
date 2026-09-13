<x-shop-layout title="Đơn hàng #{{ $order->order_code }} - Phone Shop">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-green-50 border border-green-200 text-green-800 rounded-xl px-4 py-3 mb-6 text-sm">
            Đặt hàng thành công! Cảm ơn bạn đã mua sắm tại Phone Shop.
        </div>

        <div class="bg-white border border-line rounded-2xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h1 class="text-xl font-bold text-ink">Đơn hàng #{{ $order->order_code }}</h1>
                    <p class="text-sm text-ink-soft">{{ $order->created_at->format('d/m/Y H:i') }}</p>
                </div>
                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-brand/10 text-brand">{{ $order->status_label }}</span>
            </div>

            @if ($order->address)
                <div class="mb-4 text-sm bg-slate-50 p-4 rounded-xl border border-line">
                    <p class="font-semibold text-ink">Giao đến</p>
                    <p class="text-ink-soft">{{ $order->address->recipient_name }} — {{ $order->address->phone }}</p>
                    <p class="text-ink-soft">{{ $order->address->address_line }}</p>
                </div>
            @endif

            <div class="divide-y divide-line border-t border-line">
                @foreach ($order->items as $item)
                    <div class="flex justify-between py-3 text-sm">
                        <span class="text-ink-soft">{{ $item->product_name_snapshot }} ({{ $item->variant_label_snapshot }}) x{{ $item->quantity }}</span>
                        <span class="text-ink font-medium">{{ number_format($item->subtotal, 0, ',', '.') }}đ</span>
                    </div>
                @endforeach
            </div>

            <div class="border-t border-line pt-3 mt-3 space-y-1 text-sm">
                <div class="flex justify-between">
                    <span class="text-ink-soft">Phí vận chuyển</span>
                    <span class="text-ink">{{ number_format($order->shipping_fee, 0, ',', '.') }}đ</span>
                </div>
                <div class="flex justify-between font-semibold text-base">
                    <span>Tổng cộng</span>
                    <span class="text-brand font-bold text-lg">{{ number_format($order->total_amount, 0, ',', '.') }}đ</span>
                </div>
            </div>

            <p class="mt-4 text-sm text-ink-soft">
                Phương thức thanh toán: {{ $order->payment_method === 'cod' ? 'Thanh toán khi nhận hàng (COD)' : $order->payment_method }}
            </p>
        </div>

        <a href="{{ route('orders.index') }}" class="inline-block mt-6 text-brand hover:text-brand-dark font-medium text-sm transition">&larr; Xem tất cả đơn hàng</a>
    </div>
</x-shop-layout>
