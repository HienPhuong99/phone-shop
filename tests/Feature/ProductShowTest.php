<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductSeries;
use App\Models\ProductVariant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductShowTest extends TestCase
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

    /**
     * @param  array<string, mixed>  $overrides
     */
    private function makeProduct(array $overrides = []): Product
    {
        $product = Product::create(array_merge([
            'category_id' => $this->category->id,
            'brand_id' => $this->brand->id,
            'series_id' => $this->series->id,
            'name' => 'iPhone Test',
            'slug' => 'iphone-test-'.uniqid(),
            'base_price' => 15000000,
            'status' => 'active',
        ], $overrides));

        ProductVariant::create([
            'product_id' => $product->id,
            'color' => 'Đen',
            'storage' => '128GB',
            'price' => $product->base_price,
            'sku' => 'SKU-'.uniqid(),
            'stock_quantity' => 10,
        ]);

        return $product;
    }

    public function test_breadcrumb_includes_a_link_to_the_products_series(): void
    {
        $product = $this->makeProduct(['name' => 'iPhone Breadcrumb', 'slug' => 'iphone-breadcrumb']);

        $response = $this->get(route('products.show', $product));

        $response->assertOk();
        $response->assertSee(route('products.index', ['series' => $this->series->slug]), false);
        $response->assertSeeInOrder(['Sản phẩm', 'iPhone 15 Series', 'iPhone Breadcrumb']);
    }

    public function test_shows_discount_and_savings_when_compare_price_is_set(): void
    {
        $product = $this->makeProduct([
            'name' => 'iPhone Giảm Giá', 'slug' => 'iphone-giam-gia',
            'base_price' => 18000000, 'compare_at_price' => 20000000,
        ]);

        $response = $this->get(route('products.show', $product));

        $response->assertOk();
        // The Alpine compareAtPrice binding carries the raw compare price
        // through to the client; savings/strikethrough render from it.
        $response->assertSee('compareAtPrice: 20000000', false);
    }

    public function test_does_not_render_compare_price_when_not_set(): void
    {
        $product = $this->makeProduct([
            'name' => 'iPhone Giá Đủ', 'slug' => 'iphone-gia-du',
            'base_price' => 15000000, 'compare_at_price' => null,
        ]);

        $response = $this->get(route('products.show', $product));

        $response->assertOk();
        $response->assertSee('compareAtPrice: null', false);
    }

    public function test_shows_related_products_from_the_same_series_excluding_self_and_other_series(): void
    {
        $product = $this->makeProduct(['name' => 'iPhone Chính', 'slug' => 'iphone-chinh']);
        $this->makeProduct(['name' => 'iPhone Anh Em', 'slug' => 'iphone-anh-em']);

        $otherSeries = ProductSeries::create(['name' => 'iPhone 14 Series', 'slug' => 'iphone-14-series']);
        $this->makeProduct(['name' => 'iPhone Khác Dòng', 'slug' => 'iphone-khac-dong', 'series_id' => $otherSeries->id]);

        $response = $this->get(route('products.show', $product));

        $response->assertOk();
        $response->assertSee('Cùng dòng iPhone 15 Series');
        $response->assertSee('iPhone Anh Em');
        $response->assertDontSee('iPhone Khác Dòng');
    }

    public function test_shows_comparison_table_against_the_closest_priced_product(): void
    {
        $product = $this->makeProduct([
            'name' => 'iPhone Chính', 'slug' => 'iphone-chinh', 'base_price' => 15000000,
        ]);
        $product->update(['specifications' => ['Màn hình' => '6.1 inch']]);

        $close = $this->makeProduct([
            'name' => 'iPhone Gần Giá', 'slug' => 'iphone-gan-gia', 'base_price' => 15500000,
        ]);
        $close->update(['specifications' => ['Màn hình' => '6.7 inch']]);

        // Different series so it can't leak into the "Cùng dòng" section
        // and be mistaken for a comparison-table match.
        $otherSeries = ProductSeries::create(['name' => 'iPhone 12 Series', 'slug' => 'iphone-12-series']);
        $far = $this->makeProduct([
            'name' => 'iPhone Xa Giá', 'slug' => 'iphone-xa-gia', 'base_price' => 30000000, 'series_id' => $otherSeries->id,
        ]);
        $far->update(['specifications' => ['Màn hình' => '6.9 inch']]);

        $response = $this->get(route('products.show', $product));

        $response->assertOk();
        $response->assertSee('So sánh nhanh');
        $response->assertSee('iPhone Gần Giá');
        $response->assertDontSee('iPhone Xa Giá');
    }

    public function test_omits_comparison_table_when_no_shared_spec_labels_exist(): void
    {
        $product = $this->makeProduct(['name' => 'iPhone Chính', 'slug' => 'iphone-chinh']);
        $product->update(['specifications' => ['Màn hình' => '6.1 inch']]);

        $other = $this->makeProduct(['name' => 'iPhone Khác Thông Số', 'slug' => 'iphone-khac-thong-so']);
        $other->update(['specifications' => ['Camera' => '48MP']]);

        $response = $this->get(route('products.show', $product));

        $response->assertOk();
        $response->assertDontSee('So sánh nhanh trong tầm giá');
    }

    public function test_includes_meta_description_and_json_ld_product_schema(): void
    {
        $product = $this->makeProduct([
            'name' => 'iPhone SEO', 'slug' => 'iphone-seo', 'thumbnail' => '/images/products/iphone-seo.svg',
        ]);

        $response = $this->get(route('products.show', $product));

        $response->assertOk();
        $response->assertSee('<meta name="description"', false);
        $response->assertSee('<meta property="og:image"', false);
        $response->assertSee('application/ld+json', false);
        $response->assertSee('"@type":"Product"', false);
        $response->assertSee('"@type":"Offer"', false);
    }
}
