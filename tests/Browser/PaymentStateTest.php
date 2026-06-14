<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\User;

class PaymentStateTest extends DuskTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Seed the order statuses before each test
            $this->artisan('db:seed', ['--class' => 'OrderStatusSeeder']);
            }   

    public function testContinuePayment(): void
    {
        $customer = User::where('email', 'dusk_customer@kangsayur.com')->firstOrFail();

        $this->browse(function ($browser) use ($customer): void {
            $browser->LoginAs($customer)
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
                    ->type('delivery_address','Jl. Telkomsel')
                    ->click('@proceed-to-payment')
                    ->click('@orders-button', 100)
                    ->waitForText('Awaiting Payment')
                    ->click('@continue-payment-button')
                    ->waitForText('Complete Your Payment');
        });
    }



    public function testCancelPayment(): void
    {
        $customer = User::where('email', 'dusk_customer@kangsayur.com')->firstOrFail();
        

        $this->browse(function ($browser) use ($customer): void {
            $browser->LoginAs($customer)
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
                    ->type('delivery_address','Jl. Telkomsel')
                    ->click('@proceed-to-payment')
                    ->waitFor('@orders-button') #order button in header
                    ->click('@orders-button')
                    ->waitForText('Awaiting Payment')
                    ->click('@cancel-order-button')
                    ->waitForDialog()
                    ->assertDialogOpened('Are you sure you want to cancel this order? This cannot be undone.')
                    ->acceptDialog();
        });
    }



    public function testPaidPayment(): void
    {
        $customer = User::where('email', 'dusk_customer@kangsayur.com')->firstOrFail();

        $this->browse(function ($browser) use ($customer): void {
            $browser->LoginAs($customer)
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
                    ->type('delivery_address','Jl. Telkomsel')
                    ->click('@proceed-to-payment')
                    ->waitFor('@orders-button') #order button in header
                    ->click('@orders-button') 
                    ->waitForText('Paid');
        });
    }
}