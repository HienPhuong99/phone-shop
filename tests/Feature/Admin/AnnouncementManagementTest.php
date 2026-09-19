<?php

namespace Tests\Feature\Admin;

use App\Models\Announcement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnnouncementManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $regularUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create(['name' => 'Admin', 'email' => 'admin@test.com', 'password' => 'password', 'is_admin' => true]);
        $this->regularUser = User::create(['name' => 'User', 'email' => 'user@test.com', 'password' => 'password']);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/admin/announcements')->assertRedirect('/login');
    }

    public function test_non_admin_cannot_access_announcements(): void
    {
        $this->actingAs($this->regularUser)->get('/admin/announcements')->assertForbidden();
    }

    public function test_admin_can_create_an_announcement(): void
    {
        $response = $this->actingAs($this->admin)->post('/admin/announcements', [
            'title' => 'Khuyến mãi Tết 2026',
            'message' => 'Giảm giá đến 20% cho toàn bộ iPhone.',
            'active' => '1',
        ]);

        $response->assertRedirect(route('admin.announcements.index'));
        $this->assertDatabaseHas('announcements', ['title' => 'Khuyến mãi Tết 2026', 'active' => true]);
    }

    public function test_a_cta_label_without_a_url_is_rejected(): void
    {
        $response = $this->actingAs($this->admin)->post('/admin/announcements', [
            'title' => 'Khuyến mãi', 'message' => 'Nội dung', 'cta_label' => 'Xem ngay',
        ]);

        $response->assertInvalid(['cta_url']);
    }

    public function test_an_end_date_before_the_start_date_is_rejected(): void
    {
        $response = $this->actingAs($this->admin)->post('/admin/announcements', [
            'title' => 'Khuyến mãi', 'message' => 'Nội dung',
            'starts_at' => '2026-02-10 00:00:00', 'ends_at' => '2026-02-01 00:00:00',
        ]);

        $response->assertInvalid(['ends_at']);
    }

    public function test_admin_can_update_an_announcement(): void
    {
        $announcement = Announcement::create(['title' => 'Cũ', 'message' => 'M', 'active' => true]);

        $response = $this->actingAs($this->admin)->patch("/admin/announcements/{$announcement->id}", [
            'title' => 'Mới', 'message' => 'M2',
        ]);

        $response->assertRedirect(route('admin.announcements.index'));
        $this->assertSame('Mới', $announcement->fresh()->title);
        $this->assertFalse($announcement->fresh()->active); // "active" checkbox omitted = unchecked
    }

    public function test_admin_can_delete_an_announcement(): void
    {
        $announcement = Announcement::create(['title' => 'A', 'message' => 'M']);

        $response = $this->actingAs($this->admin)->delete("/admin/announcements/{$announcement->id}");

        $response->assertRedirect();
        $this->assertDatabaseCount('announcements', 0);
    }
}
