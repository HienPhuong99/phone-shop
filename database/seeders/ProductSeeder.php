<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductSeries;
use App\Models\ProductVariant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Only these 20 iPhone lines stay listed for sale on the storefront;
     * every other model in models() is seeded with status=inactive (hidden).
     *
     * @var list<string>
     */
    private const ACTIVE_MODELS = [
        'iPhone 12',
        'iPhone 12 Pro',
        'iPhone 12 Pro Max',
        'iPhone 13 mini',
        'iPhone 13',
        'iPhone 13 Pro',
        'iPhone 13 Pro Max',
        'iPhone SE 3 (2022)',
        'iPhone 14',
        'iPhone 14 Plus',
        'iPhone 14 Pro',
        'iPhone 14 Pro Max',
        'iPhone 15',
        'iPhone 15 Plus',
        'iPhone 15 Pro',
        'iPhone 15 Pro Max',
        'iPhone 16',
        'iPhone 16 Plus',
        'iPhone 16 Pro',
        'iPhone 16 Pro Max',
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categoryName = 'Điện thoại';
        $categoryId = Category::where('name', $categoryName)->value('id');
        $brandId = Brand::where('name', 'Apple')->value('id');
        $seriesIds = ProductSeries::pluck('id', 'slug');
        $seriesNames = ProductSeries::pluck('name', 'slug');

        foreach ($this->models() as $model) {
            $slug = Str::slug($model['name']);

            $product = Product::updateOrCreate(
                ['slug' => $slug],
                [
                    'category_id' => $categoryId,
                    'brand_id' => $brandId,
                    'series_id' => $seriesIds[$model['series']],
                    'name' => $model['name'],
                    'description' => trim($model['description']),
                    'specifications' => $model['specifications'],
                    'base_price' => min(array_column($model['storages'], 'price')),
                    'thumbnail' => $model['thumbnail'] ?? null,
                    'status' => in_array($model['name'], self::ACTIVE_MODELS, true) ? 'active' : 'inactive',
                    // DatabaseSeeder disables model events (WithoutModelEvents),
                    // so Product::booted()'s saving hook never runs here —
                    // set search_text ourselves via the same helper it uses.
                    'search_text' => Product::buildSearchText($model['name'], $seriesNames[$model['series']], $categoryName),
                ]
            );

            // Reseed variants from scratch so reseeding never leaves stale combinations behind.
            $product->variants()->delete();

            foreach ($model['colors'] as $color) {
                foreach ($model['storages'] as $storage) {
                    ProductVariant::create([
                        'product_id' => $product->id,
                        'color' => $color,
                        'storage' => $storage['label'],
                        'price' => $storage['price'],
                        'sku' => strtoupper($slug.'-'.Str::slug($color).'-'.$storage['label']),
                        'stock_quantity' => fake()->numberBetween(0, 50),
                    ]);
                }
            }
        }
    }

    /**
     * Every iPhone model ever released, grouped by series, with specs and
     * variants sourced from the catalog reference docs supplied by the user.
     *
     * @return list<array{
     *     name: string,
     *     series: string,
     *     thumbnail?: string,
     *     specifications: array<string, string>,
     *     description: string,
     *     colors: list<string>,
     *     storages: list<array{label: string, price: int}>,
     * }>
     */
    private function models(): array
    {
        return [
            // ---------------------------------------------------------------
            // Kỷ nguyên khởi nguyên: 2G, 3G, 3GS
            // ---------------------------------------------------------------
            [
                'name' => 'iPhone (2G)',
                'series' => 'iphone-co-dien',
                'thumbnail' => '/images/products/iphone-2g.svg',
                'specifications' => [
                    'Màn hình' => '3.5 inch, TFT cảm ứng điện dung đa điểm, 320 x 480 pixels (165 ppi)',
                    'Vi xử lý (CPU)' => 'Samsung 32-bit RISC ARM 412 MHz',
                    'Dung lượng RAM' => '128MB',
                    'Bộ nhớ trong' => '4GB / 8GB / 16GB',
                    'Camera chính' => '2.0MP (sau), không có flash, không quay video',
                    'Camera trước' => 'Không có',
                    'Cổng kết nối' => '30-pin Dock Connector, Jack tai nghe 3.5mm',
                    'Chất liệu chế tác' => 'Vỏ nhôm phay xước kết hợp nắp nhựa phía đáy',
                    'Trọng lượng & Kích thước' => '135g; 115 x 61 x 11.6 mm',
                    'Pin & Thời lượng' => '1.400 mAh Lithium-ion tích hợp sẵn',
                ],
                'description' => <<<'DESC'
                    iPhone 2G là huyền thoại khai sinh ra định nghĩa smartphone hiện đại với màn hình cảm ứng điện dung đa điểm tuyệt vời và thiết kế nhôm phối đen kinh điển. Đây là món đồ công nghệ sưu tầm vô giá khẳng định bước ngoặt lịch sử vĩ đại của Apple.

                    Kỷ nguyên Khởi nguyên: Bước ngoặt lịch sử của ngành di động

                    Màn hình cảm ứng điện dung đa điểm thay đổi thế giới
                    Thế hệ iPhone đầu tiên (2007) mở ra kỷ nguyên mới khi loại bỏ bàn phím vật lý cồng kềnh, thay thế bằng màn hình cảm ứng điện dung đa điểm 3.5 inch mượt mà chưa từng có. Thao tác vuốt, cuộn trang web và chụm hai ngón tay để phóng to ảnh đã trở thành tiêu chuẩn vàng cho toàn bộ smartphone sau này.

                    Thiết kế nhôm nguyên khối sang trọng và hệ điều hành iPhone OS đột phá
                    Thân máy được chế tác từ nhôm phay xước cao cấp phối cùng dải nhựa đen đặc trưng phía đuôi máy, mang lại độ hoàn thiện tinh xảo vượt thời gian. Hệ điều hành iPhone OS nguyên bản tích hợp trọn vẹn trình duyệt web Safari đầy đủ, ứng dụng YouTube và iPod, định hình trải nghiệm di động hiện đại.
                    DESC,
                'colors' => ['Bạc phối Đen'],
                'storages' => [
                    ['label' => '4GB', 'price' => 500000],
                    ['label' => '8GB', 'price' => 650000],
                    ['label' => '16GB', 'price' => 800000],
                ],
            ],
            [
                'name' => 'iPhone 3G',
                'series' => 'iphone-co-dien',
                'thumbnail' => '/images/products/iphone-3g.svg',
                'specifications' => [
                    'Màn hình' => '3.5 inch, TFT 16 triệu màu, 320 x 480 pixels (165 ppi)',
                    'Vi xử lý (CPU)' => 'Samsung ARM 11 412 MHz',
                    'Dung lượng RAM' => '128MB',
                    'Bộ nhớ trong' => '8GB / 16GB',
                    'Camera chính' => '2.0MP, hỗ trợ gắn thẻ địa lý Geo-tagging',
                    'Camera trước' => 'Không có',
                    'Kết nối mạng' => 'Mạng 3G (HSDPA), GPS tích hợp, Wi-Fi 802.11b/g',
                    'Chất liệu chế tác' => 'Vỏ nhựa Polycarbonate bóng cong bo tròn',
                    'Trọng lượng & Kích thước' => '133g; 115.5 x 62.1 x 12.3 mm',
                    'Pin & Thời lượng' => '1.150 mAh',
                ],
                'description' => <<<'DESC'
                    iPhone 3G mở toang cánh cửa bước vào kỷ nguyên Internet di động tốc độ cao và lần đầu tiên đưa kho ứng dụng App Store đến tay người dùng toàn cầu. Thiết kế mặt lưng nhựa bóng uốn cong công thái học đem lại trải nghiệm cầm trên tay cực kỳ êm ái.

                    Kỷ nguyên Kết nối Tốc độ cao & Hệ sinh thái App Store

                    Cửa ngõ Internet di động với mạng 3G và kho ứng dụng App Store
                    Dòng iPhone 3G và 3GS đánh dấu sự bùng nổ của App Store, mở ra một vũ trụ ứng dụng vô tận phục vụ công việc và giải trí. Khả năng kết nối mạng 3G tốc độ cao kết hợp cùng chip định vị toàn cầu GPS biến chiếc điện thoại thành thiết bị dẫn đường và duyệt web thực thụ.

                    Thiết kế lưng cong công thái học và bước nhảy vọt về tốc độ
                    Mặt lưng nhựa bóng bo cong mềm mại ôm sát lòng bàn tay, mang đến cảm giác cầm nắm đầm chắc và thoải mái tuyệt đối. Thế hệ 3GS bổ sung khả năng quay video sắc nét, la bàn số định hướng và vi xử lý mạnh gấp đôi thế hệ trước.
                    DESC,
                'colors' => ['Đen', 'Trắng'],
                'storages' => [
                    ['label' => '8GB', 'price' => 550000],
                    ['label' => '16GB', 'price' => 700000],
                ],
            ],
            [
                'name' => 'iPhone 3GS',
                'series' => 'iphone-co-dien',
                'thumbnail' => '/images/products/iphone-3gs.svg',
                'specifications' => [
                    'Màn hình' => '3.5 inch TFT, 320 x 480 pixels, phủ lớp Oleophobic chống vân tay',
                    'Vi xử lý (CPU)' => 'Samsung Cortex-A8 600 MHz, GPU PowerVR SGX535',
                    'Dung lượng RAM' => '256MB (gấp đôi thế hệ 3G)',
                    'Bộ nhớ trong' => '8GB / 16GB / 32GB',
                    'Camera chính' => '3.15MP, tự động lấy nét (Autofocus), quay video VGA@30fps',
                    'Camera trước' => 'Không có',
                    'Cảm biến mới' => 'La bàn kỹ thuật số (Digital Compass), Voice Control',
                    'Chất liệu' => 'Vỏ nhựa cao cấp màu Đen bóng hoặc Trắng bóng',
                    'Trọng lượng & Kích thước' => '135g; 115.5 x 62.1 x 12.3 mm',
                    'Pin & Thời lượng' => '1.219 mAh',
                ],
                'description' => <<<'DESC'
                    Đại diện cho chữ "Speed", iPhone 3GS tăng gấp đôi hiệu năng xử lý với dung lượng RAM 256MB và là chiếc iPhone đầu tiên có khả năng quay video chuyển động cùng tính năng tự động lấy nét. Màn hình máy được bổ sung lớp phủ chống dầu mỡ giúp mặt kính luôn trong trẻo khi vuốt chạm.

                    Kỷ nguyên Kết nối Tốc độ cao & Hệ sinh thái App Store

                    Cửa ngõ Internet di động với mạng 3G và kho ứng dụng App Store
                    Dòng iPhone 3G và 3GS đánh dấu sự bùng nổ của App Store, mở ra một vũ trụ ứng dụng vô tận phục vụ công việc và giải trí. Khả năng kết nối mạng 3G tốc độ cao kết hợp cùng chip định vị toàn cầu GPS biến chiếc điện thoại thành thiết bị dẫn đường và duyệt web thực thụ.

                    Thiết kế lưng cong công thái học và bước nhảy vọt về tốc độ
                    Mặt lưng nhựa bóng bo cong mềm mại ôm sát lòng bàn tay, mang đến cảm giác cầm nắm đầm chắc và thoải mái tuyệt đối. Thế hệ 3GS bổ sung khả năng quay video sắc nét, la bàn số định hướng và vi xử lý mạnh gấp đôi thế hệ trước.
                    DESC,
                'colors' => ['Đen', 'Trắng'],
                'storages' => [
                    ['label' => '8GB', 'price' => 700000],
                    ['label' => '16GB', 'price' => 850000],
                    ['label' => '32GB', 'price' => 1000000],
                ],
            ],

            // ---------------------------------------------------------------
            // iPhone 4 Series: 4, 4s
            // ---------------------------------------------------------------
            [
                'name' => 'iPhone 4',
                'series' => 'iphone-4-series',
                'thumbnail' => '/images/products/iphone-4.svg',
                'specifications' => [
                    'Màn hình' => '3.5 inch Retina Display (IPS LCD), 960 x 640 pixels (326 ppi)',
                    'Vi xử lý (CPU)' => 'Apple A4 (1 nhân Cortex-A8 1.0 GHz)',
                    'Dung lượng RAM' => '512MB',
                    'Bộ nhớ trong' => '8GB / 16GB / 32GB',
                    'Camera sau' => '5.0MP f/2.8, đèn LED flash, quay phim HD 720p 30fps',
                    'Camera trước' => 'VGA (0.3MP) - Lần đầu tiên hỗ trợ gọi video FaceTime',
                    'Cổng & SIM' => 'Cổng 30-pin, chuẩn Micro-SIM',
                    'Chất liệu chế tác' => 'Khung thép không gỉ phẳng, hai mặt kính cường lực Aluminosilicate',
                    'Trọng lượng & Độ mỏng' => '137g; siêu mỏng 9.3 mm',
                    'Pin & Sạc' => '1.420 mAh',
                ],
                'description' => <<<'DESC'
                    iPhone 4 là bước nhảy vọt ngoạn mục với chuẩn màn hình Retina siêu nét 326 ppi và thiết kế khung thép kẹp kính cường lực sang trọng vượt thời gian. Máy cũng mở đầu cho tính năng đàm thoại hình ảnh FaceTime quen thuộc trên mọi gia đình hiện đại.

                    Tuyệt tác Thiết kế Khung thép & Màn hình Retina Sắc nét

                    Đỉnh cao thẩm mỹ với khung thép không gỉ và hai mặt kính phẳng
                    iPhone 4 và 4s được ngợi ca là một trong những kiệt tác thiết kế vĩ đại nhất của Apple dưới thời Steve Jobs. Cấu trúc khung viền thép không gỉ phẳng phiu đóng vai trò ăng-ten, kẹp giữa hai mặt kính cường lực bóng bẩy tạo nên vẻ đẹp sang trọng, đẳng cấp và trường tồn.

                    Màn hình Retina vượt giới hạn mắt người cùng sự ra đời của Siri
                    Tấm nền Retina Display đạt mật độ điểm ảnh 326 ppi sắc nét đến mức mắt người không thể phân biệt từng điểm ảnh ở khoảng cách thông thường. Cùng với camera 8MP quay phim Full HD và trợ lý ảo thông minh Siri trên đời 4s, đây là bước chuyển mình mang tính thời đại.
                    DESC,
                'colors' => ['Đen', 'Trắng'],
                'storages' => [
                    ['label' => '8GB', 'price' => 900000],
                    ['label' => '16GB', 'price' => 1050000],
                    ['label' => '32GB', 'price' => 1200000],
                ],
            ],
            [
                'name' => 'iPhone 4s',
                'series' => 'iphone-4-series',
                'thumbnail' => '/images/products/iphone-4s.svg',
                'specifications' => [
                    'Màn hình' => '3.5 inch Retina IPS LCD, 960 x 640 pixels, tương phản 800:1',
                    'Vi xử lý (CPU)' => 'Apple A5 lõi kép (Dual-core Cortex-A9), đồ họa 2 nhân',
                    'Dung lượng RAM' => '512MB',
                    'Bộ nhớ trong' => '8GB / 16GB / 32GB / 64GB',
                    'Camera sau' => '8.0MP f/2.4, thấu kính 5 lớp, quay phim Full HD 1080p chống rung',
                    'Camera trước' => 'VGA (0.3MP) FaceTime',
                    'Tính năng độc quyền' => 'Trợ lý ảo thông minh Siri điều khiển giọng nói',
                    'Ăng-ten' => 'Thiết kế chia 4 vạch ngắt kim loại thông minh chống mất sóng',
                    'Trọng lượng & Kích thước' => '140g; 115.2 x 58.6 x 9.3 mm',
                    'Pin & Sạc' => '1.432 mAh',
                ],
                'description' => <<<'DESC'
                    iPhone 4s khắc phục triệt để lỗi ăng-ten bằng cấu hình thông minh, bổ sung chip A5 lõi kép tăng tốc đồ họa gấp 7 lần và trình làng trợ lý ảo Siri gây sốt toàn cầu. Camera 8MP quay phim 1080p xuất sắc biến chiếc máy thành thiết bị ghi hình bỏ túi chuyên nghiệp.

                    Tuyệt tác Thiết kế Khung thép & Màn hình Retina Sắc nét

                    Đỉnh cao thẩm mỹ với khung thép không gỉ và hai mặt kính phẳng
                    iPhone 4 và 4s được ngợi ca là một trong những kiệt tác thiết kế vĩ đại nhất của Apple dưới thời Steve Jobs. Cấu trúc khung viền thép không gỉ phẳng phiu đóng vai trò ăng-ten, kẹp giữa hai mặt kính cường lực bóng bẩy tạo nên vẻ đẹp sang trọng, đẳng cấp và trường tồn.

                    Màn hình Retina vượt giới hạn mắt người cùng sự ra đời của Siri
                    Tấm nền Retina Display đạt mật độ điểm ảnh 326 ppi sắc nét đến mức mắt người không thể phân biệt từng điểm ảnh ở khoảng cách thông thường. Cùng với camera 8MP quay phim Full HD và trợ lý ảo thông minh Siri trên đời 4s, đây là bước chuyển mình mang tính thời đại.
                    DESC,
                'colors' => ['Đen', 'Trắng'],
                'storages' => [
                    ['label' => '8GB', 'price' => 1100000],
                    ['label' => '16GB', 'price' => 1250000],
                    ['label' => '32GB', 'price' => 1400000],
                    ['label' => '64GB', 'price' => 1600000],
                ],
            ],

            // ---------------------------------------------------------------
            // iPhone 5 Series: 5, 5c, 5s
            // ---------------------------------------------------------------
            [
                'name' => 'iPhone 5',
                'series' => 'iphone-5-series',
                'thumbnail' => '/images/products/iphone-5.svg',
                'specifications' => [
                    'Màn hình' => '4.0 inch Retina IPS LCD, 1136 x 640 pixels (tỉ lệ 16:9, 326 ppi)',
                    'Vi xử lý (CPU)' => 'Apple A6 (2 nhân 1.3 GHz nền tảng ARMv7)',
                    'Dung lượng RAM' => '1GB LPDDR2',
                    'Bộ nhớ trong' => '16GB / 32GB / 64GB',
                    'Camera sau' => '8.0MP f/2.4, mặt kính bảo vệ Sapphire, quay Full HD 1080p',
                    'Camera trước' => '1.2MP FaceTime HD (720p)',
                    'Cổng kết nối' => 'Lightning 8 chân đảo chiều siêu nhỏ gọn, Nano-SIM',
                    'Chất liệu' => 'Nhôm Anodized nguyên khối với đường cắt vát kim cương sáng bóng',
                    'Trọng lượng & Độ mỏng' => 'Siêu nhẹ 112g; mỏng 7.6 mm',
                    'Pin' => '1.440 mAh',
                ],
                'description' => <<<'DESC'
                    iPhone 5 đem đến thân máy nhôm nguyên khối siêu mỏng nhẹ chỉ 112g kết hợp những đường vát kim cương tinh xảo đến từng micromet. Kích thước 4.0 inch kéo dài tỉ lệ 16:9 cùng cổng cắm Lightning đảo chiều mang lại sự tiện ích tối đa cho việc cầm nắm và sạc pin.

                    Màn hình 4.0 inch Chuẩn 16:9, Cổng Lightning & Bảo mật Touch ID

                    Tỉ lệ màn hình 16:9 tối ưu và cổng sạc Lightning nhỏ gọn đảo chiều
                    Thế hệ iPhone 5 mang đến kích thước màn hình 4.0 inch thanh thoát, tối ưu chuẩn điện ảnh 16:9 và cho phép thao tác một tay trọn vẹn. Cổng sạc Lightning nhỏ gọn lật mặt nào cũng cắm được đã chính thức thay thế cổng 30-pin cũ kỹ, đặt nền móng kết nối gọn gàng bền bỉ.

                    Kiến trúc 64-bit đầu tiên trên di động và vân tay một chạm Touch ID
                    Sự xuất hiện của phím Home cảm biến vân tay Touch ID trên 5s đã thay đổi hoàn toàn thói quen bảo mật thiết bị di động. Kết hợp cùng vi xử lý Apple A7 64-bit chuẩn máy tính để bàn, dòng máy sở hữu sức mạnh xử lý vượt trội và tuổi thọ hỗ trợ phần mềm lâu dài đáng kinh ngạc.
                    DESC,
                'colors' => ['Đen (Slate)', 'Bạc (Silver)'],
                'storages' => [
                    ['label' => '16GB', 'price' => 1300000],
                    ['label' => '32GB', 'price' => 1500000],
                    ['label' => '64GB', 'price' => 1700000],
                ],
            ],
            [
                'name' => 'iPhone 5c',
                'series' => 'iphone-5-series',
                'thumbnail' => '/images/products/iphone-5c.svg',
                'specifications' => [
                    'Màn hình' => '4.0 inch Retina IPS LCD, 1136 x 640 pixels (326 ppi)',
                    'Vi xử lý (CPU)' => 'Apple A6 (2 nhân 1.3 GHz)',
                    'Dung lượng RAM' => '1GB LPDDR2',
                    'Bộ nhớ trong' => '8GB / 16GB / 32GB',
                    'Camera sau' => '8.0MP f/2.4, Flash LED, quay Full HD 1080p',
                    'Camera trước' => '1.2MP FaceTime HD',
                    'Chất liệu chế tác' => 'Vỏ nhựa Polycarbonate cứng cáp gia cường khung thép bên trong',
                    'Màu sắc trẻ trung' => 'Trắng, Xanh dương, Xanh lá cây, Vàng, Hồng',
                    'Trọng lượng & Kích thước' => '132g; 124.4 x 59.2 x 8.97 mm',
                    'Pin' => '1.510 mAh',
                ],
                'description' => <<<'DESC'
                    iPhone 5c là làn gió trẻ trung phá cách với 5 tông màu kẹo ngọt rực rỡ trên nền vỏ nhựa Polycarbonate bóng loáng có khung thép gia cố vững chắc bên trong. Máy mang trọn vẹn hiệu năng mượt mà của iPhone 5 với mức giá tiếp cận dễ dàng hơn.

                    Màn hình 4.0 inch Chuẩn 16:9, Cổng Lightning & Bảo mật Touch ID

                    Tỉ lệ màn hình 16:9 tối ưu và cổng sạc Lightning nhỏ gọn đảo chiều
                    Thế hệ iPhone 5 mang đến kích thước màn hình 4.0 inch thanh thoát, tối ưu chuẩn điện ảnh 16:9 và cho phép thao tác một tay trọn vẹn. Cổng sạc Lightning nhỏ gọn lật mặt nào cũng cắm được đã chính thức thay thế cổng 30-pin cũ kỹ, đặt nền móng kết nối gọn gàng bền bỉ.

                    Kiến trúc 64-bit đầu tiên trên di động và vân tay một chạm Touch ID
                    Sự xuất hiện của phím Home cảm biến vân tay Touch ID trên 5s đã thay đổi hoàn toàn thói quen bảo mật thiết bị di động. Kết hợp cùng vi xử lý Apple A7 64-bit chuẩn máy tính để bàn, dòng máy sở hữu sức mạnh xử lý vượt trội và tuổi thọ hỗ trợ phần mềm lâu dài đáng kinh ngạc.
                    DESC,
                'colors' => ['Trắng', 'Xanh dương', 'Hồng'],
                'storages' => [
                    ['label' => '8GB', 'price' => 1150000],
                    ['label' => '16GB', 'price' => 1300000],
                    ['label' => '32GB', 'price' => 1450000],
                ],
            ],
            [
                'name' => 'iPhone 5s',
                'series' => 'iphone-5-series',
                'thumbnail' => '/images/products/iphone-5s.svg',
                'specifications' => [
                    'Màn hình' => '4.0 inch Retina IPS LCD, 1136 x 640 pixels (326 ppi)',
                    'Vi xử lý (CPU)' => 'Apple A7 (64-bit đầu tiên thế giới) + Chip đo chuyển động M7',
                    'Dung lượng RAM' => '1GB LPDDR3',
                    'Bộ nhớ trong' => '16GB / 32GB / 64GB',
                    'Bảo mật sinh trắc' => 'Cảm biến vân tay Touch ID phủ kính Sapphire quanh viền kim loại',
                    'Camera sau' => '8.0MP f/2.2, đèn flash kép True Tone hai màu, quay Slow-mo 120fps',
                    'Camera trước' => '1.2MP FaceTime HD',
                    'Chất liệu' => 'Nhôm nguyên khối viền kim cương vát cạnh, bổ sung màu Vàng Champagne',
                    'Trọng lượng & Kích thước' => '112g; 123.8 x 58.6 x 7.6 mm',
                    'Pin' => '1.560 mAh',
                ],
                'description' => <<<'DESC'
                    iPhone 5s là tuyệt tác thiết kế đỉnh cao của dòng 4.0 inch với phím Home vân tay Touch ID một chạm mở khóa siêu nhạy và tông màu Vàng Champagne huyền thoại. Máy trang bị vi xử lý kiến trúc 64-bit đầu tiên trên thế giới cho sức mạnh bền bỉ nhiều năm.

                    Màn hình 4.0 inch Chuẩn 16:9, Cổng Lightning & Bảo mật Touch ID

                    Tỉ lệ màn hình 16:9 tối ưu và cổng sạc Lightning nhỏ gọn đảo chiều
                    Thế hệ iPhone 5 mang đến kích thước màn hình 4.0 inch thanh thoát, tối ưu chuẩn điện ảnh 16:9 và cho phép thao tác một tay trọn vẹn. Cổng sạc Lightning nhỏ gọn lật mặt nào cũng cắm được đã chính thức thay thế cổng 30-pin cũ kỹ, đặt nền móng kết nối gọn gàng bền bỉ.

                    Kiến trúc 64-bit đầu tiên trên di động và vân tay một chạm Touch ID
                    Sự xuất hiện của phím Home cảm biến vân tay Touch ID trên 5s đã thay đổi hoàn toàn thói quen bảo mật thiết bị di động. Kết hợp cùng vi xử lý Apple A7 64-bit chuẩn máy tính để bàn, dòng máy sở hữu sức mạnh xử lý vượt trội và tuổi thọ hỗ trợ phần mềm lâu dài đáng kinh ngạc.
                    DESC,
                'colors' => ['Xám (Space Gray)', 'Bạc', 'Vàng Champagne'],
                'storages' => [
                    ['label' => '16GB', 'price' => 1500000],
                    ['label' => '32GB', 'price' => 1700000],
                    ['label' => '64GB', 'price' => 1900000],
                ],
            ],

            // ---------------------------------------------------------------
            // iPhone 6 Series: 6, 6 Plus, 6s, 6s Plus
            // ---------------------------------------------------------------
            [
                'name' => 'iPhone 6',
                'series' => 'iphone-6-series',
                'thumbnail' => '/images/products/iphone-6.svg',
                'specifications' => [
                    'Màn hình' => '4.7 inch Retina HD (IPS LCD), 1334 x 750 pixels (326 ppi)',
                    'Vi xử lý (CPU)' => 'Apple A8 (tiến trình 20nm, 2 nhân 1.4 GHz)',
                    'Dung lượng RAM' => '1GB LPDDR3',
                    'Bộ nhớ trong' => '16GB / 32GB / 64GB / 128GB',
                    'Camera sau' => '8.0MP f/2.2, lấy nét theo pha Focus Pixels, quay 1080p@60fps',
                    'Camera trước' => '1.2MP FaceTime HD f/2.2',
                    'Thanh toán' => 'NFC hỗ trợ Apple Pay lần đầu tiên',
                    'Chất liệu & Độ mỏng' => 'Hợp kim nhôm Series 6000 bo cong viền; siêu mỏng 6.9 mm',
                    'Trọng lượng' => '129g',
                    'Pin' => '1.810 mAh',
                ],
                'description' => <<<'DESC'
                    iPhone 6 đánh dấu sự lột xác với màn hình 4.7 inch rộng rãi và các cạnh bo tròn siêu mỏng 6.9mm cho cảm giác cầm lướt vô cùng thanh thoát. Công nghệ lấy nét theo pha Focus Pixels giúp bắt trọn từng khoảnh khắc một cách mượt mà, chuẩn xác.

                    Kỷ nguyên Màn hình Lớn: Phân hóa Tiêu chuẩn và Dòng Plus

                    Bước chuyển mình lịch sử sang màn hình lớn 4.7 inch và 5.5 inch
                    iPhone 6 và 6 Plus đánh dấu cuộc đại cách mạng về kích thước của Apple nhằm thỏa mãn cơn khát màn hình lớn để xem phim, lướt web và giải trí đa phương tiện. Thiết kế thân máy siêu mỏng với các cạnh nhôm bo cong mềm mại mang đến vẻ ngoài thanh lịch, quyến rũ.

                    Thời lượng pin vượt bậc cùng công nghệ chống rung quang học OIS
                    Phiên bản Plus không chỉ mở rộng không gian hiển thị lên độ phân giải Full HD mà còn sở hữu thời lượng pin ấn tượng, đáp ứng trọn vẹn cả ngày dài. Hệ thống camera bổ sung chống rung quang học OIS giúp chụp ảnh và quay phim thiếu sáng sắc nét, hạn chế tối đa rung nhòe.
                    DESC,
                'colors' => ['Xám', 'Bạc', 'Vàng'],
                'storages' => [
                    ['label' => '16GB', 'price' => 1800000],
                    ['label' => '64GB', 'price' => 2200000],
                    ['label' => '128GB', 'price' => 2600000],
                ],
            ],
            [
                'name' => 'iPhone 6 Plus',
                'series' => 'iphone-6-series',
                'thumbnail' => '/images/products/iphone-6-plus.svg',
                'specifications' => [
                    'Màn hình' => '5.5 inch Retina HD Full HD, 1920 x 1080 pixels (401 ppi)',
                    'Vi xử lý (CPU)' => 'Apple A8 (2 nhân 1.4 GHz, 20nm)',
                    'Dung lượng RAM' => '1GB LPDDR3',
                    'Bộ nhớ trong' => '16GB / 64GB / 128GB',
                    'Camera sau' => '8.0MP f/2.2, hỗ trợ chống rung quang học OIS phần cứng',
                    'Camera trước' => '1.2MP FaceTime HD',
                    'Giao diện ngang' => 'Hỗ trợ xoay màn hình chính dạng ngang (Landscape mode) như iPad',
                    'Chất liệu' => 'Khung nhôm Series 6000 bo tròn, mỏng 7.1 mm',
                    'Trọng lượng' => '172g',
                    'Pin' => '2.915 mAh (thời lượng pin vượt trội)',
                ],
                'description' => <<<'DESC'
                    iPhone 6 Plus mở màn cho phân khúc phablet màn hình lớn 5.5 inch Full HD sắc nét của Apple, mang lại thời lượng pin trâu bất ngờ đáp ứng trọn vẹn 2 ngày dùng. Ống kính tích hợp chống rung quang học OIS giúp chụp ảnh đêm rõ nét và hạn chế nhòe chuyển động.

                    Kỷ nguyên Màn hình Lớn: Phân hóa Tiêu chuẩn và Dòng Plus

                    Bước chuyển mình lịch sử sang màn hình lớn 4.7 inch và 5.5 inch
                    iPhone 6 và 6 Plus đánh dấu cuộc đại cách mạng về kích thước của Apple nhằm thỏa mãn cơn khát màn hình lớn để xem phim, lướt web và giải trí đa phương tiện. Thiết kế thân máy siêu mỏng với các cạnh nhôm bo cong mềm mại mang đến vẻ ngoài thanh lịch, quyến rũ.

                    Thời lượng pin vượt bậc cùng công nghệ chống rung quang học OIS
                    Phiên bản Plus không chỉ mở rộng không gian hiển thị lên độ phân giải Full HD mà còn sở hữu thời lượng pin ấn tượng, đáp ứng trọn vẹn cả ngày dài. Hệ thống camera bổ sung chống rung quang học OIS giúp chụp ảnh và quay phim thiếu sáng sắc nét, hạn chế tối đa rung nhòe.
                    DESC,
                'colors' => ['Xám', 'Bạc', 'Vàng'],
                'storages' => [
                    ['label' => '16GB', 'price' => 2200000],
                    ['label' => '64GB', 'price' => 2600000],
                    ['label' => '128GB', 'price' => 3000000],
                ],
            ],
            [
                'name' => 'iPhone 6s',
                'series' => 'iphone-6-series',
                'thumbnail' => '/images/products/iphone-6s.svg',
                'specifications' => [
                    'Màn hình' => '4.7 inch Retina HD, tích hợp công nghệ cảm ứng lực 3D Touch',
                    'Vi xử lý (CPU)' => 'Apple A9 (2 nhân 1.84 GHz, kiến trúc Twister)',
                    'Dung lượng RAM' => '2GB LPDDR4 (tăng gấp đôi so với iPhone 6)',
                    'Bộ nhớ trong' => '16GB / 32GB / 64GB / 128GB',
                    'Camera sau' => '12MP f/2.2, quay video 4K@30fps, tính năng chụp ảnh động Live Photos',
                    'Camera trước' => '5.0MP, tính năng Retina Flash tự sáng màn hình khi selfie',
                    'Bảo mật' => 'Touch ID thế hệ 2 mở khóa tốc độ cao chớp mắt',
                    'Chất liệu' => 'Khung hợp kim nhôm Series 7000 chống cong, màu Rose Gold mới',
                    'Trọng lượng' => '143g',
                    'Pin' => '1.715 mAh',
                ],
                'description' => <<<'DESC'
                    iPhone 6s sở hữu độ bền vượt bậc nhờ nâng cấp khung nhôm Series 7000 siêu cứng cáp cùng dung lượng RAM 2GB giúp đa nhiệm nhiều ứng dụng không giật lag. Màn hình cảm ứng lực 3D Touch và camera nâng lên 12MP quay video 4K sắc nét đem lại trải nghiệm toàn diện.

                    Nâng cấp Sức mạnh Vượt trội, Nhôm Series 7000 & Cảm ứng lực 3D Touch

                    Khung nhôm Series 7000 siêu cứng cáp và sắc màu Rose Gold thời thượng
                    Rút kinh nghiệm từ thế hệ trước, Apple nâng cấp toàn bộ khung vỏ lên hợp kim nhôm Series 7000 chuyên dụng ngành hàng không vũ trụ, mang lại độ bền cơ học chống bẻ cong hoàn hảo. Sắc màu Vàng Hồng (Rose Gold) quyến rũ đã tạo nên cơn sốt thời trang trên toàn cầu.

                    Công nghệ cảm ứng lực 3D Touch và bước nhảy vọt 2GB RAM
                    Màn hình nhận diện lực nhấn 3D Touch mở ra trải nghiệm tương tác Peek & Pop xem nhanh nội dung độc đáo. Cùng với vi xử lý A9 mạnh mẽ, dung lượng RAM 2GB và camera 12MP quay 4K sắc nét, dòng máy mang lại độ mượt mà kinh ngạc suốt nhiều năm vận hành.
                    DESC,
                'colors' => ['Xám', 'Bạc', 'Vàng hồng (Rose Gold)'],
                'storages' => [
                    ['label' => '16GB', 'price' => 2500000],
                    ['label' => '64GB', 'price' => 2900000],
                    ['label' => '128GB', 'price' => 3300000],
                ],
            ],
            [
                'name' => 'iPhone 6s Plus',
                'series' => 'iphone-6-series',
                'thumbnail' => '/images/products/iphone-6s-plus.svg',
                'specifications' => [
                    'Màn hình' => '5.5 inch Retina HD Full HD (1920 x 1080), 3D Touch',
                    'Vi xử lý (CPU)' => 'Apple A9 (2 nhân 1.84 GHz)',
                    'Dung lượng RAM' => '2GB LPDDR4',
                    'Bộ nhớ trong' => '16GB / 32GB / 64GB / 128GB',
                    'Camera sau' => '12MP f/2.2, OIS chống rung quang học, quay phim 4K',
                    'Camera trước' => '5.0MP Retina Flash',
                    'Bảo mật' => 'Touch ID thế hệ 2 siêu tốc',
                    'Chất liệu' => 'Khung nhôm Series 7000 cứng cáp, màu Rose Gold',
                    'Trọng lượng' => '192g',
                    'Pin' => '2.750 mAh',
                ],
                'description' => <<<'DESC'
                    iPhone 6s Plus là chiếc máy giải trí màn hình lớn 5.5 inch Full HD đỉnh cao với camera 12MP chống rung quang học OIS quay 4K chuyên nghiệp. Khung nhôm 7000 chắc nịch cùng thời lượng pin xuất sắc đáp ứng trọn vẹn nhu cầu cày phim và lướt mạng xã hội bền bỉ.

                    Nâng cấp Sức mạnh Vượt trội, Nhôm Series 7000 & Cảm ứng lực 3D Touch

                    Khung nhôm Series 7000 siêu cứng cáp và sắc màu Rose Gold thời thượng
                    Rút kinh nghiệm từ thế hệ trước, Apple nâng cấp toàn bộ khung vỏ lên hợp kim nhôm Series 7000 chuyên dụng ngành hàng không vũ trụ, mang lại độ bền cơ học chống bẻ cong hoàn hảo. Sắc màu Vàng Hồng (Rose Gold) quyến rũ đã tạo nên cơn sốt thời trang trên toàn cầu.

                    Công nghệ cảm ứng lực 3D Touch và bước nhảy vọt 2GB RAM
                    Màn hình nhận diện lực nhấn 3D Touch mở ra trải nghiệm tương tác Peek & Pop xem nhanh nội dung độc đáo. Cùng với vi xử lý A9 mạnh mẽ, dung lượng RAM 2GB và camera 12MP quay 4K sắc nét, dòng máy mang lại độ mượt mà kinh ngạc suốt nhiều năm vận hành.
                    DESC,
                'colors' => ['Xám', 'Bạc', 'Vàng hồng (Rose Gold)'],
                'storages' => [
                    ['label' => '16GB', 'price' => 2900000],
                    ['label' => '64GB', 'price' => 3300000],
                    ['label' => '128GB', 'price' => 3700000],
                ],
            ],

            // ---------------------------------------------------------------
            // iPhone SE Series: 2016, 2020, 2022
            // ---------------------------------------------------------------
            [
                'name' => 'iPhone SE (2016)',
                'series' => 'iphone-se-series',
                'thumbnail' => '/images/products/iphone-se-2016.svg',
                'specifications' => [
                    'Màn hình' => '4.0 inch Retina IPS LCD, 1136 x 640 pixels (326 ppi)',
                    'Vi xử lý (CPU)' => 'Apple A9 (mạnh tương đương iPhone 6s)',
                    'Dung lượng RAM' => '2GB LPDDR4',
                    'Bộ nhớ trong' => '16GB / 64GB (ban đầu); bổ sung 32GB, 128GB sau này',
                    'Camera sau' => '12MP f/2.2, quay video 4K@30fps, Live Photos',
                    'Camera trước' => '1.2MP FaceTime HD',
                    'Bảo mật' => 'Touch ID thế hệ 1 tích hợp nút Home',
                    'Chất liệu' => 'Nhôm nguyên khối viền vát kim cương, màu Rose Gold',
                    'Trọng lượng & Kích thước' => 'Siêu nhẹ 113g; 123.8 x 58.6 x 7.6 mm',
                    'Pin' => '1.624 mAh',
                ],
                'description' => <<<'DESC'
                    iPhone SE (2016) kết hợp trọn vẹn thân xác nhỏ gọn 4.0 inch thanh mảnh của 5s với cấu hình chip A9 và camera 12MP quay 4K siêu khỏe của 6s. Đây là lựa chọn máy phụ cầm tay hoàn hảo nhất với trọng lượng chỉ 113g và thời lượng pin cực kỳ ấn tượng.

                    Special Edition 2016: Thân máy Nhỏ gọn, Trái tim Flagship

                    Sự kết hợp hoàn mỹ giữa thiết kế huyền thoại và cấu hình đỉnh cao
                    iPhone SE thế hệ đầu tiên là câu trả lời của Apple dành cho những ai tôn sùng thiết kế cạnh kim cương vuông vức kinh điển của iPhone 5s nhưng đòi hỏi sức mạnh phần cứng tân tiến. Thân máy 4.0 inch siêu nhẹ 113g nằm lọt thỏm trong túi áo nhưng sở hữu sức mạnh ngang ngửa iPhone 6s.

                    Quay video 4K sắc nét và thời lượng pin tối ưu vượt bậc
                    Nhờ mang trong mình con chip Apple A9 mạnh mẽ kéo một màn hình nhỏ gọn, iPhone SE 2016 tối ưu hóa điện năng xuất sắc, cho thời gian sử dụng pin vượt xa các thế hệ tiền nhiệm cùng kích cỡ. Máy hỗ trợ trọn vẹn quay video 4K, chụp ảnh Live Photos và thanh toán Apple Pay tiện lợi.
                    DESC,
                'colors' => ['Xám', 'Bạc', 'Vàng hồng (Rose Gold)'],
                'storages' => [
                    ['label' => '16GB', 'price' => 2000000],
                    ['label' => '64GB', 'price' => 2400000],
                    ['label' => '128GB', 'price' => 2800000],
                ],
            ],

            // ---------------------------------------------------------------
            // iPhone 7 Series: 7, 7 Plus
            // ---------------------------------------------------------------
            [
                'name' => 'iPhone 7',
                'series' => 'iphone-7-series',
                'thumbnail' => '/images/products/iphone-7.svg',
                'specifications' => [
                    'Màn hình' => '4.7 inch Retina HD, dải màu rộng DCI-P3, độ sáng 625 nits',
                    'Vi xử lý (CPU)' => 'Apple A10 Fusion (4 nhân: 2 nhân hiệu năng + 2 nhân tiết kiệm điện)',
                    'Dung lượng RAM' => '2GB LPDDR4',
                    'Bộ nhớ trong' => '32GB / 128GB / 256GB',
                    'Camera sau' => '12MP f/1.8, OIS chống rung quang học, 4 đèn Flash True Tone',
                    'Camera trước' => '7.0MP f/2.2 quay Full HD',
                    'Âm thanh & Nút bấm' => 'Loa kép Stereo; Nút Home cảm ứng lực Taptic Engine',
                    'Kháng nước bụi' => 'Chuẩn IP67 (chịu nước sâu 1 mét trong 30 phút)',
                    'Trọng lượng' => '138g',
                    'Pin' => '1.960 mAh',
                ],
                'description' => <<<'DESC'
                    iPhone 7 ghi dấu bước đột phá với khả năng kháng nước chuẩn IP67, phím Home cảm ứng rung Taptic Engine bền bỉ và hệ thống loa kép sống động. Ống kính khẩu độ lớn f/1.8 hỗ trợ chống rung OIS giúp nâng cao chất lượng ảnh chụp thiếu sáng vượt trội.

                    Kỷ nguyên Camera Kép Chân dung Xóa phông & Kháng nước Chuẩn IP67

                    Chuẩn mực nhiếp ảnh chân dung xóa phông và loa kép Stereo
                    iPhone 7 Series tiên phong mang cụm camera kép với ống kính Telephoto lên điện thoại, biến tính năng chụp chân dung xóa phông (Portrait Mode) mờ hậu cảnh thành trào lưu nhiếp ảnh di động. Hệ thống loa kép Stereo sống động cùng dải màu rộng DCI-P3 nâng tầm trải nghiệm nghe nhìn lên chuẩn rạp hát.

                    Kháng nước IP67 bền bỉ và nút Home cảm ứng lực Taptic Engine
                    Loại bỏ jack 3.5mm giúp máy đạt chuẩn kháng nước, kháng bụi IP67 an toàn trước những cơn mưa bất chợt hay sự cố rơi nước. Phím Home vật lý được thay thế bằng nút cảm ứng lực phản hồi rung Taptic Engine mượt mà, loại bỏ triệt để nguy cơ liệt nút bấm truyền thống.
                    DESC,
                'colors' => ['Đen nhám', 'Bạc', '(PRODUCT)RED'],
                'storages' => [
                    ['label' => '32GB', 'price' => 3200000],
                    ['label' => '128GB', 'price' => 3700000],
                    ['label' => '256GB', 'price' => 4200000],
                ],
            ],
            [
                'name' => 'iPhone 7 Plus',
                'series' => 'iphone-7-series',
                'thumbnail' => '/images/products/iphone-7-plus.svg',
                'specifications' => [
                    'Màn hình' => '5.5 inch Retina HD Full HD (1920 x 1080), dải màu rộng DCI-P3',
                    'Vi xử lý (CPU)' => 'Apple A10 Fusion (4 nhân)',
                    'Dung lượng RAM' => '3GB LPDDR4 (đáp ứng thuật toán camera kép)',
                    'Bộ nhớ trong' => '32GB / 128GB / 256GB',
                    'Camera sau' => 'Cụm camera kép 12MP: Góc rộng f/1.8 (OIS) + Tele 2x f/2.8, chụp chân dung xóa phông',
                    'Camera trước' => '7.0MP f/2.2',
                    'Bảo mật & Phím bấm' => 'Touch ID; phím Home Taptic Engine; Kháng nước IP67',
                    'Màu sắc đặc trưng' => 'Đen nhám (Matte Black), Đen bóng (Jet Black), Đỏ (PRODUCT)RED',
                    'Trọng lượng' => '188g',
                    'Pin' => '2.900 mAh',
                ],
                'description' => <<<'DESC'
                    iPhone 7 Plus là huyền thoại mở đầu trào lưu camera kép chụp chân dung xóa phông tự nhiên và zoom quang học 2x trên smartphone cao cấp. Sở hữu 3GB RAM mượt mà, màn hình lớn 5.5 inch cùng pin 2.900 mAh trâu bò, đây là chiếc máy gắn liền với thanh xuân của hàng triệu người.

                    Kỷ nguyên Camera Kép Chân dung Xóa phông & Kháng nước Chuẩn IP67

                    Chuẩn mực nhiếp ảnh chân dung xóa phông và loa kép Stereo
                    iPhone 7 Series tiên phong mang cụm camera kép với ống kính Telephoto lên điện thoại, biến tính năng chụp chân dung xóa phông (Portrait Mode) mờ hậu cảnh thành trào lưu nhiếp ảnh di động. Hệ thống loa kép Stereo sống động cùng dải màu rộng DCI-P3 nâng tầm trải nghiệm nghe nhìn lên chuẩn rạp hát.

                    Kháng nước IP67 bền bỉ và nút Home cảm ứng lực Taptic Engine
                    Loại bỏ jack 3.5mm giúp máy đạt chuẩn kháng nước, kháng bụi IP67 an toàn trước những cơn mưa bất chợt hay sự cố rơi nước. Phím Home vật lý được thay thế bằng nút cảm ứng lực phản hồi rung Taptic Engine mượt mà, loại bỏ triệt để nguy cơ liệt nút bấm truyền thống.
                    DESC,
                'colors' => ['Đen bóng (Jet Black)', 'Bạc', '(PRODUCT)RED'],
                'storages' => [
                    ['label' => '32GB', 'price' => 3800000],
                    ['label' => '128GB', 'price' => 4300000],
                    ['label' => '256GB', 'price' => 4800000],
                ],
            ],

            // ---------------------------------------------------------------
            // iPhone 8 Series: 8, 8 Plus
            // ---------------------------------------------------------------
            [
                'name' => 'iPhone 8',
                'series' => 'iphone-8-series',
                'thumbnail' => '/images/products/iphone-8.svg',
                'specifications' => [
                    'Màn hình' => '4.7 inch Retina HD, bổ sung công nghệ hiển thị True Tone',
                    'Vi xử lý (CPU)' => 'Apple A11 Bionic (6 nhân, 10nm, có bộ xử lý mạng thần kinh)',
                    'Dung lượng RAM' => '2GB',
                    'Bộ nhớ trong' => '64GB / 128GB / 256GB',
                    'Camera sau' => '12MP f/1.8, OIS, quay video 4K@60fps và Slow-motion 1080p@240fps',
                    'Camera trước' => '7.0MP f/2.2',
                    'Sạc & Pin' => '1.821 mAh; hỗ trợ sạc nhanh PD 15W và sạc không dây chuẩn Qi',
                    'Chất liệu' => 'Mặt lưng kính cường lực bóng bẩy phối viền nhôm hàng không',
                    'Trọng lượng' => '148g',
                    'Kháng nước' => 'Chuẩn IP67',
                ],
                'description' => <<<'DESC'
                    iPhone 8 hoàn thiện phong cách thiết kế nút Home cổ điển với mặt lưng kính cường lực bóng bẩy hỗ trợ sạc không dây Qi tiện ích. Sức mạnh từ chip A11 Bionic cho phép quay phim 4K 60fps mượt mà cùng màn hình True Tone bảo vệ mắt tối đa.

                    Thiết kế Hai mặt Kính Hỗ trợ Sạc không dây & Màn hình True Tone

                    Mặt lưng kính cường lực bóng bẩy và công nghệ sạc không dây Qi
                    iPhone 8 Series đánh dấu sự trở lại của thiết kế mặt lưng kính cường lực cao cấp, vừa mang lại diện mạo bóng bẩy sang trọng, vừa mở đường cho công nghệ sạc không dây chuẩn Qi tiện lợi. Khung viền nhôm hàng không phối hợp mặt kính tạo nên cảm giác cầm đầm chắc và cứng cáp.

                    Màn hình True Tone dịu mắt và vi xử lý A11 Bionic tích hợp AI
                    Công nghệ True Tone tự động điều chỉnh nhiệt độ màu theo ánh sáng môi trường xung quanh, giúp bảo vệ thị lực và mang lại trải nghiệm thị giác dễ chịu nhất. Con chip A11 Bionic với mạng thần kinh Neural Engine mang lại khả năng quay video 4K 60fps mượt mà chưa từng có.
                    DESC,
                'colors' => ['Xám', 'Bạc', 'Vàng'],
                'storages' => [
                    ['label' => '64GB', 'price' => 3500000],
                    ['label' => '128GB', 'price' => 4000000],
                    ['label' => '256GB', 'price' => 4500000],
                ],
            ],
            [
                'name' => 'iPhone 8 Plus',
                'series' => 'iphone-8-series',
                'thumbnail' => '/images/products/iphone-8-plus.svg',
                'specifications' => [
                    'Màn hình' => '5.5 inch Retina HD Full HD, công nghệ True Tone',
                    'Vi xử lý (CPU)' => 'Apple A11 Bionic (6 nhân 10nm)',
                    'Dung lượng RAM' => '3GB',
                    'Bộ nhớ trong' => '64GB / 128GB / 256GB',
                    'Camera sau' => 'Kép 12MP (Chính f/1.8 + Tele 2x f/2.8), chế độ ánh sáng chân dung Portrait Lighting',
                    'Camera trước' => '7.0MP f/2.2',
                    'Sạc & Pin' => '2.691 mAh; sạc nhanh 15W PD, sạc không dây Qi',
                    'Chất liệu' => 'Mặt lưng kính bóng phối viền nhôm, chuẩn kháng nước IP67',
                    'Trọng lượng' => '202g (đầm tay chắc nịch)',
                    'Bảo mật' => 'Touch ID tích hợp nút Home Taptic Engine',
                ],
                'description' => <<<'DESC'
                    iPhone 8 Plus là đỉnh cao cuối cùng của thiết kế nút Home phím cứng, trang bị camera kép với hiệu ứng ánh sáng Portrait Lighting chuẩn studio. Chip A11 Bionic cực khỏe cùng màn hình 5.5 inch tỉ lệ 16:9 biến máy thành công cụ chiến game MOBA cực kỳ ổn định và chuẩn xác.

                    Thiết kế Hai mặt Kính Hỗ trợ Sạc không dây & Màn hình True Tone

                    Mặt lưng kính cường lực bóng bẩy và công nghệ sạc không dây Qi
                    iPhone 8 Series đánh dấu sự trở lại của thiết kế mặt lưng kính cường lực cao cấp, vừa mang lại diện mạo bóng bẩy sang trọng, vừa mở đường cho công nghệ sạc không dây chuẩn Qi tiện lợi. Khung viền nhôm hàng không phối hợp mặt kính tạo nên cảm giác cầm đầm chắc và cứng cáp.

                    Màn hình True Tone dịu mắt và vi xử lý A11 Bionic tích hợp AI
                    Công nghệ True Tone tự động điều chỉnh nhiệt độ màu theo ánh sáng môi trường xung quanh, giúp bảo vệ thị lực và mang lại trải nghiệm thị giác dễ chịu nhất. Con chip A11 Bionic với mạng thần kinh Neural Engine mang lại khả năng quay video 4K 60fps mượt mà chưa từng có.
                    DESC,
                'colors' => ['Xám', 'Bạc', 'Vàng'],
                'storages' => [
                    ['label' => '64GB', 'price' => 4200000],
                    ['label' => '128GB', 'price' => 4700000],
                    ['label' => '256GB', 'price' => 5200000],
                ],
            ],

            // ---------------------------------------------------------------
            // iPhone X Series: X, XR, XS, XS Max
            // ---------------------------------------------------------------
            [
                'name' => 'iPhone X',
                'series' => 'iphone-x-series',
                'thumbnail' => '/images/products/iphone-x.svg',
                'specifications' => [
                    'Màn hình' => '5.8 inch Super Retina OLED, 2436 x 1125 pixels (458 ppi), HDR10, Dolby Vision',
                    'Vi xử lý (CPU)' => 'Apple A11 Bionic (6 nhân, 10nm)',
                    'Dung lượng RAM' => '3GB',
                    'Bộ nhớ trong' => '64GB / 256GB',
                    'Camera sau' => 'Kép 12MP (Góc rộng f/1.8 + Tele 2x f/2.4), OIS kép trên cả 2 ống kính',
                    'Camera trước & Cảm biến' => '7MP TrueDepth, hệ thống cảm biến 3D nhận diện khuôn mặt Face ID',
                    'Chất liệu chế tác' => 'Khung thép không gỉ bóng loáng chuyên dụng y tế, 2 mặt kính cường lực',
                    'Sạc & Pin' => '2.716 mAh (2 cell chữ L); sạc nhanh 18W, sạc không dây Qi',
                    'Trọng lượng' => '174g',
                    'Kháng nước' => 'Chuẩn IP67',
                ],
                'description' => <<<'DESC'
                    iPhone X tạo nên cuộc đại cách mạng công nghệ khi khai tử nút Home để mở ra màn hình OLED tai thỏ tràn viền và công nghệ bảo mật khuôn mặt 3D Face ID dẫn đầu thế giới. Khung viền thép sáng bóng cùng cụm camera kép chống rung OIS kép tạo nên dấu ấn sang trọng trường tồn.

                    Kỷ nguyên Tràn viền Đột phá: Face ID và Thao tác Vuốt Cử chỉ

                    Cuộc cách mạng xóa sổ nút Home và màn hình OLED Super Retina
                    Kỷ niệm 10 năm iPhone, iPhone X định hình lại tương lai điện thoại thông minh với màn hình Super Retina OLED tràn viền tuyệt đẹp, tối ưu không gian hiển thị tối đa trong thân máy vừa vặn. Khung viền thép không gỉ bóng loáng chuẩn y tế tôn lên vẻ đẹp kiêu sa và đẳng cấp độc bản.

                    Bảo mật khuôn mặt 3D Face ID và thao tác vuốt vuốt điều hướng chuẩn mực
                    Cụm camera TrueDepth tiên tiến quét hàng chục nghìn điểm vô hình để nhận diện khuôn mặt 3D siêu bảo mật ngay cả trong bóng tối. Hệ thống thao tác vuốt cử chỉ trực quan hoàn toàn thay thế nút Home vật lý, tạo ra trải nghiệm sử dụng liền mạch không góc chết.
                    DESC,
                'colors' => ['Xám', 'Bạc'],
                'storages' => [
                    ['label' => '64GB', 'price' => 4500000],
                    ['label' => '256GB', 'price' => 5200000],
                ],
            ],
            [
                'name' => 'iPhone XR',
                'series' => 'iphone-x-series',
                'thumbnail' => '/images/products/iphone-xr.svg',
                'specifications' => [
                    'Màn hình' => '6.1 inch Liquid Retina (IPS LCD), 1792 x 828 pixels (326 ppi), True Tone',
                    'Vi xử lý (CPU)' => 'Apple A12 Bionic (tiến trình 7nm đầu tiên thế giới)',
                    'Dung lượng RAM' => '3GB',
                    'Bộ nhớ trong' => '64GB / 128GB / 256GB',
                    'Camera sau' => 'Đơn 12MP f/1.8, OIS, Smart HDR, xóa phông thuật toán cao cấp',
                    'Camera trước' => '7MP TrueDepth với nhận diện Face ID',
                    'Kết nối SIM' => 'Hỗ trợ 1 eSIM + 1 Nano SIM vật lý tiện dụng',
                    'Chất liệu & Màu sắc' => 'Khung nhôm Series 7000; 6 màu sắc: Đen, Trắng, Xanh dương, Vàng, Cam San Hô, Đỏ',
                    'Trọng lượng & Pin' => '194g; 2.942 mAh (thời lượng pin vượt trội thế hệ)',
                    'Kháng nước' => 'Chuẩn IP67',
                ],
                'description' => <<<'DESC'
                    iPhone XR từng là chiếc điện thoại bán chạy nhất toàn cầu nhờ sự hòa trộn hoàn hảo giữa thời lượng pin cực trâu, vi xử lý A12 Bionic mạnh mẽ và bộ sưu tập 6 sắc màu thời trang bắt mắt. Máy hỗ trợ Face ID và thuật toán xóa phông thông minh trên ống kính đơn cực kỳ ấn tượng.

                    Kỷ nguyên Chip 7nm A12 Bionic, Màn hình Max Kích thước & Đa sắc màu

                    Kỷ nguyên chip tiến trình 7nm A12 Bionic và chuẩn màn hình khổng lồ
                    Thế hệ 2018 mang đến bước nhảy vọt hiệu năng với chip Apple A12 Bionic sản xuất trên tiến trình 7nm đầu tiên trên thế giới, tối ưu khả năng học máy Neural Engine thông minh. Mẫu XS Max lần đầu mang đến không gian hiển thị cực đại 6.5 inch OLED tuyệt mỹ cho người dùng đam mê màn hình lớn.

                    Công nghệ Smart HDR nhiếp ảnh và tính năng hỗ trợ eSIM đa sim
                    Thuật toán Smart HDR kết hợp nhiều khung hình phơi sáng để cứu sáng vùng tối và giữ chi tiết vùng cháy sáng xuất sắc. Toàn bộ dải sản phẩm đều hỗ trợ công nghệ 2 SIM (kết hợp eSIM linh hoạt), đáp ứng hoàn hảo nhu cầu liên lạc song song cho công việc và đời sống.
                    DESC,
                'colors' => ['Đen', 'Trắng', 'Đỏ'],
                'storages' => [
                    ['label' => '64GB', 'price' => 4000000],
                    ['label' => '128GB', 'price' => 4500000],
                    ['label' => '256GB', 'price' => 5000000],
                ],
            ],
            [
                'name' => 'iPhone XS',
                'series' => 'iphone-x-series',
                'thumbnail' => '/images/products/iphone-xs.svg',
                'specifications' => [
                    'Màn hình' => '5.8 inch Super Retina OLED, 2436 x 1125 pixels, HDR10, Dolby Vision',
                    'Vi xử lý (CPU)' => 'Apple A12 Bionic (7nm, 6 nhân CPU, 4 nhân GPU)',
                    'Dung lượng RAM' => '4GB (nâng cấp đa nhiệm mượt mà)',
                    'Bộ nhớ trong' => '64GB / 256GB / 512GB',
                    'Camera sau' => 'Kép 12MP (Chính f/1.8 + Tele 2x f/2.4), OIS kép, Smart HDR',
                    'Camera trước' => '7MP TrueDepth, Face ID tốc độ cao',
                    'SIM & Chống nước' => 'Hỗ trợ eSIM; Chuẩn kháng nước nâng cấp IP68 (2 mét trong 30 phút)',
                    'Chất liệu' => 'Khung thép không gỉ sáng bóng, mặt kính cường lực bền nhất thời bấy giờ',
                    'Trọng lượng & Pin' => '177g; 2.658 mAh; sạc nhanh 18W',
                    'Màu sắc' => 'Xám không gian, Bạc, Vàng Gold mạ PVD bóng bẩy',
                ],
                'description' => <<<'DESC'
                    iPhone XS sở hữu kích cỡ 5.8 inch hoàn hảo cho những bàn tay yêu thích sự gọn gàng nhưng vẫn đòi hỏi chất liệu khung thép mạ vàng PVD đẳng cấp và màn hình OLED rực rỡ. Chip A12 Bionic cùng 4GB RAM bảo chứng cho trải nghiệm mượt mà suốt nhiều năm vận hành.

                    Kỷ nguyên Chip 7nm A12 Bionic, Màn hình Max Kích thước & Đa sắc màu

                    Kỷ nguyên chip tiến trình 7nm A12 Bionic và chuẩn màn hình khổng lồ
                    Thế hệ 2018 mang đến bước nhảy vọt hiệu năng với chip Apple A12 Bionic sản xuất trên tiến trình 7nm đầu tiên trên thế giới, tối ưu khả năng học máy Neural Engine thông minh. Mẫu XS Max lần đầu mang đến không gian hiển thị cực đại 6.5 inch OLED tuyệt mỹ cho người dùng đam mê màn hình lớn.

                    Công nghệ Smart HDR nhiếp ảnh và tính năng hỗ trợ eSIM đa sim
                    Thuật toán Smart HDR kết hợp nhiều khung hình phơi sáng để cứu sáng vùng tối và giữ chi tiết vùng cháy sáng xuất sắc. Toàn bộ dải sản phẩm đều hỗ trợ công nghệ 2 SIM (kết hợp eSIM linh hoạt), đáp ứng hoàn hảo nhu cầu liên lạc song song cho công việc và đời sống.
                    DESC,
                'colors' => ['Xám', 'Bạc', 'Vàng'],
                'storages' => [
                    ['label' => '64GB', 'price' => 4800000],
                    ['label' => '256GB', 'price' => 5400000],
                    ['label' => '512GB', 'price' => 6000000],
                ],
            ],
            [
                'name' => 'iPhone XS Max',
                'series' => 'iphone-x-series',
                'thumbnail' => '/images/products/iphone-xs-max.svg',
                'specifications' => [
                    'Màn hình' => '6.5 inch Super Retina OLED khổng lồ, 2688 x 1242 pixels (458 ppi)',
                    'Vi xử lý (CPU)' => 'Apple A12 Bionic (7nm)',
                    'Dung lượng RAM' => '4GB',
                    'Bộ nhớ trong' => '64GB / 256GB / 512GB',
                    'Camera sau' => 'Kép 12MP OIS kép, Smart HDR tái tạo vùng sáng tối ấn tượng',
                    'Camera trước' => '7MP TrueDepth, Face ID',
                    'Chất liệu & Chuẩn bền' => 'Khung thép không gỉ, mặt kính bền bỉ, chuẩn kháng nước IP68',
                    'Kết nối SIM' => 'Hỗ trợ eSIM (phiên bản HK/TQ hỗ trợ 2 SIM vật lý 2 mặt)',
                    'Trọng lượng & Pin' => '208g; 3.174 mAh (pin khỏe xem phim thoải mái)',
                    'Sạc' => 'Sạc nhanh 18W, sạc không dây Qi',
                ],
                'description' => <<<'DESC'
                    iPhone XS Max là chiếc iPhone màn hình lớn 6.5 inch OLED đầu tiên của Apple, mang lại trải nghiệm xem phim và hiển thị đồ họa vô cùng mãn nhãn. Kết hợp cùng khung thép không gỉ bóng loáng và thời lượng pin dồi dào, đây luôn là mẫu máy giữ vững giá trị sử dụng lâu dài.

                    Kỷ nguyên Chip 7nm A12 Bionic, Màn hình Max Kích thước & Đa sắc màu

                    Kỷ nguyên chip tiến trình 7nm A12 Bionic và chuẩn màn hình khổng lồ
                    Thế hệ 2018 mang đến bước nhảy vọt hiệu năng với chip Apple A12 Bionic sản xuất trên tiến trình 7nm đầu tiên trên thế giới, tối ưu khả năng học máy Neural Engine thông minh. Mẫu XS Max lần đầu mang đến không gian hiển thị cực đại 6.5 inch OLED tuyệt mỹ cho người dùng đam mê màn hình lớn.

                    Công nghệ Smart HDR nhiếp ảnh và tính năng hỗ trợ eSIM đa sim
                    Thuật toán Smart HDR kết hợp nhiều khung hình phơi sáng để cứu sáng vùng tối và giữ chi tiết vùng cháy sáng xuất sắc. Toàn bộ dải sản phẩm đều hỗ trợ công nghệ 2 SIM (kết hợp eSIM linh hoạt), đáp ứng hoàn hảo nhu cầu liên lạc song song cho công việc và đời sống.
                    DESC,
                'colors' => ['Xám', 'Bạc', 'Vàng'],
                'storages' => [
                    ['label' => '64GB', 'price' => 5500000],
                    ['label' => '256GB', 'price' => 6200000],
                    ['label' => '512GB', 'price' => 6900000],
                ],
            ],

            // ---------------------------------------------------------------
            // iPhone 11 Series: 11, 11 Pro, 11 Pro Max
            // ---------------------------------------------------------------
            [
                'name' => 'iPhone 11',
                'series' => 'iphone-11-series',
                'thumbnail' => '/images/products/iphone-11.svg',
                'specifications' => [
                    'Màn hình' => '6.1 inch Liquid Retina IPS LCD, 1792 x 828 pixels, True Tone',
                    'Vi xử lý (CPU)' => 'Apple A13 Bionic (tiến trình 7nm+ thế hệ 2)',
                    'Dung lượng RAM' => '4GB',
                    'Bộ nhớ trong' => '64GB / 128GB / 256GB',
                    'Camera sau' => 'Kép 12MP: Chính f/1.8 (OIS) + Góc siêu rộng 12MP f/2.4 (120°), Chế độ chụp đêm Night Mode',
                    'Camera trước' => '12MP TrueDepth quay video 4K@60fps, hỗ trợ quay chậm Slofie',
                    'Chất liệu & Màu sắc' => 'Khung nhôm, lưng kính bóng; 6 màu: Đen, Trắng, Tím, Xanh bơ, Vàng, Đỏ',
                    'Trọng lượng & Pin' => '194g; 3.110 mAh (thời lượng pin dùng thoải mái trọn ngày)',
                    'Sạc & Kháng nước' => 'Sạc nhanh 18W, sạc không dây Qi; Chuẩn kháng nước IP68 (2 mét trong 30 phút)',
                ],
                'description' => <<<'DESC'
                    iPhone 11 được mệnh danh là "chiếc máy quốc dân" với hiệu năng mạnh mẽ từ chip A13 Bionic, camera góc siêu rộng bắt trọn đại cảnh và chế độ chụp đêm Night Mode xuất sắc. Viên pin 3.110 mAh bền bỉ kết hợp bảng màu sắc trẻ trung làm hài lòng mọi người dùng.

                    Kỷ nguyên Ba Mắt Pro, Chế độ Chụp đêm Night Mode & Pin Đột phá

                    Hệ thống ba camera đẳng cấp chuyên nghiệp và Chế độ Chụp đêm Night Mode
                    iPhone 11 Series khẳng định vị thế dẫn đầu nhiếp ảnh di động với cụm 3 camera "tam giác" độc đáo gồm camera góc siêu rộng 120 độ, góc rộng và tele. Tính năng Night Mode tự động kích hoạt giúp thu sáng ngoạn mục trong bóng tối, cho ra những bức ảnh rõ nét, trong trẻo và giàu chi tiết.

                    Mặt lưng kính nhám sang trọng và bước nhảy vọt về thời lượng pin
                    Chất liệu kính mờ nhám chống bám vân tay cao cấp lần đầu ra mắt trên dòng Pro phối hợp viền thép tạo nên sự quý phái. Đặc biệt, thời lượng pin được tăng cường ngoạn mục tới hơn 4-5 tiếng so với thế hệ trước, biến chiếc máy thành công cụ làm việc bền bỉ suốt ngày dài cường độ cao.
                    DESC,
                'colors' => ['Đen', 'Trắng', 'Tím'],
                'storages' => [
                    ['label' => '64GB', 'price' => 6000000],
                    ['label' => '128GB', 'price' => 6800000],
                    ['label' => '256GB', 'price' => 7600000],
                ],
            ],
            [
                'name' => 'iPhone 11 Pro',
                'series' => 'iphone-11-series',
                'thumbnail' => '/images/products/iphone-11-pro.svg',
                'specifications' => [
                    'Màn hình' => '5.8 inch Super Retina XDR OLED, độ sáng 800 nits (tối đa 1.200 nits)',
                    'Vi xử lý (CPU)' => 'Apple A13 Bionic (7nm+)',
                    'Dung lượng RAM' => '4GB',
                    'Bộ nhớ trong' => '64GB / 256GB / 512GB',
                    'Camera sau' => '3 camera 12MP: Chính f/1.8 (OIS) + Siêu rộng 120° + Tele 2x f/2.0 (OIS), Deep Fusion & Night Mode',
                    'Camera trước' => '12MP TrueDepth 4K@60fps',
                    'Chất liệu chế tác' => 'Khung viền thép không gỉ, mặt sau kính mờ nhám chống bám vân tay',
                    'Phụ kiện zin hộp' => 'Tặng kèm củ sạc nhanh Type-C 18W và cáp C to Lightning',
                    'Trọng lượng & Pin' => '188g; 3.046 mAh',
                    'Kháng nước' => 'Chuẩn IP68 (chịu nước sâu tới 4 mét trong 30 phút)',
                ],
                'description' => <<<'DESC'
                    iPhone 11 Pro là chiếc máy nhỏ gọn 5.8 inch đầu tiên được mang định danh "Pro" với cụm 3 camera bếp từ trứ danh và mặt kính lưng nhám mờ sang trọng bậc nhất. Màn hình Super Retina XDR siêu sáng cùng khả năng quay chụp chuyên nghiệp tạo nên đẳng cấp khác biệt.

                    Kỷ nguyên Ba Mắt Pro, Chế độ Chụp đêm Night Mode & Pin Đột phá

                    Hệ thống ba camera đẳng cấp chuyên nghiệp và Chế độ Chụp đêm Night Mode
                    iPhone 11 Series khẳng định vị thế dẫn đầu nhiếp ảnh di động với cụm 3 camera "tam giác" độc đáo gồm camera góc siêu rộng 120 độ, góc rộng và tele. Tính năng Night Mode tự động kích hoạt giúp thu sáng ngoạn mục trong bóng tối, cho ra những bức ảnh rõ nét, trong trẻo và giàu chi tiết.

                    Mặt lưng kính nhám sang trọng và bước nhảy vọt về thời lượng pin
                    Chất liệu kính mờ nhám chống bám vân tay cao cấp lần đầu ra mắt trên dòng Pro phối hợp viền thép tạo nên sự quý phái. Đặc biệt, thời lượng pin được tăng cường ngoạn mục tới hơn 4-5 tiếng so với thế hệ trước, biến chiếc máy thành công cụ làm việc bền bỉ suốt ngày dài cường độ cao.
                    DESC,
                'colors' => ['Midnight Green', 'Xám', 'Vàng'],
                'storages' => [
                    ['label' => '64GB', 'price' => 7500000],
                    ['label' => '256GB', 'price' => 8500000],
                    ['label' => '512GB', 'price' => 9500000],
                ],
            ],
            [
                'name' => 'iPhone 11 Pro Max',
                'series' => 'iphone-11-series',
                'thumbnail' => '/images/products/iphone-11-pro-max.svg',
                'specifications' => [
                    'Màn hình' => '6.5 inch Super Retina XDR OLED, 2688 x 1242 pixels, 1.200 nits',
                    'Vi xử lý (CPU)' => 'Apple A13 Bionic (7nm+)',
                    'Dung lượng RAM' => '4GB',
                    'Bộ nhớ trong' => '64GB / 256GB / 512GB',
                    'Camera sau' => '3 camera 12MP (Chính + Siêu rộng + Tele 2x), chụp đêm Night Mode, thuật toán Deep Fusion',
                    'Camera trước' => '12MP TrueDepth 4K',
                    'Chất liệu & Màu sắc' => 'Khung thép bóng, kính nhám mờ; Màu Xanh Midnight Green biểu tượng',
                    'Pin & Sạc' => '3.969 mAh (thời lượng pin tăng kỷ lục 5 tiếng); tặng kèm củ sạc nhanh 18W',
                    'Trọng lượng' => '226g (đầm tay vững chãi)',
                    'Kháng nước' => 'Chuẩn IP68 (4 mét trong 30 phút)',
                ],
                'description' => <<<'DESC'
                    iPhone 11 Pro Max ghi dấu ấn đậm nét với bước nhảy vọt về thời lượng pin khổng lồ 3.969 mAh cho phép dùng liên tục sang ngày thứ hai mà không cần sạc. Màu xanh bóng đêm Midnight Green cùng cụm 3 camera chụp đêm đỉnh cao biến chiếc máy thành biểu tượng đẳng cấp.

                    Kỷ nguyên Ba Mắt Pro, Chế độ Chụp đêm Night Mode & Pin Đột phá

                    Hệ thống ba camera đẳng cấp chuyên nghiệp và Chế độ Chụp đêm Night Mode
                    iPhone 11 Series khẳng định vị thế dẫn đầu nhiếp ảnh di động với cụm 3 camera "tam giác" độc đáo gồm camera góc siêu rộng 120 độ, góc rộng và tele. Tính năng Night Mode tự động kích hoạt giúp thu sáng ngoạn mục trong bóng tối, cho ra những bức ảnh rõ nét, trong trẻo và giàu chi tiết.

                    Mặt lưng kính nhám sang trọng và bước nhảy vọt về thời lượng pin
                    Chất liệu kính mờ nhám chống bám vân tay cao cấp lần đầu ra mắt trên dòng Pro phối hợp viền thép tạo nên sự quý phái. Đặc biệt, thời lượng pin được tăng cường ngoạn mục tới hơn 4-5 tiếng so với thế hệ trước, biến chiếc máy thành công cụ làm việc bền bỉ suốt ngày dài cường độ cao.
                    DESC,
                'colors' => ['Midnight Green', 'Xám', 'Vàng'],
                'storages' => [
                    ['label' => '64GB', 'price' => 8500000],
                    ['label' => '256GB', 'price' => 9500000],
                    ['label' => '512GB', 'price' => 10500000],
                ],
            ],

            // ---------------------------------------------------------------
            // iPhone SE Series: 2020, 2022
            // ---------------------------------------------------------------
            [
                'name' => 'iPhone SE 2 (2020)',
                'series' => 'iphone-se-series',
                'thumbnail' => '/images/products/iphone-se-2-2020.svg',
                'specifications' => [
                    'Màn hình' => '4.7 inch Retina HD (IPS LCD), 1334 x 750 pixels, True Tone',
                    'Vi xử lý (CPU)' => 'Apple A13 Bionic (ngang ngửa dòng iPhone 11)',
                    'Dung lượng RAM' => '3GB',
                    'Bộ nhớ trong' => '64GB / 128GB / 256GB',
                    'Camera sau' => '12MP f/1.8, OIS, Smart HDR 2, quay video 4K@60fps',
                    'Camera trước' => '7MP f/2.2',
                    'Bảo mật sinh trắc' => 'Cảm biến vân tay Touch ID tích hợp nút Home Taptic Engine',
                    'Sạc & Pin' => '1.821 mAh; hỗ trợ sạc nhanh 18W, sạc không dây Qi',
                    'Trọng lượng' => '148g',
                    'Kháng nước' => 'Chuẩn IP67',
                ],
                'description' => <<<'DESC'
                    iPhone SE 2020 mang trái tim chip A13 Bionic mạnh mẽ vào trong thiết kế nút Home 4.7 inch thân thuộc, giúp bạn xử lý mượt mà mọi tác vụ thường ngày với mức chi phí tối ưu nhất. Thao tác vân tay một chạm Touch ID mang lại sự an tâm tuyệt đối khi ra đường đeo khẩu trang.

                    Dòng iPhone SE Giá tốt: Thiết kế Cổ điển, Sức mạnh Flagship

                    Sức mạnh vượt tầm giá trong thân máy nhỏ gọn thân thuộc
                    Dòng iPhone SE (2020 & 2022) giữ trọn thiết kế phím Home Touch ID truyền thống với kích thước 4.7 inch bỏ túi dễ dàng, nhưng mang trong mình trái tim vi xử lý cao cấp ngang ngửa các dòng iPhone đầu bảng cùng thời (A13/A15 Bionic).

                    Trải nghiệm mượt mà lâu dài, bổ sung kết nối 5G tốc độ cao
                    Với khả năng tối ưu hóa phần mềm độc quyền từ Apple, máy đảm bảo độ mượt mà ổn định suốt nhiều năm sử dụng, chơi game mượt mà và xử lý ảnh chụp sắc nét với Smart HDR. Thế hệ SE 3 còn nâng cấp mạng 5G siêu tốc và cải thiện thời lượng pin đáng kể.
                    DESC,
                'colors' => ['Đen', 'Trắng', 'Đỏ'],
                'storages' => [
                    ['label' => '64GB', 'price' => 3300000],
                    ['label' => '128GB', 'price' => 3800000],
                    ['label' => '256GB', 'price' => 4300000],
                ],
            ],

            // ---------------------------------------------------------------
            // iPhone 12 Series: mini, 12, Pro, Pro Max
            // ---------------------------------------------------------------
            [
                'name' => 'iPhone 12 mini',
                'series' => 'iphone-12-series',
                'thumbnail' => '/images/products/iphone-12-mini.svg',
                'specifications' => [
                    'Màn hình' => '5.4 inch Super Retina XDR OLED, 2340 x 1080 pixels (476 ppi siêu nét)',
                    'Vi xử lý (CPU)' => 'Apple A14 Bionic (tiến trình 5nm đầu tiên thế giới)',
                    'Dung lượng RAM' => '4GB',
                    'Bộ nhớ trong' => '64GB / 128GB / 256GB',
                    'Camera sau' => 'Kép 12MP: Chính f/1.6 (khẩu độ lớn thu sáng tốt) + Góc siêu rộng 12MP',
                    'Camera trước' => '12MP TrueDepth, hỗ trợ chụp đêm Night Mode cho camera selfie',
                    'Kết nối & Sạc' => 'Hỗ trợ 5G; Sạc không dây MagSafe 12W, sạc nhanh 20W',
                    'Chất liệu & Kính' => 'Khung nhôm viền phẳng, mặt kính gốm Ceramic Shield chống va đập gấp 4 lần',
                    'Trọng lượng & Kích cỡ' => 'Siêu nhẹ 135g; cực kỳ nhỏ gọn lọt lòng bàn tay',
                    'Pin & Kháng nước' => '2.227 mAh; Chuẩn kháng nước IP68 (sâu 6 mét trong 30 phút)',
                ],
                'description' => <<<'DESC'
                    iPhone 12 mini là chiếc smartphone 5G mỏng nhẹ nhất thế giới với màn hình OLED 5.4 inch sắc nét tuyệt đỉnh gói gọn trong trọng lượng chỉ 135g. Đây là chân ái đích thực cho những ai tìm kiếm sự tiện lợi tối đa khi bỏ túi di chuyển nhưng vẫn muốn hiệu năng chip A14 đỉnh cao.

                    Kỷ nguyên Cạnh vuông Phẳng phiu, Mạng 5G & Hệ sinh thái Sạc MagSafe

                    Ngôn ngữ thiết kế viền phẳng hiện đại và kính gốm Ceramic Shield siêu bền
                    iPhone 12 Series làm sống lại ngôn ngữ thiết kế cạnh phẳng vuông vức mạnh mẽ, kết hợp cùng mặt kính phủ tinh thể gốm Ceramic Shield tăng khả năng chịu lực va đập gấp 4 lần. Toàn bộ dải sản phẩm đều được trang bị tấm nền OLED Super Retina XDR rực rỡ với độ tương phản tuyệt đối.

                    Tiên phong kết nối 5G siêu tốc và sạc hít nam châm MagSafe tiện dụng
                    Đây là thế hệ iPhone đầu tiên hỗ trợ băng tần mạng 5G tốc độ cao, cho phép tải phim, gọi video chất lượng 4K tức thì. Vòng nam châm MagSafe tích hợp ở mặt lưng mở ra một hệ sinh thái phụ kiện thông minh: từ đế sạc hít tự căn chỉnh, ví da cao cấp đến giá đỡ xe hơi tiện lợi.
                    DESC,
                'colors' => ['Đen', 'Trắng', 'Xanh dương'],
                'storages' => [
                    ['label' => '64GB', 'price' => 7000000],
                    ['label' => '128GB', 'price' => 8000000],
                    ['label' => '256GB', 'price' => 9000000],
                ],
            ],
            [
                'name' => 'iPhone 12',
                'series' => 'iphone-12-series',
                'thumbnail' => '/images/products/iphone-12.svg',
                'specifications' => [
                    'Màn hình' => '6.1 inch Super Retina XDR OLED, 2532 x 1170 pixels, HDR10, Dolby Vision',
                    'Vi xử lý (CPU)' => 'Apple A14 Bionic (5nm, 6 nhân CPU, 4 nhân GPU)',
                    'Dung lượng RAM' => '4GB',
                    'Bộ nhớ trong' => '64GB / 128GB / 256GB',
                    'Camera sau' => 'Kép 12MP: Chính f/1.6 (OIS) + Góc siêu rộng 120°, Night Mode trên tất cả các ống kính',
                    'Camera trước' => '12MP TrueDepth, Face ID',
                    'Kết nối & Sạc' => 'Mạng 5G siêu tốc; Sạc nam châm MagSafe 15W, sạc dây nhanh 20W',
                    'Chất liệu' => 'Khung nhôm viền phẳng vuông vức, kính Ceramic Shield siêu bền',
                    'Trọng lượng & Pin' => '164g; 2.815 mAh',
                    'Kháng nước' => 'Chuẩn IP68 (6 mét trong 30 phút)',
                ],
                'description' => <<<'DESC'
                    iPhone 12 đánh dấu cuộc cách mạng toàn diện với thiết kế cạnh vuông thời thượng, nâng cấp màn hình lên tấm nền OLED Super Retina XDR rực rỡ và bổ sung kết nối 5G cùng sạc MagSafe tiện ích. Trọng lượng nhẹ chỉ 164g mang lại cảm giác cầm sử dụng hàng ngày cực kỳ thoải mái.

                    Kỷ nguyên Cạnh vuông Phẳng phiu, Mạng 5G & Hệ sinh thái Sạc MagSafe

                    Ngôn ngữ thiết kế viền phẳng hiện đại và kính gốm Ceramic Shield siêu bền
                    iPhone 12 Series làm sống lại ngôn ngữ thiết kế cạnh phẳng vuông vức mạnh mẽ, kết hợp cùng mặt kính phủ tinh thể gốm Ceramic Shield tăng khả năng chịu lực va đập gấp 4 lần. Toàn bộ dải sản phẩm đều được trang bị tấm nền OLED Super Retina XDR rực rỡ với độ tương phản tuyệt đối.

                    Tiên phong kết nối 5G siêu tốc và sạc hít nam châm MagSafe tiện dụng
                    Đây là thế hệ iPhone đầu tiên hỗ trợ băng tần mạng 5G tốc độ cao, cho phép tải phim, gọi video chất lượng 4K tức thì. Vòng nam châm MagSafe tích hợp ở mặt lưng mở ra một hệ sinh thái phụ kiện thông minh: từ đế sạc hít tự căn chỉnh, ví da cao cấp đến giá đỡ xe hơi tiện lợi.
                    DESC,
                'colors' => ['Đen', 'Trắng', 'Xanh dương'],
                'storages' => [
                    ['label' => '64GB', 'price' => 5200000],
                    ['label' => '128GB', 'price' => 9200000],
                    ['label' => '256GB', 'price' => 10400000],
                ],
            ],
            [
                'name' => 'iPhone 12 Pro',
                'series' => 'iphone-12-series',
                'thumbnail' => '/images/products/iphone-12-pro.svg',
                'specifications' => [
                    'Màn hình' => '6.1 inch Super Retina XDR OLED, 800 nits (tối đa 1.200 nits)',
                    'Vi xử lý (CPU)' => 'Apple A14 Bionic (5nm)',
                    'Dung lượng RAM' => '6GB (nâng cấp mượt mà đa nhiệm)',
                    'Bộ nhớ trong' => '128GB / 256GB / 512GB (khởi điểm 128GB)',
                    'Camera sau' => '3 camera 12MP: Chính f/1.6 + Siêu rộng + Tele 2x (OIS), Cảm biến LiDAR đo chiều sâu, Apple ProRAW',
                    'Camera trước' => '12MP TrueDepth',
                    'Chất liệu' => 'Khung viền thép không gỉ sáng bóng, mặt lưng kính nhám mờ, kính Ceramic Shield',
                    'Màu sắc đặc trưng' => 'Xanh Thái Bình Dương (Pacific Blue), Than chì, Vàng Gold, Bạc',
                    'Trọng lượng & Pin' => '189g; 2.815 mAh; sạc nhanh 20W, MagSafe 15W',
                    'Kháng nước' => 'Chuẩn IP68 (6 mét trong 30 phút)',
                ],
                'description' => <<<'DESC'
                    iPhone 12 Pro sở hữu vẻ ngoài bóng bẩy với khung viền thép không gỉ vuông vức và sắc màu Pacific Blue hút mắt. Sự xuất hiện của cảm biến LiDAR giúp lấy nét trong đêm nhanh gấp 6 lần kết hợp định dạng ảnh chụp Apple ProRAW chuyên nghiệp đáp ứng hoàn hảo nhu cầu sáng tạo hình ảnh.

                    Kỷ nguyên Cạnh vuông Phẳng phiu, Mạng 5G & Hệ sinh thái Sạc MagSafe

                    Ngôn ngữ thiết kế viền phẳng hiện đại và kính gốm Ceramic Shield siêu bền
                    iPhone 12 Series làm sống lại ngôn ngữ thiết kế cạnh phẳng vuông vức mạnh mẽ, kết hợp cùng mặt kính phủ tinh thể gốm Ceramic Shield tăng khả năng chịu lực va đập gấp 4 lần. Toàn bộ dải sản phẩm đều được trang bị tấm nền OLED Super Retina XDR rực rỡ với độ tương phản tuyệt đối.

                    Tiên phong kết nối 5G siêu tốc và sạc hít nam châm MagSafe tiện dụng
                    Đây là thế hệ iPhone đầu tiên hỗ trợ băng tần mạng 5G tốc độ cao, cho phép tải phim, gọi video chất lượng 4K tức thì. Vòng nam châm MagSafe tích hợp ở mặt lưng mở ra một hệ sinh thái phụ kiện thông minh: từ đế sạc hít tự căn chỉnh, ví da cao cấp đến giá đỡ xe hơi tiện lợi.
                    DESC,
                'colors' => ['Pacific Blue', 'Than chì', 'Bạc'],
                'storages' => [
                    ['label' => '128GB', 'price' => 7300000],
                    ['label' => '256GB', 'price' => 11500000],
                    ['label' => '512GB', 'price' => 13000000],
                ],
            ],
            [
                'name' => 'iPhone 12 Pro Max',
                'series' => 'iphone-12-series',
                'thumbnail' => '/images/products/iphone-12-pro-max.svg',
                'specifications' => [
                    'Màn hình' => '6.7 inch Super Retina XDR OLED rộng rãi, 2778 x 1284 pixels',
                    'Vi xử lý (CPU)' => 'Apple A14 Bionic (5nm)',
                    'Dung lượng RAM' => '6GB',
                    'Bộ nhớ trong' => '128GB / 256GB / 512GB',
                    'Camera sau' => 'Cảm biến chính 12MP lớn hơn 47% với chống rung dịch chuyển cảm biến Sensor-shift OIS, Tele 2.5x (65mm), Siêu rộng, LiDAR',
                    'Camera trước' => '12MP TrueDepth',
                    'Chất liệu' => 'Khung thép không gỉ, mặt sau kính nhám mờ',
                    'Pin & Sạc' => '3.687 mAh; sạc nhanh 20W, sạc hít MagSafe 15W',
                    'Trọng lượng' => '228g',
                    'Kháng nước' => 'Chuẩn IP68 (6 mét trong 30 phút)',
                ],
                'description' => <<<'DESC'
                    iPhone 12 Pro Max là mẫu máy cao cấp nhất với màn hình lớn 6.7 inch hiển thị mãn nhãn cùng công nghệ chống rung dịch chuyển cảm biến Sensor-shift OIS thừa hưởng từ máy ảnh DSLR. Ống kính zoom tele 2.5x cùng cảm biến lớn thu sáng ngoạn mục biến máy thành ông vua quay phim thiếu sáng.

                    Kỷ nguyên Cạnh vuông Phẳng phiu, Mạng 5G & Hệ sinh thái Sạc MagSafe

                    Ngôn ngữ thiết kế viền phẳng hiện đại và kính gốm Ceramic Shield siêu bền
                    iPhone 12 Series làm sống lại ngôn ngữ thiết kế cạnh phẳng vuông vức mạnh mẽ, kết hợp cùng mặt kính phủ tinh thể gốm Ceramic Shield tăng khả năng chịu lực va đập gấp 4 lần. Toàn bộ dải sản phẩm đều được trang bị tấm nền OLED Super Retina XDR rực rỡ với độ tương phản tuyệt đối.

                    Tiên phong kết nối 5G siêu tốc và sạc hít nam châm MagSafe tiện dụng
                    Đây là thế hệ iPhone đầu tiên hỗ trợ băng tần mạng 5G tốc độ cao, cho phép tải phim, gọi video chất lượng 4K tức thì. Vòng nam châm MagSafe tích hợp ở mặt lưng mở ra một hệ sinh thái phụ kiện thông minh: từ đế sạc hít tự căn chỉnh, ví da cao cấp đến giá đỡ xe hơi tiện lợi.
                    DESC,
                'colors' => ['Pacific Blue', 'Than chì', 'Bạc'],
                'storages' => [
                    ['label' => '128GB', 'price' => 9600000],
                    ['label' => '256GB', 'price' => 13500000],
                    ['label' => '512GB', 'price' => 15000000],
                ],
            ],

            // ---------------------------------------------------------------
            // iPhone 13 Series: mini, 13, Pro, Pro Max
            // ---------------------------------------------------------------
            [
                'name' => 'iPhone 13 mini',
                'series' => 'iphone-13-series',
                'thumbnail' => '/images/products/iphone-13-mini.svg',
                'specifications' => [
                    'Màn hình' => '5.4 inch Super Retina XDR OLED, độ sáng tăng lên 800 nits, tai thỏ nhỏ hơn 20%',
                    'Vi xử lý (CPU)' => 'Apple A15 Bionic (6 nhân CPU, 4 nhân GPU)',
                    'Dung lượng RAM' => '4GB',
                    'Bộ nhớ trong' => '128GB / 256GB / 512GB (khởi điểm từ 128GB)',
                    'Camera sau' => 'Kép 12MP xếp chéo độc đáo: Cảm biến chính thừa hưởng Sensor-shift OIS, chế độ quay phim Cinematic Mode 1080p',
                    'Camera trước' => '12MP TrueDepth',
                    'Chất liệu' => 'Khung nhôm viền phẳng, kính Ceramic Shield',
                    'Pin & Sạc' => '2.438 mAh (thời lượng pin tăng thêm 1.5 tiếng so với 12 mini)',
                    'Trọng lượng' => '141g',
                    'Kháng nước' => 'Chuẩn IP68 (6 mét trong 30 phút)',
                ],
                'description' => <<<'DESC'
                    iPhone 13 mini hoàn thiện trọn vẹn điểm yếu của đời trước khi dung lượng pin tăng đáng kể và bộ nhớ khởi điểm nâng lên 128GB rộng rãi. Cụm camera kép đặt chéo thời thượng tích hợp chống rung cảm biến Sensor-shift và chế độ quay phim điện ảnh Cinematic xóa phông ấn tượng.

                    Kỷ nguyên Màn hình 120Hz ProMotion, Quay phim Điện ảnh & Pin Kỷ lục

                    Màn hình siêu mượt 120Hz ProMotion và cụm tai thỏ thu gọn tinh tế
                    Dòng Pro của iPhone 13 Series đem lại trải nghiệm thị giác đỉnh cao với tần số quét thích ứng 120Hz ProMotion siêu mượt, biến từng cử chỉ lướt web và chơi game trở nên sống động tức thì. Cụm tai thỏ được thu nhỏ 20% giúp tối ưu hóa diện tích hiển thị màn hình rộng rãi hơn.

                    Chế độ quay Điện ảnh Cinematic xóa phông và pin bền bỉ không đối thủ
                    Tính năng Cinematic Mode cho phép lấy nét chuyển chủ thể mượt mà như máy quay Hollywood chuyên nghiệp, cùng khả năng chụp siêu cận cảnh Macro cự ly 2cm. Dung lượng pin toàn dòng được nâng cấp vượt bậc, mang lại thời lượng sử dụng ấn tượng hàng đầu thị trường smartphone.
                    DESC,
                'colors' => ['Midnight', 'Starlight', 'Hồng'],
                'storages' => [
                    ['label' => '128GB', 'price' => 8500000],
                    ['label' => '256GB', 'price' => 9800000],
                    ['label' => '512GB', 'price' => 11100000],
                ],
            ],
            [
                'name' => 'iPhone 13',
                'series' => 'iphone-13-series',
                'thumbnail' => '/images/products/iphone-13.svg',
                'specifications' => [
                    'Màn hình' => '6.1 inch Super Retina XDR OLED, độ sáng 800 nits (tối đa 1.200 nits HDR), tai thỏ gọn 20%',
                    'Vi xử lý (CPU)' => 'Apple A15 Bionic (tiến trình 5nm nâng tiến)',
                    'Dung lượng RAM' => '4GB',
                    'Bộ nhớ trong' => '128GB / 256GB / 512GB',
                    'Camera sau' => 'Kép 12MP đặt chéo: Chính 12MP (Sensor-shift OIS) + Góc siêu rộng 12MP, chế độ Điện ảnh Cinematic',
                    'Camera trước' => '12MP TrueDepth',
                    'Chất liệu & Màu sắc' => 'Khung nhôm; Màu sắc: Midnight, Starlight, Xanh dương, Hồng phấn, Xanh lục bảo, Đỏ',
                    'Pin & Sạc' => '3.227 mAh (pin tăng vọt 2.5 tiếng so với iPhone 12)',
                    'Trọng lượng' => '174g',
                    'Kháng nước' => 'Chuẩn IP68 (6 mét trong 30 phút)',
                ],
                'description' => <<<'DESC'
                    iPhone 13 mang lại bước nhảy vọt về thời lượng pin thực tế với viên pin 3.227 mAh dùng bền bỉ cả ngày dài mà không lo cạn năng lượng. Thiết kế camera đặt chéo nhận diện đặc trưng cùng chip Apple A15 Bionic siêu mượt giúp chiếc máy luôn nằm trong top lựa chọn hàng đầu phân khúc.

                    Kỷ nguyên Màn hình 120Hz ProMotion, Quay phim Điện ảnh & Pin Kỷ lục

                    Màn hình siêu mượt 120Hz ProMotion và cụm tai thỏ thu gọn tinh tế
                    Dòng Pro của iPhone 13 Series đem lại trải nghiệm thị giác đỉnh cao với tần số quét thích ứng 120Hz ProMotion siêu mượt, biến từng cử chỉ lướt web và chơi game trở nên sống động tức thì. Cụm tai thỏ được thu nhỏ 20% giúp tối ưu hóa diện tích hiển thị màn hình rộng rãi hơn.

                    Chế độ quay Điện ảnh Cinematic xóa phông và pin bền bỉ không đối thủ
                    Tính năng Cinematic Mode cho phép lấy nét chuyển chủ thể mượt mà như máy quay Hollywood chuyên nghiệp, cùng khả năng chụp siêu cận cảnh Macro cự ly 2cm. Dung lượng pin toàn dòng được nâng cấp vượt bậc, mang lại thời lượng sử dụng ấn tượng hàng đầu thị trường smartphone.
                    DESC,
                'colors' => ['Midnight', 'Starlight', 'Xanh lục bảo'],
                'storages' => [
                    ['label' => '128GB', 'price' => 10000000],
                    ['label' => '256GB', 'price' => 11500000],
                    ['label' => '512GB', 'price' => 13000000],
                ],
            ],
            [
                'name' => 'iPhone 13 Pro',
                'series' => 'iphone-13-series',
                'thumbnail' => '/images/products/iphone-13-pro.svg',
                'specifications' => [
                    'Màn hình' => '6.1 inch Super Retina XDR OLED, tần số quét thích ứng 120Hz ProMotion, Always-On, 1.000 nits',
                    'Vi xử lý (CPU)' => 'Apple A15 Bionic (5 nhân GPU đồ họa cực mạnh)',
                    'Dung lượng RAM' => '6GB',
                    'Bộ nhớ trong' => '128GB / 256GB / 512GB / 1TB (lần đầu có tùy chọn 1TB)',
                    'Camera sau' => '3 camera 12MP to bản: Chính f/1.5 + Siêu rộng có lấy nét Macro 2cm + Tele 3x (77mm), quay Apple ProRes',
                    'Camera trước' => '12MP TrueDepth',
                    'Chất liệu & Màu sắc' => 'Khung thép không gỉ bóng, kính mờ nhám; Màu Xanh Sierra Blue huyền thoại',
                    'Pin & Sạc' => '3.095 mAh; sạc nhanh 20W, MagSafe 15W',
                    'Trọng lượng' => '204g',
                    'Kháng nước' => 'Chuẩn IP68 (6 mét trong 30 phút)',
                ],
                'description' => <<<'DESC'
                    iPhone 13 Pro là bước ngoặt trải nghiệm thị giác với màn hình 120Hz ProMotion siêu mượt mà trong thân máy 6.1 inch vừa vặn. Khả năng chụp siêu cận cảnh Macro 2cm sắc nét cùng ống kính tele zoom quang 3x và màu xanh Sierra Blue biến máy thành cỗ máy quay chụp bỏ túi hoàn mỹ.

                    Kỷ nguyên Màn hình 120Hz ProMotion, Quay phim Điện ảnh & Pin Kỷ lục

                    Màn hình siêu mượt 120Hz ProMotion và cụm tai thỏ thu gọn tinh tế
                    Dòng Pro của iPhone 13 Series đem lại trải nghiệm thị giác đỉnh cao với tần số quét thích ứng 120Hz ProMotion siêu mượt, biến từng cử chỉ lướt web và chơi game trở nên sống động tức thì. Cụm tai thỏ được thu nhỏ 20% giúp tối ưu hóa diện tích hiển thị màn hình rộng rãi hơn.

                    Chế độ quay Điện ảnh Cinematic xóa phông và pin bền bỉ không đối thủ
                    Tính năng Cinematic Mode cho phép lấy nét chuyển chủ thể mượt mà như máy quay Hollywood chuyên nghiệp, cùng khả năng chụp siêu cận cảnh Macro cự ly 2cm. Dung lượng pin toàn dòng được nâng cấp vượt bậc, mang lại thời lượng sử dụng ấn tượng hàng đầu thị trường smartphone.
                    DESC,
                'colors' => ['Sierra Blue', 'Bạc', 'Than chì'],
                'storages' => [
                    ['label' => '128GB', 'price' => 13000000],
                    ['label' => '256GB', 'price' => 14500000],
                    ['label' => '512GB', 'price' => 16000000],
                    ['label' => '1TB', 'price' => 18000000],
                ],
            ],
            [
                'name' => 'iPhone 13 Pro Max',
                'series' => 'iphone-13-series',
                'thumbnail' => '/images/products/iphone-13-pro-max.svg',
                'specifications' => [
                    'Màn hình' => '6.7 inch Super Retina XDR OLED, 120Hz ProMotion, 1.000 nits (tối đa 1.200 nits)',
                    'Vi xử lý (CPU)' => 'Apple A15 Bionic (5 nhân GPU đỉnh cao)',
                    'Dung lượng RAM' => '6GB',
                    'Bộ nhớ trong' => '128GB / 256GB / 512GB / 1TB',
                    'Camera sau' => 'Cụm 3 camera 12MP: Chính f/1.5 cảm biến lớn, Siêu rộng chụp Macro, Tele 3x, quay video định dạng Apple ProRes',
                    'Camera trước' => '12MP TrueDepth',
                    'Chất liệu' => 'Khung viền thép không gỉ sáng bóng, mặt kính lưng nhám mờ',
                    'Pin & Sạc' => '4.352 mAh (thời lượng pin trâu kỷ lục trong lịch sử iPhone)',
                    'Trọng lượng' => '240g (cầm đầm tay và chắc chắn)',
                    'Kháng nước' => 'Chuẩn IP68 (6 mét trong 30 phút)',
                ],
                'description' => <<<'DESC'
                    iPhone 13 Pro Max thiết lập kỷ lục vô tiền khoáng hậu về thời lượng pin bền bỉ với dung lượng 4.352 mAh đáp ứng thoải mái 2 ngày sử dụng cường độ cao. Màn hình 6.7 inch 120Hz ProMotion mượt mà đến từng thao tác vuốt cuộn kết hợp ống kính tele 3x thỏa mãn mọi kỳ vọng khắt khe nhất.

                    Kỷ nguyên Màn hình 120Hz ProMotion, Quay phim Điện ảnh & Pin Kỷ lục

                    Màn hình siêu mượt 120Hz ProMotion và cụm tai thỏ thu gọn tinh tế
                    Dòng Pro của iPhone 13 Series đem lại trải nghiệm thị giác đỉnh cao với tần số quét thích ứng 120Hz ProMotion siêu mượt, biến từng cử chỉ lướt web và chơi game trở nên sống động tức thì. Cụm tai thỏ được thu nhỏ 20% giúp tối ưu hóa diện tích hiển thị màn hình rộng rãi hơn.

                    Chế độ quay Điện ảnh Cinematic xóa phông và pin bền bỉ không đối thủ
                    Tính năng Cinematic Mode cho phép lấy nét chuyển chủ thể mượt mà như máy quay Hollywood chuyên nghiệp, cùng khả năng chụp siêu cận cảnh Macro cự ly 2cm. Dung lượng pin toàn dòng được nâng cấp vượt bậc, mang lại thời lượng sử dụng ấn tượng hàng đầu thị trường smartphone.
                    DESC,
                'colors' => ['Sierra Blue', 'Bạc', 'Than chì'],
                'storages' => [
                    ['label' => '128GB', 'price' => 15000000],
                    ['label' => '256GB', 'price' => 16500000],
                    ['label' => '512GB', 'price' => 18000000],
                    ['label' => '1TB', 'price' => 20000000],
                ],
            ],

            // ---------------------------------------------------------------
            // iPhone SE Series: 2022
            // ---------------------------------------------------------------
            [
                'name' => 'iPhone SE 3 (2022)',
                'series' => 'iphone-se-series',
                'thumbnail' => '/images/products/iphone-se-3-2022.svg',
                'specifications' => [
                    'Màn hình' => '4.7 inch Retina HD IPS LCD, 1334 x 750 pixels, True Tone',
                    'Vi xử lý (CPU)' => 'Apple A15 Bionic (mạnh mẽ tương đương iPhone 13)',
                    'Dung lượng RAM' => '4GB (nâng cấp so với thế hệ trước)',
                    'Bộ nhớ trong' => '64GB / 128GB / 256GB',
                    'Kết nối mạng' => 'Mạng 5G Sub-6 GHz tốc độ cao',
                    'Camera sau' => '12MP f/1.8, OIS, Smart HDR 4, Photographic Styles, quay 4K',
                    'Camera trước' => '7MP f/2.2',
                    'Bảo mật' => 'Touch ID tích hợp nút Home vật lý Taptic Engine',
                    'Pin & Sạc' => '2.018 mAh (pin cải thiện); sạc nhanh 20W, sạc không dây Qi',
                    'Trọng lượng & Kính' => '144g; kính trước sau gia cường bền bỉ như iPhone 13',
                ],
                'description' => <<<'DESC'
                    iPhone SE 3 (2022) đưa công nghệ mạng 5G siêu tốc và con chip Apple A15 Bionic hàng đầu vào thân máy 4.7 inch nhỏ gọn mang phím Home Touch ID cổ điển. Với RAM nâng lên 4GB và pin bền hơn, đây là mẫu máy phụ xử lý tác vụ siêu mượt cho người chuộng sự giản đơn.

                    Dòng iPhone SE Giá tốt: Thiết kế Cổ điển, Sức mạnh Flagship

                    Sức mạnh vượt tầm giá trong thân máy nhỏ gọn thân thuộc
                    Dòng iPhone SE (2020 & 2022) giữ trọn thiết kế phím Home Touch ID truyền thống với kích thước 4.7 inch bỏ túi dễ dàng, nhưng mang trong mình trái tim vi xử lý cao cấp ngang ngửa các dòng iPhone đầu bảng cùng thời (A13/A15 Bionic).

                    Trải nghiệm mượt mà lâu dài, bổ sung kết nối 5G tốc độ cao
                    Với khả năng tối ưu hóa phần mềm độc quyền từ Apple, máy đảm bảo độ mượt mà ổn định suốt nhiều năm sử dụng, chơi game mượt mà và xử lý ảnh chụp sắc nét với Smart HDR. Thế hệ SE 3 còn nâng cấp mạng 5G siêu tốc và cải thiện thời lượng pin đáng kể.
                    DESC,
                'colors' => ['Midnight', 'Starlight', 'Đỏ'],
                'storages' => [
                    ['label' => '64GB', 'price' => 6000000],
                    ['label' => '128GB', 'price' => 6800000],
                    ['label' => '256GB', 'price' => 7600000],
                ],
            ],

            // ---------------------------------------------------------------
            // iPhone 14 Series: 14, 14 Plus, 14 Pro, 14 Pro Max
            // ---------------------------------------------------------------
            [
                'name' => 'iPhone 14',
                'series' => 'iphone-14-series',
                'thumbnail' => '/images/products/iphone-14.svg',
                'specifications' => [
                    'Màn hình' => '6.1 inch Super Retina XDR OLED, 60Hz, tai thỏ gọn gàng',
                    'Vi xử lý (CPU)' => 'Apple A15 Bionic (bản 5 nhân GPU mạnh mẽ của 13 Pro)',
                    'Dung lượng RAM' => '6GB (nâng cấp đa nhiệm vượt trội)',
                    'Bộ nhớ trong' => '128GB / 256GB / 512GB',
                    'Camera sau' => 'Kép 12MP: Chính f/1.5 cảm biến lớn hơn + Siêu rộng, thuật toán xử lý ảnh Photonic Engine',
                    'Camera trước' => '12MP TrueDepth có khả năng tự động lấy nét Autofocus',
                    'Tính năng an toàn' => 'Phát hiện va chạm xe hơi (Crash Detection), SOS vệ tinh',
                    'Chất liệu' => 'Khung nhôm hàng không, kính gốm Ceramic Shield',
                    'Trọng lượng & Pin' => '172g; 3.279 mAh; sạc nhanh 20W, MagSafe 15W',
                    'Kháng nước' => 'Chuẩn IP68 (6 mét trong 30 phút)',
                ],
                'description' => <<<'DESC'
                    iPhone 14 tăng cường sức mạnh đa nhiệm với 6GB RAM và chip A15 Bionic bản 5 nhân GPU đồ họa cao cấp. Camera selfie lần đầu được trang bị lấy nét tự động Autofocus cùng thuật toán Photonic Engine và tính năng an toàn phát hiện va chạm xe hơi đem lại sự an tâm tuyệt đối.

                    Kỷ nguyên Dynamic Island Biến ảo, Camera 48MP & Kết nối Vệ tinh

                    Giao diện tương tác Dynamic Island linh hoạt và màn hình Always-On Display
                    iPhone 14 Series loại bỏ hoàn toàn tai thỏ trên dòng Pro, thay thế bằng cụm Dynamic Island hình viên thuốc có khả năng co giãn hiển thị thông báo, chỉ đường, phát nhạc sống động. Tính năng Always-On Display giữ màn hình luôn hiển thị thông tin hữu ích ở tần số 1Hz siêu tiết kiệm điện.

                    Camera chính 48MP cảm biến Quad-Pixel và công nghệ an toàn khẩn cấp
                    Độ phân giải camera chính nhảy vọt lên 48MP cho phép crop zoom 2x sắc nét quang học và chụp ảnh ProRAW siêu chi tiết. Máy bổ sung các tính năng an toàn mang tính sống còn như Phát hiện va chạm xe hơi nghiêm trọng (Crash Detection) và gửi tin nhắn cứu hộ SOS khẩn cấp qua vệ tinh.
                    DESC,
                'colors' => ['Midnight', 'Xanh dương', 'Tím'],
                'storages' => [
                    ['label' => '128GB', 'price' => 8700000],
                    ['label' => '256GB', 'price' => 14500000],
                    ['label' => '512GB', 'price' => 16000000],
                ],
            ],
            [
                'name' => 'iPhone 14 Plus',
                'series' => 'iphone-14-series',
                'thumbnail' => '/images/products/iphone-14-plus.svg',
                'specifications' => [
                    'Màn hình' => '6.7 inch Super Retina XDR OLED rộng rãi, 60Hz',
                    'Vi xử lý (CPU)' => 'Apple A15 Bionic (5 nhân GPU)',
                    'Dung lượng RAM' => '6GB',
                    'Bộ nhớ trong' => '128GB / 256GB / 512GB',
                    'Camera sau' => 'Kép 12MP (Chính f/1.5 + Siêu rộng), chống rung OIS, Photonic Engine',
                    'Camera trước' => '12MP TrueDepth có Autofocus',
                    'Tính năng an toàn' => 'Phát hiện va chạm (Crash Detection), SOS khẩn cấp qua vệ tinh',
                    'Chất liệu' => 'Khung nhôm siêu nhẹ, mặt lưng kính bóng',
                    'Trọng lượng & Pin' => '203g (rất nhẹ so với bản 6.7 inch khác); Pin 4.323 mAh cực trâu',
                    'Kháng nước' => 'Chuẩn IP68 (6 mét trong 30 phút)',
                ],
                'description' => <<<'DESC'
                    iPhone 14 Plus hồi sinh phiên bản "Plus" mang đến không gian màn hình cực đại 6.7 inch trong thân máy khung nhôm siêu nhẹ chỉ 203g. Thời lượng pin 4.323 mAh trâu hàng đầu giúp bạn cày phim, chơi game và làm việc cả ngày dài mà không lo tìm ổ cắm sạc.

                    Kỷ nguyên Dynamic Island Biến ảo, Camera 48MP & Kết nối Vệ tinh

                    Giao diện tương tác Dynamic Island linh hoạt và màn hình Always-On Display
                    iPhone 14 Series loại bỏ hoàn toàn tai thỏ trên dòng Pro, thay thế bằng cụm Dynamic Island hình viên thuốc có khả năng co giãn hiển thị thông báo, chỉ đường, phát nhạc sống động. Tính năng Always-On Display giữ màn hình luôn hiển thị thông tin hữu ích ở tần số 1Hz siêu tiết kiệm điện.

                    Camera chính 48MP cảm biến Quad-Pixel và công nghệ an toàn khẩn cấp
                    Độ phân giải camera chính nhảy vọt lên 48MP cho phép crop zoom 2x sắc nét quang học và chụp ảnh ProRAW siêu chi tiết. Máy bổ sung các tính năng an toàn mang tính sống còn như Phát hiện va chạm xe hơi nghiêm trọng (Crash Detection) và gửi tin nhắn cứu hộ SOS khẩn cấp qua vệ tinh.
                    DESC,
                'colors' => ['Midnight', 'Xanh dương', 'Vàng'],
                'storages' => [
                    ['label' => '128GB', 'price' => 9600000],
                    ['label' => '256GB', 'price' => 16000000],
                    ['label' => '512GB', 'price' => 17500000],
                ],
            ],
            [
                'name' => 'iPhone 14 Pro',
                'series' => 'iphone-14-series',
                'thumbnail' => '/images/products/iphone-14-pro.svg',
                'specifications' => [
                    'Màn hình' => '6.1 inch Super Retina XDR OLED, 120Hz ProMotion, Dynamic Island, Always-On Display, độ sáng 2.000 nits',
                    'Vi xử lý (CPU)' => 'Apple A16 Bionic (tiến trình 4nm tiên tiến)',
                    'Dung lượng RAM' => '6GB',
                    'Bộ nhớ trong' => '128GB / 256GB / 512GB / 1TB',
                    'Camera sau' => 'Cụm 3 camera: Chính 48MP Quad-Pixel (crop zoom 2x sắc nét) + Siêu rộng 12MP + Tele 3x 12MP, Photonic Engine',
                    'Camera trước' => '12MP TrueDepth Autofocus',
                    'Chất liệu & Màu sắc' => 'Khung viền thép bóng, kính mờ nhám; Màu Tím đậm Deep Purple gây sốt',
                    'Trọng lượng & Pin' => '206g; 3.200 mAh; sạc nhanh 20W, MagSafe 15W',
                    'Kháng nước' => 'Chuẩn IP68 (6 mét trong 30 phút)',
                ],
                'description' => <<<'DESC'
                    iPhone 14 Pro tạo nên cơn sốt toàn cầu khi thay thế tai thỏ bằng giao diện biến ảo Dynamic Island thông minh và nâng cấp camera chính lên độ phân giải 48MP siêu sắc nét. Màn hình Always-On Display cùng sắc màu tím Deep Purple tôn vinh đẳng cấp sang trọng của người sở hữu.

                    Kỷ nguyên Dynamic Island Biến ảo, Camera 48MP & Kết nối Vệ tinh

                    Giao diện tương tác Dynamic Island linh hoạt và màn hình Always-On Display
                    iPhone 14 Series loại bỏ hoàn toàn tai thỏ trên dòng Pro, thay thế bằng cụm Dynamic Island hình viên thuốc có khả năng co giãn hiển thị thông báo, chỉ đường, phát nhạc sống động. Tính năng Always-On Display giữ màn hình luôn hiển thị thông tin hữu ích ở tần số 1Hz siêu tiết kiệm điện.

                    Camera chính 48MP cảm biến Quad-Pixel và công nghệ an toàn khẩn cấp
                    Độ phân giải camera chính nhảy vọt lên 48MP cho phép crop zoom 2x sắc nét quang học và chụp ảnh ProRAW siêu chi tiết. Máy bổ sung các tính năng an toàn mang tính sống còn như Phát hiện va chạm xe hơi nghiêm trọng (Crash Detection) và gửi tin nhắn cứu hộ SOS khẩn cấp qua vệ tinh.
                    DESC,
                'colors' => ['Deep Purple', 'Space Black', 'Vàng'],
                'storages' => [
                    ['label' => '128GB', 'price' => 12400000],
                    ['label' => '256GB', 'price' => 19500000],
                    ['label' => '512GB', 'price' => 21000000],
                    ['label' => '1TB', 'price' => 23000000],
                ],
            ],
            [
                'name' => 'iPhone 14 Pro Max',
                'series' => 'iphone-14-series',
                'thumbnail' => '/images/products/iphone-14-pro-max.svg',
                'specifications' => [
                    'Màn hình' => '6.7 inch Super Retina XDR OLED, 120Hz ProMotion, Dynamic Island, Always-On, ngoài trời 2.000 nits',
                    'Vi xử lý (CPU)' => 'Apple A16 Bionic (4nm, 6 nhân CPU, 5 nhân GPU)',
                    'Dung lượng RAM' => '6GB',
                    'Bộ nhớ trong' => '128GB / 256GB / 512GB / 1TB',
                    'Camera sau' => 'Chính 48MP cảm biến 4 điểm ảnh (zoom 2x quang học) + Siêu rộng 12MP + Tele 3x 12MP, quay Action Mode chống rung',
                    'Camera trước' => '12MP TrueDepth Autofocus',
                    'Chất liệu' => 'Khung thép không gỉ sáng bóng, mặt lưng kính nhám mờ sang trọng',
                    'Pin & Sạc' => '4.323 mAh; sạc nhanh 20W, MagSafe 15W',
                    'Trọng lượng' => '240g',
                    'Kháng nước' => 'Chuẩn IP68 (6 mét trong 30 phút)',
                ],
                'description' => <<<'DESC'
                    iPhone 14 Pro Max quy tụ mọi tinh hoa công nghệ với màn hình lớn 6.7 inch độ sáng ngoài trời lên tới 2.000 nits cùng camera 48MP quay chụp đỉnh cao. Thiết kế viền thép sáng loáng, thời lượng pin bền bỉ và giao diện Dynamic Island biến đây thành mẫu flagship đáng khao khát nhất.

                    Kỷ nguyên Dynamic Island Biến ảo, Camera 48MP & Kết nối Vệ tinh

                    Giao diện tương tác Dynamic Island linh hoạt và màn hình Always-On Display
                    iPhone 14 Series loại bỏ hoàn toàn tai thỏ trên dòng Pro, thay thế bằng cụm Dynamic Island hình viên thuốc có khả năng co giãn hiển thị thông báo, chỉ đường, phát nhạc sống động. Tính năng Always-On Display giữ màn hình luôn hiển thị thông tin hữu ích ở tần số 1Hz siêu tiết kiệm điện.

                    Camera chính 48MP cảm biến Quad-Pixel và công nghệ an toàn khẩn cấp
                    Độ phân giải camera chính nhảy vọt lên 48MP cho phép crop zoom 2x sắc nét quang học và chụp ảnh ProRAW siêu chi tiết. Máy bổ sung các tính năng an toàn mang tính sống còn như Phát hiện va chạm xe hơi nghiêm trọng (Crash Detection) và gửi tin nhắn cứu hộ SOS khẩn cấp qua vệ tinh.
                    DESC,
                'colors' => ['Deep Purple', 'Space Black', 'Bạc'],
                'storages' => [
                    ['label' => '128GB', 'price' => 14600000],
                    ['label' => '256GB', 'price' => 21500000],
                    ['label' => '512GB', 'price' => 23000000],
                    ['label' => '1TB', 'price' => 25000000],
                ],
            ],

            // ---------------------------------------------------------------
            // iPhone 15 Series: 15, 15 Plus, 15 Pro, 15 Pro Max
            // ---------------------------------------------------------------
            [
                'name' => 'iPhone 15',
                'series' => 'iphone-15-series',
                'thumbnail' => '/images/products/iphone-15.svg',
                'specifications' => [
                    'Màn hình' => '6.1 inch Super Retina XDR OLED, 60Hz, Dynamic Island, độ sáng ngoài trời 2.000 nits',
                    'Vi xử lý (CPU)' => 'Apple A16 Bionic (4nm)',
                    'Dung lượng RAM' => '6GB',
                    'Bộ nhớ trong' => '128GB / 256GB / 512GB',
                    'Camera sau' => 'Kép nâng cấp: Chính 48MP (hỗ trợ zoom 2x crop quang học) + Góc siêu rộng 12MP',
                    'Camera trước' => '12MP TrueDepth Autofocus',
                    'Cổng kết nối' => 'USB-C (chuẩn USB 2.0, tốc độ 480 Mbps)',
                    'Chất liệu & Mặt lưng' => 'Khung nhôm bo cong êm tay, mặt lưng kính nhám pha màu trong phôi',
                    'Trọng lượng & Pin' => '171g; 3.349 mAh; sạc nhanh 20W, MagSafe 15W',
                    'Màu sắc' => 'Đen, Xanh dương, Xanh lá cây, Vàng nhạt, Hồng pastel ngọt ngào',
                ],
                'description' => <<<'DESC'
                    iPhone 15 lột xác ngoạn mục khi mang giao diện Dynamic Island, camera chính 48MP và cổng sạc USB-C hiện đại xuống phân khúc tiêu chuẩn. Mặt lưng kính nhám pha màu pastel tinh tế phối cùng các cạnh bo cong êm ái chấm dứt hoàn toàn cảm giác cấn tay khi cầm nắm.

                    Kỷ nguyên Khung viền Titanium Siêu nhẹ, Cổng USB-C & Nút Action Button

                    Khung hợp kim Titanium Grade 5 nhẹ êm và mặt lưng kính nhám pha màu
                    iPhone 15 Series ghi dấu ấn với chất liệu Titanium hàng không vũ trụ trên dòng Pro, giúp giảm trọng lượng máy đáng kể và mang lại cảm giác cầm êm ái nhờ các cạnh viền bo cong nhẹ. Mặt lưng kính nhám mờ sang trọng chấm dứt tình trạng bám dính mồ hôi và dấu vân tay.

                    Đồng bộ hóa cổng USB-C tiện lợi và nâng cấp camera 48MP toàn dòng
                    Chuyển đổi sang cổng kết nối chuẩn USB-C quốc tế giúp dùng chung cáp sạc với iPad, MacBook và sạc ngược cho tai nghe AirPods. Camera 48MP xuất hiện trên cả dòng thường, đi kèm phím bấm Action Button đa nhiệm thay thế cần gạt rung cũ, nâng tầm trải nghiệm cá nhân hóa.
                    DESC,
                'colors' => ['Đen', 'Xanh dương', 'Hồng pastel'],
                'storages' => [
                    ['label' => '128GB', 'price' => 11300000],
                    ['label' => '256GB', 'price' => 17500000],
                    ['label' => '512GB', 'price' => 19000000],
                ],
            ],
            [
                'name' => 'iPhone 15 Plus',
                'series' => 'iphone-15-series',
                'thumbnail' => '/images/products/iphone-15-plus.svg',
                'specifications' => [
                    'Màn hình' => '6.7 inch Super Retina XDR OLED, 60Hz, Dynamic Island, đỉnh sáng 2.000 nits',
                    'Vi xử lý (CPU)' => 'Apple A16 Bionic (4nm)',
                    'Dung lượng RAM' => '6GB',
                    'Bộ nhớ trong' => '128GB / 256GB / 512GB',
                    'Camera sau' => 'Chính 48MP (zoom 2x sắc nét) + Siêu rộng 12MP, chụp chân dung thế hệ mới',
                    'Camera trước' => '12MP TrueDepth Autofocus',
                    'Cổng kết nối' => 'USB-C tiện lợi đồng bộ hệ sinh thái',
                    'Chất liệu' => 'Khung nhôm viền cong mềm mại, mặt lưng kính nhám pha màu',
                    'Trọng lượng & Pin' => '201g; 4.383 mAh (thời lượng pin on-screen vượt mốc 9-10 tiếng)',
                    'Kháng nước' => 'Chuẩn IP68 (6 mét trong 30 phút)',
                ],
                'description' => <<<'DESC'
                    iPhone 15 Plus là quán quân về thời lượng pin trong thế giới smartphone với viên pin 4.383 mAh cho phép sử dụng cường độ cao cả ngày mà không cần sạc dự phòng. Màn hình lớn 6.7 inch rực rỡ cùng cổng sạc USB-C và trọng lượng nhẹ chỉ 201g đem lại sự hài lòng tuyệt đối.

                    Kỷ nguyên Khung viền Titanium Siêu nhẹ, Cổng USB-C & Nút Action Button

                    Khung hợp kim Titanium Grade 5 nhẹ êm và mặt lưng kính nhám pha màu
                    iPhone 15 Series ghi dấu ấn với chất liệu Titanium hàng không vũ trụ trên dòng Pro, giúp giảm trọng lượng máy đáng kể và mang lại cảm giác cầm êm ái nhờ các cạnh viền bo cong nhẹ. Mặt lưng kính nhám mờ sang trọng chấm dứt tình trạng bám dính mồ hôi và dấu vân tay.

                    Đồng bộ hóa cổng USB-C tiện lợi và nâng cấp camera 48MP toàn dòng
                    Chuyển đổi sang cổng kết nối chuẩn USB-C quốc tế giúp dùng chung cáp sạc với iPad, MacBook và sạc ngược cho tai nghe AirPods. Camera 48MP xuất hiện trên cả dòng thường, đi kèm phím bấm Action Button đa nhiệm thay thế cần gạt rung cũ, nâng tầm trải nghiệm cá nhân hóa.
                    DESC,
                'colors' => ['Đen', 'Xanh lá', 'Hồng'],
                'storages' => [
                    ['label' => '128GB', 'price' => 13700000],
                    ['label' => '256GB', 'price' => 19500000],
                    ['label' => '512GB', 'price' => 21000000],
                ],
            ],
            [
                'name' => 'iPhone 15 Pro',
                'series' => 'iphone-15-series',
                'thumbnail' => '/images/products/iphone-15-pro.svg',
                'specifications' => [
                    'Màn hình' => '6.1 inch Super Retina XDR OLED, 120Hz ProMotion, Always-On, Dynamic Island, 2.000 nits',
                    'Vi xử lý (CPU)' => 'Apple A17 Pro (tiến trình 3nm, 6 nhân CPU, 6 nhân GPU hỗ trợ Ray Tracing)',
                    'Dung lượng RAM' => '8GB (nâng cấp xử lý đồ họa nặng)',
                    'Bộ nhớ trong' => '128GB / 256GB / 512GB / 1TB',
                    'Camera sau' => '3 camera: Chính 48MP (tùy chọn tiêu cự 24/28/35mm) + Siêu rộng 12MP + Tele 3x 12MP',
                    'Nút bấm & Cổng' => 'Nút Action Button tùy biến linh hoạt; Cổng USB-C chuẩn USB 3 (10 Gbps)',
                    'Chất liệu chế tác' => 'Khung hợp kim Titanium Grade 5 siêu nhẹ, lưng kính nhám mờ',
                    'Trọng lượng & Pin' => '187g (nhẹ hơn 19g so với đời trước); Pin 3.274 mAh',
                    'Màu sắc' => 'Titan Tự nhiên, Titan Xanh, Titan Trắng, Titan Đen',
                    'Kháng nước' => 'Chuẩn IP68 (6 mét trong 30 phút)',
                ],
                'description' => <<<'DESC'
                    iPhone 15 Pro tái định nghĩa chuẩn mực flagship nhỏ gọn với khung viền Titanium Grade 5 siêu nhẹ chỉ 187g kết hợp sức mạnh chip A17 Pro 3nm hỗ trợ chơi game đồ họa Console. Cổng USB-C tốc độ cao 10Gbps và nút Action Button đa năng mang đến trải nghiệm tiện ích đỉnh cao.

                    Kỷ nguyên Khung viền Titanium Siêu nhẹ, Cổng USB-C & Nút Action Button

                    Khung hợp kim Titanium Grade 5 nhẹ êm và mặt lưng kính nhám pha màu
                    iPhone 15 Series ghi dấu ấn với chất liệu Titanium hàng không vũ trụ trên dòng Pro, giúp giảm trọng lượng máy đáng kể và mang lại cảm giác cầm êm ái nhờ các cạnh viền bo cong nhẹ. Mặt lưng kính nhám mờ sang trọng chấm dứt tình trạng bám dính mồ hôi và dấu vân tay.

                    Đồng bộ hóa cổng USB-C tiện lợi và nâng cấp camera 48MP toàn dòng
                    Chuyển đổi sang cổng kết nối chuẩn USB-C quốc tế giúp dùng chung cáp sạc với iPad, MacBook và sạc ngược cho tai nghe AirPods. Camera 48MP xuất hiện trên cả dòng thường, đi kèm phím bấm Action Button đa nhiệm thay thế cần gạt rung cũ, nâng tầm trải nghiệm cá nhân hóa.
                    DESC,
                'colors' => ['Titan Tự nhiên', 'Titan Xanh', 'Titan Trắng'],
                'storages' => [
                    ['label' => '128GB', 'price' => 15500000],
                    ['label' => '256GB', 'price' => 23500000],
                    ['label' => '512GB', 'price' => 25000000],
                    ['label' => '1TB', 'price' => 27000000],
                ],
            ],
            [
                'name' => 'iPhone 15 Pro Max',
                'series' => 'iphone-15-series',
                'thumbnail' => '/images/products/iphone-15-pro-max.svg',
                'specifications' => [
                    'Màn hình' => '6.7 inch Super Retina XDR OLED, 120Hz ProMotion, Dynamic Island, viền siêu mỏng',
                    'Vi xử lý (CPU)' => 'Apple A17 Pro (3nm, GPU 6 nhân hỗ trợ Ray Tracing)',
                    'Dung lượng RAM' => '8GB',
                    'Bộ nhớ trong' => '256GB / 512GB / 1TB (loại bỏ hoàn toàn bản 128GB)',
                    'Camera sau' => 'Chính 48MP + Siêu rộng 12MP + Tele tiềm vọng lăng kính Tetraprism Zoom quang 5x (120mm)',
                    'Nút bấm & Cổng' => 'Nút Action Button; Cổng USB-C chuẩn USB 3 (tốc độ 10 Gbps, xuất ProRes vào SSD)',
                    'Chất liệu' => 'Khung hợp kim Titanium Grade 5 kết hợp khung nhôm, mặt sau kính nhám mờ',
                    'Trọng lượng & Pin' => '221g (cầm nhẹ hơn rõ rệt so với 14 Pro Max); Pin 4.422 mAh rất trâu',
                    'Màu sắc' => 'Titan Tự nhiên (Natural Titanium), Titan Xanh, Titan Trắng, Titan Đen',
                ],
                'description' => <<<'DESC'
                    iPhone 15 Pro Max là đỉnh cao công nghệ của Apple sở hữu camera tiềm vọng Tetraprism zoom quang học 5x sắc nét và bộ nhớ khởi điểm nâng lên 256GB rộng rãi. Khung viền Titanium Grade 5 giúp máy nhẹ hơn 19g, kết hợp cổng USB-C tốc độ 10Gbps phục vụ quay phim chuẩn điện ảnh.

                    Kỷ nguyên Khung viền Titanium Siêu nhẹ, Cổng USB-C & Nút Action Button

                    Khung hợp kim Titanium Grade 5 nhẹ êm và mặt lưng kính nhám pha màu
                    iPhone 15 Series ghi dấu ấn với chất liệu Titanium hàng không vũ trụ trên dòng Pro, giúp giảm trọng lượng máy đáng kể và mang lại cảm giác cầm êm ái nhờ các cạnh viền bo cong nhẹ. Mặt lưng kính nhám mờ sang trọng chấm dứt tình trạng bám dính mồ hôi và dấu vân tay.

                    Đồng bộ hóa cổng USB-C tiện lợi và nâng cấp camera 48MP toàn dòng
                    Chuyển đổi sang cổng kết nối chuẩn USB-C quốc tế giúp dùng chung cáp sạc với iPad, MacBook và sạc ngược cho tai nghe AirPods. Camera 48MP xuất hiện trên cả dòng thường, đi kèm phím bấm Action Button đa nhiệm thay thế cần gạt rung cũ, nâng tầm trải nghiệm cá nhân hóa.
                    DESC,
                'colors' => ['Titan Tự nhiên', 'Titan Xanh', 'Titan Đen'],
                'storages' => [
                    ['label' => '256GB', 'price' => 19000000],
                    ['label' => '512GB', 'price' => 28000000],
                    ['label' => '1TB', 'price' => 31000000],
                ],
            ],

            // ---------------------------------------------------------------
            // iPhone 16 Series: 16, 16 Plus, 16 Pro, 16 Pro Max
            // ---------------------------------------------------------------
            [
                'name' => 'iPhone 16',
                'series' => 'iphone-16-series',
                'thumbnail' => '/images/products/iphone-16.svg',
                'specifications' => [
                    'Màn hình' => '6.1 inch Super Retina XDR OLED, 60Hz, Dynamic Island, độ sáng từ 1 nit đến 2.000 nits',
                    'Vi xử lý (CPU)' => 'Apple A18 (tiến trình 3nm thế hệ thứ 2, 6 nhân CPU, 5 nhân GPU)',
                    'Dung lượng RAM' => '8GB (tiêu chuẩn tối thiểu vận hành Apple Intelligence)',
                    'Bộ nhớ trong' => '128GB / 256GB / 512GB',
                    'Camera sau' => 'Cụm xếp dọc: Chính 48MP Fusion (zoom 2x) + Siêu rộng 12MP có chụp Macro, quay Spatial Video',
                    'Nút bấm mới' => 'Trang bị Nút Action Button & Phím bấm cảm ứng lực Camera Control',
                    'Chất liệu' => 'Khung nhôm hàng không, mặt lưng kính pha màu, Ceramic Shield thế hệ mới',
                    'Trọng lượng & Pin' => '170g; 3.561 mAh; sạc nhanh MagSafe lên đến 25W',
                    'Màu sắc' => 'Đen, Trắng, Hồng đậm cá tính, Xanh Mòng Két (Teal), Xanh Lưu Ly (Ultramarine)',
                ],
                'description' => <<<'DESC'
                    iPhone 16 mở ra kỷ nguyên trí tuệ nhân tạo Apple Intelligence với con chip Apple A18 cực mạnh cùng dung lượng RAM 8GB tiêu chuẩn. Cụm camera kép xếp dọc hỗ trợ quay Spatial Video cho Apple Vision Pro cùng phím bấm cảm ứng Camera Control biến việc chụp ảnh trở nên nhanh chóng hơn bao giờ hết.

                    Kỷ nguyên Trí tuệ Nhân tạo Apple Intelligence & Phím Chụp Camera Control

                    Tối ưu hóa phần cứng cho Trí tuệ Nhân tạo Apple Intelligence và Chip A18 Series
                    iPhone 16 Series được thiết kế nguyên bản để vận hành hệ thống trí tuệ nhân tạo cá nhân hóa Apple Intelligence. Toàn bộ dòng máy đều trang bị dung lượng RAM 8GB tối thiểu và thế hệ chip xử lý A18 / A18 Pro tiến trình 3nm thế hệ thứ hai siêu mạnh mẽ và tiết kiệm năng lượng.

                    Nút bấm cơ học cảm ứng Camera Control và Viền màn hình mỏng kỷ lục
                    Phím điều khiển Camera Control tích hợp cảm ứng lực và cử chỉ trượt giúp bạn mở máy ảnh, khóa nét, căn chỉnh tiêu cự và chụp hình mượt mà như máy ảnh cơ chuyên nghiệp. Kích thước hiển thị dòng Pro được mở rộng lên 6.3 inch và 6.9 inch với viền bezel siêu mỏng hàng đầu thế giới.
                    DESC,
                'colors' => ['Đen', 'Xanh Mòng Két (Teal)', 'Xanh Lưu Ly (Ultramarine)'],
                'storages' => [
                    ['label' => '128GB', 'price' => 16900000],
                    ['label' => '256GB', 'price' => 20500000],
                    ['label' => '512GB', 'price' => 22000000],
                ],
            ],
            [
                'name' => 'iPhone 16 Plus',
                'series' => 'iphone-16-series',
                'thumbnail' => '/images/products/iphone-16-plus.svg',
                'specifications' => [
                    'Màn hình' => '6.7 inch Super Retina XDR OLED, 60Hz, Dynamic Island, độ sáng 1 - 2.000 nits',
                    'Vi xử lý (CPU)' => 'Apple A18 (3nm thế hệ 2, tích hợp Neural Engine 16 nhân thế hệ mới)',
                    'Dung lượng RAM' => '8GB (tối ưu hóa cho Apple Intelligence)',
                    'Bộ nhớ trong' => '128GB / 256GB / 512GB',
                    'Camera sau' => 'Chính 48MP Fusion + Siêu rộng 12MP có Macro, quay Video không gian Spatial Video',
                    'Nút bấm' => 'Action Button đa năng + Phím điều khiển Camera Control nhạy bén',
                    'Pin & Sạc' => '4.674 mAh (thời lượng pin khổng lồ); sạc nhanh MagSafe 25W',
                    'Chất liệu' => 'Khung nhôm cao cấp, mặt lưng kính nhám pha màu rực rỡ',
                    'Trọng lượng' => '199g',
                    'Kháng nước' => 'Chuẩn IP68 (6 mét trong 30 phút)',
                ],
                'description' => <<<'DESC'
                    iPhone 16 Plus mang đến không gian hiển thị 6.7 inch mãn nhãn cùng viên pin dung lượng khổng lồ 4.674 mAh cho thời gian sử dụng bền bỉ bất tận. Trang bị đầy đủ phím Camera Control, Action Button và sức mạnh chip A18, đây là chiếc máy màn hình lớn toàn năng và đáng giá.

                    Kỷ nguyên Trí tuệ Nhân tạo Apple Intelligence & Phím Chụp Camera Control

                    Tối ưu hóa phần cứng cho Trí tuệ Nhân tạo Apple Intelligence và Chip A18 Series
                    iPhone 16 Series được thiết kế nguyên bản để vận hành hệ thống trí tuệ nhân tạo cá nhân hóa Apple Intelligence. Toàn bộ dòng máy đều trang bị dung lượng RAM 8GB tối thiểu và thế hệ chip xử lý A18 / A18 Pro tiến trình 3nm thế hệ thứ hai siêu mạnh mẽ và tiết kiệm năng lượng.

                    Nút bấm cơ học cảm ứng Camera Control và Viền màn hình mỏng kỷ lục
                    Phím điều khiển Camera Control tích hợp cảm ứng lực và cử chỉ trượt giúp bạn mở máy ảnh, khóa nét, căn chỉnh tiêu cự và chụp hình mượt mà như máy ảnh cơ chuyên nghiệp. Kích thước hiển thị dòng Pro được mở rộng lên 6.3 inch và 6.9 inch với viền bezel siêu mỏng hàng đầu thế giới.
                    DESC,
                'colors' => ['Đen', 'Trắng', 'Hồng'],
                'storages' => [
                    ['label' => '128GB', 'price' => 19900000],
                    ['label' => '256GB', 'price' => 22500000],
                    ['label' => '512GB', 'price' => 24000000],
                ],
            ],
            [
                'name' => 'iPhone 16 Pro',
                'series' => 'iphone-16-series',
                'thumbnail' => '/images/products/iphone-16-pro.svg',
                'specifications' => [
                    'Màn hình' => '6.3 inch Super Retina XDR OLED, 120Hz ProMotion, Always-On, viền mỏng kỷ lục',
                    'Vi xử lý (CPU)' => 'Apple A18 Pro (tiến trình 3nm thế hệ 2, 6 nhân CPU, 6 nhân GPU cực mạnh)',
                    'Dung lượng RAM' => '8GB',
                    'Bộ nhớ trong' => '128GB / 256GB / 512GB / 1TB',
                    'Camera sau' => 'Chính 48MP Fusion + Siêu rộng 48MP + Tele tiềm vọng 12MP Zoom quang 5x (Tetraprism), quay video 4K@120fps',
                    'Âm thanh & Nút' => '4 micro chuẩn studio, tính năng Audio Mix; Nút Action Button & Camera Control',
                    'Chất liệu & Màu sắc' => 'Khung Titanium Grade 5 hoàn thiện hạt mịn; Màu Titan Sa Mạc (Desert Titanium) mới',
                    'Trọng lượng & Pin' => '199g; 3.582 mAh; sạc MagSafe 25W',
                    'Kháng nước' => 'Chuẩn IP68 (6 mét trong 30 phút)',
                ],
                'description' => <<<'DESC'
                    iPhone 16 Pro nâng tầm trải nghiệm với màn hình mở rộng lên 6.3 inch sở hữu viền benzel siêu mỏng kỷ lục thế giới và ống kính tiềm vọng zoom quang 5x đẳng cấp. Chip A18 Pro kết hợp khả năng quay phim 4K 120fps Dolby Vision và hệ thống 4 micro chuẩn studio đáp ứng trọn vẹn tiêu chuẩn sáng tạo điện ảnh.

                    Kỷ nguyên Trí tuệ Nhân tạo Apple Intelligence & Phím Chụp Camera Control

                    Tối ưu hóa phần cứng cho Trí tuệ Nhân tạo Apple Intelligence và Chip A18 Series
                    iPhone 16 Series được thiết kế nguyên bản để vận hành hệ thống trí tuệ nhân tạo cá nhân hóa Apple Intelligence. Toàn bộ dòng máy đều trang bị dung lượng RAM 8GB tối thiểu và thế hệ chip xử lý A18 / A18 Pro tiến trình 3nm thế hệ thứ hai siêu mạnh mẽ và tiết kiệm năng lượng.

                    Nút bấm cơ học cảm ứng Camera Control và Viền màn hình mỏng kỷ lục
                    Phím điều khiển Camera Control tích hợp cảm ứng lực và cử chỉ trượt giúp bạn mở máy ảnh, khóa nét, căn chỉnh tiêu cự và chụp hình mượt mà như máy ảnh cơ chuyên nghiệp. Kích thước hiển thị dòng Pro được mở rộng lên 6.3 inch và 6.9 inch với viền bezel siêu mỏng hàng đầu thế giới.
                    DESC,
                'colors' => ['Titan Sa Mạc (Desert)', 'Titan Tự nhiên', 'Titan Trắng'],
                'storages' => [
                    ['label' => '128GB', 'price' => 23800000],
                    ['label' => '256GB', 'price' => 27500000],
                    ['label' => '512GB', 'price' => 29000000],
                    ['label' => '1TB', 'price' => 31000000],
                ],
            ],
            [
                'name' => 'iPhone 16 Pro Max',
                'series' => 'iphone-16-series',
                'thumbnail' => '/images/products/iphone-16-pro-max.svg',
                'specifications' => [
                    'Màn hình' => '6.9 inch Super Retina XDR OLED (màn hình lớn nhất lịch sử iPhone), 120Hz ProMotion, viền siêu mỏng',
                    'Vi xử lý (CPU)' => 'Apple A18 Pro (3nm thế hệ 2, băng thông bộ nhớ tăng 17%, hỗ trợ Ray Tracing nhanh gấp đôi)',
                    'Dung lượng RAM' => '8GB (tối ưu hóa phần cứng sâu cho Apple Intelligence)',
                    'Bộ nhớ trong' => '256GB / 512GB / 1TB',
                    'Camera sau' => 'Chính 48MP Fusion + Siêu rộng 48MP sắc nét + Tele tiềm vọng 5x 12MP, quay phim 4K@120fps Dolby Vision',
                    'Nút bấm & Âm thanh' => 'Phím cảm ứng lực Camera Control trượt zoom + Action Button; 4 micro phòng thu, Audio Mix tách giọng',
                    'Chất liệu' => 'Khung Titanium Grade 5 hoàn thiện tinh xảo, kính Ceramic Shield thế hệ mới',
                    'Pin & Sạc' => '4.685 mAh (viên pin lớn nhất từ trước đến nay); sạc MagSafe 25W',
                    'Trọng lượng' => '227g',
                    'Màu sắc' => 'Titan Sa Mạc (Desert Titanium), Titan Tự Nhiên, Titan Trắng, Titan Đen',
                ],
                'description' => <<<'DESC'
                    iPhone 16 Pro Max là đỉnh cao công nghệ smartphone với màn hình khổng lồ 6.9 inch viền siêu mỏng tuyệt mỹ cùng thời lượng pin 4.685 mAh kỷ lục lớn nhất lịch sử Apple. Sự kết hợp giữa chip A18 Pro, camera 48MP kép quay 4K 120fps và phím Camera Control độc đáo kiến tạo trải nghiệm di động tối thượng.

                    Kỷ nguyên Trí tuệ Nhân tạo Apple Intelligence & Phím Chụp Camera Control

                    Tối ưu hóa phần cứng cho Trí tuệ Nhân tạo Apple Intelligence và Chip A18 Series
                    iPhone 16 Series được thiết kế nguyên bản để vận hành hệ thống trí tuệ nhân tạo cá nhân hóa Apple Intelligence. Toàn bộ dòng máy đều trang bị dung lượng RAM 8GB tối thiểu và thế hệ chip xử lý A18 / A18 Pro tiến trình 3nm thế hệ thứ hai siêu mạnh mẽ và tiết kiệm năng lượng.

                    Nút bấm cơ học cảm ứng Camera Control và Viền màn hình mỏng kỷ lục
                    Phím điều khiển Camera Control tích hợp cảm ứng lực và cử chỉ trượt giúp bạn mở máy ảnh, khóa nét, căn chỉnh tiêu cự và chụp hình mượt mà như máy ảnh cơ chuyên nghiệp. Kích thước hiển thị dòng Pro được mở rộng lên 6.3 inch và 6.9 inch với viền bezel siêu mỏng hàng đầu thế giới.
                    DESC,
                'colors' => ['Titan Sa Mạc (Desert)', 'Titan Tự nhiên', 'Titan Đen'],
                'storages' => [
                    ['label' => '256GB', 'price' => 28200000],
                    ['label' => '512GB', 'price' => 34000000],
                    ['label' => '1TB', 'price' => 37000000],
                ],
            ],
        ];
    }
}
