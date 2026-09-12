<x-admin-layout title="Dashboard - Admin">
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

    @vite('resources/js/admin-dashboard.js')
</x-admin-layout>
