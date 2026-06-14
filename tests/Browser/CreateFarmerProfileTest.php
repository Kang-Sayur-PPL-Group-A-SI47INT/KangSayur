<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\User;

class CreateFarmerProfileTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     */
    public function test_update_farmer_profile_success(): void
    {
        $user = User::factory()->create([
            'role' => 'farmer',
            'verification_status' => 'verified'
        ]);

        $this->browse(function ($browser) use ($user): void {
            $browser->loginAs($user)
                ->visit('/farmer/dashboard')
                ->pause(1000)
                ->assertPathIs('/farmer/dashboard')
                ->assertSee('Manage Produce')
                ->click('button.flex.items-center.p-1.rounded-full')
                ->pause(1000)
                ->clickLink('Edit Profile')
                ->pause(1000)
                ->assertPathIs('/farmer/profile')
                ->type('name', 'Pak Tani Sukses')
                ->type('farm_description', 'Kebun hidroponik modern dengan teknologi IoT.')
                // city and address are readonly — auto-filled from map pin
                ->type('farmer-mapp-search', 'telkom indonesia') // 'search' is the name or CSS selector of the input
                ->pause(1000)
                ->keys('input[name="farmer-mapp-search"]', '{enter}')
                ->pause(2000)
                ->assertSee("-8.67233730, 115.22623390")
                ->press('Simpan Perubahan')
                ->assertPathIs('/farmer/profile')
                ->assertSee('Profil berhasil diperbarui.')
            ;
        });
    }

    public function test_update_farmer_profile_failed(): void
    {
        $user = User::factory()->create([
            'role' => 'farmer',
            'verification_status' => 'verified'
        ]);

        $this->browse(function ($browser) use ($user): void {
            $browser->loginAs($user)
                ->visit('/farmer/dashboard')
                ->pause(1000)
                ->assertPathIs('/farmer/dashboard')
                ->assertSee('Manage Produce')
                ->click('button.flex.items-center.p-1.rounded-full')
                ->pause(1000)
                ->clickLink('Edit Profile')
                ->pause(1000)
                ->assertPathIs('/farmer/profile')
                ->type('name', '')
                ->type('farm_description', 'Kebun hidroponik modern dengan teknologi IoT.')
                // city and address are readonly — auto-filled from map pin
                ->type('farmer-mapp-search', 'telkom indonesia') // 'search' is the name or CSS selector of the input
                ->keys('input[name="farmer-mapp-search"]', '{enter}')
                ->pause(1000)
                ->press('Simpan Perubahan')
                ->assertPathIs('/farmer/profile')
                ->assertDontSee('Profil berhasil diperbarui.')
            ;
        });
    }

    /**
     * Test update farmer profile fails when name exceeds max length.
     */
    public function test_update_farmer_profile_name_exceeds_max_length(): void
    {
        $user = User::factory()->create([
            'role' => 'farmer',
            'verification_status' => 'verified'
        ]);

        $this->browse(function ($browser) use ($user): void {
            $browser->loginAs($user)
                ->visit('/farmer/profile')
                ->pause(1000)
                ->assertPathIs('/farmer/profile')
                ->type('name', str_repeat('A', 50))
                ->type('farm_description', 'Test description')
                // city and address are readonly — auto-filled from map pin
                ->press('Simpan Perubahan')
                ->assertPathIs('/farmer/profile')
                ->assertDontSee('Profil berhasil diperbarui.')
            ;
        });
    }


    /**
     * Test customer cannot access farmer profile edit page.
     */
    public function test_customer_cannot_access_farmer_profile_edit(): void
    {
        $user = User::factory()->create([
            'role' => 'customer'
        ]);

        $this->browse(function ($browser) use ($user): void {
            $browser->loginAs($user)
                ->visit('/farmer/profile')
                ->pause(1000)
                ->assertSee('403')
            ;
        });
    }
}
