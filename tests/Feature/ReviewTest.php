<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductSeries;
use App\Models\ProductVariant;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Product $product;

    private ProductVariant $variant;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::create([
            'name' => 'Buyer', 'email' => 'buyer@example.com', 'password' => 'password',
        ]);

        $this->product = Product::create([
            'category_id' => Category::create(['name' => 'Điện thoại', 'slug' => 'dien-thoai'])->id,
            'brand_id' => Brand::create(['name' => 'Apple', 'slug' => 'apple'])->id,
            'series_id' => ProductSeries::create(['name' => 'iPhone 15 Series', 'slug' => 'iphone-15-series'])->id,
            'name' => 'iPhone Test', 'slug' => 'iphone-test', 'base_price' => 20000000, 'status' => 'active',
        ]);

        $this->variant = ProductVariant::create([
            'product_id' => $this->product->id, 'color' => 'Đen', 'storage' => '128GB',
            'price' => 20000000, 'sku' => 'SKU-TEST-0001', 'stock_quantity' => 5,
        ]);
    }

    private function createOrder(string $status): Order
    {
        $order = Order::create([
            'user_id' => $this->user->id,
            'order_code' => 'ORD-'.uniqid(),
            'total_amount' => $this->variant->price,
            'payment_method' => 'cod',
            'status' => $status,
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'variant_id' => $this->variant->id,
            'product_name_snapshot' => $this->product->name,
            'variant_label_snapshot' => $this->variant->label,
            'price_snapshot' => $this->variant->price,
            'quantity' => 1,
        ]);

        return $order;
    }

    public function test_guest_cannot_submit_a_review(): void
    {
        $response = $this->post(route('reviews.store', $this->product), ['rating' => 5]);

        $response->assertRedirect('/login');
    }

    public function test_user_without_a_completed_order_cannot_review(): void
    {
        $this->createOrder(Order::STATUS_PENDING);

        $response = $this->actingAs($this->user)->post(route('reviews.store', $this->product), [
            'rating' => 5, 'content' => 'Tốt lắm',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseCount('reviews', 0);
    }

    public function test_user_with_a_completed_order_can_review(): void
    {
        $this->createOrder(Order::STATUS_COMPLETED);

        $response = $this->actingAs($this->user)->post(route('reviews.store', $this->product), [
            'rating' => 4, 'content' => 'Máy đẹp, giao nhanh.',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('reviews', [
            'product_id' => $this->product->id,
            'user_id' => $this->user->id,
            'rating' => 4,
            'content' => 'Máy đẹp, giao nhanh.',
        ]);
    }

    public function test_user_cannot_review_the_same_product_twice(): void
    {
        $this->createOrder(Order::STATUS_COMPLETED);
        Review::create([
            'product_id' => $this->product->id, 'user_id' => $this->user->id, 'rating' => 5,
        ]);

        $response = $this->actingAs($this->user)->post(route('reviews.store', $this->product), [
            'rating' => 3,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseCount('reviews', 1);
        $this->assertDatabaseHas('reviews', ['rating' => 5]);
    }

    public function test_rating_must_be_between_one_and_five(): void
    {
        $this->createOrder(Order::STATUS_COMPLETED);

        $response = $this->actingAs($this->user)->post(route('reviews.store', $this->product), [
            'rating' => 6,
        ]);

        $response->assertInvalid(['rating']);
    }

    public function test_product_page_shows_average_rating_and_distribution(): void
    {
        $this->createOrder(Order::STATUS_COMPLETED);
        Review::create(['product_id' => $this->product->id, 'user_id' => $this->user->id, 'rating' => 4, 'content' => 'Tốt']);

        $otherUser = User::create(['name' => 'Reviewer 2', 'email' => 'r2@example.com', 'password' => 'password']);
        Review::create(['product_id' => $this->product->id, 'user_id' => $otherUser->id, 'rating' => 2, 'content' => 'Tạm']);

        $response = $this->get(route('products.show', $this->product));

        $response->assertOk();
        $response->assertSee('3'); // average of 4 and 2
        $response->assertSee('2 đánh giá');
        $response->assertSee('Tốt');
        $response->assertSee('Tạm');
    }
}
