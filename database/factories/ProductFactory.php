<?php

namespace Database\Factories;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductSeries;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * Storefront is Apple-only. Catalog: model name => [series name, thumbnail path].
     * Every model has its own distinct image file (no two products share a URL)
     * so the storefront behaves like a real catalog for perf testing — each
     * product image is a genuine separate network request.
     *
     * @var array<string, array{0: string, 1: string}>
     */
    public const CATALOG = [
        'iPhone 15' => ['iPhone 15 Series', '/images/products/iphone-15.svg'],
        'iPhone 15 Pro' => ['iPhone 15 Series', '/images/products/iphone-15-pro.svg'],
        'iPhone 15 Pro Max' => ['iPhone 15 Series', '/images/products/iphone-15-pro-max.svg'],
        'iPhone 14' => ['iPhone 14 Series', '/images/products/iphone-14.svg'],
        'iPhone 14 Pro' => ['iPhone 14 Series', '/images/products/iphone-14-pro.svg'],
        'iPhone 13' => ['iPhone 13 Series', '/images/products/iphone-13.svg'],
        'iPhone 13 Pro' => ['iPhone 13 Series', '/images/products/iphone-13-pro.svg'],
        'iPhone 12' => ['iPhone 12 Series', '/images/products/iphone-12.svg'],
        'iPhone 12 Pro' => ['iPhone 12 Series', '/images/products/iphone-12-pro.svg'],
        'iPhone 11' => ['iPhone 11 Series', '/images/products/iphone-11.svg'],
        'iPhone 11 Pro' => ['iPhone 11 Series', '/images/products/iphone-11-pro.svg'],
        'iPhone XS' => ['iPhone X Series', '/images/products/iphone-xs.svg'],
        'iPhone XS Max' => ['iPhone X Series', '/images/products/iphone-xs-max.svg'],
    ];

    public function definition(): array
    {
        $model = fake()->randomElement(array_keys(self::CATALOG));
        [$seriesName, $thumbnail] = self::CATALOG[$model];
        $name = $model.' '.fake()->unique()->numerify('####');

        return [
            'category_id' => Category::where('name', 'Điện thoại')->value('id'),
            'brand_id' => Brand::where('name', 'Apple')->value('id'),
            'series_id' => ProductSeries::where('name', $seriesName)->value('id'),
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => fake()->paragraphs(3, true),
            'base_price' => fake()->numberBetween(3000000, 35000000),
            'thumbnail' => $thumbnail,
            'status' => 'active',
        ];
    }
}
