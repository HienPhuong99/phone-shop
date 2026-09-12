<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class VnpayIpnTest extends TestCase
{
    use RefreshDatabase;

    private ProductVariant $variant;

    private Order $order;

    protected function setUp(): void
    {
        parent::setUp();

        Config::set('vnpay.hash_secret', 'TESTSECRET123');

        $product = Product::create([
            'category_id' => Category::create(['name' => 'Điện thoại', 'slug' => 'dien-thoai'])->id,
            'brand_id' => Brand::create(['name' => 'Apple', 'slug' => 'apple'])->id,
            'name' => 'iPhone Test',
            'slug' => 'iphone-test',
            'base_price' => 20000000,
            'status' => 'active',
        ]);

        $this->variant = ProductVariant::create([
            'product_id' => $product->id,
            'color' => 'Đen',
            'storage' => '128GB',
            'price' => 20000000,
            'sku' => 'SKU-TEST-0001',
            'stock_quantity' => 5,
        ]);

        $user = User::create([
            'name' => 'Buyer',
            'email' => 'buyer@example.com',
            'password' => 'password',
        ]);

        $this->order = Order::create([
            'user_id' => $user->id,
            'order_code' => 'DHTEST0001',
            'total_amount' => 20030000,
            'shipping_fee' => 30000,
            'status' => Order::STATUS_PENDING,
            'payment_method' => 'vnpay',
        ]);

        OrderItem::create([
            'order_id' => $this->order->id,
            'variant_id' => $this->variant->id,
            'product_name_snapshot' => 'iPhone Test',
            'variant_label_snapshot' => 'Đen - 128GB',
            'price_snapshot' => 20000000,
            'quantity' => 1,
        ]);

        // Stock was already decremented at order-creation time, mirroring the real checkout flow.
        $this->variant->decrement('stock_quantity', 1);

        Payment::create([
            'order_id' => $this->order->id,
            'gateway' => 'vnpay',
            'amount' => $this->order->total_amount,
            'status' => Payment::STATUS_PENDING,
        ]);
    }

    private function signedIpnParams(array $overrides = []): array
    {
        $params = array_merge([
            'vnp_TxnRef' => $this->order->order_code,
            'vnp_Amount' => (int) round($this->order->total_amount * 100),
            'vnp_ResponseCode' => '00',
            'vnp_TransactionNo' => '14000001',
            'vnp_TransactionStatus' => '00',
        ], $overrides);

        ksort($params);
        $hashData = http_build_query($params, '', '&', PHP_QUERY_RFC3986);
        $params['vnp_SecureHash'] = hash_hmac('sha512', $hashData, 'TESTSECRET123');

        return $params;
    }

    public function test_ipn_confirms_successful_payment_and_marks_order_paid(): void
    {
        $response = $this->get('/vnpay/ipn?'.http_build_query($this->signedIpnParams()));

        $response->assertJson(['RspCode' => '00']);
        $this->assertSame(Order::STATUS_PAID, $this->order->fresh()->status);
        $this->assertSame(Payment::STATUS_SUCCESS, $this->order->payment->fresh()->status);
        $this->assertSame(4, $this->variant->fresh()->stock_quantity);
    }

    public function test_ipn_rejects_tampered_signature(): void
    {
        $params = $this->signedIpnParams();
        $params['vnp_Amount'] = 1;

        $response = $this->get('/vnpay/ipn?'.http_build_query($params));

        $response->assertJson(['RspCode' => '97']);
        $this->assertSame(Order::STATUS_PENDING, $this->order->fresh()->status);
    }

    public function test_ipn_is_idempotent_and_does_not_reprocess_a_confirmed_order(): void
    {
        $params = $this->signedIpnParams();

        $first = $this->get('/vnpay/ipn?'.http_build_query($params));
        $first->assertJson(['RspCode' => '00']);

        $second = $this->get('/vnpay/ipn?'.http_build_query($params));
        $second->assertJson(['RspCode' => '02']);

        // Stock must only ever be touched once, not once per duplicate IPN call.
        $this->assertSame(4, $this->variant->fresh()->stock_quantity);
    }

    public function test_failed_payment_restores_stock_and_cancels_order(): void
    {
        $params = $this->signedIpnParams([
            'vnp_ResponseCode' => '24',
            'vnp_TransactionStatus' => '02',
        ]);

        $response = $this->get('/vnpay/ipn?'.http_build_query($params));

        $response->assertJson(['RspCode' => '00']);
        $this->assertSame(Order::STATUS_CANCELLED, $this->order->fresh()->status);
        $this->assertSame(Payment::STATUS_FAILED, $this->order->payment->fresh()->status);
        $this->assertSame(5, $this->variant->fresh()->stock_quantity);
    }

    public function test_ipn_rejects_unknown_order(): void
    {
        $params = $this->signedIpnParams(['vnp_TxnRef' => 'DOES-NOT-EXIST']);

        $response = $this->get('/vnpay/ipn?'.http_build_query($params));

        $response->assertJson(['RspCode' => '01']);
    }
}
