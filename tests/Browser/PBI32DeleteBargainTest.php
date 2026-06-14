<?php

namespace Tests\Browser;

use App\Models\User;
use App\Models\Produce;
use App\Models\Listing;
use App\Models\Offer;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class PBI32DeleteBargainTest extends DuskTestCase
{
    private function setupBargain(string $status = 'pending'): array
    {
        $farmer = User::factory()->create([
            'role' => 'farmer',
            'verification_status' => 'verified',
        ]);

        $produce = Produce::first() ?? Produce::create([
            'name' => 'Kubis Organik',
            'category' => 'Sayuran',
            'emoji' => '🥬',
        ]);

        $listing = Listing::create([
            'title' => 'Kubis Lembang Segar ' . uniqid(),
            'content' => 'Kubis segar Lembang.',
            'price' => 12000,
            'quantity' => 80,
            'unit' => 'kg',
            'status' => 'active',
            'produce_produce_id' => $produce->produce_id,
            'user_user_id' => $farmer->user_id,
        ]);

        $customer = User::factory()->create(['role' => 'customer']);

        $offer = Offer::create([
            'offered_price' => 10000,
            'status' => $status,
            'listing_listing_id' => $listing->listing_id,
            'user_user_id' => $customer->user_id,
        ]);

        return compact('farmer', 'customer', 'listing', 'offer');
    }

    public function test_customer_can_withdraw_pending_bargain(): void
    {
        $setup = $this->setupBargain('pending');

        $this->browse(function (Browser $browser) use ($setup) {
            $browser->loginAs($setup['customer'])
                ->visit(route('customer.offers.show', $setup['offer']))
                ->assertSee('Offer Details')
                ->assertSee('Withdraw Offer')
                ->press('Withdraw Offer')
                ->acceptDialog()
                ->pause(1000)
                ->assertPathIs('/offers')
                ->assertSee('Offer withdrawn successfully.')
                ->assertDontSee($setup['listing']->title);
        });
    }

    public function test_customer_cannot_withdraw_accepted_bargain(): void
    {
        $setup = $this->setupBargain('accepted');

        $this->browse(function (Browser $browser) use ($setup) {
            $browser->loginAs($setup['customer'])
                ->visit(route('customer.offers.show', $setup['offer']))
                ->assertDontSee('Withdraw Offer');
        });
    }
}
