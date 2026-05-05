<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\User;

class LogoutTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     */
    public function test_logout_customer_success(): void
    {
        $user = User::factory()->create([
            'role' => 'customer'
        ]);
        
        $this->browse(function ($browser) use ($user): void {
            $browser->loginAs($user)
                ->visit('/marketplace')
                ->pause(1000)
                ->assertPathIs('/marketplace')
                ->assertSee('The Marketplace')
                // Click profile dropdown to reveal logout
                ->click('button.flex.items-center.p-1.rounded-full')
                ->pause(1000)
                ->press('Log Out')
                ->pause(1000)
                ->assertPathIs('/')
                ;
        });
    }

    public function test_logout_customer_failed(): void
    {
        $this->browse(function (Browser $browser) {
            // After logging out, attempting to visit a protected route should fail and redirect to login
            $browser->visit('/marketplace')
                ->assertPathIs('/login')
                ->assertSee('Welcome Back')
                ;
        });
    }

    public function test_logout_farmer_success(): void
    {
        $user = User::factory()->create([
            'role' => 'farmer'
        ]);
        
        $this->browse(function ($browser) use ($user): void {
            $browser->loginAs($user)
                ->visit('/farmer/dashboard')
                ->pause(1000)
                ->assertPathIs('/farmer/dashboard')
                ->assertSee('Manage Produce')
                // Click profile dropdown to reveal logout
                ->click('button.flex.items-center.p-1.rounded-full')
                ->pause(1000)
                ->press('Log Out')
                ->pause(1000)
                ->assertPathIs('/')
                ;
        });
    }
}
