<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Post;
use App\Models\Product;
use App\Models\ProductSeries;
use App\Models\ProductVariant;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeoTest extends TestCase
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

    public function test_sitemap_lists_active_products_and_published_posts_only(): void
    {
        $this->product('iPhone 15', 'iphone-15');
        $this->product('iPhone cũ đã ẩn', 'iphone-da-an', status: 'inactive');
        Post::factory()->create(['slug' => 'bai-da-dang']);
        Post::factory()->draft()->create(['slug' => 'bai-nhap']);

        $response = $this->get(route('sitemap'));

        $response->assertHeader('Content-Type', 'application/xml');
        $response->assertSee(route('products.show', 'iphone-15'), false);
        $response->assertSee(route('posts.show', 'bai-da-dang'), false);
        $response->assertDontSee(route('products.show', 'iphone-da-an'), false);
        $response->assertDontSee(route('posts.show', 'bai-nhap'), false);
    }

    public function test_sitemap_lists_the_storefront_pages(): void
    {
        $response = $this->get(route('sitemap'));

        $response->assertSee(route('home'), false);
        $response->assertSee(route('posts.index'), false);
        $response->assertSee(route('pages.policies.warranty'), false);
    }

    public function test_robots_keeps_crawlers_out_of_cart_and_search_and_points_at_the_sitemap(): void
    {
        $response = $this->get(route('robots'));

        $response->assertSee('Disallow: /gio-hang');
        $response->assertSee('Disallow: /tim-kiem');
        $response->assertSee('Disallow: /admin');
        $response->assertSee('Sitemap: '.route('sitemap'));
    }

    public function test_product_page_carries_a_canonical_url_and_product_schema(): void
    {
        $product = $this->product('iPhone 15', 'iphone-15');

        $response = $this->get(route('products.show', $product->slug));

        $response->assertSee('<link rel="canonical" href="'.route('products.show', 'iphone-15').'">', false);
        $response->assertSee('"@type":"Product"', false);
        $response->assertSee('"@type":"BreadcrumbList"', false);
    }

    public function test_product_schema_reports_the_rating_only_once_the_product_has_reviews(): void
    {
        $product = $this->product('iPhone 15', 'iphone-15');

        $this->get(route('products.show', $product->slug))->assertDontSee('"aggregateRating"', false);

        $user = User::create(['name' => 'Khách', 'email' => 'khach@test.com', 'password' => 'password']);
        Review::create(['product_id' => $product->id, 'user_id' => $user->id, 'rating' => 4]);

        $response = $this->get(route('products.show', $product->slug));

        $response->assertSee('"ratingValue":"4"', false);
        $response->assertSee('"reviewCount":"1"', false);
    }

    public function test_filtered_listing_points_its_canonical_back_at_the_plain_listing(): void
    {
        $response = $this->get(route('products.index', ['series' => 'iphone-15-series', 'sort' => 'price_asc']));

        $response->assertSee('<link rel="canonical" href="'.route('products.index').'">', false);
    }

    public function test_second_listing_page_keeps_its_own_canonical(): void
    {
        $response = $this->get(route('products.index', ['page' => 2]));

        $response->assertSee('<link rel="canonical" href="'.route('products.index').'?page=2">', false);
    }

    public function test_search_results_are_kept_out_of_the_index(): void
    {
        $response = $this->get(route('search', ['q' => 'iphone']));

        $response->assertSee('<meta name="robots" content="noindex, follow">', false);
    }

    public function test_product_page_stays_indexable(): void
    {
        $product = $this->product('iPhone 15', 'iphone-15');

        $response = $this->get(route('products.show', $product->slug));

        $response->assertDontSee('name="robots"', false);
    }

    public function test_home_page_describes_the_shop_for_search_engines(): void
    {
        $response = $this->get(route('home'));

        $response->assertSee('"@type":"Store"', false);
        $response->assertSee(config('shop.phone'), false);
        $response->assertSee('"@type":"WebSite"', false);
        $response->assertSee('<meta name="description"', false);
    }

    public function test_every_static_page_ships_a_meta_description(): void
    {
        foreach (['pages.about', 'pages.contact', 'pages.services', 'pages.installment', 'pages.policies', 'pages.policies.warranty'] as $route) {
            $this->get(route($route))->assertSee('<meta name="description" content="', false);
        }
    }

    private function product(string $name, string $slug, string $status = 'active'): Product
    {
        $product = Product::create([
            'category_id' => $this->category->id,
            'brand_id' => $this->brand->id,
            'series_id' => $this->series->id,
            'name' => $name,
            'slug' => $slug,
            'base_price' => 24990000,
            'status' => $status,
        ]);

        ProductVariant::create([
            'product_id' => $product->id,
            'color' => 'Đen',
            'storage' => '128GB',
            'price' => 24990000,
            'stock_quantity' => 5,
            'sku' => strtoupper($slug).'-128',
        ]);

        return $product;
    }
}
