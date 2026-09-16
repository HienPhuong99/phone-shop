<?php

namespace Tests\Feature\Admin;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductSeries;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $regularUser;

    private Category $category;

    private Brand $brand;

    private ProductSeries $series;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $this->admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@test.com',
            'password' => 'password',
            'is_admin' => true,
        ]);

        $this->regularUser = User::create([
            'name' => 'User',
            'email' => 'user@test.com',
            'password' => 'password',
        ]);

        $this->category = Category::create(['name' => 'Điện thoại', 'slug' => 'dien-thoai']);
        $this->brand = Brand::create(['name' => 'Apple', 'slug' => 'apple']);
        $this->series = ProductSeries::create(['name' => 'iPhone 15 Series', 'slug' => 'iphone-15-series']);
    }

    public function test_non_admin_cannot_access_admin_panel(): void
    {
        $response = $this->actingAs($this->regularUser)->get('/admin');

        $response->assertForbidden();
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/admin');

        $response->assertRedirect('/login');
    }

    public function test_admin_can_create_product_with_thumbnail_upload(): void
    {
        $file = UploadedFile::fake()->image('phone.jpg');

        $response = $this->actingAs($this->admin)->post('/admin/products', [
            'category_id' => $this->category->id,
            'brand_id' => $this->brand->id,
            'series_id' => $this->series->id,
            'name' => 'iPhone Test',
            'base_price' => 20000000,
            'status' => 'active',
            'thumbnail' => $file,
        ]);

        $product = Product::first();

        $response->assertRedirect(route('admin.products.edit', $product));
        $this->assertNotNull($product->thumbnail);
        Storage::disk('public')->assertExists('products/'.basename($product->thumbnail));
    }

    public function test_admin_can_add_variant_and_image_to_product(): void
    {
        $product = Product::create([
            'category_id' => $this->category->id,
            'brand_id' => $this->brand->id,
            'series_id' => $this->series->id,
            'name' => 'iPhone Test',
            'slug' => 'iphone-test',
            'base_price' => 20000000,
            'status' => 'active',
        ]);

        $this->actingAs($this->admin)->post("/admin/products/{$product->slug}/variants", [
            'color' => 'Đen',
            'storage' => '128GB',
            'price' => 20000000,
            'sku' => 'SKU-TEST-1',
            'stock_quantity' => 10,
        ])->assertRedirect();

        $this->assertSame(1, $product->variants()->count());

        $image = UploadedFile::fake()->image('gallery.jpg');

        $this->actingAs($this->admin)->post("/admin/products/{$product->slug}/images", [
            'image' => $image,
        ])->assertRedirect();

        $this->assertSame(1, $product->images()->count());
        Storage::disk('public')->assertExists('products/'.basename($product->images()->first()->url));
    }

    public function test_admin_can_toggle_product_status_to_hide_and_show_it(): void
    {
        $product = Product::create([
            'category_id' => $this->category->id,
            'brand_id' => $this->brand->id,
            'series_id' => $this->series->id,
            'name' => 'iPhone Test',
            'slug' => 'iphone-test',
            'base_price' => 20000000,
            'status' => 'active',
        ]);

        $this->actingAs($this->admin)
            ->patch(route('admin.products.toggle-status', $product))
            ->assertRedirect();

        $this->assertSame('inactive', $product->fresh()->status);

        $this->actingAs($this->admin)
            ->patch(route('admin.products.toggle-status', $product))
            ->assertRedirect();

        $this->assertSame('active', $product->fresh()->status);
    }

    public function test_admin_can_toggle_product_featured_flag_for_homepage_slider(): void
    {
        $product = Product::create([
            'category_id' => $this->category->id,
            'brand_id' => $this->brand->id,
            'series_id' => $this->series->id,
            'name' => 'iPhone Test',
            'slug' => 'iphone-test',
            'base_price' => 20000000,
            'status' => 'active',
        ]);

        $this->assertFalse($product->is_featured);

        $this->actingAs($this->admin)
            ->patch(route('admin.products.toggle-featured', $product))
            ->assertRedirect();

        $this->assertTrue($product->fresh()->is_featured);

        $this->actingAs($this->admin)
            ->patch(route('admin.products.toggle-featured', $product))
            ->assertRedirect();

        $this->assertFalse($product->fresh()->is_featured);
    }

    public function test_admin_can_set_featured_tagline_when_updating_product(): void
    {
        $product = Product::create([
            'category_id' => $this->category->id,
            'brand_id' => $this->brand->id,
            'series_id' => $this->series->id,
            'name' => 'iPhone Test',
            'slug' => 'iphone-test',
            'base_price' => 20000000,
            'status' => 'active',
        ]);

        $this->actingAs($this->admin)->patch(route('admin.products.update', $product), [
            'category_id' => $this->category->id,
            'brand_id' => $this->brand->id,
            'series_id' => $this->series->id,
            'name' => $product->name,
            'base_price' => $product->base_price,
            'status' => 'active',
            'is_featured' => '1',
            'featured_tagline' => 'Camera vượt trội, hiệu năng đỉnh cao.',
        ])->assertRedirect();

        $product->refresh();
        $this->assertTrue($product->is_featured);
        $this->assertSame('Camera vượt trội, hiệu năng đỉnh cao.', $product->featured_tagline);
    }

    public function test_updating_product_rejects_featured_tagline_over_160_characters(): void
    {
        $product = Product::create([
            'category_id' => $this->category->id,
            'brand_id' => $this->brand->id,
            'series_id' => $this->series->id,
            'name' => 'iPhone Test',
            'slug' => 'iphone-test',
            'base_price' => 20000000,
            'status' => 'active',
        ]);

        $this->actingAs($this->admin)->patch(route('admin.products.update', $product), [
            'category_id' => $this->category->id,
            'brand_id' => $this->brand->id,
            'series_id' => $this->series->id,
            'name' => $product->name,
            'base_price' => $product->base_price,
            'status' => 'active',
            'featured_tagline' => str_repeat('a', 161),
        ])->assertInvalid(['featured_tagline']);
    }

    public function test_admin_can_set_compare_at_price_to_show_a_discount(): void
    {
        $product = Product::create([
            'category_id' => $this->category->id,
            'brand_id' => $this->brand->id,
            'series_id' => $this->series->id,
            'name' => 'iPhone Test',
            'slug' => 'iphone-test',
            'base_price' => 20000000,
            'status' => 'active',
        ]);

        $this->actingAs($this->admin)->patch(route('admin.products.update', $product), [
            'category_id' => $this->category->id,
            'brand_id' => $this->brand->id,
            'series_id' => $this->series->id,
            'name' => $product->name,
            'base_price' => 18000000,
            'compare_at_price' => 20000000,
            'status' => 'active',
        ])->assertRedirect();

        $product->refresh();
        $this->assertSame('20000000.00', $product->compare_at_price);
        $this->assertSame(10, $product->discount_percent);
    }

    public function test_updating_product_rejects_compare_at_price_not_greater_than_base_price(): void
    {
        $product = Product::create([
            'category_id' => $this->category->id,
            'brand_id' => $this->brand->id,
            'series_id' => $this->series->id,
            'name' => 'iPhone Test',
            'slug' => 'iphone-test',
            'base_price' => 20000000,
            'status' => 'active',
        ]);

        $this->actingAs($this->admin)->patch(route('admin.products.update', $product), [
            'category_id' => $this->category->id,
            'brand_id' => $this->brand->id,
            'series_id' => $this->series->id,
            'name' => $product->name,
            'base_price' => 20000000,
            'compare_at_price' => 20000000,
            'status' => 'active',
        ])->assertInvalid(['compare_at_price']);
    }

    public function test_toggling_product_status_busts_category_and_series_nav_cache(): void
    {
        $product = Product::create([
            'category_id' => $this->category->id,
            'brand_id' => $this->brand->id,
            'series_id' => $this->series->id,
            'name' => 'iPhone Test',
            'slug' => 'iphone-test',
            'base_price' => 20000000,
            'status' => 'active',
        ]);

        // Prime both caches while the product is still active.
        $this->assertTrue(Category::navList()->contains('id', $this->category->id));
        $this->assertTrue(ProductSeries::navList()->contains('id', $this->series->id));

        $this->actingAs($this->admin)->patch(route('admin.products.toggle-status', $product))->assertRedirect();

        // Hiding the only product in this category/series should drop both
        // from the nav immediately, not after the cache's 1-hour TTL.
        $this->assertFalse(Category::navList()->contains('id', $this->category->id));
        $this->assertFalse(ProductSeries::navList()->contains('id', $this->series->id));
    }

    public function test_deleting_category_with_products_is_blocked(): void
    {
        Product::create([
            'category_id' => $this->category->id,
            'brand_id' => $this->brand->id,
            'series_id' => $this->series->id,
            'name' => 'iPhone Test',
            'slug' => 'iphone-test',
            'base_price' => 20000000,
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->admin)->delete("/admin/categories/{$this->category->id}");

        $response->assertRedirect();
        $this->assertNotNull($this->category->fresh());
    }
}
