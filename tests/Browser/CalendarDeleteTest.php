<?php

namespace Tests\Browser;

use App\Models\User;
use App\Models\Produce;
use App\Models\Listing;
use App\Models\HarvestSchedule;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class CalendarDeleteTest extends DuskTestCase
{
    public function testDeleteHarvestSchedule(): void
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
        $futureDay = $futureDate->day;

        $schedule = HarvestSchedule::create([
            'listing_id' => $listing->listing_id,
            'availability_date' => $futureDate->format('Y-m-d'),
            'estimated_stock' => 50,
        ]);

        $this->browse(function (Browser $browser) use ($user, $futureDay, $schedule) {
            $browser->loginAs($user)
                ->visit('/farmer/harvest-calendar')
                ->waitFor('[dusk="day-cell-' . $futureDay . '"]')
                ->click('[dusk="day-cell-' . $futureDay . '"]')
                ->pause(500)
                ->waitFor('[dusk="delete-schedule-' . $schedule->id . '"]')
                ->click('[dusk="delete-schedule-' . $schedule->id . '"]')
                ->pause(500)
                ->press('Delete')
                ->pause(1000)
                ->assertPathIs('/farmer/harvest-calendar')
                ->assertDontSee('×50');
        });
    }

    public function testDeleteHarvestScheduleCancel(): void
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
        $futureDay = $futureDate->day;

        $schedule = HarvestSchedule::create([
            'listing_id' => $listing->listing_id,
            'availability_date' => $futureDate->format('Y-m-d'),
            'estimated_stock' => 50,
        ]);

        $this->browse(function (Browser $browser) use ($user, $futureDay, $schedule) {
            $browser->loginAs($user)
                ->visit('/farmer/harvest-calendar')
                ->waitFor('[dusk="day-cell-' . $futureDay . '"]')
                ->click('[dusk="day-cell-' . $futureDay . '"]')
                ->pause(500)
                ->waitFor('[dusk="delete-schedule-' . $schedule->id . '"]')
                ->click('[dusk="delete-schedule-' . $schedule->id . '"]')
                ->pause(500)
                ->press('Cancel')
                ->pause(500)
                ->assertPathIs('/farmer/harvest-calendar')
                ->assertSee('Wortel Segar')
                ->assertSee('×50');
        });

        $this->assertDatabaseHas('harvest_schedules', [
            'id' => $schedule->id,
        ]);
    }
}
