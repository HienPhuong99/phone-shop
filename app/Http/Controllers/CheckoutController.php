<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\ProductVariant;
use App\Services\CartService;
use App\Services\ShippingService;
use App\Services\VnpayService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function __construct(
        private readonly CartService $cartService,
        private readonly VnpayService $vnpay,
        private readonly ShippingService $shipping,
    ) {}

    public function index(Request $request): View|RedirectResponse
    {
        $cart = $this->cartService->currentCart($request);
        $cart->load(['items.variant.product']);

        if ($cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng của bạn đang trống.');
        }

        $user = $request->user();
        $addresses = $user ? $user->addresses()->orderByDesc('is_default')->get() : collect();

        $subtotal = (float) $cart->items->sum(fn ($item) => $item->quantity * $item->variant->price);
        $coupon = $this->cartService->appliedCoupon($request, $subtotal);
        $discount = $coupon?->discountFor($subtotal) ?? 0.0;

        $provinces = ShippingService::PROVINCES;
        // Every province's fee, handed to the page so it can show the live
        // total as soon as a province is picked, without a round trip.
        $shippingFees = collect($provinces)->mapWithKeys(fn ($p) => [$p => $this->shipping->feeFor($p)]);

        return view('checkout.index', compact('cart', 'addresses', 'subtotal', 'discount', 'coupon', 'provinces', 'shippingFees'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'address_id' => ['nullable', 'integer', 'exists:addresses,id'],
            'recipient_name' => ['required_without:address_id', 'nullable', 'string', 'max:255'],
            'phone' => ['required_without:address_id', 'nullable', 'string', 'max:20'],
            'address_line' => ['required_without:address_id', 'nullable', 'string', 'max:255'],
            'province' => ['required_without:address_id', 'nullable', Rule::in(ShippingService::PROVINCES)],
            'payment_method' => ['required', 'in:cod,vnpay'],
        ]);

        $user = $request->user();

        $cart = $this->cartService->currentCart($request);
        $cart->load('items.variant');

        if ($cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng của bạn đang trống.');
        }

        if (! empty($data['address_id'])) {
            // A saved address only exists for — and can only be reused by
            // — a logged-in owner; a guest never has address_id in $data.
            abort_if(! $user, 403);
            $address = $user->addresses()->findOrFail($data['address_id']);
        } else {
            $address = Address::create([
                'user_id' => $user?->id,
                'recipient_name' => $data['recipient_name'],
                'phone' => $data['phone'],
                'address_line' => $data['address_line'],
                'province' => $data['province'],
                'is_default' => $user ? $user->addresses()->doesntExist() : false,
            ]);
        }

        try {
            $order = DB::transaction(function () use ($cart, $user, $address, $data, $request) {
                $variantIds = $cart->items->pluck('variant_id');

                $variants = ProductVariant::whereIn('id', $variantIds)
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id');

                foreach ($cart->items as $item) {
                    $variant = $variants->get($item->variant_id);

                    if (! $variant || $variant->stock_quantity < $item->quantity) {
                        throw ValidationException::withMessages([
                            'quantity' => "Sản phẩm \"{$item->variant->product->name} ({$item->variant->label})\" không đủ hàng.",
                        ]);
                    }
                }

                // Recomputed here (not from the page-load subtotal) against
                // the just-locked variant prices — the same "never trust a
                // value older than this transaction" reasoning the existing
                // stock check already followed.
                $subtotal = (float) $cart->items->sum(fn ($item) => $item->quantity * $variants->get($item->variant_id)->price);
                $shippingFee = $this->shipping->feeFor($address->province);
                $coupon = $this->cartService->appliedCoupon($request, $subtotal);
                $discount = $coupon?->discountFor($subtotal) ?? 0.0;

                $order = Order::create([
                    'user_id' => $user?->id,
                    'order_code' => $this->generateOrderCode(),
                    'address_id' => $address->id,
                    'total_amount' => $subtotal + $shippingFee - $discount,
                    'shipping_fee' => $shippingFee,
                    'coupon_code' => $coupon?->code,
                    'discount_amount' => $discount,
                    'status' => Order::STATUS_PENDING,
                    'payment_method' => $data['payment_method'],
                ]);

                foreach ($cart->items as $item) {
                    $variant = $variants->get($item->variant_id);

                    OrderItem::create([
                        'order_id' => $order->id,
                        'variant_id' => $variant->id,
                        'product_name_snapshot' => $variant->product->name,
                        'variant_label_snapshot' => $variant->label,
                        'price_snapshot' => $variant->price,
                        'quantity' => $item->quantity,
                    ]);

                    $variant->decrement('stock_quantity', $item->quantity);
                }

                $coupon?->increment('used_count');

                $cart->items()->delete();

                return $order;
            });
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }

        $this->cartService->removeCoupon($request);

        if ($data['payment_method'] === 'vnpay') {
            Payment::create([
                'order_id' => $order->id,
                'gateway' => 'vnpay',
                'amount' => $order->total_amount,
                'status' => Payment::STATUS_PENDING,
            ]);

            return redirect()->away($this->vnpay->buildPaymentUrl($order, $request->ip()));
        }

        if ($user) {
            return redirect()->route('orders.show', $order)->with('status', 'Đặt hàng thành công!');
        }

        // A guest has no account to view "my orders" on, so the signed
        // link is their way back to this confirmation — the signature is
        // what proves it's genuinely their order without a login.
        $signedUrl = URL::signedRoute('orders.guest-show', ['order' => $order]);

        return redirect($signedUrl)->with('status', 'Đặt hàng thành công!');
    }

    private function generateOrderCode(): string
    {
        do {
            $code = 'DH'.now()->format('ymd').Str::upper(Str::random(5));
        } while (Order::where('order_code', $code)->exists());

        return $code;
    }
}
