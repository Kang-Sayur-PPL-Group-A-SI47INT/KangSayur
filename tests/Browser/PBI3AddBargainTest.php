<?php

namespace Tests\Browser;

use App\Models\User;
use App\Models\Produce;
use App\Models\Listing;
use App\Models\Offer;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class PBI3AddBargainTest extends DuskTestCase
{
    /**
     * Set up a farmer, produce, and listing for the tests.
     */
    private function setupListing(): array
    {
        $farmer = User::factory()->create([
            'role' => 'farmer',
            'verification_status' => 'verified',
        ]);

        $produce = Produce::first() ?? Produce::create([
            'name' => 'Wortel Organik',
            'category' => 'Sayuran',
            'emoji' => '🥕',
        ]);

        $listing = Listing::create([
            'title' => 'Wortel Lembang Segar ' . uniqid(),
            'content' => 'Wortel segar dipanen langsung dari kebun Lembang.',
            'price' => 15000,
            'quantity' => 50,
            'unit' => 'kg',
            'status' => 'active',
            'produce_produce_id' => $produce->produce_id,
            'user_user_id' => $farmer->user_id,
        ]);

        return compact('farmer', 'produce', 'listing');
    }

    /**
     * Test case for successfully adding a bargain (Positive scenario).
     */
    public function test_customer_can_successfully_add_bargain(): void
    {
        $setup = $this->setupListing();
        $customer = User::factory()->create(['role' => 'customer']);

        $this->browse(function (Browser $browser) use ($setup, $customer) {
            $browser->loginAs($customer)
                ->visit(route('marketplace.show', $setup['listing']))
                ->assertSee($setup['listing']->title)
                ->assertSee('Make an Offer')
                ->type('offered_price', '12000')
                ->press('Make an Offer')
                ->pause(1000)
                // Should redirect to customer.offers.show
                ->assertPathBeginsWith('/offers/')
                ->assertSee('Offer sent!')
                ->assertSee('Wortel Lembang Segar')
                ->assertSee('Rp 12.000')
                ->assertSee('Pending');
        });
    }

    /**
     * Test case for trying to add a duplicate bargain (Negative scenario).
     */
    public function test_customer_cannot_add_duplicate_pending_bargain(): void
    {
        $setup = $this->setupListing();
        $customer = User::factory()->create(['role' => 'customer']);

        // Create an existing pending offer
        Offer::create([
            'offered_price' => 13000,
            'status' => 'pending',
            'listing_listing_id' => $setup['listing']->listing_id,
            'user_user_id' => $customer->user_id,
        ]);

        $this->browse(function (Browser $browser) use ($setup, $customer) {
            $browser->loginAs($customer)
                ->visit(route('marketplace.show', $setup['listing']))
                ->type('offered_price', '12000')
                ->press('Make an Offer')
                ->pause(1000)
                // Should stay on the same page with error message
                ->assertPathIs('/marketplace/' . $setup['listing']->listing_id)
                ->assertSee('You already have a pending offer for this listing.');
        });
    }

    /**
     * Test case for adding a bargain with invalid price (Negative scenario).
     */
    public function test_customer_cannot_add_bargain_with_invalid_price(): void
    {
        $setup = $this->setupListing();
        $customer = User::factory()->create(['role' => 'customer']);

        $this->browse(function (Browser $browser) use ($setup, $customer) {
            $browser->loginAs($customer)
                ->visit(route('marketplace.show', $setup['listing']))
                ->type('offered_price', '-500') // Invalid price
                ->press('Make an Offer')
                ->pause(1000)
                // Browser HTML5 validation or server-side validation should keep it on show page
                ->assertPathIs('/marketplace/' . $setup['listing']->listing_id);
        });
    }
}
