<?php

namespace Tests\Browser;

use App\Models\User;
use App\Models\Produce;
use App\Models\Listing;
use App\Models\Cart;
use App\Models\CartItem;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class FarmerGeolocationTest extends DuskTestCase
{
    // ─── Farmer Geolocation Tests ───

    /**
     * Test farmer can see the interactive map on profile edit page.
     */
    public function test_farmer_map(): void
    {
        $farmer = User::factory()->create([
            'role' => 'farmer',
            'verification_status' => 'verified',
        ]);

        $this->browse(function (Browser $browser) use ($farmer) {
            $browser->loginAs($farmer)
                ->visit('/farmer/profile')
                ->assertPathIs('/farmer/profile')
                ->assertSee('Lokasi Pertanian')
                ->assertPresent('#farmer-map')
                ->type('name', 'Petani Geolocation Test')
                ->type('city', 'Bandung')
                ->type('address', 'Jl. Dago No. 100')
                ->type('farmer-mapp-search', 'telkom indonesia') // 'search' is the name or CSS selector of the input
                ->keys('input[name="farmer-mapp-search"]', '{enter}') 
                ->pause(1000)
                ->press('Simpan Perubahan')
                ->pause(1000)
                ->assertPathIs('/farmer/profile')
                ->assertSee('Profil berhasil diperbarui')
                ->assertSee('-8.67233730')
                ->assertSee('115.22623390');
        });
    }


    /**
     * Test farmer map search input is present and functional.
     */
    public function test_farmer_map_search(): void
    {
        $farmer = User::factory()->create([
            'role' => 'farmer',
            'verification_status' => 'verified',
        ]);

        $this->browse(function (Browser $browser) use ($farmer) {
            $browser->loginAs($farmer)
                ->visit('/farmer/profile')
                ->assertPresent('#farmer-map-search')
                ->assertAttribute('#farmer-map-search', 'placeholder', 'Cari alamat atau nama tempat...')
                ->assertPresent('#farmer-geolocate-btn')
                ->assertSeeIn('#farmer-geolocate-btn', 'Lokasi Saya');
        });
    }

    /**
     * Test customer cannot access farmer profile edit page.
     */
    public function test_customer_cannot_access_farmer_profile_edit(): void
    {
        $customer = User::factory()->create([
            'role' => 'customer',
        ]);

        $this->browse(function (Browser $browser) use ($customer) {
            $browser->loginAs($customer)
                ->visit('/farmer/profile')
                ->assertSee('403');
        });
    }

    /**
     * Test unauthenticated user cannot access farmer profile edit.
     */
    public function test_unauthenticated_cannot_access_farmer_profile(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/farmer/profile')
                ->assertPathIs('/login');
        });
    }

    /**
     * Test farmer save profile without name fails.
     */
    public function test_farmer_save_profile_without_name(): void
    {
        $farmer = User::factory()->create([
            'role' => 'farmer',
            'verification_status' => 'verified',
        ]);

        $this->browse(function (Browser $browser) use ($farmer) {
            $browser->loginAs($farmer)
                ->visit('/farmer/profile')
                ->assertPathIs('/farmer/profile')
                ->type('name', '')
                ->press('Simpan Perubahan')
                ->pause(1000)
                ->assertPathIs('/farmer/profile')
                ->assertDontSee('Profil berhasil diperbarui');
        });
    }
}
