<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductSeries;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomeTest extends TestCase
{
    use RefreshDatabase;

    private Category $category;

    private Brand $brand;

    private ProductSeries $series;

    protected function setUp(): void
    {
        parent::setUp();

        $this->category = Category::create(['name' => 'Điện thoại', 'slug' => 'dien-thoai']);
        $this->brand = Brand::create(['name' => 'Apple', 'slug' => 'apple']);
        $this->series = ProductSeries::create(['name' => 'iPhone 15 Series', 'slug' => 'iphone-15-series']);
    }

    public function test_home_shows_featured_product_with_tagline_in_hero_slider(): void
    {
        Product::create([
            'category_id' => $this->category->id,
            'brand_id' => $this->brand->id,
            'series_id' => $this->series->id,
            'name' => 'iPhone 17 Pro Max',
            'slug' => 'iphone-17-pro-max',
            'base_price' => 34990000,
            'status' => 'active',
            'is_featured' => true,
            'featured_tagline' => 'Camera vượt trội, hiệu năng đỉnh cao.',
        ]);

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('iPhone 17 Pro Max');
        $response->assertSee('Camera vượt trội, hiệu năng đỉnh cao.');
    }

    public function test_home_falls_back_to_placeholder_when_no_featured_products(): void
    {
        Product::create([
            'category_id' => $this->category->id,
            'brand_id' => $this->brand->id,
            'series_id' => $this->series->id,
            'name' => 'iPhone Thường',
            'slug' => 'iphone-thuong',
            'base_price' => 15000000,
            'status' => 'active',
            'is_featured' => false,
        ]);

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('Sản phẩm nổi bật');
    }

    public function test_home_hero_slider_excludes_inactive_products_even_if_featured(): void
    {
        Product::create([
            'category_id' => $this->category->id,
            'brand_id' => $this->brand->id,
            'series_id' => $this->series->id,
            'name' => 'iPhone Ngừng Bán',
            'slug' => 'iphone-ngung-ban',
            'base_price' => 15000000,
            'status' => 'inactive',
            'is_featured' => true,
        ]);

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertDontSee('iPhone Ngừng Bán');
    }
}
