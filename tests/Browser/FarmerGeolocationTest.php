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
    // ─── Positive Cases ──────────────────────────────────────────────────────

    /**
     * Farmer can see the interactive map on profile edit, search for a location,
     * save the profile, and see the updated coordinates displayed on the page.
     */
    public function test_farmer_map(): void
    {
        $farmer = User::factory()->create([
            'role'                => 'farmer',
            'verification_status' => 'verified',
        ]);

        $this->browse(function (Browser $browser) use ($farmer) {
            $browser->loginAs($farmer)
                ->visit('/farmer/profile')
                ->assertPathIs('/farmer/profile')
                ->assertSee('Lokasi Pertanian')
                ->assertPresent('#farmer-map')
                ->type('name', 'Petani Geolocation Test')
                // city and address are readonly — auto-filled from map pin
                ->type('farmer-mapp-search', 'telkom indonesia')
                ->keys('input[name="farmer-mapp-search"]', '{enter}')
                ->pause(2000)
                ->press('Simpan Perubahan')
                ->pause(1000)
                ->assertPathIs('/farmer/profile')
                ->assertSee('Profil berhasil diperbarui')
                ->assertSee('-8.67233730')
                ->assertSee('115.22623390');
        });
    }

    /**
     * Farmer map search input is present and has the expected attributes.
     */
    public function test_farmer_map_search(): void
    {
        $farmer = User::factory()->create([
            'role'                => 'farmer',
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

    // ─── Negative Cases ──────────────────────────────────────────────────────

    /**
     * Farmer profile save is blocked by JS validation when no map pin has been
     * placed (latitude / longitude hidden inputs are empty).
     * The error banner should become visible.
     */
    public function test_farmer_save_blocked_without_map_pin(): void
    {
        $farmer = User::factory()->create([
            'role'                => 'farmer',
            'verification_status' => 'verified',
            // No latitude / longitude — no pin pre-set
        ]);

        $this->browse(function (Browser $browser) use ($farmer) {
            $browser->loginAs($farmer)
                ->visit('/farmer/profile')
                ->assertPathIs('/farmer/profile')
                ->type('name', 'Petani Tanpa Peta')
                // Intentionally skip the map — hidden inputs remain empty
                ->press('Simpan Perubahan')
                ->pause(500)
                ->assertSee('Harap pilih lokasi pertanian pada peta sebelum menyimpan.');
        });
    }

    /**
     * Customer role is denied access to the farmer profile edit page (403).
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
     * Unauthenticated user visiting the farmer profile edit page is redirected
     * to the login page.
     */
    public function test_unauthenticated_cannot_access_farmer_profile(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/farmer/profile')
                ->assertPathIs('/login');
        });
    }

    /**
     * Farmer profile page shows the map search box and geolocation button
     * even when the farmer has no existing coordinates.
     */
    public function test_farmer_map_visible_without_existing_coordinates(): void
    {
        $farmer = User::factory()->create([
            'role'                => 'farmer',
            'verification_status' => 'verified',
            // No latitude / longitude
        ]);

        $this->browse(function (Browser $browser) use ($farmer) {
            $browser->loginAs($farmer)
                ->visit('/farmer/profile')
                ->assertPresent('#farmer-map')
                ->assertPresent('#farmer-map-search')
                ->assertPresent('#farmer-geolocate-btn')
                ->assertSee('Belum diatur'); // default coordinate display text
        });
    }
}
