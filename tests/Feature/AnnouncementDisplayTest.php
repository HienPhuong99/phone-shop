<?php

namespace Tests\Feature;

use App\Models\Announcement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnnouncementDisplayTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_current_announcement_renders_on_the_storefront(): void
    {
        Announcement::create([
            'title' => 'Khuyến mãi Tết 2026', 'message' => 'Giảm giá đến 20%.', 'active' => true,
        ]);

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('Khuyến mãi Tết 2026');
        $response->assertSee('Giảm giá đến 20%.');
    }

    public function test_an_inactive_announcement_does_not_render(): void
    {
        Announcement::create(['title' => 'Đã tắt', 'message' => 'M', 'active' => false]);

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertDontSee('Đã tắt');
    }

    public function test_an_expired_announcement_does_not_render(): void
    {
        Announcement::create([
            'title' => 'Hết hạn rồi', 'message' => 'M', 'active' => true, 'ends_at' => now()->subDay(),
        ]);

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertDontSee('Hết hạn rồi');
    }

    public function test_a_call_to_action_renders_only_when_both_label_and_url_are_set(): void
    {
        Announcement::create([
            'title' => 'Có nút', 'message' => 'M', 'active' => true,
            'cta_label' => 'Xem ngay', 'cta_url' => '/san-pham',
        ]);

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('Xem ngay');
        $response->assertSee('href="/san-pham"', false);
    }

    public function test_no_popup_markup_renders_when_there_is_no_current_announcement(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertDontSee('id="announcement-title"', false);
    }
}
