<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\Product;
use App\Models\User;

class pbi4Test extends DuskTestCase
{
    public function test_pagination() {

        $user = User::factory()->create();
        $this->browse(function ($browser) use ($user) {
           
            $browser->loginAs($user)
        ->visit('/marketplace')
        ->assertSee('Marketplace')
        ->click('a[href*="/marketplace?page=2"]')
        ->pause(1000)
        ->assertQueryStringHas('page', 2);
        });

}}