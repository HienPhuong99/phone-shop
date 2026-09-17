<?php

namespace App\Services;

class SpecificationGrouper
{
    /**
     * Display groups, in page order. Each entry lists the standard spec
     * keys it collects; a key not listed anywhere lands in GROUP_OTHER so
     * nothing a product declares is ever dropped from the page.
     *
     * @var array<string, list<string>>
     */
    private const GROUPS = [
        'Màn hình' => ['Màn hình'],
        'Hiệu năng & Bộ nhớ' => ['Vi xử lý (CPU)', 'Dung lượng RAM', 'Bộ nhớ trong'],
        'Camera' => ['Camera sau', 'Camera trước'],
        'Pin & Sạc' => ['Pin', 'Sạc'],
        'Thiết kế & Chất liệu' => ['Chất liệu', 'Trọng lượng', 'Kích thước', 'Màu sắc'],
        'Kết nối & Tính năng' => ['Kháng nước', 'Bảo mật', 'Kết nối', 'Cổng kết nối', 'Nút bấm', 'Âm thanh', 'Tính năng nổi bật'],
    ];

    private const GROUP_OTHER = 'Thông số khác';

    /**
     * Icon name per group, resolved by the Blade view to an inline SVG.
     *
     * @var array<string, string>
     */
    private const ICONS = [
        'Màn hình' => 'display',
        'Hiệu năng & Bộ nhớ' => 'chip',
        'Camera' => 'camera',
        'Pin & Sạc' => 'battery',
        'Thiết kế & Chất liệu' => 'shield',
        'Kết nối & Tính năng' => 'signal',
        self::GROUP_OTHER => 'dots',
    ];

    /**
     * Turns a product's flat specifications map into display groups,
     * keeping GROUPS' order and dropping groups the product has no spec
     * for.
     *
     * @param  array<string, string>|null  $specifications
     * @return list<array{label: string, icon: string, specs: array<string, string>}>
     */
    public function group(?array $specifications): array
    {
        if (empty($specifications)) {
            return [];
        }

        $grouped = [];
        $claimed = [];

        foreach (self::GROUPS as $label => $keys) {
            $specs = [];
            foreach ($keys as $key) {
                if (isset($specifications[$key])) {
                    $specs[$key] = $specifications[$key];
                    $claimed[$key] = true;
                }
            }

            if ($specs !== []) {
                $grouped[] = ['label' => $label, 'icon' => self::ICONS[$label], 'specs' => $specs];
            }
        }

        $leftovers = array_diff_key($specifications, $claimed);

        if ($leftovers !== []) {
            $grouped[] = [
                'label' => self::GROUP_OTHER,
                'icon' => self::ICONS[self::GROUP_OTHER],
                'specs' => $leftovers,
            ];
        }

        return $grouped;
    }
}
