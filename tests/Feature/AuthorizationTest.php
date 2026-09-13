<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductSeries;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Guards against IDOR (insecure direct object reference): a user must never
 * be able to read or modify another user's cart items or orders just by
 * guessing/incrementing an id in the URL.
 */
class AuthorizationTest extends TestCase
{
    use RefreshDatabase;

    private User $owner;

    private User $attacker;

    private ProductVariant $variant;

    protected function setUp(): void
    {
        parent::setUp();

        $this->owner = User::create(['name' => 'Owner', 'email' => 'owner@example.com', 'password' => 'password']);
        $this->attacker = User::create(['name' => 'Attacker', 'email' => 'attacker@example.com', 'password' => 'password']);

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
            'stock_quantity' => 10,
        ]);
    }

    public function test_user_cannot_update_another_users_cart_item(): void
    {
        $ownerCart = Cart::create(['user_id' => $this->owner->id]);
        $item = $ownerCart->items()->create(['variant_id' => $this->variant->id, 'quantity' => 1]);

        $response = $this->actingAs($this->attacker)->patch("/gio-hang/{$item->id}", ['quantity' => 5]);

        $response->assertForbidden();
        $this->assertSame(1, $item->fresh()->quantity);
    }

    public function test_user_cannot_delete_another_users_cart_item(): void
    {
        $ownerCart = Cart::create(['user_id' => $this->owner->id]);
        $item = $ownerCart->items()->create(['variant_id' => $this->variant->id, 'quantity' => 1]);

        $response = $this->actingAs($this->attacker)->delete("/gio-hang/{$item->id}");

        $response->assertForbidden();
        $this->assertNotNull(CartItem::find($item->id));
    }

    public function test_user_cannot_view_another_users_order(): void
    {
        $order = Order::create([
            'user_id' => $this->owner->id,
            'order_code' => 'DHOWNER01',
            'total_amount' => 20030000,
            'shipping_fee' => 30000,
            'status' => Order::STATUS_PENDING,
            'payment_method' => 'cod',
        ]);

        $response = $this->actingAs($this->attacker)->get("/don-hang/{$order->id}");

        $response->assertForbidden();
    }

    public function test_user_only_sees_their_own_orders_in_index(): void
    {
        Order::create([
            'user_id' => $this->owner->id,
            'order_code' => 'DHOWNER01',
            'total_amount' => 20030000,
            'shipping_fee' => 30000,
            'status' => Order::STATUS_PENDING,
            'payment_method' => 'cod',
        ]);

        $response = $this->actingAs($this->attacker)->get('/don-hang');

        $response->assertOk();
        $response->assertDontSee('DHOWNER01');
    }
}
