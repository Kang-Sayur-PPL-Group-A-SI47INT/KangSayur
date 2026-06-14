<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\User;

class CheckoutTest extends DuskTestCase
{
    public function testCheckoutQris(): void
    {
        $user = User::factory()->create([
            'role'      => 'customer',
            'latitude'  => -6.9175,
            'longitude' => 107.6191,
            'address'   => 'Jl. Telkom, Bandung',
        ]);

        $this->browse(function ($browser) use ($user): void {
            $browser->LoginAs($user)
                    ->visit('/')
                    ->clickLink('Marketplace')
                    ->assertPathIs('/marketplace')
                    ->click('.w-full.h-full.flex.items-center.justify-center.bg-gradient-to-br.from-red-100.to-rose-50') #marketplace item
                    ->press('Add to Cart')
                    ->click('.relative.p-2.text-gray-500.hover\:text-green-700.transition-colors') #cart icon
                    ->assertPathIs('/cart')
                    ->click('@proceed-to-checkout')
                    ->type('delivery_name','test')
                    ->type('delivery_phone','676767676767')
                    // delivery_address is readonly — auto-filled from map pin
                    ->click('@proceed-to-payment')
                    ->waitForText('Complete Your Payment')
                    ->click('@qris-method-btn')
                    ->click('@confirm-pay-btn')
                    ->waitFor('@qris-qr-image')
                    ->assertVisible('@qris-qr-image');
                    #SucessfulCheckoutCheckQR
        });
    }

     public function testCheckoutPhoneFail(): void
     {
         $user = User::factory()->create([
             'role'      => 'customer',
             'latitude'  => -6.9175,
             'longitude' => 107.6191,
             'address'   => 'Jl. Telkom, Bandung',
         ]);

        $this->browse(function ($browser) use ($user): void {
            $browser->LoginAs($user)
                    ->visit('/')
                    ->clickLink('Marketplace')
                    ->assertPathIs('/marketplace')
                    ->click('.w-full.h-full.flex.items-center.justify-center.bg-gradient-to-br.from-red-100.to-rose-50') #marketplace item
                    ->press('Add to Cart')
                    ->click('.relative.p-2.text-gray-500.hover\:text-green-700.transition-colors') #cart icon
                    ->assertPathIs('/cart')
                    ->click('@proceed-to-checkout')
                    ->type('delivery_name','test')
                    ->type('delivery_phone','324')
                    // delivery_address is readonly — auto-filled from map pin
                    ->click('@proceed-to-payment')
                    ->waitForText('The delivery phone field must be between 7 and 16 digits.');
                    #failPhoneNumber
        });
    }


    public function testCheckoutFormFail(): void
    {
        $user = User::factory()->create([
            'role'=>'customer',
        ]);

        $this->browse(function ($browser) use ($user): void {
            $browser->LoginAs($user)
                    ->visit('/')
                    ->clickLink('Marketplace')
                    ->assertPathIs('/marketplace')
                    ->click('.w-full.h-full.flex.items-center.justify-center.bg-gradient-to-br.from-red-100.to-rose-50') #marketplace item
                    ->press('Add to Cart')
                    ->click('.relative.p-2.text-gray-500.hover\:text-green-700.transition-colors') #cart icon
                    ->assertPathIs('/cart')
                    ->click('@proceed-to-checkout')
                    ->assertPathIs('/checkout');
                    #CheckoutFormFail
        });
    }
}
