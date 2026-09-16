<x-admin-layout title="Đơn hàng #{{ $order->order_code }} - Admin">
    <div class="max-w-2xl">
        <div class="bg-white border border-gray-200 rounded-lg p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Đơn hàng #{{ $order->order_code }}</h2>
                    <p class="text-sm text-gray-500">{{ $order->created_at->format('d/m/Y H:i') }} — {{ $order->user?->name ?? 'Khách vãng lai' }}</p>
                </div>

                <form method="POST" action="{{ route('admin.orders.update', $order) }}" class="flex items-center gap-2">
                    @csrf
                    @method('PATCH')
                    <select name="status" class="text-sm rounded-md border-gray-300">
                        @foreach (['pending' => 'Chờ xử lý', 'paid' => 'Đã thanh toán', 'shipping' => 'Đang giao', 'completed' => 'Hoàn thành', 'cancelled' => 'Đã huỷ'] as $value => $label)
                            <option value="{{ $value }}" {{ $order->status === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="text-sm bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1.5 rounded-md">Cập nhật</button>
                </form>
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
                @if ($order->discount_amount > 0)
                    <div class="flex justify-between">
                        <span class="text-gray-600">Giảm giá{{ $order->coupon_code ? " ({$order->coupon_code})" : '' }}</span>
                        <span class="text-emerald-600">−{{ number_format($order->discount_amount, 0, ',', '.') }}đ</span>
                    </div>
                @endif
                <div class="flex justify-between font-semibold text-base">
                    <span>Tổng cộng</span>
                    <span class="text-indigo-600">{{ number_format($order->total_amount, 0, ',', '.') }}đ</span>
                </div>
            </div>

            <p class="mt-4 text-sm text-gray-500">
                Phương thức: {{ $order->payment_method === 'cod' ? 'COD' : strtoupper($order->payment_method) }}
                @if ($order->payment)
                    — Thanh toán: {{ $order->payment->status }}
                @endif
            </p>
        </div>

        <a href="{{ route('admin.orders.index') }}" class="inline-block mt-4 text-indigo-600 hover:underline text-sm">&larr; Quay lại danh sách</a>
    </div>
</x-admin-layout>
