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
    
    public function test_customer_map(): void
    {
        // Create farmer with produce and listing
        $farmer = User::factory()->create([
            'role' => 'farmer',
            'verification_status' => 'verified',
            'latitude' => -6.91750000,
            'longitude' => 107.61910000,
        ]);

        $produce = Produce::first() ?? Produce::create([
            'name' => 'Bayam',
            'category' => 'Sayuran',
            'emoji' => '🥬',
        ]);

        $listing = Listing::create([
            'title' => 'Bayam Segar Geo',
            'content' => 'Bayam segar untuk test geolocation',
            'price' => 10000,
            'quantity' => 100,
            'unit' => 'kg',
            'status' => 'active',
            'produce_produce_id' => $produce->produce_id,
            'user_user_id' => $farmer->user_id,
        ]);

        // Create customer with cart
        $customer = User::factory()->create([
            'role' => 'customer',
        ]);

        $cart = Cart::create(['user_user_id' => $customer->user_id]);
        CartItem::create([
            'quantity' => 2,
            'cart_cart_id' => $cart->cart_id,
            'listing_listing_id' => $listing->listing_id,
        ]);

        $this->browse(function (Browser $browser) use ($customer) {
            $browser->loginAs($customer)
                ->visit('/checkout')
                ->assertPathIs('/checkout')
                ->assertSee('Checkout')
                ->assertSee('Pin Delivery Location')
                ->assertPresent('#checkout-map')
                ->type('delivery_name', 'Customer Test')
                ->type('delivery_phone', '081234567890')
                ->type('delivery_address', 'Jl. Sudirman No. 1, Bandung')
                // Set coordinates via hidden inputs (simulating map click)
                ->type('customer-map-search', 'Telkom university') // 'search' is the name or CSS selector of the input
                ->keys('input[name="customer-map-search"]', '{enter}') 
                ->pause(1000)
                ->assertSee("-6.973208, 107.630854");
        });
    }

    
}
