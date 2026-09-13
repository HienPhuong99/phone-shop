<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductSeries;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BuyNowTest extends TestCase
{
    use RefreshDatabase;

    private ProductVariant $variant;

    protected function setUp(): void
    {
        parent::setUp();

        $product = Product::create([
            'category_id' => Category::create(['name' => 'Điện thoại', 'slug' => 'dien-thoai'])->id,
            'brand_id' => Brand::create(['name' => 'Apple', 'slug' => 'apple'])->id,
            'series_id' => ProductSeries::create(['name' => 'iPhone 15 Series', 'slug' => 'iphone-15-series'])->id,
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
    }

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'variant_id' => $this->variant->id,
            'quantity' => 1,
            'recipient_name' => 'Khách Vãng Lai',
            'phone' => '0900000000',
            'address_line' => '123 Test Street',
            'payment_method' => 'cod',
        ], $overrides);
    }

    public function test_guest_buy_now_creates_order_logs_the_guest_in_and_decrements_stock(): void
    {
        $response = $this->post('/mua-ngay', $this->payload(['quantity' => 2]));

        $order = Order::first();

        $this->assertNotNull($order);
        $response->assertRedirect(route('orders.show', $order));
        $this->assertAuthenticatedAs($order->user);
        $this->assertSame(Order::STATUS_PENDING, $order->status);
        $this->assertSame(1, $order->items()->count());
        $this->assertSame(2, $order->items()->first()->quantity);
        $this->assertSame(3, $this->variant->fresh()->stock_quantity);
        $this->assertSame('Khách Vãng Lai', $order->address->recipient_name);
    }

    public function test_repeat_purchases_from_the_same_phone_reuse_the_same_guest_account(): void
    {
        $this->post('/mua-ngay', $this->payload());
        $firstUserId = Order::first()->user_id;

        auth()->logout();
        $this->variant->update(['stock_quantity' => 5]);

        $this->post('/mua-ngay', $this->payload());
        $secondUserId = Order::latest('id')->first()->user_id;

        $this->assertSame($firstUserId, $secondUserId);
        $this->assertSame(1, User::count());
    }

    public function test_buy_now_fails_when_stock_is_insufficient_and_does_not_create_an_order(): void
    {
        $response = $this->post('/mua-ngay', $this->payload(['quantity' => 999]));

        $response->assertSessionHasErrors('quantity', null, 'buyNow');
        $this->assertSame(0, Order::count());
        $this->assertSame(5, $this->variant->fresh()->stock_quantity);
    }

    public function test_logged_in_user_can_use_buy_now_without_going_through_the_cart(): void
    {
        $user = User::create([
            'name' => 'Logged In Buyer',
            'email' => 'buyer@example.com',
            'password' => 'password',
        ]);

        $response = $this->actingAs($user)->post('/mua-ngay', $this->payload());

        $order = Order::first();

        $response->assertRedirect(route('orders.show', $order));
        $this->assertSame($user->id, $order->user_id);
        $this->assertSame(1, User::count());
    }
}
