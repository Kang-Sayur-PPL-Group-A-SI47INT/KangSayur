<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class LoginTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     */
    public function test_login_customer_success(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->clickLink('Sign In')
                ->assertPathIs('/login')
                ->type('email', 'test99@gmail.com')
                ->type('password', '12345678')
                ->press('Login')
                ->assertPathIs('/marketplace')
                ->assertSee('The Marketplace')
                ;
        });
    }

    public function test_login_customer_failed(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->clickLink('Sign In')
                ->assertPathIs('/login')
                ->type('email', 'test99@gmail.com')
                ->type('password', '2345')
                ->press('Login')
                ->assertPathIs('/login')
                ->assertSee('These credentials do not match our records.')
                ;
        });
    }

    public function test_login_farmer_success(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->clickLink('Sign In')
                ->assertPathIs('/login')
                ->click('label:has(input[value="farmer"])')
                ->assertRadioSelected('role', 'farmer')
                ->type('email', 'test12@gmail.com')
                ->type('password', '123456789')
                ->press('Login')
                ->assertPathIs('/farmer/dashboard')
                ->assertSee('Manage Produce')
                ;
        });
    }

    public function test_login_farmer_failed(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
            ->clickLink('Sign In')
            ->assertPathIs('/login')
            ->click('label:has(input[value="farmer"])') 
            ->assertRadioSelected('role', 'farmer')
            ->type('email', 'test12@gmail.com')
            ->type('password', '23')
            ->press('Login')
            ->assertPathIs('/login')
            ->assertSee('These credentials do not match our records.')
            ;
        });
    }
}
