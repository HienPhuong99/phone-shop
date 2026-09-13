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
            'iPhone 15 Series' => 'Dòng mới nhất, camera 48MP và khung Titan trên bản Pro.',
            'iPhone 14 Series' => 'Hiệu năng mạnh, Dynamic Island trên các bản Pro.',
            'iPhone 13 Series' => 'Lựa chọn giá tốt, vẫn còn cập nhật iOS mới.',
            'iPhone 12 Series' => 'Thiết kế khung vuông, hỗ trợ MagSafe.',
            'iPhone 11 Series' => 'Pin trâu, phù hợp nhu cầu dùng phổ thông.',
            'iPhone X Series' => 'Dòng máy cũ, phù hợp ngân sách hạn chế.',
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
