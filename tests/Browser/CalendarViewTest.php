<?php

namespace Tests\Browser;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class CalendarViewTest extends DuskTestCase
{
    public function testViewHarvestCalendar(): void
    {
        $user = User::factory()->create([
            'role' => 'farmer',
            'verification_status' => 'verified',
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/farmer/harvest-calendar')
                ->waitForText('Harvest Calendar')
                ->assertSee('Harvest Calendar')
                ->assertSee('Plan and track your upcoming harvests');
        });
    }
}
