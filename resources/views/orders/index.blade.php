<x-shop-layout title="Đơn hàng của tôi - Phone Shop">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">Đơn hàng của tôi</h1>

        @if ($orders->isEmpty())
            <div class="bg-white border border-gray-200 rounded-lg p-10 text-center text-gray-500">
                Bạn chưa có đơn hàng nào.
            </div>
        @else
            <div class="bg-white border border-gray-200 rounded-lg divide-y divide-gray-100">
                @foreach ($orders as $order)
                    <a href="{{ route('orders.show', $order) }}" class="flex items-center justify-between p-4 hover:bg-gray-50">
                        <div>
                            <p class="font-medium text-gray-900">#{{ $order->order_code }}</p>
                            <p class="text-sm text-gray-500">{{ $order->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        <div class="text-right">
                            <p class="font-medium text-gray-900">{{ number_format($order->total_amount, 0, ',', '.') }}đ</p>
                            <p class="text-sm text-indigo-600">{{ $order->status_label }}</p>
                        </div>
                    </a>
                @endforeach
            </div>

            <div class="mt-6">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
</x-shop-layout>
