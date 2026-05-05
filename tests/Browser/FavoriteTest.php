<?php

namespace Tests\Browser;


use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\User;
use App\Models\Produce;
use App\Models\Listing;


class FavoriteTest extends DuskTestCase
{
    public function testFavoriteRemove(): void
    {
        $user = User::factory()->create([
            'role'=>'customer',
        ]);

        
        $farmer = User::factory()->create([
            'role' => 'farmer',
        ]);

        $produce = Produce::create([
            'name' => 'Tomato',
            'category' => 'Vegetables',
        ]);

        Listing::create([
            'title' => 'Fresh Tomatoes',
            'content' => 'Freshly harvested tomatoes',
            'price' => 15000,
            'quantity' => 100,
            'unit' => 'kg',
            'status' => 'active',
            'produce_produce_id' => $produce->produce_id,
            'user_user_id' => $farmer->user_id,
        ]);

        $this->browse(function ($browser) use ($user): void {
            $browser->LoginAs($user)
                ->visit('/')
                ->clickLink('Favorites')
                ->assertPathIs('/favorites')
                ->clickLink('Explore Marketplace')
                ->assertPathIs('/marketplace')
                ->click('@add-to-favorite')
                ->waitForText('Added to favorites!')
                ->clickLink('Favorites')
                ->click('@add-to-favoritePage')
                ->assertSee('Removed from favorites.');
                #success favorite remove
        });
        

    }
    public function testFavoriteCheck(): void
    {
        $user = User::factory()->create([
            'role'=>'customer',
        ]);

        
        $farmer = User::factory()->create([
            'role' => 'farmer',
        ]);

        $produce = Produce::create([
            'name' => 'Tomato',
            'category' => 'Vegetables',
        ]);

        Listing::create([
            'title' => 'Fresh Tomatoes',
            'content' => 'Freshly harvested tomatoes',
            'price' => 15000,
            'quantity' => 100,
            'unit' => 'kg',
            'status' => 'active',
            'produce_produce_id' => $produce->produce_id,
            'user_user_id' => $farmer->user_id,
        ]);

        $this->browse(function ($browser) use ($user): void {
            $browser->LoginAs($user)
                ->visit('/')
                ->clickLink('Favorites')
                ->assertPathIs('/favorites')
                ->clickLink('Explore Marketplace')
                ->assertPathIs('/marketplace');
                #check favorite
        });
        

    }

    public function testFavoriteAdd(): void
    {
        $user = User::factory()->create([
            'role'=>'customer',
        ]);

        
        $farmer = User::factory()->create([
            'role' => 'farmer',
        ]);

        $produce = Produce::create([
            'name' => 'Tomato',
            'category' => 'Vegetables',
        ]);

        Listing::create([
            'title' => 'Fresh Tomatoes',
            'content' => 'Freshly harvested tomatoes',
            'price' => 15000,
            'quantity' => 100,
            'unit' => 'kg',
            'status' => 'active',
            'produce_produce_id' => $produce->produce_id,
            'user_user_id' => $farmer->user_id,
        ]);

        $this->browse(function ($browser) use ($user): void {
            $browser->LoginAs($user)
                ->visit('/')
                ->clickLink('Favorites')
                ->assertPathIs('/favorites')
                ->clickLink('Explore Marketplace')
                ->assertPathIs('/marketplace')
                ->click('@add-to-favorite')
                ->waitForText('Added to favorites!');
                #success favorite added
        });
        

    }
}
