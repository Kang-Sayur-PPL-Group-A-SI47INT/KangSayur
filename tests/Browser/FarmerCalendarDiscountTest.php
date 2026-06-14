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
    public function testFarmerSeesDiscountPreviewOnCreate(): void
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
                ->type('estimated_stock', '20')
                ->pause(500)
                ->waitForText('will trigger a 15% auto-discount')
                ->assertSee('🏷️')
                ->assertSee('Based on your average harvest of 10 units')
                ->assertSee('this schedule of 20 units (+100% surplus)');
        });
    }
}
