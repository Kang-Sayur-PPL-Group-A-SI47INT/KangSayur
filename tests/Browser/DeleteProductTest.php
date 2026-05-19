<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\User;
use App\Models\Produce;
use App\Models\Listing;

class DeleteProductTest extends DuskTestCase
{
    public function test_delete_product(): void
    {
        $farmer = User::factory()->create([
            'role' => 'farmer',
            'verification_status' => 'verified'
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

        $this->browse(function (Browser $browser) use ($farmer, $listing) {
            $browser->loginAs($farmer)
                ->visit('/farmer/listings')
                ->assertSee('Wortel Segar Organik')
                ->press('Delete')
                ->acceptDialog()
                ->pause(100)
                ->assertSee('Listing deleted.')
                ->assertDontSee('Wortel Segar Organik');
        });
    }
}
