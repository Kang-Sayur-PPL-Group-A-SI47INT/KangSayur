<?php

namespace Tests\Browser;

use App\Models\User;
use App\Models\Transaction;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ReadOrderTest extends DuskTestCase
{
    public function testreadorder(): void
    {
        $farmer = User::where('role', 'farmer')->first() ?? User::factory()->create([
            'role' => 'farmer',
            'verification_status' => 'verified'
        ]);

        $this->browse(function (Browser $browser) use ($farmer) {
            $browser->loginAs($farmer)
                ->visit('/farmer/orders')
                ->assertPathIs('/farmer/orders')
                ->assertSee('Order Management 📦')
                ->assertSee('KS-20260505-001') // Berdasarkan Mock Data
                ->assertSee('Sayur Bayam Segar');
        });
    }
}