<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductSeries;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompareTest extends TestCase
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
        return Product::create(array_merge([
            'category_id' => $this->category->id,
            'brand_id' => $this->brand->id,
            'series_id' => $this->series->id,
            'name' => 'iPhone Test',
            'slug' => 'iphone-test-'.uniqid(),
            'base_price' => 15000000,
            'status' => 'active',
        ], $overrides));
    }

    public function test_shows_a_prompt_when_fewer_than_two_products_are_selected(): void
    {
        $product = $this->makeProduct();

        $response = $this->get(route('compare.show', ['slugs' => $product->slug]));

        $response->assertOk();
        $response->assertSee('Chọn ít nhất 2 sản phẩm');
    }

    public function test_compares_the_union_of_spec_labels_showing_a_dash_for_missing_ones(): void
    {
        $a = $this->makeProduct(['name' => 'iPhone A', 'slug' => 'iphone-a']);
        $a->update(['specifications' => ['Màn hình' => '6.1 inch', 'Camera sau' => '48MP']]);

        $b = $this->makeProduct(['name' => 'iPhone B', 'slug' => 'iphone-b']);
        $b->update(['specifications' => ['Màn hình' => '6.7 inch']]);

        $response = $this->get(route('compare.show', ['slugs' => 'iphone-a,iphone-b']));

        $response->assertOk();
        $response->assertSee('iPhone A');
        $response->assertSee('iPhone B');
        $response->assertSee('Màn hình');
        $response->assertSee('Camera sau');
        $response->assertSee('48MP');
        $response->assertSee('—'); // dash for iPhone B's missing Camera sau row
    }

    public function test_caps_comparison_at_three_products(): void
    {
        $slugs = [];
        foreach (range(1, 4) as $i) {
            $product = $this->makeProduct(['name' => "iPhone Số {$i}", 'slug' => "iphone-so-{$i}"]);
            $slugs[] = $product->slug;
        }

        $response = $this->get(route('compare.show', ['slugs' => implode(',', $slugs)]));

        $response->assertOk();
        $response->assertSee('iPhone Số 1');
        $response->assertSee('iPhone Số 2');
        $response->assertSee('iPhone Số 3');
        $response->assertDontSee('iPhone Số 4');
    }

    public function test_flags_an_identical_spec_with_a_match_chip(): void
    {
        $a = $this->makeProduct(['name' => 'iPhone A', 'slug' => 'iphone-a']);
        $a->update(['specifications' => ['Dung lượng RAM' => '8GB']]);

        $b = $this->makeProduct(['name' => 'iPhone B', 'slug' => 'iphone-b']);
        $b->update(['specifications' => ['Dung lượng RAM' => '8GB (tối ưu cho Apple Intelligence)']]);

        $response = $this->get(route('compare.show', ['slugs' => 'iphone-a,iphone-b']));

        $response->assertOk();
        $response->assertSee('Giống nhau');
    }

    public function test_highlights_the_winning_value_for_a_measurable_spec(): void
    {
        $a = $this->makeProduct(['name' => 'iPhone A', 'slug' => 'iphone-a']);
        $a->update(['specifications' => ['Pin' => '4.422 mAh']]);

        $b = $this->makeProduct(['name' => 'iPhone B', 'slug' => 'iphone-b']);
        $b->update(['specifications' => ['Pin' => '4.685 mAh']]);

        $response = $this->get(route('compare.show', ['slugs' => 'iphone-a,iphone-b']));

        $response->assertOk();
        $response->assertSee('+263 mAh');
    }

    public function test_does_not_declare_a_winner_for_a_non_measurable_spec(): void
    {
        $a = $this->makeProduct(['name' => 'iPhone A', 'slug' => 'iphone-a']);
        $a->update(['specifications' => ['Chất liệu' => 'Khung nhôm']]);

        $b = $this->makeProduct(['name' => 'iPhone B', 'slug' => 'iphone-b']);
        $b->update(['specifications' => ['Chất liệu' => 'Khung titan']]);

        $response = $this->get(route('compare.show', ['slugs' => 'iphone-a,iphone-b']));

        $response->assertOk();
        $response->assertDontSee('Giống nhau');
        // No measurable winner text should appear for a material comparison.
        $response->assertDontSee('Lớn hơn');
        $response->assertDontSee('Nhẹ hơn');
    }

    public function test_shows_the_hide_matches_toggle_only_when_something_matches(): void
    {
        $a = $this->makeProduct(['name' => 'iPhone A', 'slug' => 'iphone-a']);
        $a->update(['specifications' => ['Chất liệu' => 'Khung nhôm']]);

        $b = $this->makeProduct(['name' => 'iPhone B', 'slug' => 'iphone-b']);
        $b->update(['specifications' => ['Chất liệu' => 'Khung titan']]);

        $response = $this->get(route('compare.show', ['slugs' => 'iphone-a,iphone-b']));

        $response->assertOk();
        $response->assertDontSee('Ẩn điểm giống nhau');
    }

    public function test_specs_render_grouped_under_their_category_heading(): void
    {
        $a = $this->makeProduct(['name' => 'iPhone A', 'slug' => 'iphone-a']);
        $a->update(['specifications' => ['Màn hình' => '6.1 inch', 'Pin' => '3.000 mAh']]);

        $b = $this->makeProduct(['name' => 'iPhone B', 'slug' => 'iphone-b']);
        $b->update(['specifications' => ['Màn hình' => '6.7 inch', 'Pin' => '4.000 mAh']]);

        $response = $this->get(route('compare.show', ['slugs' => 'iphone-a,iphone-b']));

        $response->assertOk();
        $response->assertSeeInOrder(['Màn hình', '6.1 inch', 'Pin &amp; Sạc', '3.000 mAh'], false);
    }

    public function test_ignores_inactive_products(): void
    {
        $active = $this->makeProduct(['name' => 'iPhone Active', 'slug' => 'iphone-active']);
        $inactive = $this->makeProduct(['name' => 'iPhone Inactive', 'slug' => 'iphone-inactive', 'status' => 'inactive']);

        $response = $this->get(route('compare.show', ['slugs' => "{$active->slug},{$inactive->slug}"]));

        $response->assertOk();
        $response->assertSee('Chọn ít nhất 2 sản phẩm'); // only 1 active product resolved
    }
}
