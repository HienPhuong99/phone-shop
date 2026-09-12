<x-admin-layout title="Đơn hàng - Admin">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg font-semibold text-gray-900">Đơn hàng</h2>

        <form method="GET" class="flex items-center gap-2">
            <select name="status" onchange="this.form.submit()" class="text-sm rounded-md border-gray-300">
                <option value="">Tất cả trạng thái</option>
                @foreach (['pending' => 'Chờ xử lý', 'paid' => 'Đã thanh toán', 'shipping' => 'Đang giao', 'completed' => 'Hoàn thành', 'cancelled' => 'Đã huỷ'] as $value => $label)
                    <option value="{{ $value }}" {{ request('status') === $value ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </form>
    </div>

    <div class="bg-white border border-gray-200 rounded-lg divide-y divide-gray-100">
        @foreach ($orders as $order)
            <a href="{{ route('admin.orders.show', $order) }}" class="flex items-center justify-between p-4 hover:bg-gray-50">
                <div>
                    <p class="font-medium text-gray-900">#{{ $order->order_code }}</p>
                    <p class="text-sm text-gray-500">{{ $order->user?->name ?? 'Khách vãng lai' }} — {{ $order->created_at->format('d/m/Y H:i') }}</p>
                </div>
                <div class="text-right">
                    <p class="font-medium text-gray-900">{{ number_format($order->total_amount, 0, ',', '.') }}đ</p>
                    <p class="text-sm text-indigo-600">{{ $order->status_label }}</p>
                </div>
            </a>
        @endforeach
    </div>

    <div class="mt-4">{{ $orders->links() }}</div>
</x-admin-layout>
