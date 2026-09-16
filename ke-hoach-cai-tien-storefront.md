# Kế hoạch: Bổ sung tìm kiếm & cải tiến giao diện storefront

## Tổng quan

**Bối cảnh:** Storefront hiện đã có đủ luồng mua hàng cơ bản — trang chủ (hero slider theo `is_featured`, danh sách dòng máy, danh mục, sản phẩm mới), trang danh sách có lọc + sắp xếp, trang chi tiết với gallery và bộ chọn phiên bản, giỏ hàng, thanh toán COD/VNPay, đơn hàng, các trang tĩnh và toàn bộ admin CRUD. Catalog gồm 46 model iPhone trong seeder, trong đó 20 model đang `active`, chia theo 14 dòng sản phẩm.

**Xác nhận nghi ngờ của bạn: đúng là đang thiếu tìm kiếm.** Rà soát toàn bộ `routes/`, `app/Http/Controllers/` và `resources/views/` chỉ tìm thấy **một** ô tìm kiếm duy nhất, và nó nằm trong admin (`resources/views/admin/products/index.blade.php:8` + `Admin/ProductController.php:24`). Phía khách hàng không có bất kỳ đường nào để gõ tên máy: header trong `layouts/shop.blade.php` chỉ có logo, 2 link nav, giỏ hàng và tài khoản; thanh tab dưới cùng trên mobile cũng chỉ có 4 mục. Người mua muốn tìm "iPhone 13" phải tự đoán đường: vào Sản phẩm → lọc dòng máy → lật trang. Với 20 sản phẩm đang bán thì còn chịu được, nhưng đây là tính năng mà **mọi** web cùng ngành đều đặt ở vị trí trung tâm header ([CellphoneS](https://cellphones.com.vn/), [Thế Giới Di Động](https://www.thegioididong.com/dtdd), [ShopDunk](https://shopdunk.com/)), và theo [Baymard](https://baymard.com/blog/autocomplete-design) thì khách dùng tìm kiếm có tỉ lệ chốt đơn cao gần gấp đôi khách chỉ lướt danh mục.

**Mockup giao diện đề xuất:** https://claude.ai/artifact/HxBC4PvFVJDfQ4YKJQxLGG — 7 artboard vẽ đúng theo bộ màu/typography đang dùng (`tailwind.config.js`: brand `#0B4174`, ink `#0f172a`, ink-soft `#64748b`, paper `#f8fafc`, line `#e2e8f0`, Inter, bo góc 16px, `shadow-sm`), giá lấy từ `ProductSeeder`:

| Artboard | Nội dung |
|---|---|
| 1a | Header mới + dropdown gợi ý tìm kiếm (desktop) |
| 1b | Lớp tìm kiếm toàn màn hình (mobile) |
| 2a | Trang kết quả `/tim-kiem?q=` |
| 2b | Trạng thái không có kết quả + gợi ý sửa chính tả |
| 3a | Thẻ sản phẩm: hiện tại vs đề xuất (có ghi chú lý do từng thay đổi) |
| 3b | Trang danh sách với bộ lọc nhiều lựa chọn + chip lọc nhanh |
| 4 | Trang chi tiết nâng cấp (breadcrumb, giá gốc, trả góp, so sánh, đánh giá, sản phẩm cùng dòng) |

---

## Phần 0 — Rà soát: còn thiếu gì so với web cùng ngành

| Hạng mục | Hiện trạng trong code | Các web cùng ngành đang làm |
|---|---|---|
| **Tìm kiếm** | Không có ở storefront | Ô tìm kiếm chiếm giữa header trên mọi trang, gợi ý tức thì kèm ảnh + giá |
| Giá gốc / % giảm | Chỉ có `base_price` | Giá gạch ngang + badge "Giảm 12%" trên từng thẻ |
| Lọc nhiều lựa chọn | `radio` chọn một, submit ngay khi đổi (`products/index.blade.php`) | Checkbox nhiều lựa chọn, chip mức giá dựng sẵn, lọc theo dung lượng/màu |
| Danh mục rỗng | `Phụ kiện`, `Máy tính bảng` vẫn hiện trong bộ lọc dù 0 sản phẩm | Ẩn hẳn nhánh rỗng |
| Sắp xếp | Mới nhất / giá tăng / giá giảm | Thêm "Bán chạy", "Liên quan nhất", "Giảm giá nhiều" |
| Tồn kho ở danh sách | Chỉ hiện trong trang chi tiết | "Còn 12 máy" / "Sắp hết hàng" ngay trên thẻ |
| Đánh giá sản phẩm | Không có | Điểm sao + số lượt đánh giá + số đã bán |
| Yêu thích / So sánh | Không có | Cả 3 web tham khảo đều có |
| Sản phẩm liên quan / đã xem | Không có | Cuối trang chi tiết |
| Breadcrumb | Có ở trang tĩnh, **thiếu** ở trang sản phẩm | Có ở mọi trang |
| Trả góp / thu cũ | Trang chi tiết **đã quảng cáo** "Trả góp 0%", "Thu cũ lên đời" nhưng không có trang hay công cụ nào phía sau (`products/show.blade.php:174-193`, các link trong `pages/services.blade.php` đều trỏ về `/lien-he`) | Trang riêng + công cụ định giá/ước tính số tiền mỗi tháng |
| Mua không cần đăng nhập | `/thanh-toan` nằm trong `middleware('auth')` | Cho đặt hàng rồi mới tạo tài khoản; tra cứu đơn bằng mã + số điện thoại |
| Địa chỉ & phí ship | Một dòng `address_line`, phí cố định 30.000đ (`CheckoutController::SHIPPING_FEE`) | Tỉnh/quận/phường, phí theo vùng, miễn phí theo ngưỡng đơn |
| Mã giảm giá | Không có | Ô nhập mã ở giỏ hàng |
| SEO | Chỉ có `<title>`, không meta description/OG/JSON-LD | Đầy đủ, có `Product` schema |

Hai dòng **in đậm** là phần cần làm trước; phần còn lại xếp theo giai đoạn bên dưới.

---

## Giai đoạn 1 — Tìm kiếm (ưu tiên số 1)

Mục tiêu: gõ được từ khoá ở mọi trang, có gợi ý ngay khi gõ, có trang kết quả riêng, và **tìm được cả khi gõ không dấu**.

### 1.1. Chuẩn hoá dữ liệu tìm kiếm

Người Việt gõ "iphone 15 pro max" hay "dien thoai pin trau" — so trực tiếp với `name` sẽ trượt. Thêm một cột đã chuẩn hoá thay vì xử lý lúc truy vấn:

- Migration `add_search_text_to_products_table`: `$table->text('search_text')->nullable()->index();`
- Trong `Product`, dùng model event `saving` (đăng ký ở `booted()`) để dựng lại `search_text` từ: `name` + tên dòng sản phẩm + danh mục + màu/dung lượng của các variant, qua `Str::ascii(Str::lower($value))` để bỏ dấu.
- Thêm một Artisan command `products:reindex-search` (hoặc gọi lại trong `ProductSeeder`) để dựng dữ liệu cho các bản ghi cũ.

**Không dùng Laravel Scout / Meilisearch ở giai đoạn này.** Với 46 sản phẩm trên SQLite, `LIKE '%...%'` trên cột đã chuẩn hoá cho kết quả dưới 1ms, còn Scout kéo theo dependency mới (CLAUDE.md: không đổi dependency khi chưa được duyệt). Nếu sau này catalog vượt vài nghìn dòng và chuyển sang MySQL thì mới tính tới full-text index hoặc Scout — lúc đó chỉ cần đổi phần thân của scope ở 1.2.

### 1.2. Scope truy vấn dùng chung

`ProductController@index` hiện đang dựng query lọc ngay trong controller (35 dòng). Trước khi thêm tìm kiếm, tách phần lọc đó ra để trang kết quả tìm kiếm dùng lại được mà không copy-paste:

- Thêm `scopeSearch(Builder $query, string $term): Builder` vào `Product` — tách từ khoá thành từng chữ, mỗi chữ một điều kiện `where('search_text', 'like', "%{$word}%")` (AND giữa các chữ, đúng kỳ vọng "gõ thêm chữ thì kết quả hẹp lại").
- Thêm `scopeFilter(Builder $query, array $filters): Builder` gom toàn bộ điều kiện `series`/`category`/`min_price`/`max_price` đang có trong controller.

### 1.3. Routes & controller

Thêm vào `routes/web.php`, đặt ngay sau nhóm `/san-pham`:

```php
Route::get('/tim-kiem', [SearchController::class, 'index'])->name('search');
Route::get('/tim-kiem/goi-y', [SearchController::class, 'suggest'])
    ->middleware('throttle:60,1')
    ->name('search.suggest');
```

- `SearchController@index`: đọc `q`, gọi `Product::query()->where('status', 'active')->search($q)->filter($request->all())`, phân trang 12, trả `view('search.index')`. (Điều kiện `status = active` đang lặp ở `HomeController` và `ProductController`, nên tiện tay tách luôn thành `scopeActive`.) Query string giữ nguyên khi đổi trang/bộ lọc (`withQueryString()` như `ProductController` đang làm).
- `SearchController@suggest`: trả JSON tối đa 5 sản phẩm (`id`, `name`, `slug`, `series`, `thumbnail_thumb`, `final_price`, `in_stock`) + tổng số kết quả. Theo convention trong CLAUDE.md, bọc bằng một `ProductSuggestionResource` thay vì `->toArray()` thủ công.
- Bỏ qua truy vấn khi `q` ngắn hơn 2 ký tự (trả mảng rỗng, không đụng DB).

### 1.4. Component thanh tìm kiếm

Tạo `resources/views/components/search-bar.blade.php` và nhúng vào `layouts/shop.blade.php` (xem artboard 1a/1b):

- **Desktop:** ô tìm kiếm nằm giữa header, chiếm phần rộng nhất; hàng thứ hai của header là các dòng sản phẩm để vào nhanh. Dropdown gợi ý: khối "Từ khoá được tìm nhiều" (hard-code 4 từ khoá, sau này lấy từ log tìm kiếm), danh sách sản phẩm có ảnh thumbnail + giá + trạng thái kho, dòng cuối "Xem tất cả N kết quả".
- **Mobile:** icon kính lúp trong header mở lớp tìm kiếm toàn màn hình, có autofocus, lịch sử tìm kiếm lưu ở `localStorage`. **Không** thêm mục thứ 5 vào thanh tab dưới — 4 mục hiện tại đang vừa đẹp.
- **Alpine:** debounce 250ms (`fetch` + `AbortController` để huỷ request cũ), điều hướng bằng `↑ ↓`, `Enter` mở mục đang chọn, `Esc` đóng. Toàn bộ nằm trong `x-data` của component, không cần file JS mới.
- Ô input luôn nằm trong `<form method="GET" action="{{ route('search') }}">` để khi JS lỗi hoặc chưa tải xong thì nhấn Enter vẫn ra trang kết quả.

### 1.5. Trạng thái rỗng (artboard 2b)

Không trả trang trắng:

- Gợi ý sửa chính tả: so `q` với danh sách tên model bằng `levenshtein()` của PHP (khoảng cách ≤ 3), hiện "Có phải bạn muốn tìm **iPhone 15 Pro**?".
- Gợi ý cách gõ lại + chip các dòng máy phổ biến + 4 sản phẩm bán chạy.
- Một dòng nhắc rằng nhiều đời máy cũ đang ở trạng thái `inactive` nên không hiện, kèm hotline — đây là tình huống thật với catalog hiện tại (26/46 model đang ẩn).

### 1.6. Test

- `tests/Feature/SearchTest.php` (PHPUnit, tạo bằng `php artisan make:test --phpunit SearchTest`): tìm đúng tên; tìm không dấu ra kết quả; sản phẩm `inactive` không lọt vào kết quả; `q` rỗng/1 ký tự không lỗi; endpoint gợi ý trả đúng cấu trúc JSON và tối đa 5 mục.
- `tests/search.spec.ts` (Playwright): gõ vào ô tìm kiếm → dropdown hiện → `Enter` → tới trang kết quả. Chỉ thêm 1 spec, đúng tinh thần "chỉ guard luồng đáng giữ lâu dài" trong CLAUDE.md.

---

## Giai đoạn 2 — Thẻ sản phẩm & bộ lọc (artboard 3a, 3b)

### 2.1. Thẻ sản phẩm

`x-product-card` hiện chỉ có ảnh, dòng máy, tên, giá. Bổ sung 4 lớp thông tin:

1. **Giá gốc + % giảm** — cần migration thêm `compare_at_price` (nullable) vào `products`, và ô nhập tương ứng trong form admin. Chỉ hiện badge khi `compare_at_price > final_price`.
2. **Chip dung lượng** — lấy từ `variants` đã eager-load sẵn, không tốn truy vấn mới.
3. **Tồn kho** — "Còn N máy" / "Sắp hết hàng" (ngưỡng 5) / "Hết hàng". Dùng `withSum('variants', 'stock_quantity')` ở controller để tránh N+1.
4. **Nút yêu thích + "So sánh"** — chỉ là giao diện ở giai đoạn này, nối tính năng ở Giai đoạn 4.

Cẩn thận với hiệu năng: `HomeController` và `ProductController` cần `withMin('variants', 'price')` thay cho accessor `getFinalPriceAttribute()` khi hiển thị danh sách, vì accessor hiện sort collection trong PHP cho từng sản phẩm.

### 2.2. Bộ lọc

- Đổi `radio` → `checkbox` nhiều lựa chọn cho dòng sản phẩm và dung lượng; bỏ `onchange="this.form.submit()"`, thay bằng nút "Áp dụng · N sản phẩm".
- Chip mức giá dựng sẵn: Dưới 10 triệu / 10–15 / 15–25 / Trên 25 triệu (map sang `min_price`, `max_price` sẵn có).
- Thêm lọc theo màu (từ `variants.color`) và "Chỉ hiện máy còn hàng".
- Hàng chip hiển thị điều kiện đang áp dụng, mỗi chip có dấu × để bỏ riêng + "Xoá tất cả".
- **Ẩn danh mục rỗng:** `Category::navList()` thêm `->has('products')` — hiện tại `Phụ kiện` và `Máy tính bảng` luôn dẫn tới trang trắng. Nhớ xoá cache `nav-list` khi đổi.
- Thêm sắp xếp "Bán chạy" và "Giảm giá nhiều nhất". Lưu ý `OrderItem` gắn với `variant_id` chứ không gắn thẳng vào sản phẩm, nên cần thêm quan hệ `hasManyThrough(OrderItem::class, ProductVariant::class)` vào `Product` rồi `withSum('orderItems', 'quantity')` — đừng đếm bằng vòng lặp PHP.

### 2.3. Test

Cập nhật/bổ sung feature test: lọc nhiều dòng sản phẩm cùng lúc trả đúng tập kết quả; lọc "còn hàng" loại sản phẩm hết hàng; danh mục rỗng không xuất hiện trong bộ lọc.

---

## Giai đoạn 3 — Trang chi tiết (artboard 4)

Giữ nguyên phần đang chạy tốt (gallery, chuyển màu/dung lượng, thanh mua hàng dính ở mobile), thêm:

- **Breadcrumb** Trang chủ / Sản phẩm / Dòng máy / Tên máy.
- **Giá gốc + tiết kiệm bao nhiêu** (dùng `compare_at_price` ở 2.1).
- **Box trả góp có số thật:** `giá ÷ 12` hiển thị "từ 1.583.000đ/tháng", link sang trang dịch vụ. Hiện tại trang chi tiết đang hứa "Trả góp 0%" mà không dẫn đi đâu — hoặc làm cho tử tế, hoặc bỏ dòng đó đi.
- **Bảng so sánh nhanh** với model kế cận cùng tầm giá, lấy từ `specifications` (đã là JSON sẵn trong DB).
- **Sản phẩm cùng dòng** + **Đã xem gần đây** (`localStorage`, không cần bảng mới).
- **SEO:** meta description từ `description`, thẻ OG dùng `thumbnail`, JSON-LD `Product` kèm `offers`. Đây là thứ rẻ nhất để làm mà tác động lớn nhất tới lượng truy cập tự nhiên.

---

## Giai đoạn 4 — Đánh giá, yêu thích, so sánh

- **Đánh giá:** bảng `reviews` (`product_id`, `user_id`, `order_item_id`, `rating`, `content`). Chỉ cho đánh giá khi user thật sự đã mua variant đó (kiểm tra qua `order_items` của đơn đã hoàn tất) — đây là điểm khác biệt đáng tin so với review tự do. Hiện điểm trung bình + phân bố sao ở trang chi tiết, điểm + số lượt trên thẻ sản phẩm.
- **Yêu thích:** bảng `wishlists` cho user đăng nhập; khách chưa đăng nhập lưu tạm ở `localStorage` rồi gộp vào khi đăng nhập (cùng cách `CartService` đang xử lý giỏ hàng theo session).
- **So sánh:** trang `/so-sanh?ids=...`, tối đa 3 máy, bảng dựng từ `specifications`. Không cần bảng DB mới.

---

## Giai đoạn 5 — Hoàn thiện luồng mua

Xếp cuối vì phạm vi lớn hơn và đụng tới thanh toán:

- **Đặt hàng không cần đăng nhập** + tra cứu đơn bằng `order_code` + số điện thoại (cột `user_id` trong `orders` đã `nullable` sẵn — thiết kế DB đã tính tới việc này).
- **Địa chỉ tỉnh/quận/phường** và phí vận chuyển theo vùng, thay cho `SHIPPING_FEE = 30000` cứng.
- **Mã giảm giá** ở giỏ hàng.
- **Trang trả góp / thu cũ đổi mới thật** kèm công cụ ước tính, thay cho 4 link đang trỏ chung về `/lien-he`.

---

## Nguyên tắc khi thực hiện

- **Không thêm dependency mới** (Scout, Meilisearch, thư viện UI…). Toàn bộ đề xuất trên làm được bằng Laravel + Alpine + Tailwind đang có.
- Bám sát pattern sẵn có: controller mỏng, query gom vào scope của model, view tách component như `x-product-card`, `x-hero-product-slider`.
- Dùng token màu/bo góc/đổ bóng trong `tailwind.config.js` — mockup đã vẽ đúng bộ token này, không phát sinh màu mới.
- Mỗi giai đoạn: chạy `php artisan test` + `vendor/bin/pint --dirty --format agent`, commit riêng, message tiếng Việt không dấu theo convention cũ.
- Sau khi sửa Blade/CSS/JS nhớ `npm run build` (hoặc `composer run dev` khi đang code) thì giao diện mới hiện.

## Nếu chỉ làm được một việc

Làm **Giai đoạn 1**. Thanh tìm kiếm là thứ duy nhất trong danh sách này mà người dùng sẽ đi tìm rồi không thấy — mọi mục còn lại chỉ là "giá mà có thì tốt hơn".
