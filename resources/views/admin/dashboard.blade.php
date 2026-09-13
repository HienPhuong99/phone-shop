<x-admin-layout title="Dashboard - Admin">
    <!-- Liên kết nhanh -->
    <div class="flex flex-wrap items-center gap-3 mb-6">
        <a href="{{ route('admin.products.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-lg inline-flex items-center gap-1.5 shadow-sm">
            + Thêm sản phẩm
        </a>
        <a href="{{ route('admin.product-series.create') }}" class="bg-white hover:bg-gray-50 text-gray-700 border border-gray-200 text-sm font-medium px-4 py-2 rounded-lg inline-flex items-center gap-1.5 shadow-sm">
            + Thêm dòng sản phẩm
        </a>
        <a href="{{ route('admin.orders.index', ['status' => 'pending']) }}" class="bg-white hover:bg-gray-50 text-gray-700 border border-gray-200 text-sm font-medium px-4 py-2 rounded-lg inline-flex items-center gap-2 shadow-sm">
            <span>Đơn chờ xử lý</span>
            @if ($pendingOrdersCount > 0)
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">
                    {{ $pendingOrdersCount }}
                </span>
            @else
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-600">
                    0
                </span>
            @endif
        </a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <p class="text-sm text-gray-500">Tổng số đơn hàng</p>
            <p class="text-2xl font-semibold text-gray-900 mt-1">{{ number_format($totalOrders) }}</p>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <p class="text-sm text-gray-500">Đơn hàng hôm nay</p>
            <p class="text-2xl font-semibold text-gray-900 mt-1">{{ number_format($ordersToday) }}</p>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <p class="text-sm text-gray-500">Doanh thu tháng này</p>
            <p class="text-2xl font-semibold text-indigo-600 mt-1">{{ number_format($revenueThisMonth, 0, ',', '.') }}đ</p>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-4">
            <p class="text-sm text-gray-500">Tổng doanh thu</p>
            <p class="text-2xl font-semibold text-indigo-600 mt-1">{{ number_format($totalRevenue, 0, ',', '.') }}đ</p>
        </div>
    </div>

    <div class="bg-white rounded-lg border border-gray-200 p-4">
        <h2 class="text-sm font-semibold text-gray-900 mb-4">Doanh thu 7 ngày gần nhất</h2>
        <canvas
            id="revenueChart"
            height="80"
            data-labels="{{ $chartLabels->toJson() }}"
            data-values="{{ $chartValues->toJson() }}"
        ></canvas>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
        <!-- Đơn hàng gần đây -->
        <div class="bg-white rounded-lg border border-gray-200 p-4 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-sm font-semibold text-gray-900">Đơn hàng gần đây</h2>
                    <a href="{{ route('admin.orders.index') }}" class="text-xs text-indigo-600 hover:underline">Xem tất cả</a>
                </div>

                <div class="divide-y divide-gray-100">
                    @forelse ($recentOrders as $order)
                        <a href="{{ route('admin.orders.show', $order) }}" class="flex items-center justify-between py-3 hover:bg-gray-50 -mx-2 px-2 rounded-md">
                            <div>
                                <p class="font-medium text-gray-900 text-sm">#{{ $order->order_code }}</p>
                                <p class="text-xs text-gray-500">{{ $order->user?->name ?? 'Khách vãng lai' }} &bull; {{ $order->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                            <div class="text-right">
                                <p class="font-medium text-gray-900 text-sm">{{ number_format($order->total_amount, 0, ',', '.') }}đ</p>
                                <p class="text-xs text-indigo-600">{{ $order->status_label }}</p>
                            </div>
                        </a>
                    @empty
                        <p class="text-sm text-gray-500 py-6 text-center">Chưa có đơn hàng nào.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Cột phải: Sắp hết hàng + Bán chạy tháng này -->
        <div class="space-y-6">
            <!-- Cảnh báo sắp hết hàng -->
            <div class="bg-white rounded-lg border border-gray-200 p-4">
                <h2 class="text-sm font-semibold text-gray-900 mb-4">Cảnh báo sắp hết hàng</h2>
                <div class="divide-y divide-gray-100">
                    @forelse ($lowStockVariants as $variant)
                        <a href="{{ route('admin.products.edit', $variant->product) }}" class="flex items-center justify-between py-2.5 hover:bg-gray-50 -mx-2 px-2 rounded-md text-sm">
                            <div class="min-w-0 pr-2">
                                <p class="font-medium text-gray-900 truncate">{{ $variant->product->name }}</p>
                                <p class="text-xs text-gray-500">{{ $variant->label }}</p>
                            </div>
                            <div class="flex-shrink-0">
                                @if ($variant->stock_quantity == 0)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-red-100 text-red-800">
                                        Hết hàng (0)
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-amber-100 text-amber-800">
                                        Còn {{ $variant->stock_quantity }}
                                    </span>
                                @endif
                            </div>
                        </a>
                    @empty
                        <p class="text-sm text-gray-500 py-4 text-center">Không có biến thể nào sắp hết hàng.</p>
                    @endforelse
                </div>
            </div>

            <!-- Sản phẩm bán chạy tháng này -->
            <div class="bg-white rounded-lg border border-gray-200 p-4">
                <h2 class="text-sm font-semibold text-gray-900 mb-4">Sản phẩm bán chạy tháng này</h2>
                <div class="divide-y divide-gray-100">
                    @forelse ($topProducts as $product)
                        <div class="flex items-center justify-between py-2.5 text-sm">
                            <p class="font-medium text-gray-900 truncate pr-2">{{ $product->product_name_snapshot }}</p>
                            <div class="flex-shrink-0 text-right">
                                <span class="text-sm font-semibold text-indigo-600">{{ number_format($product->total_sold) }}</span>
                                <span class="text-xs text-gray-500">đã bán</span>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 py-4 text-center">Chưa có dữ liệu bán hàng tháng này.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    @vite('resources/js/admin-dashboard.js')
</x-admin-layout>
