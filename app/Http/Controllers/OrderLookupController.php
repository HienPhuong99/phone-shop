<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderLookupController extends Controller
{
    public function create(): View
    {
        return view('orders.lookup');
    }

    /**
     * Order code + phone is the guest's proof of identity — no login
     * needed. Matches on the shipping address's phone, since a guest order
     * has no user account to check against.
     */
    public function show(Request $request): View
    {
        $data = $request->validate([
            'order_code' => ['required', 'string', 'max:50'],
            'phone' => ['required', 'string', 'max:20'],
        ]);

        $order = Order::where('order_code', $data['order_code'])
            ->whereHas('address', fn ($q) => $q->where('phone', $data['phone']))
            ->with(['items', 'address', 'payment'])
            ->first();

        if (! $order) {
            return view('orders.lookup', [
                'error' => 'Không tìm thấy đơn hàng khớp với mã đơn và số điện thoại đã nhập.',
            ]);
        }

        return view('orders.show', ['order' => $order, 'isGuestView' => true]);
    }

    /**
     * Reachable only via a valid signed URL (route middleware 'signed'),
     * generated once at the end of guest checkout — that signature is
     * what proves ownership here, no further check needed.
     */
    public function confirmation(Order $order): View
    {
        $order->load(['items', 'address', 'payment']);

        return view('orders.show', ['order' => $order, 'isGuestView' => true]);
    }
}
