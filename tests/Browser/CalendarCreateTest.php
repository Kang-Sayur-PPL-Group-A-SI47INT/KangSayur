<?php

namespace Tests\Browser;

use App\Models\User;
use App\Models\Produce;
use App\Models\Listing;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class CalendarCreateTest extends DuskTestCase
{
    public function testCreateHarvestSchedule(): void
    {
        $user = User::factory()->create([
            'role' => 'farmer',
            'verification_status' => 'verified',
        ]);

        $produce = Produce::firstOrCreate(
            ['name' => 'Wortel'],
            ['category' => 'Sayuran', 'emoji' => '🥕']
        );

        $listing = Listing::create([
            'title' => 'Wortel Segar Lembang',
            'content' => 'Wortel segar Lembang.',
            'price' => 15000,
            'quantity' => 100,
            'unit' => 'kg',
            'status' => 'active',
            'user_user_id' => $user->user_id,
            'produce_produce_id' => $produce->produce_id,
        ]);

        $futureDate = now()->addDays(5);
        $futureDateInput = $futureDate->format('Y-m-d');
        $futureDay = $futureDate->day;

        $this->browse(function (Browser $browser) use ($user, $listing, $futureDateInput, $futureDay) {
            $browser->loginAs($user)
                ->visit('/farmer/harvest-calendar')
                ->waitForText('Harvest Calendar')
                ->press('Add Schedule')
                ->pause(500)
                ->select('listing_id', $listing->listing_id);
            $browser->script("document.querySelector('input[name=\"availability_date\"]').value = '{$futureDateInput}';");
            $browser->type('estimated_stock', '50')
                ->press('Create Schedule')
                ->pause(1000)
                ->assertPathIs('/farmer/harvest-calendar')
                ->assertSee('Wortel Segar')
                ->assertSee('×50');
        });
    }

    public function testCreateHarvestScheduleNegative(): void
    {
        $user = User::factory()->create([
            'role' => 'farmer',
            'verification_status' => 'verified',
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/farmer/harvest-calendar')
                ->waitForText('Harvest Calendar')
                ->press('Add Schedule')
                ->pause(500)
                ->press('Create Schedule')
                ->pause(500)
                ->assertPathIs('/farmer/harvest-calendar');
        });
    }
}
