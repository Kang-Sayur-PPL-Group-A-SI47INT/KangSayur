<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\User;
use App\Models\Listing;
use App\Models\Produce;

class ReadFarmerProfileTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     */
    public function test_customer_can_view_farmer_profile_from_marketplace(): void
    {
        // Create farmer with public profile
        $farmer = User::factory()->create([
            'role' => 'farmer',
            'verification_status' => 'verified',
            'name' => 'Pak Tani Dusk',
            'city' => 'Bandung',
            'farm_description' => 'Kebun organik dataran tinggi.',
            'is_public_profile' => true,
        ]);

        // Create produce & listing for this farmer
        $produce = Produce::firstOrCreate(
            ['name' => 'Wortel'],
            ['category' => 'Sayuran', 'emoji' => '🥕']
        );

        Listing::create([
            'title' => 'Wortel Dusk Test',
            'content' => 'Wortel segar untuk pengujian.',
            'price' => 15000,
            'quantity' => 50,
            'unit' => 'kg',
            'status' => 'active',
            'user_user_id' => $farmer->user_id,
            'produce_produce_id' => $produce->produce_id,
        ]);

        // Create customer
        $customer = User::factory()->create([
            'role' => 'customer',
        ]);

        $this->browse(function (Browser $browser) use ($customer, $farmer) {
            $browser->loginAs($customer)
                ->visit('/marketplace')
                ->pause(1000)
                ->assertSee('The Marketplace')
                // Click the listing card that belongs to our farmer
                ->clickLink('Wortel Dusk Test')
                ->pause(1000)
                ->assertSee('Wortel Dusk Test')
                ->assertSee('Pak Tani Dusk')
                // Click the farmer profile link (avatar + name area)
                ->click('a[href*="farmer/profile/' . $farmer->user_id . '"]')
                ->pause(1000)
                ->assertPathIs('/farmer/profile/' . $farmer->user_id)
                ->assertSee('Pak Tani Dusk')
                ->assertSee('Bandung')
                ->assertSee('Kebun organik dataran tinggi.')
                ->assertSee('Wortel Dusk Test')
            ;
        });
    }

    public function test_customer_cannot_view_private_farmer_profile(): void
    {
        // Create farmer with private profile
        $farmer = User::factory()->create([
            'role' => 'farmer',
            'verification_status' => 'verified',
            'name' => 'Pak Private',
            'city' => 'Jakarta',
            'is_public_profile' => false,
        ]);

        // Create customer
        $customer = User::factory()->create([
            'role' => 'customer',
        ]);

        $this->browse(function (Browser $browser) use ($customer, $farmer) {
            $browser->loginAs($customer)
                ->visit('/farmer/profile/' . $farmer->user_id)
                ->pause(1000)
                ->assertSee('404')
            ;
        });
    }
}
