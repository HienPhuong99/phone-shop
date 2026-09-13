# Kế hoạch: Hoàn thiện Dashboard & CRUD trong Admin

## Tổng quan

**Bối cảnh:** Từ sau Giai đoạn 5-7 (xem lịch sử git), dự án đã được một agent khác (Antigravity/Gemini) tiếp tục phát triển: pivot storefront sang bán riêng iPhone, reskin giao diện theo tam300.com, và thêm khái niệm **"Dòng sản phẩm" (`ProductSeries`)** — ví dụ "iPhone 15 Series", "iPhone 14 Series" — để nhóm các model theo thế hệ. Cột `series_id` trên bảng `products` là **bắt buộc** (`NOT NULL`, `restrictOnDelete`).

**Vấn đề:** `ProductSeries` được thêm ở tầng model + migration + seeder, và form tạo/sửa sản phẩm đã có dropdown chọn dòng sản phẩm — nhưng **không có nơi nào trong admin để tự thêm/sửa/xoá một dòng sản phẩm mới**. Đây là lỗ hổng CRUD rõ ràng nhất. Song song đó, trang Dashboard admin hiện chỉ có 4 ô số liệu + 1 biểu đồ doanh thu, khá sơ sài so với nhu cầu vận hành thực tế.

**Hai việc cần làm**, theo đúng thứ tự ưu tiên:

1. **CRUD "Dòng sản phẩm"** trong admin — việc cấp thiết nhất, vì thiếu nó thì danh mục dòng sản phẩm chỉ có thể sửa qua tinker/seeder, không đúng tinh thần "admin tự quản lý".
2. **Hoàn thiện Dashboard** — bổ sung các widget thực sự hữu ích cho người vận hành (đơn hàng gần đây, cảnh báo sắp hết hàng, sản phẩm bán chạy), không chỉ là số liệu tĩnh.

**Nguyên tắc khi thực hiện:**

- Bám sát pattern code đã có (đặc biệt là `CategoryController`/`BrandController` cho CRUD đơn giản, và `admin/categories/_form.blade.php` cho cách tách partial form). Không phát minh pattern mới nếu pattern cũ đã đủ dùng.
- Sau mỗi phần, chạy `php artisan test` và `vendor/bin/pint` — không được để test đỏ hoặc code lệch style.
- Không tự ý mở rộng phạm vi: các trang tĩnh (`/dich-vu`, `/chinh-sach`, `/gioi-thieu`, `/lien-he` qua `PageController`) **không cần** CRUD admin — chúng là nội dung tĩnh, ngoài phạm vi kế hoạch này.
- `ProductSeries` hiện **không** cần gắn theo brand (vì storefront chỉ bán Apple) — không thêm logic lọc series theo brand trừ khi catalog mở rộng sang hãng khác. Đừng over-engineer phần này.
- Mỗi phần xong nên commit riêng (giống lịch sử "Giai doan X" trước đó), message tiếng Việt không dấu theo đúng convention cũ, kèm dòng `Co-Authored-By: Claude Sonnet 5 <noreply@anthropic.com>`.

---

## Chi tiết

### Phần 1 — CRUD "Dòng sản phẩm" (`ProductSeries`)

**1.1. Controller:** Tạo `app/Http/Controllers/Admin/ProductSeriesController.php`, copy đúng cấu trúc của `app/Http/Controllers/Admin/BrandController.php` (đơn giản nhất, không có quan hệ cha-con như Category). Các method: `index`, `create`, `store`, `edit`, `update`, `destroy`.

- Validation (`store`/`update`):
  ```php
  'name' => ['required', 'string', 'max:255'],
  'description' => ['nullable', 'string', 'max:1000'],
  'sort_order' => ['nullable', 'integer', 'min:0'],
  ```
  `slug` tự sinh từ `name` bằng `Str::slug()`, giống `BrandController`.
- `sort_order`: nếu để trống ở form tạo mới, mặc định bằng `ProductSeries::max('sort_order') + 1` (để dòng mới luôn xếp cuối).
- `destroy`: bọc trong `try/catch (QueryException)` giống `CategoryController::destroy()` — nếu còn sản phẩm thuộc dòng này thì `restrictOnDelete` sẽ ném lỗi FK, bắt lại và trả về `back()->with('error', 'Không thể xoá dòng sản phẩm vì vẫn còn sản phẩm thuộc dòng này.')`.

**1.2. Routes:** Thêm vào `routes/admin.php`, đặt cạnh dòng `Route::resource('brands', ...)`:
```php
Route::resource('product-series', ProductSeriesController::class)->except(['show']);
```
(Đặt tên resource là `product-series` để URL là `/admin/product-series`, route name là `admin.product-series.*`.)

**1.3. Views:** Tạo thư mục `resources/views/admin/product-series/` với 4 file, copy nguyên cấu trúc từ `resources/views/admin/brands/`:
- `_form.blade.php`: input `name`, textarea `description` (tuỳ chọn), input số `sort_order` (tuỳ chọn, placeholder "Để trống = xếp cuối").
- `create.blade.php`, `edit.blade.php`: giống hệt bố cục `admin/brands/create.blade.php` và `edit.blade.php`, đổi route name.
- `index.blade.php`: liệt kê theo `orderBy('sort_order')` (không phải `orderBy('name')` như Brand, vì thứ tự hiển thị dòng sản phẩm có ý nghĩa — dòng mới nhất lên trước). Hiển thị thêm cột "Số sản phẩm" (`$series->products()->count()`) để admin biết dòng nào đang được dùng trước khi xoá.

**1.4. Sidebar:** Thêm link "Dòng sản phẩm" vào `resources/views/layouts/admin.blade.php`, trong mảng `$links`, đặt ngay sau "Thương hiệu" và trước "Đơn hàng":
```php
['route' => 'admin.product-series.index', 'label' => 'Dòng sản phẩm'],
```

**1.5. Test:** Thêm vào `tests/Feature/Admin/ProductManagementTest.php` (hoặc file test mới `ProductSeriesManagementTest.php` nếu muốn tách riêng):
- Admin tạo được dòng sản phẩm mới.
- Xoá dòng sản phẩm đang có sản phẩm thuộc nó bị chặn (giống test `test_deleting_category_with_products_is_blocked`).
- Non-admin bị chặn 403 (test này có thể dùng chung route middleware, không bắt buộc lặp lại nếu đã có test chung cho `admin` middleware).

**Việc cần làm trước khi bắt đầu:** đọc `app/Http/Controllers/Admin/BrandController.php`, `app/Http/Controllers/Admin/CategoryController.php`, và toàn bộ `resources/views/admin/brands/*.blade.php` để chắc chắn copy đúng convention (đặt tên biến, class Tailwind, cấu trúc `@csrf`/`@method`).

---

### Phần 2 — Rà soát CRUD hiện có (Products / Categories / Brands)

Sau khi xong Phần 1, kiểm tra lại 3 CRUD đang có, sửa các điểm sau nếu phát hiện đúng như mô tả (đọc code thật trước khi sửa — mô tả dưới đây dựa trên trạng thái tại thời điểm viết kế hoạch, có thể đã đổi):

- **`admin/products/edit.blade.php`**: form biến thể (thêm/sửa) dùng `flex flex-wrap` — kiểm tra lại trên màn hình hẹp (đã từng bị tràn ra ngoài khung một lần, xem commit "Giai doan 5"), đảm bảo vẫn wrap đúng sau các thay đổi UI gần đây (reskin tam300.com).
- **Xoá ảnh sản phẩm / biến thể**: xác nhận nút xoá ảnh trong `admin/products/edit.blade.php` vẫn hoạt động đúng sau khi đổi field ảnh sang `/images/products/*.svg` tĩnh cho dữ liệu seed — ảnh do admin tự upload qua `Storage::disk('public')` phải không bị nhầm với ảnh tĩnh trong `public/images/products/`.
- **Category/Brand index**: không cần thêm tìm kiếm (danh sách nhỏ, không cần thiết) — không làm nếu không thấy vấn đề thực tế.
- **Thông báo lỗi/thành công**: đảm bảo cả 3 CRUD (Product, Category, Brand) và CRUD mới (ProductSeries) đều hiển thị `session('status')` và `session('error')` nhất quán qua `layouts/admin.blade.php` — không tự thêm banner riêng lẻ trong từng view.

Đây là bước rà soát, không phải thêm tính năng mới — chỉ sửa nếu phát hiện lỗi thật, tránh refactor không cần thiết.

---

### Phần 3 — Hoàn thiện Dashboard

File cần sửa: `app/Http/Controllers/Admin/DashboardController.php` và `resources/views/admin/dashboard.blade.php`.

Giữ nguyên 4 ô số liệu + biểu đồ 7 ngày đang có, **thêm** các khối sau (thêm mới, không thay cái cũ):

**3.1. Đơn hàng gần đây** (bảng, 5 dòng mới nhất)
```php
$recentOrders = Order::with('user')->latest()->take(5)->get();
```
Hiển thị: mã đơn, khách hàng (`$order->user?->name ?? 'Khách vãng lai'`), tổng tiền, trạng thái (dùng lại style pill đã có ở `admin/orders/index.blade.php`), link tới `admin.orders.show`.

**3.2. Cảnh báo sắp hết hàng** (danh sách, biến thể có `stock_quantity <= 5`, tối đa 5 dòng)
```php
$lowStockVariants = ProductVariant::with('product')
    ->where('stock_quantity', '<=', 5)
    ->orderBy('stock_quantity')
    ->take(5)
    ->get();
```
Hiển thị: tên sản phẩm + biến thể (`color - storage`), số lượng tồn còn lại (in đậm màu đỏ/cam nếu = 0), link tới `admin.products.edit`. Nếu danh sách rỗng, hiển thị dòng "Không có biến thể nào sắp hết hàng." thay vì để trống.

**3.3. Sản phẩm bán chạy tháng này** (top 5 theo tổng số lượng đã bán trong `order_items`, chỉ tính đơn có status thuộc `[paid, completed]`)
```php
$topProducts = OrderItem::query()
    ->select('product_name_snapshot')
    ->selectRaw('SUM(quantity) as total_sold')
    ->whereHas('order', fn ($q) => $q->whereIn('status', [Order::STATUS_PAID, Order::STATUS_COMPLETED])
        ->whereMonth('created_at', now()->month)
        ->whereYear('created_at', now()->year))
    ->groupBy('product_name_snapshot')
    ->orderByDesc('total_sold')
    ->take(5)
    ->get();
```
Dùng `product_name_snapshot` (không join ngược về `Product`) vì đây là dữ liệu lịch sử đã snapshot — đúng nguyên tắc đã áp dụng từ Giai đoạn 3, không phá vỡ nó.

**3.4. Liên kết nhanh** (hàng nút bấm ở đầu trang, trước các ô số liệu)
- "+ Thêm sản phẩm" → `admin.products.create`
- "+ Thêm dòng sản phẩm" → `admin.product-series.create` (route mới từ Phần 1)
- "Đơn chờ xử lý" → `admin.orders.index` kèm query `?status=pending`, hiển thị kèm số lượng đơn đang chờ (`Order::where('status', 'pending')->count()`)

**Bố cục gợi ý cho `dashboard.blade.php`:** giữ hàng 4 ô số liệu + chart như cũ ở trên cùng; thêm hàng liên kết nhanh ngay dưới tiêu đề trang (trước hàng 4 ô số liệu); thêm lưới 2 cột bên dưới chart: cột trái là "Đơn hàng gần đây", cột phải chia đôi theo chiều dọc thành "Sắp hết hàng" và "Bán chạy tháng này". Dùng lại đúng class Tailwind đang có (`bg-white rounded-lg border border-gray-200 p-4`) cho mọi khối mới — không tạo style card mới.

**Không cần:** thêm thư viện mới, thêm biểu đồ thứ hai (donut trạng thái đơn) — nếu muốn làm thêm, hỏi người dùng trước vì đây là mở rộng ngoài yêu cầu gốc, không phải lỗ hổng cần vá.

---

### Phần 4 — Hoàn tất

1. `php artisan test` — toàn bộ test phải pass (baseline hiện tại: 47 test, 120 assertion — số này sẽ tăng lên sau khi thêm test cho `ProductSeries`).
2. `vendor/bin/pint` — chạy và commit các thay đổi style nếu có.
3. Kiểm tra qua trình duyệt (dùng `.claude/launch.json` đã có sẵn để mở preview, hoặc `php artisan serve`):
   - Tạo/sửa/xoá một dòng sản phẩm từ `/admin/product-series`.
   - Xoá dòng sản phẩm đang có sản phẩm → phải bị chặn với thông báo lỗi.
   - Vào `/admin` xem đủ 3 widget mới (đơn gần đây, sắp hết hàng, bán chạy) render đúng dữ liệu thật từ seeder.
4. Chạy `php artisan migrate:fresh --seed` một lần cuối để đảm bảo không có gì phụ thuộc dữ liệu cũ còn sót lại trong DB dev.
5. Commit theo từng phần (Phần 1 CRUD series, Phần 3 dashboard) hoặc gộp một commit "Hoan thien dashboard va CRUD dong san pham trong admin" nếu làm liền mạch — tuỳ độ dài diff, không bắt buộc tách.
