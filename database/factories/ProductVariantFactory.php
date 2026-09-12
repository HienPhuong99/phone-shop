<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProductVariant>
 */
class ProductVariantFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'color' => fake()->randomElement(['Đen', 'Trắng', 'Xanh', 'Vàng', 'Tím', 'Bạc']),
            'storage' => fake()->randomElement(['128GB', '256GB', '512GB', '1TB']),
            'price' => fake()->numberBetween(3000000, 40000000),
            'sku' => strtoupper(fake()->unique()->bothify('SKU-????-####')),
            'stock_quantity' => fake()->numberBetween(0, 100),
        ];
    }
}
