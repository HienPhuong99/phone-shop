<?php

namespace Tests\Feature\Admin;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductSeries;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductSeriesManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $regularUser;

    protected function setUp(): void
    {
        parent::setUp();

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
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/admin/product-series');

        $response->assertRedirect('/login');
    }

    public function test_non_admin_cannot_access_product_series(): void
    {
        $response = $this->actingAs($this->regularUser)->get('/admin/product-series');

        $response->assertForbidden();
    }

    public function test_admin_can_view_product_series_index(): void
    {
        ProductSeries::create([
            'name' => 'iPhone 15 Series',
            'slug' => 'iphone-15-series',
            'sort_order' => 1,
        ]);

        $response = $this->actingAs($this->admin)->get('/admin/product-series');

        $response->assertOk();
        $response->assertSee('iPhone 15 Series');
    }

    public function test_admin_can_create_product_series(): void
    {
        $response = $this->actingAs($this->admin)->post('/admin/product-series', [
            'name' => 'iPhone 16 Series',
            'description' => 'Thế hệ iPhone mới nhất',
            'sort_order' => '',
        ]);

        $response->assertRedirect(route('admin.product-series.index'));
        $response->assertSessionHas('status', 'Đã tạo dòng sản phẩm.');

        $series = ProductSeries::where('name', 'iPhone 16 Series')->first();
        $this->assertNotNull($series);
        $this->assertSame('iphone-16-series', $series->slug);
        $this->assertSame('Thế hệ iPhone mới nhất', $series->description);
        $this->assertSame(1, $series->sort_order);
    }

    public function test_admin_can_update_product_series(): void
    {
        $series = ProductSeries::create([
            'name' => 'iPhone 14 Series',
            'slug' => 'iphone-14-series',
            'sort_order' => 5,
        ]);

        $response = $this->actingAs($this->admin)->patch("/admin/product-series/{$series->id}", [
            'name' => 'iPhone 14 Series Pro',
            'description' => 'Cập nhật mô tả',
            'sort_order' => 10,
        ]);

        $response->assertRedirect(route('admin.product-series.index'));
        $response->assertSessionHas('status', 'Đã cập nhật dòng sản phẩm.');

        $series->refresh();
        $this->assertSame('iPhone 14 Series Pro', $series->name);
        $this->assertSame('iphone-14-series-pro', $series->slug);
        $this->assertSame(10, $series->sort_order);
    }

    public function test_admin_can_delete_unused_product_series(): void
    {
        $series = ProductSeries::create([
            'name' => 'iPhone 13 Series',
            'slug' => 'iphone-13-series',
        ]);

        $response = $this->actingAs($this->admin)->delete("/admin/product-series/{$series->id}");

        $response->assertRedirect();
        $response->assertSessionHas('status', 'Đã xoá dòng sản phẩm.');
        $this->assertDatabaseMissing('product_series', ['id' => $series->id]);
    }

    public function test_deleting_product_series_with_products_is_blocked(): void
    {
        $category = Category::create(['name' => 'Điện thoại', 'slug' => 'dien-thoai']);
        $brand = Brand::create(['name' => 'Apple', 'slug' => 'apple']);
        $series = ProductSeries::create([
            'name' => 'iPhone 15 Series',
            'slug' => 'iphone-15-series',
        ]);

        Product::create([
            'category_id' => $category->id,
            'brand_id' => $brand->id,
            'series_id' => $series->id,
            'name' => 'iPhone 15 Pro Max',
            'slug' => 'iphone-15-pro-max',
            'base_price' => 30000000,
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->admin)->delete("/admin/product-series/{$series->id}");

        $response->assertRedirect();
        $response->assertSessionHas('error', 'Không thể xoá dòng sản phẩm vì vẫn còn sản phẩm thuộc dòng này.');
        $this->assertDatabaseHas('product_series', ['id' => $series->id]);
    }
}
