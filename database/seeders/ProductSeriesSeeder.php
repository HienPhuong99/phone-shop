<?php

namespace Database\Seeders;

use App\Models\ProductSeries;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $series = [
            'iPhone 16 Series' => 'Dòng mới nhất, tối ưu Apple Intelligence với phím Camera Control.',
            'iPhone 15 Series' => 'Camera 48MP và khung Titan trên bản Pro, cổng USB-C toàn dòng.',
            'iPhone 14 Series' => 'Hiệu năng mạnh, Dynamic Island trên các bản Pro.',
            'iPhone 13 Series' => 'Lựa chọn giá tốt, vẫn còn cập nhật iOS mới.',
            'iPhone 12 Series' => 'Thiết kế khung vuông, hỗ trợ MagSafe.',
            'iPhone 11 Series' => 'Pin trâu, phù hợp nhu cầu dùng phổ thông.',
            'iPhone X Series' => 'Dòng máy cũ, phù hợp ngân sách hạn chế.',
            'iPhone SE Series' => 'Thân máy nhỏ gọn Touch ID, cấu hình flagship với giá tốt.',
            'iPhone 8 Series' => 'Nút Home cuối cùng, mặt lưng kính hỗ trợ sạc không dây.',
            'iPhone 7 Series' => 'Camera kép chụp chân dung đầu tiên, kháng nước chuẩn IP67.',
            'iPhone 6 Series' => 'Màn hình lớn đầu tiên, thân máy mỏng nhẹ bo tròn.',
            'iPhone 5 Series' => 'Màn hình 4 inch tỉ lệ 16:9, cổng Lightning, Touch ID ra mắt.',
            'iPhone 4 Series' => 'Thiết kế khung thép kẹp kính, màn hình Retina đầu tiên.',
            'iPhone Cổ Điển' => 'Thế hệ khai sinh smartphone hiện đại, hàng sưu tầm.',
        ];

        foreach (array_values(array_keys($series)) as $index => $name) {
            ProductSeries::updateOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'name' => $name,
                    'description' => $series[$name],
                    'sort_order' => $index,
                ]
            );
        }
    }
}
