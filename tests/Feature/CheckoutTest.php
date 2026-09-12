<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Cart;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private ProductVariant $variant;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::create([
            'name' => 'Buyer',
            'email' => 'buyer@example.com',
            'password' => 'password',
        ]);

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
    }

    private function addToCart(int $quantity = 1): Cart
    {
        $cart = Cart::create(['user_id' => $this->user->id]);
        $cart->items()->create(['variant_id' => $this->variant->id, 'quantity' => $quantity]);

        return $cart;
    }

    public function test_guest_is_redirected_to_login_when_visiting_checkout(): void
    {
        $this->addToCart();

        $response = $this->get('/thanh-toan');

        $response->assertRedirect('/login');
    }

    public function test_checkout_with_empty_cart_redirects_to_cart(): void
    {
        Cart::create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)->get('/thanh-toan');

        $response->assertRedirect(route('cart.index'));
    }

    public function test_cod_checkout_creates_order_and_decrements_stock(): void
    {
        $this->addToCart(2);

        $response = $this->actingAs($this->user)->post('/thanh-toan', [
            'recipient_name' => 'Buyer',
            'phone' => '0900000000',
            'address_line' => '123 Test Street',
            'payment_method' => 'cod',
        ]);

        $order = Order::first();

        $response->assertRedirect(route('orders.show', $order));
        $this->assertNotNull($order);
        $this->assertSame(Order::STATUS_PENDING, $order->status);
        $this->assertSame(1, $order->items()->count());
        $this->assertSame(2, $order->items()->first()->quantity);
        $this->assertSame(3, $this->variant->fresh()->stock_quantity);
        $this->assertSame(0, $this->user->carts()->first()->items()->count());

        // Snapshots must be captured, not live references.
        $this->assertSame('iPhone Test', $order->items()->first()->product_name_snapshot);
        $this->assertSame('Đen - 128GB', $order->items()->first()->variant_label_snapshot);
    }

    public function test_checkout_fails_when_stock_is_insufficient_and_does_not_touch_stock_or_create_order(): void
    {
        $this->addToCart(999);

        $response = $this->actingAs($this->user)->post('/thanh-toan', [
            'recipient_name' => 'Buyer',
            'phone' => '0900000000',
            'address_line' => '123 Test Street',
            'payment_method' => 'cod',
        ]);

        $response->assertSessionHasErrors('quantity');
        $this->assertSame(0, Order::count());
        $this->assertSame(5, $this->variant->fresh()->stock_quantity);
    }

    public function test_total_amount_is_computed_server_side_from_variant_price_not_client_input(): void
    {
        $this->addToCart(1);

        // A malicious client cannot inject total_amount or price overrides — the
        // checkout form never accepts them, so this simply proves the field is ignored.
        $this->actingAs($this->user)->post('/thanh-toan', [
            'recipient_name' => 'Buyer',
            'phone' => '0900000000',
            'address_line' => '123 Test Street',
            'payment_method' => 'cod',
            'total_amount' => 1,
        ]);

        $order = Order::first();

        $this->assertEquals(20030000, $order->total_amount);
    }
}
