<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\User;


class FavoriteTest extends DuskTestCase
{
    public function testFavorite(): void
    {
        $user = User::factory()->create([
            'role'=>'customer',
        ]);

        $this->browse(function ($browser) use ($user): void {
            $browser->LoginAs($user)
                ->visit('/')
                ->clickLink('Favorites')
                ->assertPathIs('/favorites')
                ->clickLink('Explore Marketplace')
                ->click('@add-to-favorite');
                #success
        });
    }
}
