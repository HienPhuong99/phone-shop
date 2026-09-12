<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductVariant;
use App\Services\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    private const SHIPPING_FEE = 30000;

    public function __construct(private readonly CartService $cartService) {}

    public function index(Request $request): View|RedirectResponse
    {
        $cart = $this->cartService->currentCart($request);
        $cart->load(['items.variant.product']);

        if ($cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng của bạn đang trống.');
        }

        $addresses = $request->user()->addresses()->orderByDesc('is_default')->get();
        $shippingFee = self::SHIPPING_FEE;

        return view('checkout.index', compact('cart', 'addresses', 'shippingFee'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'address_id' => ['nullable', 'integer', 'exists:addresses,id'],
            'recipient_name' => ['required_without:address_id', 'nullable', 'string', 'max:255'],
            'phone' => ['required_without:address_id', 'nullable', 'string', 'max:20'],
            'address_line' => ['required_without:address_id', 'nullable', 'string', 'max:255'],
            'payment_method' => ['required', 'in:cod'],
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

        try {
            $order = DB::transaction(function () use ($cart, $user, $address, $data) {
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

                $totalAmount = $cart->items->sum(fn ($item) => $item->quantity * $variants->get($item->variant_id)->price);

                $order = Order::create([
                    'user_id' => $user->id,
                    'order_code' => $this->generateOrderCode(),
                    'address_id' => $address->id,
                    'total_amount' => $totalAmount + self::SHIPPING_FEE,
                    'shipping_fee' => self::SHIPPING_FEE,
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

                $cart->items()->delete();

                return $order;
            });
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }

        return redirect()->route('orders.show', $order)->with('status', 'Đặt hàng thành công!');
    }

    private function generateOrderCode(): string
    {
        do {
            $code = 'DH'.now()->format('ymd').Str::upper(Str::random(5));
        } while (Order::where('order_code', $code)->exists());

        return $code;
    }
}
