<?php

namespace Tests\Browser;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class PBI33AdminUserTrackingTest extends DuskTestCase
{
    /**
     * Test admin can access the user management page.
     */
    public function test_admin_user_list(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $customer = User::factory()->create([
            'role' => 'customer',
            'name' => 'Customer Tracking Test',
        ]);

        $farmer = User::factory()->create([
            'role' => 'farmer',
            'verification_status' => 'verified',
            'name' => 'Farmer Tracking Test',
        ]);

        $this->browse(function (Browser $browser) use ($admin, $customer, $farmer) {
            $browser->loginAs($admin)
                ->visit('/admin/users')
                ->assertPathIs('/admin/users')
                ->assertSee('Kelola Pengguna 👥')
                ->assertSee('Daftar semua pengguna terdaftar')
                ->assertSee($customer->name)
                ->assertSee($farmer->name)
                ->assertSee($customer->email)
                ->assertSee($farmer->email)
                ->assertSee('Customer')
                ->assertSee('Farmer')
                ->assertSee('Admin')
                ->assertSee('Verified')
                ->select('role', 'farmer')
                ->press('Filter')
                ->pause(1000)
                ->assertQueryStringHas('role', 'farmer')
                ->type('search', 'Customer Tracking Test')
                ->select('role', 'customer')
                ->press('Filter')
                ->pause(1000)
                ->assertSee('Customer Tracking Test')
                ->select('status', 'banned')
                ->press('Filter')
                ->pause(1000)
                ->assertQueryStringHas('status', 'banned')
                ->assertSee('Banned');
        });
    }

    /**
     * Test customer cannot access admin users page.
     */
    public function test_customer_cannot_access_admin_users(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);

        $this->browse(function (Browser $browser) use ($customer) {
            $browser->loginAs($customer)
                ->visit('/admin/users')
                ->assertSee('403');
        });
    }

    /**
     * Test farmer cannot access admin users page.
     */
    public function test_farmer_cannot_access_admin_users(): void
    {
        $farmer = User::factory()->create([
            'role' => 'farmer',
            'verification_status' => 'verified',
        ]);

        $this->browse(function (Browser $browser) use ($farmer) {
            $browser->loginAs($farmer)
                ->visit('/admin/users')
                ->assertSee('403');
        });
    }

    /**
     * Test admin search for nonexistent user.
     */
    public function test_admin_search_nonexistent_user(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin)
                ->visit('/admin/users')
                ->type('search', 'NonExistentUserXYZ99999')
                ->press('Filter')
                ->pause(1000)
                ->assertDontSee('Customer Tracking Test');
        });
    }

    /**
     * Test unauthenticated user cannot access admin users.
     */
    public function test_unauthenticated_cannot_access_admin_users(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/admin/users')
                ->assertPathIs('/login');
        });
    }

}
