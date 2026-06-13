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
            'role' => 'farmer',
            'verification_status' => 'verified'
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
            'role' => 'farmer',
            'verification_status' => 'verified'
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

    /**
     * Test create listing fails without title.
     */
    public function test_create_listing_without_title(): void
    {
        $user = User::factory()->create([
            'role' => 'farmer',
            'verification_status' => 'verified'
        ]);

        $produce = Produce::create([
            'name' => 'Kangkung',
            'category' => 'Vegetables'
        ]);

        $this->browse(function ($browser) use ($user, $produce): void {
            $browser->loginAs($user)
                ->visit('/farmer/listings/create')
                ->assertPathIs('/farmer/listings/create')
                ->select('produce_produce_id', $produce->produce_id)
                ->type('title', '')
                ->type('price', '12000')
                ->type('quantity', '25')
                ->select('unit', 'kg')
                ->type('content', 'Test content')
                ->press('Publish Listing')
                ->pause(1000)
                ->assertPathIs('/farmer/listings/create')
            ;
        });
    }

    /**
     * Test create listing fails with negative price.
     */
    public function test_create_listing_with_negative_price(): void
    {
        $user = User::factory()->create([
            'role' => 'farmer',
            'verification_status' => 'verified'
        ]);

        $produce = Produce::create([
            'name' => 'Sawi',
            'category' => 'Vegetables'
        ]);

        $this->browse(function ($browser) use ($user, $produce): void {
            $browser->loginAs($user)
                ->visit('/farmer/listings/create')
                ->assertPathIs('/farmer/listings/create')
                ->select('produce_produce_id', $produce->produce_id)
                ->type('title', 'Negative Price Test')
                ->type('price', '-100')
                ->type('quantity', '25')
                ->select('unit', 'kg')
                ->type('content', 'Test content')
                ->press('Publish Listing')
                ->pause(1000)
                ->assertPathIs('/farmer/listings/create')
            ;
        });
    }

    /**
     * Test create listing fails with zero quantity.
     */
    public function test_create_listing_with_zero_quantity(): void
    {
        $user = User::factory()->create([
            'role' => 'farmer',
            'verification_status' => 'verified'
        ]);

        $produce = Produce::create([
            'name' => 'Tomat',
            'category' => 'Vegetables'
        ]);

        $this->browse(function ($browser) use ($user, $produce): void {
            $browser->loginAs($user)
                ->visit('/farmer/listings/create')
                ->assertPathIs('/farmer/listings/create')
                ->select('produce_produce_id', $produce->produce_id)
                ->type('title', 'Zero Quantity Test')
                ->type('price', '12000')
                ->type('quantity', '0')
                ->select('unit', 'kg')
                ->type('content', 'Test content')
                ->press('Publish Listing')
                ->pause(1000)
                ->assertPathIs('/farmer/listings/create')
            ;
        });
    }

    /**
     * Test unverified farmer cannot create listing.
     */
    public function test_unverified_farmer_cannot_create_listing(): void
    {
        $user = User::factory()->create([
            'role' => 'farmer',
            'verification_status' => 'unverified'
        ]);

        $this->browse(function ($browser) use ($user): void {
            $browser->loginAs($user)
                ->visit('/farmer/listings/create')
                ->pause(1000)
                ->assertPathIs('/farmer/profile')
            ;
        });
    }

    /**
     * Test customer cannot access create listing page.
     */
    public function test_customer_cannot_access_create_listing(): void
    {
        $user = User::factory()->create([
            'role' => 'customer'
        ]);

        $this->browse(function ($browser) use ($user): void {
            $browser->loginAs($user)
                ->visit('/farmer/listings/create')
                ->pause(1000)
                ->assertSee('403')
            ;
        });
    }
}
