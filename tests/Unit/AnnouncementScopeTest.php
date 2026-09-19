<?php

namespace Tests\Unit;

use App\Models\Announcement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnnouncementScopeTest extends TestCase
{
    use RefreshDatabase;

    public function test_an_active_announcement_with_no_dates_is_current(): void
    {
        Announcement::create(['title' => 'A', 'message' => 'M', 'active' => true]);

        $this->assertSame(1, Announcement::current()->count());
    }

    public function test_an_inactive_announcement_is_never_current(): void
    {
        Announcement::create(['title' => 'A', 'message' => 'M', 'active' => false]);

        $this->assertSame(0, Announcement::current()->count());
    }

    public function test_an_announcement_scheduled_for_the_future_is_not_current_yet(): void
    {
        Announcement::create([
            'title' => 'A', 'message' => 'M', 'active' => true,
            'starts_at' => now()->addDay(),
        ]);

        $this->assertSame(0, Announcement::current()->count());
    }

    public function test_an_announcement_past_its_end_date_is_no_longer_current(): void
    {
        Announcement::create([
            'title' => 'A', 'message' => 'M', 'active' => true,
            'ends_at' => now()->subDay(),
        ]);

        $this->assertSame(0, Announcement::current()->count());
    }

    public function test_an_announcement_inside_its_window_is_current(): void
    {
        Announcement::create([
            'title' => 'A', 'message' => 'M', 'active' => true,
            'starts_at' => now()->subDay(), 'ends_at' => now()->addDay(),
        ]);

        $this->assertSame(1, Announcement::current()->count());
    }

    public function test_schedule_label_reflects_where_the_announcement_stands(): void
    {
        $off = Announcement::create(['title' => 'A', 'message' => 'M', 'active' => false]);
        $scheduled = Announcement::create(['title' => 'B', 'message' => 'M', 'active' => true, 'starts_at' => now()->addDay()]);
        $expired = Announcement::create(['title' => 'C', 'message' => 'M', 'active' => true, 'ends_at' => now()->subDay()]);
        $live = Announcement::create(['title' => 'D', 'message' => 'M', 'active' => true]);

        $this->assertSame('Đã tắt', $off->schedule_label);
        $this->assertSame('Lên lịch', $scheduled->schedule_label);
        $this->assertSame('Đã hết hạn', $expired->schedule_label);
        $this->assertSame('Đang hiện', $live->schedule_label);
    }
}
