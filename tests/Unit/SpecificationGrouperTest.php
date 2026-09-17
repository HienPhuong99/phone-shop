<?php

namespace Tests\Unit;

use App\Services\SpecificationGrouper;
use Tests\TestCase;

class SpecificationGrouperTest extends TestCase
{
    private SpecificationGrouper $grouper;

    protected function setUp(): void
    {
        parent::setUp();

        $this->grouper = new SpecificationGrouper;
    }

    public function test_specs_are_collected_into_their_groups_in_page_order(): void
    {
        $groups = $this->grouper->group([
            'Trọng lượng' => '227g',
            'Màn hình' => '6.9 inch OLED',
            'Pin' => '4.685 mAh',
            'Vi xử lý (CPU)' => 'A18 Pro',
        ]);

        $this->assertSame(
            ['Màn hình', 'Hiệu năng & Bộ nhớ', 'Pin & Sạc', 'Thiết kế & Chất liệu'],
            array_column($groups, 'label')
        );
        $this->assertSame(['Vi xử lý (CPU)' => 'A18 Pro'], $groups[1]['specs']);
    }

    public function test_groups_the_product_has_no_spec_for_are_left_out(): void
    {
        $groups = $this->grouper->group(['Màn hình' => '3.5 inch TFT']);

        $this->assertCount(1, $groups);
        $this->assertSame('Màn hình', $groups[0]['label']);
    }

    public function test_an_unrecognised_key_still_reaches_the_page(): void
    {
        $groups = $this->grouper->group([
            'Màn hình' => '6.1 inch',
            'Phụ kiện zin hộp' => 'Cáp Lightning, tai EarPods',
        ]);

        $other = end($groups);

        $this->assertSame('Thông số khác', $other['label']);
        $this->assertSame(['Phụ kiện zin hộp' => 'Cáp Lightning, tai EarPods'], $other['specs']);
    }

    public function test_no_spec_is_dropped_or_duplicated(): void
    {
        $specs = [
            'Màn hình' => 'a', 'Vi xử lý (CPU)' => 'b', 'Dung lượng RAM' => 'c',
            'Bộ nhớ trong' => 'd', 'Camera sau' => 'e', 'Camera trước' => 'f',
            'Pin' => 'g', 'Sạc' => 'h', 'Chất liệu' => 'i', 'Trọng lượng' => 'j',
            'Kích thước' => 'k', 'Màu sắc' => 'l', 'Kháng nước' => 'm',
            'Bảo mật' => 'n', 'Kết nối' => 'o', 'Cổng kết nối' => 'p',
            'Nút bấm' => 'q', 'Âm thanh' => 'r', 'Tính năng nổi bật' => 's',
            'Khoá lạ' => 't',
        ];

        $flattened = array_merge(...array_column($this->grouper->group($specs), 'specs'));

        $this->assertSame($specs, $flattened);
    }

    public function test_a_product_without_specifications_produces_no_groups(): void
    {
        $this->assertSame([], $this->grouper->group(null));
        $this->assertSame([], $this->grouper->group([]));
    }
}
