<?php

namespace Tests\Browser;

use App\Models\User;
use App\Models\Produce;
use App\Models\Listing;
use App\Models\Offer;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class PBI24UpdateBargainTest extends DuskTestCase
{
    private function setupBargain(string $status = 'pending'): array
    {
        $farmer = User::factory()->create([
            'role' => 'farmer',
            'verification_status' => 'verified',
        ]);

        $produce = Produce::first() ?? Produce::create([
            'name' => 'Kentang Lembang',
            'category' => 'Sayuran',
            'emoji' => '🥔',
        ]);

        $listing = Listing::create([
            'title' => 'Kentang Lembang Super ' . uniqid(),
            'content' => 'Kentang segar dari Lembang.',
            'price' => 20000,
            'quantity' => 100,
            'unit' => 'kg',
            'status' => 'active',
            'produce_produce_id' => $produce->produce_id,
            'user_user_id' => $farmer->user_id,
        ]);

        $customer = User::factory()->create(['role' => 'customer']);

        $offer = Offer::create([
            'offered_price' => 16000,
            'status' => $status,
            'listing_listing_id' => $listing->listing_id,
            'user_user_id' => $customer->user_id,
        ]);

        return compact('farmer', 'customer', 'listing', 'offer');
    }

    /**
     * Test case for customer updating their offer price (Positive scenario).
     */
    public function test_customer_can_update_offered_price(): void
    {
        $setup = $this->setupBargain('pending');

        $this->browse(function (Browser $browser) use ($setup) {
            $browser->loginAs($setup['customer'])
                ->visit(route('customer.offers.show', $setup['offer']))
                ->assertSee('Offer Details')
                ->type('offered_price', '17000')
                ->press('Update Offer')
                ->pause(1000)
                ->assertPathIs('/offers/' . $setup['offer']->offer_id)
                ->assertSee('Offer updated successfully!')
                ->assertSee('Rp 17.000');
        });
    }

    /**
     * Test case for farmer countering customer's offer (Positive scenario).
     */
    public function test_farmer_can_counter_offer(): void
    {
        $setup = $this->setupBargain('pending');

        $this->browse(function (Browser $browser) use ($setup) {
            $browser->loginAs($setup['farmer'])
                ->visit(route('farmer.offers.show', $setup['offer']))
                ->assertSee('Offer Details')
                ->type('counter_price', '18000')
                ->press('Counter Offer')
                ->pause(1000)
                ->assertPathIs('/farmer/offers/' . $setup['offer']->offer_id)
                ->assertSee('Counter offer sent!')
                ->assertSee('Rp 18.000')
                ->assertSee('Countered');
        });
    }

    /**
     * Test case for customer accepting farmer's counter offer (Positive scenario).
     */
    public function test_customer_can_accept_counter_offer(): void
    {
        $setup = $this->setupBargain('countered');
        $setup['offer']->update(['counter_price' => 18000]);

        $this->browse(function (Browser $browser) use ($setup) {
            $browser->loginAs($setup['customer'])
                ->visit(route('customer.offers.show', $setup['offer']))
                ->assertSee("Farmer's Counter")
                ->assertSee('Rp 18.000')
                ->press('Accept Counter Offer')
                ->pause(1000)
                ->assertPathIs('/offers/' . $setup['offer']->offer_id)
                ->assertSee('Counter offer accepted!')
                ->assertSee('Accepted');
        });
    }

    /**
     * Test case for customer trying to update an accepted/rejected offer (Negative scenario).
     */
    public function test_customer_cannot_update_non_active_offer(): void
    {
        $setup = $this->setupBargain('accepted');

        $this->browse(function (Browser $browser) use ($setup) {
            $browser->loginAs($setup['customer'])
                ->visit(route('customer.offers.show', $setup['offer']))
                // Actions container should not exist for non-active offers
                ->assertDontSee('Update Offer')
                ->assertDontSee('Withdraw Offer');
        });
    }
}
