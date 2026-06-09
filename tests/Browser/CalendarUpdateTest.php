<?php

namespace Tests\Browser;

use App\Models\User;
use App\Models\Produce;
use App\Models\Listing;
use App\Models\HarvestSchedule;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class CalendarUpdateTest extends DuskTestCase
{
    public function testUpdateHarvestSchedule(): void
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
                ->waitFor('[dusk="edit-schedule-' . $schedule->id . '"]')
                ->click('[dusk="edit-schedule-' . $schedule->id . '"]')
                ->pause(500)
                ->within('@edit-schedule-form', function (Browser $form) {
                    $form->type('estimated_stock', '120')
                        ->press('Save Changes');
                })
                ->pause(1000)
                ->assertPathIs('/farmer/harvest-calendar')
                ->assertSee('Wortel Segar')
                ->assertSee('×120');
        });
    }

    public function testUpdateHarvestScheduleValidationFailure(): void
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

        $dateA = now()->addDays(5);
        $dateB = now()->addDays(6);
        $dayA = $dateA->day;
        $dateBInput = $dateB->format('Y-m-d');

        $scheduleA = HarvestSchedule::create([
            'listing_id' => $listing->listing_id,
            'availability_date' => $dateA->format('Y-m-d'),
            'estimated_stock' => 50,
        ]);

        $scheduleB = HarvestSchedule::create([
            'listing_id' => $listing->listing_id,
            'availability_date' => $dateB->format('Y-m-d'),
            'estimated_stock' => 100,
        ]);

        $this->browse(function (Browser $browser) use ($user, $dayA, $scheduleA, $dateBInput) {
            $browser->loginAs($user)
                ->visit('/farmer/harvest-calendar')
                ->waitFor('[dusk="day-cell-' . $dayA . '"]')
                ->click('[dusk="day-cell-' . $dayA . '"]')
                ->pause(500)
                ->waitFor('[dusk="edit-schedule-' . $scheduleA->id . '"]')
                ->click('[dusk="edit-schedule-' . $scheduleA->id . '"]')
                ->pause(500);

            // Change availability date using script to duplicate scheduleB's date
            $browser->script("document.querySelector('form[dusk=\"edit-schedule-form\"] input[name=\"availability_date\"]').value = '{$dateBInput}';");

            $browser->within('@edit-schedule-form', function (Browser $form) {
                $form->press('Save Changes');
            })
            ->pause(1000)
            ->assertPathIs('/farmer/harvest-calendar')
            ->assertSee('A schedule already exists for this listing on the selected date.');
        });

        $this->assertDatabaseHas('harvest_schedules', [
            'id' => $scheduleA->id,
            'availability_date' => $dateA->format('Y-m-d'),
        ]);
    }
}
