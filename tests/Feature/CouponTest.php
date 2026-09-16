<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Cart;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductSeries;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CouponTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private ProductVariant $variant;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::create(['name' => 'Buyer', 'email' => 'buyer@example.com', 'password' => 'password']);

        $product = Product::create([
            'category_id' => Category::create(['name' => 'Điện thoại', 'slug' => 'dien-thoai'])->id,
            'brand_id' => Brand::create(['name' => 'Apple', 'slug' => 'apple'])->id,
            'series_id' => ProductSeries::create(['name' => 'iPhone 15 Series', 'slug' => 'iphone-15-series'])->id,
            'name' => 'iPhone Test', 'slug' => 'iphone-test', 'base_price' => 10000000, 'status' => 'active',
        ]);

        $this->variant = ProductVariant::create([
            'product_id' => $product->id, 'color' => 'Đen', 'storage' => '128GB',
            'price' => 10000000, 'sku' => 'SKU-TEST-0001', 'stock_quantity' => 5,
        ]);
    }

    private function addToCart(int $quantity = 1): Cart
    {
        $cart = Cart::create(['user_id' => $this->user->id]);
        $cart->items()->create(['variant_id' => $this->variant->id, 'quantity' => $quantity]);

        return $cart;
    }

    public function test_applying_a_valid_percent_coupon_shows_the_discount_on_the_cart_page(): void
    {
        Coupon::create(['code' => 'GIAM10', 'type' => 'percent', 'value' => 10, 'active' => true]);
        $this->addToCart();

        $this->actingAs($this->user)->post(route('cart.coupon.apply'), ['code' => 'giam10']);

        $response = $this->actingAs($this->user)->get(route('cart.index'));

        $response->assertOk();
        $response->assertSee('GIAM10');
        $response->assertSee('1.000.000'); // 10% of 10.000.000đ
    }

    public function test_an_invalid_coupon_code_is_rejected(): void
    {
        $this->addToCart();

        $response = $this->actingAs($this->user)->post(route('cart.coupon.apply'), ['code' => 'KHONGTONTAI']);

        $response->assertSessionHas('error');
        $response = $this->actingAs($this->user)->get(route('cart.index'));
        $response->assertDontSee('KHONGTONTAI');
    }

    public function test_an_expired_coupon_is_rejected(): void
    {
        Coupon::create([
            'code' => 'HETHAN', 'type' => 'fixed', 'value' => 100000, 'active' => true,
            'expires_at' => now()->subDay(),
        ]);
        $this->addToCart();

        $this->actingAs($this->user)->post(route('cart.coupon.apply'), ['code' => 'HETHAN']);

        $response = $this->actingAs($this->user)->get(route('cart.index'));
        $response->assertDontSee('HETHAN');
    }

    public function test_a_coupon_below_its_minimum_order_amount_is_rejected(): void
    {
        Coupon::create([
            'code' => 'DON20TR', 'type' => 'fixed', 'value' => 100000, 'active' => true,
            'min_order_amount' => 20000000,
        ]);
        $this->addToCart(1); // cart subtotal is 10.000.000đ, below the 20tr minimum

        $this->actingAs($this->user)->post(route('cart.coupon.apply'), ['code' => 'DON20TR']);

        $response = $this->actingAs($this->user)->get(route('cart.index'));
        $response->assertDontSee('DON20TR');
    }

    public function test_a_coupon_past_its_usage_limit_is_rejected(): void
    {
        Coupon::create([
            'code' => 'GIOIHAN', 'type' => 'fixed', 'value' => 100000, 'active' => true,
            'max_uses' => 1, 'used_count' => 1,
        ]);
        $this->addToCart();

        $this->actingAs($this->user)->post(route('cart.coupon.apply'), ['code' => 'GIOIHAN']);

        $response = $this->actingAs($this->user)->get(route('cart.index'));
        $response->assertDontSee('GIOIHAN');
    }

    public function test_removing_a_coupon_clears_it_from_the_cart(): void
    {
        Coupon::create(['code' => 'GIAM10', 'type' => 'percent', 'value' => 10, 'active' => true]);
        $this->addToCart();
        $this->actingAs($this->user)->post(route('cart.coupon.apply'), ['code' => 'GIAM10']);

        $this->actingAs($this->user)->delete(route('cart.coupon.remove'));

        $response = $this->actingAs($this->user)->get(route('cart.index'));
        $response->assertDontSee('GIAM10 đã áp dụng', false);
    }

    public function test_checkout_persists_the_discount_and_increments_the_coupon_usage(): void
    {
        $coupon = Coupon::create(['code' => 'GIAM10', 'type' => 'percent', 'value' => 10, 'active' => true]);
        $this->addToCart();
        $this->actingAs($this->user)->post(route('cart.coupon.apply'), ['code' => 'GIAM10']);

        $this->actingAs($this->user)->post('/thanh-toan', [
            'recipient_name' => 'Buyer',
            'phone' => '0900000000',
            'address_line' => '123 Test Street',
            'province' => 'Thành phố Hà Nội',
            'payment_method' => 'cod',
        ]);

        $order = Order::first();

        $this->assertSame('GIAM10', $order->coupon_code);
        $this->assertEquals(1000000, $order->discount_amount); // 10% of 10.000.000đ
        // 10.000.000 - 1.000.000 discount + 20.000 Hà Nội shipping.
        $this->assertEquals(9020000, $order->total_amount);
        $this->assertSame(1, $coupon->fresh()->used_count);
    }
}
