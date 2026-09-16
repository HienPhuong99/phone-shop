<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductSeries;
use App\Models\ProductVariant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductListingTest extends TestCase
{
    use RefreshDatabase;

    private Category $category;

    private Brand $brand;

    private ProductSeries $series15;

    private ProductSeries $series14;

    protected function setUp(): void
    {
        parent::setUp();

        $this->category = Category::create(['name' => 'Điện thoại', 'slug' => 'dien-thoai']);
        $this->brand = Brand::create(['name' => 'Apple', 'slug' => 'apple']);
        $this->series15 = ProductSeries::create(['name' => 'iPhone 15 Series', 'slug' => 'iphone-15-series']);
        $this->series14 = ProductSeries::create(['name' => 'iPhone 14 Series', 'slug' => 'iphone-14-series']);
    }

    /**
     * @param  array<string, mixed>  $overrides
     */
    private function makeProduct(array $overrides = []): Product
    {
        return Product::create(array_merge([
            'category_id' => $this->category->id,
            'brand_id' => $this->brand->id,
            'series_id' => $this->series15->id,
            'name' => 'iPhone Test',
            'slug' => 'iphone-test-'.uniqid(),
            'base_price' => 15000000,
            'status' => 'active',
        ], $overrides));
    }

    /**
     * @param  array<string, mixed>  $overrides
     */
    private function makeVariant(Product $product, array $overrides = []): ProductVariant
    {
        return ProductVariant::create(array_merge([
            'product_id' => $product->id,
            'color' => 'Đen',
            'storage' => '128GB',
            'price' => $product->base_price,
            'sku' => 'SKU-'.uniqid(),
            'stock_quantity' => 10,
        ], $overrides));
    }

    public function test_filtering_by_multiple_series_returns_products_from_either(): void
    {
        // Names deliberately don't echo their series number — the sidebar
        // always lists every active series regardless of the filter, so a
        // product named "iPhone 13" would false-positive against the
        // "iPhone 13 Series" checkbox label elsewhere on the same page.
        $this->makeProduct(['name' => 'Máy Thuộc Dòng 15', 'slug' => 'may-dong-15', 'series_id' => $this->series15->id]);
        $this->makeProduct(['name' => 'Máy Thuộc Dòng 14', 'slug' => 'may-dong-14', 'series_id' => $this->series14->id]);
        $this->makeProduct(['name' => 'Máy Thuộc Dòng 13', 'slug' => 'may-dong-13', 'series_id' => ProductSeries::create(['name' => 'iPhone 13 Series', 'slug' => 'iphone-13-series'])->id]);

        $response = $this->get(route('products.index', ['series' => ['iphone-15-series', 'iphone-14-series']]));

        $response->assertOk();
        $response->assertSee('Máy Thuộc Dòng 15');
        $response->assertSee('Máy Thuộc Dòng 14');
        $response->assertDontSee('Máy Thuộc Dòng 13');
    }

    public function test_filtering_by_storage_only_returns_products_with_that_variant(): void
    {
        $has256 = $this->makeProduct(['name' => 'iPhone Có 256', 'slug' => 'iphone-co-256']);
        $this->makeVariant($has256, ['storage' => '256GB']);

        $only128 = $this->makeProduct(['name' => 'iPhone Chỉ 128', 'slug' => 'iphone-chi-128']);
        $this->makeVariant($only128, ['storage' => '128GB']);

        $response = $this->get(route('products.index', ['storage' => ['256GB']]));

        $response->assertOk();
        $response->assertSee('iPhone Có 256');
        $response->assertDontSee('iPhone Chỉ 128');
    }

    public function test_filtering_by_color_only_returns_products_with_that_variant(): void
    {
        $black = $this->makeProduct(['name' => 'iPhone Đen', 'slug' => 'iphone-den']);
        $this->makeVariant($black, ['color' => 'Đen']);

        $white = $this->makeProduct(['name' => 'iPhone Trắng', 'slug' => 'iphone-trang']);
        $this->makeVariant($white, ['color' => 'Trắng']);

        $response = $this->get(route('products.index', ['color' => ['Trắng']]));

        $response->assertOk();
        $response->assertSee('iPhone Trắng');
        $response->assertDontSee('iPhone Đen');
    }

    public function test_in_stock_filter_excludes_products_with_zero_stock(): void
    {
        $inStock = $this->makeProduct(['name' => 'iPhone Còn Hàng', 'slug' => 'iphone-con-hang']);
        $this->makeVariant($inStock, ['stock_quantity' => 5]);

        $outOfStock = $this->makeProduct(['name' => 'iPhone Hết Hàng', 'slug' => 'iphone-het-hang']);
        $this->makeVariant($outOfStock, ['stock_quantity' => 0]);

        $response = $this->get(route('products.index', ['in_stock' => '1']));

        $response->assertOk();
        $response->assertSee('iPhone Còn Hàng');
        $response->assertDontSee('iPhone Hết Hàng');
    }

    public function test_category_nav_hides_categories_with_no_active_products(): void
    {
        $this->makeProduct();
        $emptyCategory = Category::create(['name' => 'Phụ kiện', 'slug' => 'phu-kien']);

        $response = $this->get(route('products.index'));

        $response->assertOk();
        $response->assertSee('Điện thoại');
        $response->assertDontSee('Phụ kiện');
    }

    public function test_series_nav_hides_series_with_no_active_products(): void
    {
        $this->makeProduct(['series_id' => $this->series15->id]);
        // series14 has no products at all — should not appear in the filter.

        $response = $this->get(route('products.index'));

        $response->assertOk();
        $response->assertSee('iPhone 15 Series');
        $response->assertDontSee('iPhone 14 Series');
    }

    public function test_series_nav_hides_series_whose_only_products_are_inactive(): void
    {
        $this->makeProduct(['series_id' => $this->series15->id]);
        $this->makeProduct(['series_id' => $this->series14->id, 'status' => 'inactive', 'slug' => 'iphone-hidden']);

        $response = $this->get(route('products.index'));

        $response->assertOk();
        $response->assertSee('iPhone 15 Series');
        $response->assertDontSee('iPhone 14 Series');
    }

    public function test_product_card_shows_discount_badge_only_when_compare_price_is_higher(): void
    {
        $discounted = $this->makeProduct([
            'name' => 'iPhone Giảm Giá',
            'slug' => 'iphone-giam-gia',
            'base_price' => 18000000,
            'compare_at_price' => 20000000,
        ]);
        $this->makeVariant($discounted);

        $fullPrice = $this->makeProduct([
            'name' => 'iPhone Giá Đủ',
            'slug' => 'iphone-gia-du',
            'base_price' => 15000000,
            'compare_at_price' => null,
        ]);
        $this->makeVariant($fullPrice);

        $response = $this->get(route('products.index'));

        $response->assertOk();
        $response->assertSee('Giảm 10%');
        // "20.000.000đ" only appears struck through next to the discounted product.
        $response->assertSeeInOrder(['iPhone Giảm Giá', '20.000.000đ']);
    }

    public function test_sort_by_discount_orders_highest_percent_off_first(): void
    {
        $small = $this->makeProduct([
            'name' => 'iPhone Giảm Ít', 'slug' => 'iphone-giam-it',
            'base_price' => 19000000, 'compare_at_price' => 20000000,
        ]);
        $this->makeVariant($small);

        $big = $this->makeProduct([
            'name' => 'iPhone Giảm Nhiều', 'slug' => 'iphone-giam-nhieu',
            'base_price' => 15000000, 'compare_at_price' => 20000000,
        ]);
        $this->makeVariant($big);

        $response = $this->get(route('products.index', ['sort' => 'discount']));

        $response->assertOk();
        $response->assertSeeInOrder(['iPhone Giảm Nhiều', 'iPhone Giảm Ít']);
    }

    public function test_sort_by_best_selling_excludes_cancelled_orders(): void
    {
        $lowSeller = $this->makeProduct(['name' => 'iPhone Ít Bán', 'slug' => 'iphone-it-ban']);
        $lowVariant = $this->makeVariant($lowSeller);

        $bestSeller = $this->makeProduct(['name' => 'iPhone Bán Chạy', 'slug' => 'iphone-ban-chay']);
        $bestVariant = $this->makeVariant($bestSeller);

        // Real (paid) demand: 5 units of the eventual best seller.
        $paidOrder = Order::create([
            'order_code' => 'ORD-PAID-1',
            'total_amount' => 5 * $bestVariant->price,
            'payment_method' => 'cod',
            'status' => Order::STATUS_PAID,
        ]);
        OrderItem::create([
            'order_id' => $paidOrder->id,
            'variant_id' => $bestVariant->id,
            'product_name_snapshot' => $bestSeller->name,
            'variant_label_snapshot' => $bestVariant->label,
            'price_snapshot' => $bestVariant->price,
            'quantity' => 5,
        ]);

        // A cancelled order for 100 units — must not count toward "bán chạy".
        $cancelledOrder = Order::create([
            'order_code' => 'ORD-CANCELLED-1',
            'total_amount' => 100 * $lowVariant->price,
            'payment_method' => 'cod',
            'status' => Order::STATUS_CANCELLED,
        ]);
        OrderItem::create([
            'order_id' => $cancelledOrder->id,
            'variant_id' => $lowVariant->id,
            'product_name_snapshot' => $lowSeller->name,
            'variant_label_snapshot' => $lowVariant->label,
            'price_snapshot' => $lowVariant->price,
            'quantity' => 100,
        ]);

        $response = $this->get(route('products.index', ['sort' => 'best_selling']));

        $response->assertOk();
        $response->assertSeeInOrder(['iPhone Bán Chạy', 'iPhone Ít Bán']);
    }
}
