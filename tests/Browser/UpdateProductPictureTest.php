<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\User;
use App\Models\Produce;
use App\Models\Listing;

class UpdateProductPictureTest extends DuskTestCase
{
    public function test_update_product_picture(): void
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
            'status' => 'active',
            'produce_produce_id' => $product->produce_id,
            'user_user_id' => $farmer->user_id,
        ]);


        $imagePath = base_path('tests/Browser/assets/test-product.jpg');

        $this->browse(function (Browser $browser) use ($farmer, $listing, $imagePath) {

            $browser->loginAs($farmer)
                ->visit('/farmer/listings')
                ->assertSee('Edit')
                ->clickLink('Edit')
                ->assertPathIs('/farmer/listings/' . $listing->listing_id . '/edit')
                ->attach('images[]', $imagePath)
                ->press('Update Listing')
                ->pause(1000)
                ->assertSee('Listing updated successfully!')
                ->assertPathIs('/farmer/listings');
        });
    }

    public function test_update_product_picture_fail(): void
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
            'status' => 'active',
            'produce_produce_id' => $product->produce_id,
            'user_user_id' => $farmer->user_id,
        ]);


        $imagePath = base_path('tests/Browser/assets/test-pic.pdf');

        $this->browse(function (Browser $browser) use ($farmer, $listing, $imagePath) {

            $browser->loginAs($farmer)
                ->visit('/farmer/listings')
                ->assertSee('Edit')
                ->clickLink('Edit')
                ->assertPathIs('/farmer/listings/' . $listing->listing_id . '/edit')
                ->attach('images[]', $imagePath)
                ->press('Update Listing')
                ->pause(1000)
                ->assertPathIs('/farmer/listings/' . $listing->listing_id . '/edit');
        });
    }
}
