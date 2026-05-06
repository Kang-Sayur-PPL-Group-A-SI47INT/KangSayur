<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\Product;
use App\Models\User;

class pbi6Test extends DuskTestCase
{
    public function test_detail() {

        $user = User::factory()->create();
        $this->browse(function ($browser) use ($user) {
           
            $browser->loginAs($user)
                    ->visit('/marketplace')
                    ->waitFor('[dusk="product-card"]') 
                    ->click('@product-card')
                    ->pause(1000)
                    ->assertSee('Description');
    });
}}