<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\User;
use App\Models\Produce;

class CreateListingTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     */
    public function test_create_listing_success(): void
    {
        $user = User::factory()->create([
            'role' => 'farmer'
        ]);

        $produce = Produce::create([
            'name' => 'Organic Spinach',
            'category' => 'Vegetables'
        ]);

        $this->browse(function ($browser) use ($user, $produce): void {
            $browser->loginAs($user)
                ->visit('/farmer/dashboard')
                ->clickLink('+ New Listing')
                ->assertPathIs('/farmer/listings/create')
                ->attach('images[]', base_path('tests/Browser/fixtures/auth-vegetables.png'))
                ->select('produce_produce_id', $produce->produce_id)
                ->type('title', 'Fresh Highland Spinach')
                ->type('price', '12000')
                ->type('quantity', '25')
                ->select('unit', 'kg')
                ->type('availability_date', '05202026') 
                ->type('content', 'Grown using organic fertilizers in Lembang. Harvested daily.')
                ->press('Publish Listing')
                ->pause(1000)
                ->assertPathIs('/farmer/listings')
                ->assertSee('Fresh Highland Spinach')
                ;

        });
    }

    public function test_create_listing_failed(): void
    {
        $user = User::factory()->create([
            'role' => 'farmer'
        ]);

        $produce = Produce::create([
            'name' => 'Organic Spinach',
            'category' => 'Vegetables'
        ]);

        $this->browse(function ($browser) use ($user, $produce): void {
            $browser->loginAs($user)
                ->visit('/farmer/dashboard')
                ->clickLink('+ New Listing')
                ->assertPathIs('/farmer/listings/create')
                ->type('title', 'Fresh Highland Spinach')
                ->type('price', '12000')
                ->type('quantity', '25')
                ->select('unit', 'kg')
                ->type('availability_date', '05202026') 
                ->type('content', 'Grown using organic fertilizers in Lembang. Harvested daily.')
                ->press('Publish Listing')
                ->pause(1000)
                ->assertPathIs('/farmer/listings/create')
                ;

        });
    }
}
