<?php

namespace App\Services;

use App\Models\Address;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class OrderService
{
    public const SHIPPING_FEE = 30000;

    /**
     * Create an order from a flat list of line items, re-checking stock and
     * locking each variant for update inside the transaction. Shared by the
     * cart checkout and the single-product "buy now" flow so both stay
     * race-condition-safe the same way.
     *
     * @param  Collection<int, array{variant_id: int, quantity: int}>  $lineItems
     *
     * @throws ValidationException
     */
    public function placeOrder(User $user, Address $address, string $paymentMethod, Collection $lineItems): Order
    {
        return DB::transaction(function () use ($user, $address, $paymentMethod, $lineItems) {
            $variantIds = $lineItems->pluck('variant_id');

            $variants = ProductVariant::whereIn('id', $variantIds)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            foreach ($lineItems as $line) {
                $variant = $variants->get($line['variant_id']);

                if (! $variant || $variant->stock_quantity < $line['quantity']) {
                    throw ValidationException::withMessages([
                        'quantity' => $variant
                            ? "Sản phẩm \"{$variant->product->name} ({$variant->label})\" không đủ hàng."
                            : 'Sản phẩm không tồn tại.',
                    ]);
                }
            }

            $totalAmount = $lineItems->sum(fn ($line) => $line['quantity'] * $variants->get($line['variant_id'])->price);

            $order = Order::create([
                'user_id' => $user->id,
                'order_code' => $this->generateOrderCode(),
                'address_id' => $address->id,
                'total_amount' => $totalAmount + self::SHIPPING_FEE,
                'shipping_fee' => self::SHIPPING_FEE,
                'status' => Order::STATUS_PENDING,
                'payment_method' => $paymentMethod,
            ]);

            foreach ($lineItems as $line) {
                $variant = $variants->get($line['variant_id']);

                OrderItem::create([
                    'order_id' => $order->id,
                    'variant_id' => $variant->id,
                    'product_name_snapshot' => $variant->product->name,
                    'variant_label_snapshot' => $variant->label,
                    'price_snapshot' => $variant->price,
                    'quantity' => $line['quantity'],
                ]);

                $variant->decrement('stock_quantity', $line['quantity']);
            }

            return $order;
        });
    }

    private function generateOrderCode(): string
    {
        do {
            $code = 'DH'.now()->format('ymd').Str::upper(Str::random(5));
        } while (Order::where('order_code', $code)->exists());

        return $code;
    }
}
