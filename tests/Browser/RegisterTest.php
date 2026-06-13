<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\BannedIdentifier;

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

    /**
     * Test registration fails with empty name.
     */
    public function test_register_with_empty_name(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->clickLink('Get Started')
                ->assertPathIs('/register')
                ->type('name', '')
                ->type('email', 'emptyname_test@gmail.com')
                ->type('password', '12345678')
                ->type('password_confirmation', '12345678')
                ->press('Join the Community')
                ->assertPathIs('/register')
                ;
        });
    }

    /**
     * Test registration fails when passwords don't match.
     */
    public function test_register_with_password_mismatch(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->clickLink('Get Started')
                ->assertPathIs('/register')
                ->type('name', 'Mismatch Test')
                ->type('email', 'mismatch_test@gmail.com')
                ->type('password', '12345678')
                ->type('password_confirmation', '87654321')
                ->press('Join the Community')
                ->assertPathIs('/register')
                ->assertSee('Konfirmasi password tidak sesuai.')
                ;
        });
    }

    /**
     * Test registration fails with short password.
     */
    public function test_register_with_short_password(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->clickLink('Get Started')
                ->assertPathIs('/register')
                ->type('name', 'Short Pass Test')
                ->type('email', 'shortpass_test@gmail.com')
                ->type('password', '123')
                ->type('password_confirmation', '123')
                ->press('Join the Community')
                ->assertPathIs('/register')
                ->assertSee('Password minimal 8 karakter.')
                ;
        });
    }

    /**
     * Test registration fails with invalid email format.
     */
    public function test_register_with_invalid_email_format(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->clickLink('Get Started')
                ->assertPathIs('/register')
                ->type('name', 'Invalid Email Test')
                ->type('email', 'not-a-valid-email')
                ->type('password', '12345678')
                ->type('password_confirmation', '12345678')
                ->press('Join the Community')
                ->assertPathIs('/register')
                ;
        });
    }

    /**
     * Test registration fails with banned email.
     */
    public function test_register_with_banned_email(): void
    {
        $bannedUser = \App\Models\User::factory()->create([
            'role' => 'customer',
            'is_banned' => true,
        ]);

        \App\Models\BannedIdentifier::create([
            'type' => 'email',
            'value' => 'banned_register_test@gmail.com',
            'user_user_id' => $bannedUser->user_id,
            'banned_by' => $bannedUser->user_id,
            'reason' => 'Test ban',
        ]);

        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->clickLink('Get Started')
                ->assertPathIs('/register')
                ->type('name', 'Banned Email Test')
                ->type('email', 'banned_register_test@gmail.com')
                ->type('password', '12345678')
                ->type('password_confirmation', '12345678')
                ->press('Join the Community')
                ->assertPathIs('/register')
                ->assertSee('Email ini telah diblokir')
                ;
        });
    }
}
