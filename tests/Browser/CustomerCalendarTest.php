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
    /**
     * Test positive case: A customer can view the harvest calendar.
     */
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

    /**
     * Test positive case: A customer can view a schedule with an auto-discount in the calendar modal.
     */
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

        // Establish a baseline average of 10
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
            'estimated_stock' => 20, // Avg will be (10+20)/2 = 15. Surplus is 20-15 = 5. 5/15 = 33% (>25% -> 15% discount)
        ]);

        // Manually apply the discount since we're bypassing the controller
        HarvestDiscountService::applyDiscount($listing, $schedule);
        $listing->refresh();

        $this->browse(function (Browser $browser) use ($customer, $futureDay) {
            $browser->loginAs($customer)
                ->visit('/customer/harvest-calendar')
                ->waitForText('Harvest Calendar')
                // Wait for the specific day cell to be visible and click it
                ->waitFor("div[x-data] div.grid.grid-cols-7 > div:not(.bg-gray-50\\/30)") // Broad selector to wait for grid
                ->pause(1000)
                ->script("document.querySelectorAll('div.grid.grid-cols-7 > div').forEach(el => {
                    if (el.textContent.includes('$futureDay')) {
                        el.click();
                    }
                });");
                
            $browser->pause(1000)
                // Wait for the modal to appear
                ->waitFor('#customer-date-detail-content')
                ->assertSee('Wortel Segar Lembang Test')
                ->assertSee('🏷️')
                ->assertSee('15% OFF')
                ->assertSee('85.000'); // Effective price
        });
    }
}
