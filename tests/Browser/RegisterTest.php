<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class RegisterTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     */
    public function test_register_customer_success(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->clickLink('Get Started')
                ->assertPathIs('/register')
                ->type('name', 'Ark')
                ->type('email', 'test99@gmail.com')
                ->type('password', '12345678')
                ->type('password_confirmation', '12345678')
                ->press('Join the Community')
                ->assertPathIs('/marketplace')
                ->assertSee('The Marketplace')
                ;
        });
    }

    public function test_register_customer_failed(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->clickLink('Get Started')
                ->assertPathIs('/register')
                ->type('name', 'mamama')
                ->type('email', 'test99@gmail.com')
                ->type('password', '12345678')
                ->type('password_confirmation', '12345678')
                ->press('Join the Community')
                ->assertPathIs('/register')
                ->assertSee('Email sudah terdaftar.')
                ;
        });
    }

    public function test_register_farmer_success(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->clickLink('Get Started')
                ->assertPathIs('/register')
                ->click('label:has(input[value="farmer"])')
                ->assertRadioSelected('role', 'farmer')
                ->type('name', 'regihiuhihi')
                ->type('email', 'test12@gmail.com')
                ->type('password', '123456789')
                ->type('password_confirmation', '123456789')
                ->press('Join the Community')
                ->assertPathIs('/farmer/dashboard')
                ->assertSee('Manage Produce')
                ;
        });
    }

    public function test_register_farmer_failed(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->clickLink('Get Started')
                ->assertPathIs('/register')
                ->click('label:has(input[value="farmer"])')  // ← same as test_register_farmer
            ->assertRadioSelected('role', 'farmer')
            ->type('name', 'testt')
            ->type('email', 'test12@gmail.com')
            ->type('password', '12345678')
            ->type('password_confirmation', '12345678')
            ->press('Join the Community')
            ->assertPathIs('/register')
            ->assertSee('Email sudah terdaftar.')
            ;
        });
    }
    
}
