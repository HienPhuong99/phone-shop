<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductSeries;
use App\Models\ProductVariant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SearchTest extends TestCase
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

    private function makeProduct(string $name, string $slug, string $status = 'active'): Product
    {
        return Product::create([
            'category_id' => $this->category->id,
            'brand_id' => $this->brand->id,
            'series_id' => $this->series->id,
            'name' => $name,
            'slug' => $slug,
            'base_price' => 15000000,
            'status' => $status,
        ]);
    }

    public function test_search_page_finds_product_by_exact_name(): void
    {
        $this->makeProduct('iPhone 15 Pro Max', 'iphone-15-pro-max');

        $response = $this->get(route('search', ['q' => 'iPhone 15 Pro Max']));

        $response->assertOk();
        $response->assertSee('iPhone 15 Pro Max');
    }

    public function test_search_matches_input_typed_without_diacritics(): void
    {
        $this->makeProduct('iPhone Cổ Điển', 'iphone-co-dien');

        $response = $this->get(route('search', ['q' => 'co dien']));

        $response->assertOk();
        $response->assertSee('iPhone Cổ Điển');
    }

    public function test_search_matches_series_name_not_just_product_name(): void
    {
        $this->makeProduct('iPhone 15', 'iphone-15');

        $response = $this->get(route('search', ['q' => 'iPhone 15 Series']));

        $response->assertOk();
        $response->assertSee('iPhone 15');
    }

    public function test_search_narrows_results_as_more_words_are_typed(): void
    {
        $this->makeProduct('iPhone 15 Pro Max', 'iphone-15-pro-max');
        $this->makeProduct('iPhone 15 Pro', 'iphone-15-pro');

        // Both match "iPhone 15 Pro" (each contains those three words)...
        $broad = $this->get(route('search', ['q' => 'iPhone 15 Pro']));
        $broad->assertOk();
        $broad->assertSee('2 sản phẩm phù hợp');

        // ...but only one matches once "Max" narrows it further.
        $narrow = $this->get(route('search', ['q' => 'iPhone 15 Pro Max']));
        $narrow->assertOk();
        $narrow->assertSee('1 sản phẩm phù hợp');
    }

    public function test_search_excludes_inactive_products(): void
    {
        $this->makeProduct('iPhone Ngừng Bán', 'iphone-ngung-ban', status: 'inactive');

        $response = $this->get(route('search', ['q' => 'Ngừng Bán']));

        $response->assertOk();
        $response->assertDontSee('iPhone Ngừng Bán');
    }

    public function test_zero_result_search_suggests_the_closest_product_name(): void
    {
        $this->makeProduct('iPhone 15 Pro', 'iphone-15-pro');

        // A transposed typo ("iphnoe") that no longer LIKE-matches "iphone"
        // as a substring, so this genuinely falls through to zero results
        // and exercises the Levenshtein suggestion.
        $response = $this->get(route('search', ['q' => 'iphnoe 15 pro']));

        $response->assertOk();
        $response->assertSee('Có phải bạn muốn tìm');
        $response->assertSee('iPhone 15 Pro');
    }

    public function test_suggest_endpoint_returns_empty_for_query_shorter_than_two_chars(): void
    {
        $this->makeProduct('iPhone 15', 'iphone-15');

        $response = $this->getJson(route('search.suggest', ['q' => 'i']));

        $response->assertOk();
        $response->assertExactJson(['products' => [], 'total' => 0]);
    }

    public function test_suggest_endpoint_returns_up_to_five_matches_with_price_and_stock(): void
    {
        $product = $this->makeProduct('iPhone 15 Pro Max', 'iphone-15-pro-max');
        ProductVariant::create([
            'product_id' => $product->id,
            'color' => 'Titan Tự nhiên',
            'storage' => '256GB',
            'price' => 19000000,
            'sku' => 'IP15PM-TTN-256GB',
            'stock_quantity' => 5,
        ]);

        for ($i = 0; $i < 6; $i++) {
            $this->makeProduct("iPhone 15 Pro Max {$i}", "iphone-15-pro-max-{$i}");
        }

        $response = $this->getJson(route('search.suggest', ['q' => 'iphone 15 pro max']));

        $response->assertOk();
        $response->assertJsonCount(5, 'products');
        $response->assertJsonPath('total', 7);
        $response->assertJsonFragment([
            'name' => 'iPhone 15 Pro Max',
            'slug' => 'iphone-15-pro-max',
            'price' => 19000000.0,
            'in_stock' => true,
        ]);
    }
}
