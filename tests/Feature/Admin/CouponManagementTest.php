<?php

namespace Tests\Feature\Admin;

use App\Models\Coupon;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CouponManagementTest extends TestCase
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
        $this->get('/admin/coupons')->assertRedirect('/login');
    }

    public function test_non_admin_cannot_access_coupons(): void
    {
        $this->actingAs($this->regularUser)->get('/admin/coupons')->assertForbidden();
    }

    public function test_admin_can_create_a_coupon(): void
    {
        $response = $this->actingAs($this->admin)->post('/admin/coupons', [
            'code' => 'tet2026',
            'type' => 'fixed',
            'value' => 500000,
            'active' => '1',
        ]);

        $response->assertRedirect(route('admin.coupons.index'));
        $this->assertDatabaseHas('coupons', ['code' => 'TET2026', 'type' => 'fixed', 'value' => 500000]);
    }

    public function test_percent_coupon_over_100_is_rejected(): void
    {
        $response = $this->actingAs($this->admin)->post('/admin/coupons', [
            'code' => 'QUANHIEU',
            'type' => 'percent',
            'value' => 150,
        ]);

        $response->assertInvalid(['value']);
        $this->assertDatabaseCount('coupons', 0);
    }

    public function test_duplicate_code_is_rejected(): void
    {
        Coupon::create(['code' => 'GIAM10', 'type' => 'percent', 'value' => 10]);

        $response = $this->actingAs($this->admin)->post('/admin/coupons', [
            'code' => 'giam10',
            'type' => 'percent',
            'value' => 5,
        ]);

        $response->assertInvalid(['code']);
    }

    public function test_admin_can_update_a_coupon(): void
    {
        $coupon = Coupon::create(['code' => 'GIAM10', 'type' => 'percent', 'value' => 10, 'active' => true]);

        $response = $this->actingAs($this->admin)->patch("/admin/coupons/{$coupon->id}", [
            'code' => 'GIAM10',
            'type' => 'percent',
            'value' => 15,
        ]);

        $response->assertRedirect(route('admin.coupons.index'));
        $this->assertEquals(15, $coupon->fresh()->value);
        $this->assertFalse($coupon->fresh()->active); // "active" checkbox omitted = unchecked
    }

    public function test_admin_can_delete_a_coupon(): void
    {
        $coupon = Coupon::create(['code' => 'GIAM10', 'type' => 'percent', 'value' => 10]);

        $response = $this->actingAs($this->admin)->delete("/admin/coupons/{$coupon->id}");

        $response->assertRedirect();
        $this->assertDatabaseCount('coupons', 0);
    }
}
