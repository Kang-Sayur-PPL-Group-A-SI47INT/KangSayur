<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\User;


class CartTest extends DuskTestCase
{
    public function testCart(): void
    {
        $user = User::factory()->create([
            'role'=>'customer',
        ]);

        $this->browse(function ($browser) use ($user): void {
            $browser->LoginAs($user)
                ->visit('/')
                ->click('.relative.p-2.text-gray-500.hover\:text-green-700.transition-colors') #cart icon
                ->assertPathIs('/cart')
                ->clickLink('Start Shopping')
                ->assertPathIs('/marketplace')
                ->click('.w-full.h-full.flex.items-center.justify-center.bg-gradient-to-br.from-red-100.to-rose-50') #marketplace item
                ->press('Add to Cart') #add to cart
                ->assertSee('Item added to cart!')
                ->click('@cart-icon')
                ->assertPathIs('/cart');
                #success cart
        });
    }
    public function testCartRemove(): void
    {
        $user = User::factory()->create([
            'role'=>'customer',
        ]);

        $this->browse(function ($browser) use ($user): void {
            $browser->LoginAs($user)
                ->visit('/')
                ->click('.relative.p-2.text-gray-500.hover\:text-green-700.transition-colors') #cart icon
                ->assertPathIs('/cart')
                ->clickLink('Start Shopping')
                ->assertPathIs('/marketplace')
                ->click('.w-full.h-full.flex.items-center.justify-center.bg-gradient-to-br.from-red-100.to-rose-50') #marketplace item
                ->press('Add to Cart') #add to cart
                ->assertSee('Item added to cart!')
                ->click('@cart-icon')
                ->assertPathIs('/cart')
                #success cart
                ->click('@cart-remove')
                ->waitForText('Item removed from cart.');
        });
    }
}
