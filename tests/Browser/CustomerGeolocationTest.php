<?php

namespace Tests\Browser;

use App\Models\User;
use App\Models\Produce;
use App\Models\Listing;
use App\Models\Cart;
use App\Models\CartItem;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class CustomerGeolocationTest extends DuskTestCase
{
    // ─── Helper ─────────────────────────────────────────────────────────────

    /**
     * Create a farmer + produce + listing and return them.
     */
    private function createFarmerWithListing(): array
    {
        $farmer = User::factory()->create([
            'role'                => 'farmer',
            'verification_status' => 'verified',
            'latitude'            => -6.91750000,
            'longitude'           => 107.61910000,
        ]);

        $produce = Produce::first() ?? Produce::create([
            'name'     => 'Bayam',
            'category' => 'Sayuran',
            'emoji'    => '🥬',
        ]);

        $listing = Listing::create([
            'title'              => 'Bayam Segar Geo',
            'content'            => 'Bayam segar untuk test geolocation',
            'price'              => 10000,
            'quantity'           => 100,
            'unit'               => 'kg',
            'status'             => 'active',
            'produce_produce_id' => $produce->produce_id,
            'user_user_id'       => $farmer->user_id,
        ]);

        return compact('farmer', 'produce', 'listing');
    }

    /**
     * Create a customer with a cart containing one item.
     */
    private function createCustomerWithCart(int $listingId, array $customerAttrs = []): User
    {
        $customer = User::factory()->create(array_merge(['role' => 'customer'], $customerAttrs));
        $cart     = Cart::create(['user_user_id' => $customer->user_id]);
        CartItem::create([
            'quantity'           => 2,
            'cart_cart_id'       => $cart->cart_id,
            'listing_listing_id' => $listingId,
        ]);
        return $customer;
    }

    // ─── Positive Cases ──────────────────────────────────────────────────────

    /**
     * Customer can visit checkout, see the map, search for a location,
     * and have coordinates appear in the coordinate badge.
     */
    public function test_customer_map(): void
    {
        $setup    = $this->createFarmerWithListing();
        $customer = $this->createCustomerWithCart($setup['listing']->listing_id);

        $this->browse(function (Browser $browser) use ($customer) {
            $browser->loginAs($customer)
                ->visit('/checkout')
                ->assertPathIs('/checkout')
                ->assertSee('Checkout')
                ->assertSee('Pin Delivery Location')
                ->assertPresent('#checkout-map')
                ->type('delivery_name', 'Customer Test')
                ->type('delivery_phone', '081234567890')
                // delivery_address is readonly — auto-filled from map pin
                ->type('customer-map-search', 'Telkom university')
                ->keys('input[name="customer-map-search"]', '{enter}')
                ->pause(2000)
                ->assertSee('-6.973208');
        });
    }

    // ─── Negative Cases ──────────────────────────────────────────────────────

    /**
     * Customer cannot proceed to payment if no map pin has been placed
     * (delivery_latitude / delivery_longitude hidden inputs are empty).
     * The JS validation should block the form and show an error banner.
     */
    public function test_customer_checkout_blocked_without_map_pin(): void
    {
        $setup    = $this->createFarmerWithListing();
        // Customer with NO pre-set coordinates — no pin will be placed on the map
        $customer = $this->createCustomerWithCart($setup['listing']->listing_id);

        $this->browse(function (Browser $browser) use ($customer) {
            $browser->loginAs($customer)
                ->visit('/checkout')
                ->assertPathIs('/checkout')
                ->type('delivery_name', 'Customer No Pin')
                ->type('delivery_phone', '081234567890')
                // Intentionally skip the map — hidden inputs remain empty
                ->click('@proceed-to-payment')
                ->pause(500)
                ->assertSee('Please pin your delivery location on the map before continuing.');
        });
    }

    

   
}
