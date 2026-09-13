<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\ProductVariant;
use App\Models\User;
use App\Services\OrderService;
use App\Services\VnpayService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * One-step purchase from a product page: no cart, no visible registration —
 * a guest is signed into a lightweight account created behind the scenes
 * (keyed by phone number) so order history/detail pages keep working the
 * normal, authenticated way.
 */
class BuyNowController extends Controller
{
    public function __construct(
        private readonly OrderService $orderService,
        private readonly VnpayService $vnpay,
    ) {}

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'variant_id' => ['required', 'integer', 'exists:product_variants,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'recipient_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'address_line' => ['required', 'string', 'max:255'],
            'payment_method' => ['required', 'in:cod,vnpay'],
        ]);

        $variant = ProductVariant::findOrFail($data['variant_id']);

        $user = $request->user();

        if (! $user) {
            $user = $this->findOrCreateGuestUser($data['recipient_name'], $data['phone']);
            Auth::login($user);
        }

        $address = $user->addresses()->create([
            'recipient_name' => $data['recipient_name'],
            'phone' => $data['phone'],
            'address_line' => $data['address_line'],
            'is_default' => $user->addresses()->doesntExist(),
        ]);

        $lineItems = new Collection([[
            'variant_id' => $variant->id,
            'quantity' => $data['quantity'],
        ]]);

        try {
            $order = $this->orderService->placeOrder($user, $address, $data['payment_method'], $lineItems);
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors(), 'buyNow')->withInput();
        }

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

    /**
     * Reuse the same guest account on repeat "mua ngay" purchases from the
     * same phone number instead of creating a new one every time.
     */
    private function findOrCreateGuestUser(string $name, string $phone): User
    {
        $normalizedPhone = preg_replace('/\D+/', '', $phone);
        $email = "khach-{$normalizedPhone}@khach.phuonghihi.local";

        return User::firstOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'phone' => $phone,
                'password' => Hash::make(Str::random(40)),
            ]
        );
    }
}
