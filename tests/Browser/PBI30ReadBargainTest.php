<?php

namespace Tests\Browser;

use App\Models\User;
use App\Models\Produce;
use App\Models\Listing;
use App\Models\Offer;
use App\Models\Message;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class PBI30ReadBargainTest extends DuskTestCase
{
    private function setupBargain(): array
    {
        $farmer1 = User::factory()->create([
            'role' => 'farmer',
            'verification_status' => 'verified',
        ]);

        $farmer2 = User::factory()->create([
            'role' => 'farmer',
            'verification_status' => 'verified',
        ]);

        $produce = Produce::first() ?? Produce::create([
            'name' => 'Brokoli Organik',
            'category' => 'Sayuran',
            'emoji' => '🥦',
        ]);

        $listing1 = Listing::create([
            'title' => 'Brokoli Lembang 1 ' . uniqid(),
            'content' => 'Brokoli sehat Lembang.',
            'price' => 25000,
            'quantity' => 40,
            'unit' => 'kg',
            'status' => 'active',
            'produce_produce_id' => $produce->produce_id,
            'user_user_id' => $farmer1->user_id,
        ]);

        $customer1 = User::factory()->create(['role' => 'customer']);
        $customer2 = User::factory()->create(['role' => 'customer']);

        $offer1 = Offer::create([
            'offered_price' => 20000,
            'status' => 'pending',
            'listing_listing_id' => $listing1->listing_id,
            'user_user_id' => $customer1->user_id,
        ]);

        Message::create([
            'content' => 'Halo pak, boleh kurangan sedikit?',
            'sender_user_id' => $customer1->user_id,
            'receiver_user_id' => $farmer1->user_id,
            'user_user_id' => $customer1->user_id,
            'offer_offer_id' => $offer1->offer_id,
        ]);

        return compact('farmer1', 'farmer2', 'customer1', 'customer2', 'listing1', 'offer1');
    }

    public function test_customer_can_read_bargain_list_and_details(): void
    {
        $setup = $this->setupBargain();

        $this->browse(function (Browser $browser) use ($setup) {
            $browser->loginAs($setup['customer1'])
                ->visit('/offers')
                ->assertPathIs('/offers')
                ->assertSee('Negotiations')
                ->assertSee($setup['listing1']->title)
                ->clickLink($setup['listing1']->title)
                ->pause(1000)
                ->assertPathIs('/offers/' . $setup['offer1']->offer_id)
                ->assertSee('Offer Details')
                ->assertSee('Halo pak, boleh kurangan sedikit?');
        });
    }

    public function test_farmer_can_read_received_bargain_list_and_details(): void
    {
        $setup = $this->setupBargain();

        $this->browse(function (Browser $browser) use ($setup) {
            $browser->loginAs($setup['farmer1'])
                ->visit('/farmer/offers')
                ->assertPathIs('/farmer/offers')
                ->assertSee('Negotiations')
                ->visit('/farmer/offers/' . $setup['offer1']->offer_id)
                ->pause(1000)
                ->assertPathIs('/farmer/offers/' . $setup['offer1']->offer_id)
                ->assertSee('Offer Details')
                ->assertSee('Halo pak, boleh kurangan sedikit?');
        });
    }

    public function test_customer_cannot_view_other_customers_bargain(): void
    {
        $setup = $this->setupBargain();

        $this->browse(function (Browser $browser) use ($setup) {
            $browser->loginAs($setup['customer2'])
                ->visit('/offers/' . $setup['offer1']->offer_id)
                ->pause(1000)
                ->assertSee('403');
        });
    }

    public function test_farmer_cannot_view_other_farmers_bargain(): void
    {
        $setup = $this->setupBargain();

        $this->browse(function (Browser $browser) use ($setup) {
            $browser->loginAs($setup['farmer2'])
                ->visit('/farmer/offers/' . $setup['offer1']->offer_id)
                ->pause(1000)
                ->assertSee('403');
        });
    }
}
