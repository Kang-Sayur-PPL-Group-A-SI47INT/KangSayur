<?php

namespace Tests\Browser;

use App\Models\User;
use App\Models\Transaction;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class UpdateOrderTest extends DuskTestCase
{
    public function testupdateorder(): void
    {
        $customer = User::where('role', 'customer')->first() ?? User::factory()->create(['role' => 'customer']);

        $this->browse(function (Browser $browser) use ($customer) {
            $browser->loginAs($customer)
                ->visit('/customer/orders')
                ->assertPathIs('/customer/orders')
                ->assertSee('My Orders')
                ->assertSee('KS-CUST-001') // Berdasarkan Mock Data
                ->assertSee('Processing');
        });
    }
}