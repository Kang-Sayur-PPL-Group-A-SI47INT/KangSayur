<?php

namespace Tests\Browser;

use App\Models\User;
use App\Models\Produce;
use App\Models\Listing;
use App\Models\ListingStockLog;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class FarmerCalendarDiscountTest extends DuskTestCase
{
    /**
     * Test positive case: A farmer sees the discount preview when creating a schedule with high stock.
     */
    public function test_farmer_sees_discount_preview_on_create(): void
    {
        $farmer = User::factory()->create([
            'role' => 'farmer',
            'verification_status' => 'verified',
        ]);

        $produce = Produce::firstOrCreate(
            ['name' => 'Tomat'],
            ['category' => 'Sayuran', 'emoji' => '🍅']
        );

        $listing = Listing::create([
            'title' => 'Tomat Merah Segar',
            'content' => 'Tomat segar.',
            'price' => 20000,
            'quantity' => 10,
            'unit' => 'kg',
            'status' => 'active',
            'user_user_id' => $farmer->user_id,
            'produce_produce_id' => $produce->produce_id,
        ]);

        // Establish a baseline average of 10
        ListingStockLog::create([
            'listing_id' => $listing->listing_id,
            'quantity' => 10,
            'source' => 'restock',
            'created_at' => now()->subDays(5),
        ]);

        $this->browse(function (Browser $browser) use ($farmer, $listing) {
            $browser->loginAs($farmer)
                ->visit('/farmer/harvest-calendar')
                ->waitForText('Harvest Calendar')
                ->press('Add Schedule')
                ->pause(500)
                ->select('listing_id', $listing->listing_id)
                // When we type 20, average is 10. 20 is +100% surplus -> 15% discount.
                ->type('estimated_stock', '20')
                ->pause(500)
                // Wait for the Alpine JS preview text
                ->waitForText('will trigger a 15% auto-discount')
                ->assertSee('🏷️')
                ->assertSee('Based on your average harvest of 10 units')
                ->assertSee('this schedule of 20 units (+100% surplus)');
        });
    }
}
