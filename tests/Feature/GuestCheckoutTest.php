<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Cart;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductSeries;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Tests\TestCase;

class GuestCheckoutTest extends TestCase
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
            'name' => 'iPhone Test', 'slug' => 'iphone-test', 'base_price' => 15000000, 'status' => 'active',
        ]);

        $this->variant = ProductVariant::create([
            'product_id' => $product->id, 'color' => 'Đen', 'storage' => '128GB',
            'price' => 15000000, 'sku' => 'SKU-TEST-0001', 'stock_quantity' => 5,
        ]);
    }

    /**
     * Simulates the guest cart a real browser session would already have —
     * CartController@store builds this the same way via CartService.
     */
    private function guestAddToCart(int $quantity = 1): void
    {
        $sessionId = (string) Str::uuid();
        $cart = Cart::create(['session_id' => $sessionId]);
        $cart->items()->create(['variant_id' => $this->variant->id, 'quantity' => $quantity]);
        $this->withSession(['cart_session_id' => $sessionId]);
    }

    public function test_guest_can_complete_a_cod_order_without_an_account(): void
    {
        $this->guestAddToCart();

        $response = $this->post('/thanh-toan', [
            'recipient_name' => 'Khách Vãng Lai',
            'phone' => '0911222333',
            'address_line' => '45 Nguyễn Trãi',
            'province' => 'Thành phố Hồ Chí Minh',
            'payment_method' => 'cod',
        ]);

        $order = Order::first();

        $this->assertNotNull($order);
        $this->assertNull($order->user_id);
        $this->assertSame(Order::STATUS_PENDING, $order->status);
        // Redirected to the signed one-time confirmation link, not the
        // auth-only "my orders" page a guest could never reach.
        $response->assertRedirectToSignedRoute('orders.guest-show', ['order' => $order->id]);
    }

    public function test_guest_cannot_reuse_a_saved_address_id(): void
    {
        $owner = User::create(['name' => 'Owner', 'email' => 'owner@example.com', 'password' => 'password']);
        $address = $owner->addresses()->create([
            'recipient_name' => 'Owner', 'phone' => '0900000000', 'address_line' => 'X', 'province' => 'Thành phố Hà Nội',
        ]);

        $this->guestAddToCart();

        $response = $this->post('/thanh-toan', [
            'address_id' => $address->id,
            'payment_method' => 'cod',
        ]);

        $response->assertForbidden();
    }

    public function test_guest_order_confirmation_requires_a_valid_signature(): void
    {
        $this->guestAddToCart();

        $this->post('/thanh-toan', [
            'recipient_name' => 'Khách Vãng Lai',
            'phone' => '0911222333',
            'address_line' => '45 Nguyễn Trãi',
            'province' => 'Thành phố Hồ Chí Minh',
            'payment_method' => 'cod',
        ]);

        $order = Order::first();

        // A guessed URL without the signature query param is rejected.
        $response = $this->get(route('orders.guest-show', ['order' => $order->id]));

        $response->assertForbidden();
    }

    public function test_guest_order_confirmation_shows_the_order_with_a_valid_signature(): void
    {
        $this->guestAddToCart();

        $this->post('/thanh-toan', [
            'recipient_name' => 'Khách Vãng Lai',
            'phone' => '0911222333',
            'address_line' => '45 Nguyễn Trãi',
            'province' => 'Thành phố Hồ Chí Minh',
            'payment_method' => 'cod',
        ]);

        $order = Order::first();
        $signedUrl = URL::signedRoute('orders.guest-show', ['order' => $order->id]);

        $response = $this->get($signedUrl);

        $response->assertOk();
        $response->assertSee($order->order_code);
    }

    public function test_a_logged_in_user_still_gets_the_normal_order_confirmation(): void
    {
        $user = User::create(['name' => 'Buyer', 'email' => 'buyer@example.com', 'password' => 'password']);
        $cart = Cart::create(['user_id' => $user->id]);
        $cart->items()->create(['variant_id' => $this->variant->id, 'quantity' => 1]);

        $response = $this->actingAs($user)->post('/thanh-toan', [
            'recipient_name' => 'Buyer',
            'phone' => '0900000000',
            'address_line' => '123 Test Street',
            'province' => 'Thành phố Hà Nội',
            'payment_method' => 'cod',
        ]);

        $order = Order::first();

        $this->assertSame($user->id, $order->user_id);
        $response->assertRedirect(route('orders.show', $order));
    }
}
