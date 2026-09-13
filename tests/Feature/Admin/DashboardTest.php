<?php

namespace Tests\Feature\Admin;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductSeries;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
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

    public function test_admin_can_view_dashboard_with_empty_widgets(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin');

        $response->assertOk();
        $response->assertSee('+ Thêm sản phẩm');
        $response->assertSee('+ Thêm dòng sản phẩm');
        $response->assertSee('Đơn chờ xử lý');
        $response->assertSee('Đơn hàng gần đây');
        $response->assertSee('Chưa có đơn hàng nào.');
        $response->assertSee('Cảnh báo sắp hết hàng');
        $response->assertSee('Không có biến thể nào sắp hết hàng.');
        $response->assertSee('Sản phẩm bán chạy tháng này');
        $response->assertSee('Chưa có dữ liệu bán hàng tháng này.');
    }

    public function test_admin_dashboard_displays_recent_orders_low_stock_and_top_products(): void
    {
        $category = Category::create(['name' => 'Điện thoại', 'slug' => 'dien-thoai']);
        $brand = Brand::create(['name' => 'Apple', 'slug' => 'apple']);
        $series = ProductSeries::create(['name' => 'iPhone 15 Series', 'slug' => 'iphone-15-series']);

        $product = Product::create([
            'category_id' => $category->id,
            'brand_id' => $brand->id,
            'series_id' => $series->id,
            'name' => 'iPhone 15 Pro',
            'slug' => 'iphone-15-pro',
            'base_price' => 28000000,
            'status' => 'active',
        ]);

        $lowStockVariant = ProductVariant::create([
            'product_id' => $product->id,
            'color' => 'Titan Tự Nhiên',
            'storage' => '128GB',
            'price' => 28000000,
            'sku' => 'IP15P-128-NAT',
            'stock_quantity' => 2,
        ]);

        $order = Order::create([
            'user_id' => $this->regularUser->id,
            'order_code' => 'ORD-2026-TEST',
            'total_amount' => 28000000,
            'shipping_fee' => 0,
            'status' => Order::STATUS_PAID,
            'payment_method' => 'cod',
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'variant_id' => $lowStockVariant->id,
            'product_name_snapshot' => 'iPhone 15 Pro',
            'variant_label_snapshot' => 'Titan Tự Nhiên - 128GB',
            'price_snapshot' => 28000000,
            'quantity' => 3,
        ]);

        $response = $this->actingAs($this->admin)->get('/admin');

        $response->assertOk();
        $response->assertSee('ORD-2026-TEST');
        $response->assertSee('iPhone 15 Pro');
        $response->assertSee('Titan Tự Nhiên - 128GB');
        $response->assertSee('Còn 2');
        $response->assertSee('3');
        $response->assertSee('đã bán');
    }
}
