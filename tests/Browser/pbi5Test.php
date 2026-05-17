<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\Product;
use App\Models\User;

class pbi5Test extends DuskTestCase
{

    public function test_search_positive()
{

        $user = User::factory()->create();
        $this->browse(function ($browser) use ($user) {
        $browser->loginAs($user)
        ->visit('/marketplace')
                ->type('search', 'tomat') 
                ->press('Search')
                ->pause(1000)
                ->assertSee('Tomat');
    });
}

    public function test_search_negative()
{

        $user = User::factory()->create();
        $this->browse(function ($browser) use ($user) {
        $browser->loginAs($user)
        ->visit('/marketplace')
        ->type('@search-input', 'baju') 
        ->press('Search')
        ->pause(1000)
        ->assertSee('No produce found');
    });
}

    public function test_filter_category()
{

        $user = User::factory()->create();
        $this->browse(function ($browser) use ($user) {
        $browser->loginAs($user)
                ->visit('/marketplace')
                ->radio('category', 'Sayuran')
                ->press('Apply Filters')
                ->pause(1000)

                ->assertSee('Sayuran');
    });
}

    public function test_filter_price()
{

        $user = User::factory()->create();
        $this->browse(function ($browser) use ($user) {
        $browser->loginAs($user)
                ->visit('/marketplace')
                ->type('min_price', '1000')
                ->type('max_price', '5000')
                ->press('Apply Filters')
                ->pause(1000);
    });
}

    public function test_filter_loc()
{

        $user = User::factory()->create();
        $this->browse(function ($browser) use ($user) {
        $browser->loginAs($user)
                ->visit('/marketplace')
                ->select('city', 'Bandung')
                ->press('Apply Filters')
                ->pause(1000)
                ->assertSee('Bandung');
    });
}

    public function test_filter_rating()
{

        $user = User::factory()->create();
        $this->browse(function ($browser) use ($user) {
        $browser->loginAs($user)
                ->visit('/marketplace')
                ->click('@rating-star-3')  
                ->pause(1000);
    });
}

    public function test_filter_reset()
{

        $user = User::factory()->create();
        $this->browse(function ($browser) use ($user) {
        $browser->loginAs($user)
                ->visit('/marketplace')
                ->type('min_price', '1000')
                ->press('Apply Filters')
                ->pause(1000)
                ->clickLink('Reset all')
                ->pause(1000)
                ->assertInputValue('min_price', '');
    });
}
    public function test_sort()
{

        $user = User::factory()->create();
        $this->browse(function ($browser) use ($user) {
        $browser->loginAs($user)
                ->visit('/marketplace')
                ->select('sort', 'price_asc') 
                ->pause(1000);


    });
}

}