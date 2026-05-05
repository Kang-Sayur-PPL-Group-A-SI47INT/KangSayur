<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\User;
use App\Models\Produce;
use App\Models\Listing;

class UpdateProductInformationTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     */
    public function test_update_product_information(): void
    {

        $farmer = User::factory()->create([
            'role' => 'farmer'
        ]);

        $product = Produce::create([
            'name' => 'Wortel Segar Organik',
            'category' => 'Sayuran',
            'emoji' => '🥕',
        ]);

        $listing = Listing::create([
            'title' => 'Wortel Segar Organik',
            'content' => 'Wortel segar organik berkualitas tinggi',
            'price' => 1000,
            'unit' => 'kg',
            'quantity' => 100,
            'date' => 05202026,
            'status' => 'active',
            'produce_produce_id' => $product->produce_id,
            'user_user_id' => $farmer->user_id,
        ]);

        $this->browse(function (Browser $browser) use ($farmer, $product, $listing) {
            $browser->loginAs($farmer)
                ->visit('/farmer/listings')
                ->assertSee('Edit')
                ->clickLink('Edit')
                ->assertPathIs('/farmer/listings/' . $listing->listing_id . '/edit')
                ->type('title', 'Wortel Segar Organik')
                ->type('content', 'Wortel segar organik berkualitas tinggi')
                ->type('price', '1000')
                ->type('unit', 'kg')
                ->type('availability_date', '05202026')
                ->type('quantity', '100')
                ->select('produce_produce_id', $product->produce_id)
                ->select('status', 'active')
                ->press('Update Listing')
                ->pause(1000)
                ->assertSee('Listing updated successfully!')
                ->assertPathIs('/farmer/listings');

        });
    }

    public function test_update_product_information_fail(): void
    {

        $farmer = User::factory()->create([
            'role' => 'farmer'
        ]);

        $product = Produce::create([
            'name' => 'Wortel Segar Organik',
            'category' => 'Sayuran',
            'emoji' => '🥕',
        ]);

        $listing = Listing::create([
            'title' => 'Wortel Segar Organik',
            'content' => 'Wortel segar organik berkualitas tinggi',
            'price' => 1000,
            'unit' => 'kg',
            'quantity' => 100,
            'date' => 05202026,
            'status' => 'active',
            'produce_produce_id' => $product->produce_id,
            'user_user_id' => $farmer->user_id,
        ]);

        $this->browse(function (Browser $browser) use ($farmer, $product, $listing) {
            $browser->loginAs($farmer)
                ->visit('/farmer/listings')
                ->assertSee('Edit')
                ->clickLink('Edit')
                ->assertPathIs('/farmer/listings/' . $listing->listing_id . '/edit')
                ->type('title', '')
                ->type('content', 'Wortel segar organik berkualitas tinggi')
                ->type('price', '1000')
                ->type('unit', 'kg')
                ->type('availability_date', '05202026')
                ->type('quantity', '100')
                ->select('produce_produce_id', $product->produce_id)
                ->select('status', 'active')
                ->press('Update Listing')
                ->pause(1000)
                ->assertPathIs('/farmer/listings/' . $listing->listing_id . '/edit');

        });
    }


}
