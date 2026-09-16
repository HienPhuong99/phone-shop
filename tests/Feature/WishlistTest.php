<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductSeries;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WishlistTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Product $product;

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
    }

    public function test_guest_cannot_manage_wishlist(): void
    {
        $this->get(route('wishlist.index'))->assertRedirect('/login');
        $this->post(route('wishlist.store', $this->product))->assertRedirect('/login');
    }

    public function test_user_can_add_a_product_to_wishlist(): void
    {
        $response = $this->actingAs($this->user)->post(route('wishlist.store', $this->product));

        $response->assertRedirect();
        $this->assertDatabaseHas('wishlists', [
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
        ]);
    }

    public function test_adding_the_same_product_twice_does_not_duplicate(): void
    {
        $this->actingAs($this->user)->post(route('wishlist.store', $this->product));
        $this->actingAs($this->user)->post(route('wishlist.store', $this->product));

        $this->assertDatabaseCount('wishlists', 1);
    }

    public function test_user_can_remove_a_product_from_wishlist(): void
    {
        $this->user->wishlists()->create(['product_id' => $this->product->id]);

        $response = $this->actingAs($this->user)->delete(route('wishlist.destroy', $this->product));

        $response->assertRedirect();
        $this->assertDatabaseCount('wishlists', 0);
    }

    public function test_wishlist_index_only_shows_the_current_users_products(): void
    {
        $this->user->wishlists()->create(['product_id' => $this->product->id]);

        $otherUser = User::create(['name' => 'Other', 'email' => 'other@example.com', 'password' => 'password']);
        $otherProduct = Product::create([
            'category_id' => $this->product->category_id,
            'brand_id' => $this->product->brand_id,
            'series_id' => $this->product->series_id,
            'name' => 'iPhone Của Người Khác', 'slug' => 'iphone-cua-nguoi-khac', 'base_price' => 15000000, 'status' => 'active',
        ]);
        $otherUser->wishlists()->create(['product_id' => $otherProduct->id]);

        $response = $this->actingAs($this->user)->get(route('wishlist.index'));

        $response->assertOk();
        $response->assertSee('iPhone Test');
        $response->assertDontSee('iPhone Của Người Khác');
    }
}
