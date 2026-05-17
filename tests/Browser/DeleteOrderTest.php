<?php

namespace Tests\Browser;

use App\Models\User;
use App\Models\Transaction;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class DeleteOrderTest extends DuskTestCase
{
    public function testdeleteorder(): void
    {
        $farmer = User::where('role', 'farmer')->first() ?? User::factory()->create([
            'role' => 'farmer',
            'verification_status' => 'verified'
        ]);

        $this->browse(function (Browser $browser) use ($farmer) {
            $browser->loginAs($farmer)
                ->visit('/farmer/orders')
                ->waitFor('@delete-order-1')
                ->scrollIntoView('@delete-order-1')
                ->script('window.confirm = function() { return true; }');

            $browser->press('@delete-order-1')
                ->pause(1000)
                ->assertSee('has been deleted successfully!');
        });

        $this->browse(function (Browser $browser) use ($farmer) {
            $browser->loginAs($farmer)
                ->visit('/farmer/orders')
                ->waitFor('@delete-order-2')
                ->scrollIntoView('@delete-order-2')
                ->script('window.confirm = function() { return false; }');

            $browser->press('@delete-order-2')
                ->pause(1000)
                ->assertSee('Order Management');
        });
    }
}
