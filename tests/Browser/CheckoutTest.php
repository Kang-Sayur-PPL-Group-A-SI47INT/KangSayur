<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\User;

class CheckoutTest extends DuskTestCase
{
    public function testCheckout(): void
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
                    ->type('delivery_name','test')
                    ->type('delivery_phone','676767')
                    ->type('delivery_address','Jl. Telkomsel')
                    ->click('@proceed-to-payment')
                    ->assertSee('Complete Your Payment')
                    ->click('@payment-method')
                    ->click('@confirm-pay')
                    ->waitForText('Payment successful! Your order is being processed.'); 
                    #SucessfulCheckout                   
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
