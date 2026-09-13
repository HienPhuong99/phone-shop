<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Services\CartService;
use App\Services\OrderService;
use App\Services\VnpayService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function __construct(
        private readonly CartService $cartService,
        private readonly OrderService $orderService,
        private readonly VnpayService $vnpay,
    ) {}

    public function index(Request $request): View|RedirectResponse
    {
        $cart = $this->cartService->currentCart($request);
        $cart->load(['items.variant.product']);

        if ($cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng của bạn đang trống.');
        }

        $addresses = $request->user()->addresses()->orderByDesc('is_default')->get();
        $shippingFee = OrderService::SHIPPING_FEE;

        return view('checkout.index', compact('cart', 'addresses', 'shippingFee'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'address_id' => ['nullable', 'integer', 'exists:addresses,id'],
            'recipient_name' => ['required_without:address_id', 'nullable', 'string', 'max:255'],
            'phone' => ['required_without:address_id', 'nullable', 'string', 'max:20'],
            'address_line' => ['required_without:address_id', 'nullable', 'string', 'max:255'],
            'payment_method' => ['required', 'in:cod,vnpay'],
        ]);

        $user = $request->user();

        $cart = $this->cartService->currentCart($request);
        $cart->load('items.variant');

        if ($cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng của bạn đang trống.');
        }

        if (! empty($data['address_id'])) {
            $address = $user->addresses()->findOrFail($data['address_id']);
        } else {
            $address = $user->addresses()->create([
                'recipient_name' => $data['recipient_name'],
                'phone' => $data['phone'],
                'address_line' => $data['address_line'],
                'is_default' => $user->addresses()->doesntExist(),
            ]);
        }

        $lineItems = $cart->items->map(fn ($item) => [
            'variant_id' => $item->variant_id,
            'quantity' => $item->quantity,
        ]);

        try {
            $order = $this->orderService->placeOrder($user, $address, $data['payment_method'], $lineItems);
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }

        $cart->items()->delete();

        if ($data['payment_method'] === 'vnpay') {
            Payment::create([
                'order_id' => $order->id,
                'gateway' => 'vnpay',
                'amount' => $order->total_amount,
                'status' => Payment::STATUS_PENDING,
            ]);

            return redirect()->away($this->vnpay->buildPaymentUrl($order, $request->ip()));
        }

        return redirect()->route('orders.show', $order)->with('status', 'Đặt hàng thành công!');
    }
}
