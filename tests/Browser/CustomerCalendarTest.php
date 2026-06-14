<?php

namespace Tests\Browser;

use App\Models\User;
use App\Models\Produce;
use App\Models\Listing;
use App\Models\HarvestSchedule;
use App\Services\HarvestDiscountService;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class CustomerCalendarTest extends DuskTestCase
{
    public function test_customer_can_view_calendar(): void
    {
        $customer = User::factory()->create([
            'role' => 'customer',
        ]);

        $this->browse(function (Browser $browser) use ($customer) {
            $browser->loginAs($customer)
                ->visit('/customer/harvest-calendar')
                ->waitForText('Harvest Calendar')
                ->assertSee('Harvest Calendar 🌾')
                ->assertSee('See what\'s being harvested soon');
        });
    }

    public function test_customer_sees_discount_in_calendar(): void
    {
        $customer = User::factory()->create([
            'role' => 'customer',
        ]);

        $farmer = User::factory()->create([
            'role' => 'farmer',
            'verification_status' => 'verified',
        ]);

        $produce = Produce::firstOrCreate(
            ['name' => 'Wortel'],
            ['category' => 'Sayuran', 'emoji' => '🥕']
        );

        $listing = Listing::create([
            'title' => 'Wortel Segar Lembang Test',
            'content' => 'Wortel segar.',
            'price' => 100000,
            'quantity' => 10,
            'unit' => 'kg',
            'status' => 'active',
            'user_user_id' => $farmer->user_id,
            'produce_produce_id' => $produce->produce_id,
        ]);

        \App\Models\ListingStockLog::create([
            'listing_id' => $listing->listing_id,
            'quantity' => 10,
            'source' => 'restock',
            'created_at' => now()->subDays(5),
        ]);

        $futureDate = now()->addDays(5);
        $futureDay = $futureDate->day;

        $schedule = HarvestSchedule::create([
            'listing_id' => $listing->listing_id,
            'availability_date' => $futureDate->format('Y-m-d'),
            'estimated_stock' => 20,
        ]);

        HarvestDiscountService::applyDiscount($listing, $schedule);
        $listing->refresh();

        $this->browse(function (Browser $browser) use ($customer, $futureDay) {
            $browser->loginAs($customer)
                ->visit('/customer/harvest-calendar')
                ->waitForText('Harvest Calendar')
                ->waitFor("div[x-data] div.grid.grid-cols-7 > div:not(.bg-gray-50\\/30)")
                ->pause(1000)
                ->script("document.querySelectorAll('div.grid.grid-cols-7 > div').forEach(el => {
                    if (el.textContent.includes('$futureDay')) {
                        el.click();
                    }
                });");
                
            $browser->pause(1000)
                ->waitFor('#customer-date-detail-content')
                ->assertSee('Wortel Segar Lembang Test')
                ->assertSee('🏷️')
                ->assertSee('15% OFF')
                ->assertSee('85.000');
        });
    }
}
