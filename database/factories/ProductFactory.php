<?php

namespace Database\Factories;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $model = fake()->randomElement([
            'iPhone 15', 'iPhone 15 Pro', 'iPhone 14', 'Galaxy S24', 'Galaxy S24 Ultra',
            'Galaxy A55', 'Redmi Note 13', 'Xiaomi 14', 'Reno 11', 'Oppo A78',
            'Find X7', 'Galaxy Z Flip 5', 'iPhone 13', 'Redmi 13C', 'Xiaomi 13T',
        ]);
        $name = $model.' '.fake()->unique()->numerify('####');

        return [
            'category_id' => Category::inRandomOrder()->value('id'),
            'brand_id' => Brand::inRandomOrder()->value('id'),
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => fake()->paragraphs(3, true),
            'base_price' => fake()->numberBetween(3000000, 35000000),
            'thumbnail' => null,
            'status' => 'active',
        ];
    }
}
