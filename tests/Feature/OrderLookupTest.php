<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductSeries;
use App\Models\ProductVariant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderLookupTest extends TestCase
{
    use RefreshDatabase;

    private Order $order;

    protected function setUp(): void
    {
        parent::setUp();

        $product = Product::create([
            'category_id' => Category::create(['name' => 'Điện thoại', 'slug' => 'dien-thoai'])->id,
            'brand_id' => Brand::create(['name' => 'Apple', 'slug' => 'apple'])->id,
            'series_id' => ProductSeries::create(['name' => 'iPhone 15 Series', 'slug' => 'iphone-15-series'])->id,
            'name' => 'iPhone Test', 'slug' => 'iphone-test', 'base_price' => 10000000, 'status' => 'active',
        ]);

        $variant = ProductVariant::create([
            'product_id' => $product->id, 'color' => 'Đen', 'storage' => '128GB',
            'price' => 10000000, 'sku' => 'SKU-TEST-0001', 'stock_quantity' => 5,
        ]);

        $address = Address::create([
            'recipient_name' => 'Khách Vãng Lai', 'phone' => '0911222333',
            'address_line' => '45 Nguyễn Trãi', 'province' => 'Thành phố Hồ Chí Minh',
        ]);

        $this->order = Order::create([
            'order_code' => 'DH999999TEST', 'address_id' => $address->id,
            'total_amount' => 10020000, 'shipping_fee' => 20000,
            'status' => Order::STATUS_PENDING, 'payment_method' => 'cod',
        ]);

        OrderItem::create([
            'order_id' => $this->order->id, 'variant_id' => $variant->id,
            'product_name_snapshot' => $product->name, 'variant_label_snapshot' => $variant->label,
            'price_snapshot' => $variant->price, 'quantity' => 1,
        ]);
    }

    public function test_correct_order_code_and_phone_shows_the_order(): void
    {
        $response = $this->post(route('orders.lookup.show'), [
            'order_code' => 'DH999999TEST',
            'phone' => '0911222333',
        ]);

        $response->assertOk();
        $response->assertSee('DH999999TEST');
        $response->assertSee('iPhone Test');
    }

    public function test_wrong_phone_does_not_reveal_the_order(): void
    {
        $response = $this->post(route('orders.lookup.show'), [
            'order_code' => 'DH999999TEST',
            'phone' => '0999999999',
        ]);

        $response->assertOk();
        $response->assertDontSee('iPhone Test');
        $response->assertSee('Không tìm thấy đơn hàng');
    }

    public function test_wrong_order_code_does_not_reveal_the_order(): void
    {
        $response = $this->post(route('orders.lookup.show'), [
            'order_code' => 'DH000000WRONG',
            'phone' => '0911222333',
        ]);

        $response->assertOk();
        $response->assertDontSee('iPhone Test');
    }

    public function test_order_code_and_phone_are_required(): void
    {
        $response = $this->post(route('orders.lookup.show'), []);

        $response->assertInvalid(['order_code', 'phone']);
    }
}
