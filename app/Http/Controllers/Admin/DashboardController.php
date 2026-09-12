<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $totalOrders = Order::count();
        $completedStatuses = [Order::STATUS_PAID, Order::STATUS_COMPLETED];
        $totalRevenue = Order::whereIn('status', $completedStatuses)->sum('total_amount');
        $ordersToday = Order::whereDate('created_at', today())->count();
        $revenueThisMonth = Order::whereIn('status', $completedStatuses)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total_amount');

        $days = collect(range(6, 0))->map(fn ($i) => Carbon::today()->subDays($i));

        $revenueByDay = $days->map(function (Carbon $day) use ($completedStatuses) {
            return [
                'label' => $day->format('d/m'),
                'value' => (float) Order::whereIn('status', $completedStatuses)
                    ->whereDate('created_at', $day)
                    ->sum('total_amount'),
            ];
        });

        return view('admin.dashboard', [
            'totalOrders' => $totalOrders,
            'totalRevenue' => $totalRevenue,
            'ordersToday' => $ordersToday,
            'revenueThisMonth' => $revenueThisMonth,
            'chartLabels' => $revenueByDay->pluck('label'),
            'chartValues' => $revenueByDay->pluck('value'),
        ]);
    }
}
