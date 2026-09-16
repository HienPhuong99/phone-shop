<?php

namespace App\Services;

class ShippingService
{
    /**
     * The 34 Vietnamese provinces/cities (after the July 2025 merger that
     * consolidated the previous 63 units), for the checkout address form
     * and for pricing shipping by region.
     *
     * @var list<string>
     */
    public const PROVINCES = [
        'An Giang', 'Bắc Ninh', 'Cà Mau', 'Cao Bằng', 'Đắk Lắk', 'Điện Biên',
        'Đồng Nai', 'Đồng Tháp', 'Gia Lai', 'Hà Tĩnh', 'Hưng Yên', 'Khánh Hòa',
        'Lai Châu', 'Lạng Sơn', 'Lào Cai', 'Lâm Đồng', 'Nghệ An', 'Ninh Bình',
        'Phú Thọ', 'Quảng Ngãi', 'Quảng Ninh', 'Quảng Trị', 'Sơn La',
        'Tây Ninh', 'Thái Nguyên', 'Thanh Hóa', 'Tuyên Quang', 'Vĩnh Long',
        'Thành phố Hà Nội', 'Thành phố Hải Phòng', 'Thành phố Đà Nẵng',
        'Thành phố Cần Thơ', 'Thành phố Huế', 'Thành phố Hồ Chí Minh',
    ];

    /**
     * The two largest metro areas — fastest and cheapest to ship to.
     *
     * @var list<string>
     */
    private const TIER_1_FEE = 20000;

    private const TIER_1_PROVINCES = ['Thành phố Hà Nội', 'Thành phố Hồ Chí Minh'];

    /**
     * Other centrally-controlled cities — still well-served, slightly more.
     */
    private const TIER_2_FEE = 25000;

    private const TIER_2_PROVINCES = ['Thành phố Đà Nẵng', 'Thành phố Hải Phòng', 'Thành phố Cần Thơ', 'Thành phố Huế'];

    /**
     * Everywhere else.
     */
    private const TIER_3_FEE = 35000;

    /**
     * Shipping fee in VND for an address in $province. Falls back to the
     * standard tier for an unrecognized or missing province, so checkout
     * never breaks over a bad value.
     */
    public function feeFor(?string $province): int
    {
        return match (true) {
            in_array($province, self::TIER_1_PROVINCES, true) => self::TIER_1_FEE,
            in_array($province, self::TIER_2_PROVINCES, true) => self::TIER_2_FEE,
            default => self::TIER_3_FEE,
        };
    }
}
