<?php

namespace Tests\Unit;

use App\Services\ShippingService;
use Tests\TestCase;

class ShippingServiceTest extends TestCase
{
    private ShippingService $shipping;

    protected function setUp(): void
    {
        parent::setUp();

        $this->shipping = new ShippingService;
    }

    public function test_the_two_biggest_metro_areas_get_the_cheapest_tier(): void
    {
        $this->assertSame(20000, $this->shipping->feeFor('Thành phố Hà Nội'));
        $this->assertSame(20000, $this->shipping->feeFor('Thành phố Hồ Chí Minh'));
    }

    public function test_other_centrally_controlled_cities_get_the_middle_tier(): void
    {
        $this->assertSame(25000, $this->shipping->feeFor('Thành phố Đà Nẵng'));
        $this->assertSame(25000, $this->shipping->feeFor('Thành phố Hải Phòng'));
        $this->assertSame(25000, $this->shipping->feeFor('Thành phố Cần Thơ'));
        $this->assertSame(25000, $this->shipping->feeFor('Thành phố Huế'));
    }

    public function test_every_other_province_gets_the_standard_tier(): void
    {
        $this->assertSame(35000, $this->shipping->feeFor('Lào Cai'));
        $this->assertSame(35000, $this->shipping->feeFor('Cà Mau'));
    }

    public function test_an_unrecognized_or_missing_province_falls_back_to_the_standard_tier(): void
    {
        $this->assertSame(35000, $this->shipping->feeFor(null));
        $this->assertSame(35000, $this->shipping->feeFor('Not A Real Province'));
    }
}
