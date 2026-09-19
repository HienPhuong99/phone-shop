<?php

namespace Tests\Unit;

use App\Models\Product;
use App\Services\ProductComparator;
use Illuminate\Support\Collection;
use Tests\TestCase;

class ProductComparatorTest extends TestCase
{
    private ProductComparator $comparator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->comparator = new ProductComparator;
    }

    /**
     * @param  list<array<string, string>>  $specs
     * @return Collection<int, Product>
     */
    private function products(array $specs): Collection
    {
        return collect($specs)->map(fn (array $s) => new Product(['specifications' => $s]));
    }

    public function test_identical_values_are_a_match_with_no_winner(): void
    {
        $products = $this->products([
            ['Chất liệu' => 'Khung nhôm'],
            ['Chất liệu' => 'Khung nhôm'],
        ]);

        $row = $this->comparator->compareRow('Chất liệu', $products);

        $this->assertTrue($row['isMatch']);
        $this->assertNull($row['winnerIndex']);
        $this->assertNull($row['delta']);
    }

    public function test_a_trailing_parenthetical_aside_does_not_hide_a_real_match(): void
    {
        $products = $this->products([
            ['Dung lượng RAM' => '8GB'],
            ['Dung lượng RAM' => '8GB (tối ưu hóa phần cứng sâu cho Apple Intelligence)'],
        ]);

        $row = $this->comparator->compareRow('Dung lượng RAM', $products);

        $this->assertTrue($row['isMatch']);
    }

    public function test_a_winnable_spec_flags_the_higher_value_with_its_margin(): void
    {
        $products = $this->products([
            ['Pin' => 'Pin 4.422 mAh rất trâu'],
            ['Pin' => '4.685 mAh (viên pin lớn nhất từ trước đến nay)'],
        ]);

        $row = $this->comparator->compareRow('Pin', $products);

        $this->assertFalse($row['isMatch']);
        $this->assertSame(1, $row['winnerIndex']);
        $this->assertSame('+263 mAh', $row['delta']);
    }

    public function test_a_lower_is_better_spec_flags_the_lighter_value(): void
    {
        $products = $this->products([
            ['Trọng lượng' => '221g (cầm nhẹ hơn rõ rệt so với 14 Pro Max)'],
            ['Trọng lượng' => '227g'],
        ]);

        $row = $this->comparator->compareRow('Trọng lượng', $products);

        $this->assertSame(0, $row['winnerIndex']);
        $this->assertSame('Nhẹ hơn 6g', $row['delta']);
    }

    public function test_the_closest_competitor_sets_the_margin_not_the_farthest_one(): void
    {
        // Regression: for a "lower wins" spec across 3 products, the margin
        // must come from the second-lightest phone, not the heaviest one.
        $products = $this->products([
            ['Trọng lượng' => '170g'],
            ['Trọng lượng' => '227g'],
            ['Trọng lượng' => '199g'],
        ]);

        $row = $this->comparator->compareRow('Trọng lượng', $products);

        $this->assertSame(0, $row['winnerIndex']);
        $this->assertSame('Nhẹ hơn 29g', $row['delta']);
    }

    public function test_storage_option_lists_compare_by_their_largest_tier(): void
    {
        $products = $this->products([
            ['Bộ nhớ trong' => '256GB / 512GB / 1TB'],
            ['Bộ nhớ trong' => '128GB / 256GB'],
        ]);

        $row = $this->comparator->compareRow('Bộ nhớ trong', $products);

        $this->assertSame(0, $row['winnerIndex']);
        $this->assertSame('+768GB', $row['delta']);
    }

    public function test_a_tie_declares_no_winner(): void
    {
        $products = $this->products([
            ['Pin' => '4.685 mAh'],
            ['Pin' => '4.685 mAh'],
        ]);

        $row = $this->comparator->compareRow('Pin', $products);

        // Same value both ways: it's a match, not a tied "no winner" race.
        $this->assertTrue($row['isMatch']);
        $this->assertNull($row['winnerIndex']);
    }

    public function test_non_winnable_specs_only_get_diff_coloring_never_a_winner(): void
    {
        $products = $this->products([
            ['Camera sau' => 'Chính 48MP + Siêu rộng 12MP'],
            ['Camera sau' => 'Chính 48MP Fusion + Siêu rộng 48MP sắc nét'],
        ]);

        $row = $this->comparator->compareRow('Camera sau', $products);

        $this->assertFalse($row['isMatch']);
        $this->assertNull($row['winnerIndex']);
        $this->assertNull($row['delta']);
    }

    public function test_a_spec_missing_from_one_product_is_not_a_match(): void
    {
        $products = $this->products([
            ['Sạc' => 'Sạc MagSafe 25W'],
            [],
        ]);

        $row = $this->comparator->compareRow('Sạc', $products);

        $this->assertFalse($row['isMatch']);
        $this->assertSame(['Sạc MagSafe 25W', null], $row['values']);
    }
}
