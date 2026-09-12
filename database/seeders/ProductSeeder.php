<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Product::factory()
            ->count(15)
            ->create()
            ->each(function (Product $product) {
                ProductVariant::factory()
                    ->count(fake()->numberBetween(2, 4))
                    ->create(['product_id' => $product->id]);
            });
    }
}
