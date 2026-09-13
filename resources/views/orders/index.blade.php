<x-shop-layout title="Đơn hàng của tôi - phuonghihi">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <h1 class="text-3xl font-bold text-ink mb-6">Đơn hàng của tôi</h1>

        @if ($orders->isEmpty())
            <div class="bg-white border border-line rounded-2xl shadow-sm p-10 text-center text-ink-soft">
                Bạn chưa có đơn hàng nào.
            </div>
        @else
            <div class="bg-white border border-line rounded-2xl shadow-sm divide-y divide-line overflow-hidden">
                @foreach ($orders as $order)
                    <a href="{{ route('orders.show', $order) }}" class="flex items-center justify-between p-4 hover:bg-slate-50 transition">
                        <div>
                            <p class="font-semibold text-ink">#{{ $order->order_code }}</p>
                            <p class="text-sm text-ink-soft">{{ $order->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        <div class="text-right">
                            <p class="font-bold text-brand">{{ number_format($order->total_amount, 0, ',', '.') }}đ</p>
                            <p class="text-sm font-medium text-ink-soft">{{ $order->status_label }}</p>
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
