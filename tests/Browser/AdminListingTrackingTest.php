<?php

namespace Tests\Browser;

use App\Models\User;
use App\Models\Produce;
use App\Models\Listing;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class PBI34AdminListingTrackingTest extends DuskTestCase
{
    /**
     * Helper to create a farmer with listings.
     */
    private function createFarmerWithListings(): array
    {
        $farmer = User::factory()->create([
            'role' => 'farmer',
            'verification_status' => 'verified',
            'name' => 'Petani Listing Test',
        ]);

        $produce = Produce::first() ?? Produce::create([
            'name' => 'Bayam',
            'category' => 'Sayuran',
            'emoji' => '🥬',
        ]);

        $activeListing = Listing::create([
            'title' => 'Bayam Segar Admin Test',
            'content' => 'Bayam segar untuk admin listing test',
            'price' => 12000,
            'quantity' => 50,
            'unit' => 'kg',
            'status' => 'active',
            'produce_produce_id' => $produce->produce_id,
            'user_user_id' => $farmer->user_id,
        ]);

        $inactiveListing = Listing::create([
            'title' => 'Bayam Layu Admin Test',
            'content' => 'Bayam layu',
            'price' => 5000,
            'quantity' => 0,
            'unit' => 'kg',
            'status' => 'inactive',
            'produce_produce_id' => $produce->produce_id,
            'user_user_id' => $farmer->user_id,
        ]);

        return compact('farmer', 'produce', 'activeListing', 'inactiveListing');
    }

    /**
     * Test admin can access the listing management page.
     */
    public function test_admin_listing_list(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $setup = $this->createFarmerWithListings();

        $this->browse(function (Browser $browser) use ($admin, $setup) {
            $browser->loginAs($admin)
                ->visit('/admin/listings')
                ->assertPathIs('/admin/listings')
                ->assertSee('Kelola Listing 📦')
                ->assertSee('Daftar semua listing produk')
                ->assertSee('Bayam Segar Admin Test')
                ->assertSee('Petani Listing Test')  // Farmer name
                ->assertSee('Rp 12.000')
                ->assertSee('50 kg')                  // Stock column
                ->select('status', 'active')
                ->press('Filter')
                ->pause(1000)
                ->assertQueryStringHas('status', 'active')
                ->assertSee('Active');                 // Status badge
        });
    }

    public function test_admin_can_search_listings_by_title(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $setup = $this->createFarmerWithListings();

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin)
                ->visit('/admin/listings')
                ->type('search', 'Bayam Segar Admin Test')
                ->press('Filter')
                ->pause(1000)
                ->assertSee('Bayam Segar Admin Test');
        });
    }


    public function test_admin_can_see_different_status_badges(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $setup = $this->createFarmerWithListings();

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin)
                ->visit('/admin/listings')
                ->assertSee('Active')
                ->assertSee('Inactive');
        });
    }


    public function test_admin_can_deactivate_listing(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $setup = $this->createFarmerWithListings();

        $this->browse(function (Browser $browser) use ($admin, $setup) {
            $browser->loginAs($admin)
                ->visit('/admin/listings')
                ->assertSee('Bayam Segar Admin Test')
                // Find the deactivate button for the first active listing
                ->assertSee('🚫 Nonaktifkan');
        });
    }


    public function test_admin_can_see_listing_creation_date(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $setup = $this->createFarmerWithListings();

        $this->browse(function (Browser $browser) use ($admin, $setup) {
            $browser->loginAs($admin)
                ->visit('/admin/listings')
                ->assertSee($setup['activeListing']->created_at->format('d M Y'));
        });
    }


    public function test_admin_sees_produce_info(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $setup = $this->createFarmerWithListings();

        $this->browse(function (Browser $browser) use ($admin, $setup) {
            $browser->loginAs($admin)
                ->visit('/admin/listings')
                ->assertSee('Bayam');  // Produce name
        });
    }

  
    public function test_admin_sees_empty_listings(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin)
                ->visit('/admin/listings')
                ->type('search', 'NonExistentListingXYZ123')
                ->press('Filter')
                ->pause(1000)
                ->assertSee('Tidak ada listing ditemukan.');
        });
    }




    public function test_admin_can_filter_sold_out_listings(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $farmer = User::factory()->create([
            'role' => 'farmer',
            'verification_status' => 'verified',
        ]);

        $produce = Produce::first() ?? Produce::create([
            'name' => 'Wortel',
            'category' => 'Sayuran',
            'emoji' => '🥕',
        ]);

        Listing::create([
            'title' => 'Wortel Habis',
            'content' => 'Wortel sold out test',
            'price' => 15000,
            'quantity' => 0,
            'unit' => 'kg',
            'status' => 'sold_out',
            'produce_produce_id' => $produce->produce_id,
            'user_user_id' => $farmer->user_id,
        ]);

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin)
                ->visit('/admin/listings')
                ->select('status', 'sold_out')
                ->press('Filter')
                ->pause(1000)
                ->assertQueryStringHas('status', 'sold_out');
        });
    }

    /**
     * Test customer cannot access admin listings page.
     */
    public function test_customer_cannot_access_admin_listings(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);

        $this->browse(function (Browser $browser) use ($customer) {
            $browser->loginAs($customer)
                ->visit('/admin/listings')
                ->assertSee('403');
        });
    }

    /**
     * Test farmer cannot access admin listings page.
     */
    public function test_farmer_cannot_access_admin_listings(): void
    {
        $farmer = User::factory()->create([
            'role' => 'farmer',
            'verification_status' => 'verified',
        ]);

        $this->browse(function (Browser $browser) use ($farmer) {
            $browser->loginAs($farmer)
                ->visit('/admin/listings')
                ->assertSee('403');
        });
    }

    /**
     * Test unauthenticated user cannot access admin listings.
     */
    public function test_unauthenticated_cannot_access_admin_listings(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/admin/listings')
                ->assertSee('403');
        });
    }

    /**
     * Test admin filter with invalid status shows no results.
     */
    public function test_admin_filter_with_invalid_status(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin)
                ->visit('/admin/listings?status=nonexistent_status')
                ->assertSee('Tidak ada listing ditemukan.');
        });
    }
}
