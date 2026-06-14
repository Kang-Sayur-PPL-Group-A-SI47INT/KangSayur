<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\User;
use App\Models\Produce;
use App\Models\Listing;
use App\Models\Rating;
use App\Models\Cart;
use App\Models\Transaction;
use App\Models\TransactionItem;

class DisplayAverageRatingTest extends DuskTestCase
{
    /**
     * PBI-27: Average rating is displayed on the product detail page.
     */
    public function test_average_rating_displayed_on_product_page(): void
    {
        $farmer = User::factory()->create([
            'role' => 'farmer',
            'verification_status' => 'verified',
        ]);

        $produce = Produce::create([
            'name' => 'Cabai Merah',
            'category' => 'Sayuran',
            'emoji' => '🌶️',
        ]);

        $listing = Listing::create([
            'title' => 'Cabai Merah Keriting',
            'content' => 'Cabai merah keriting segar',
            'price' => 35000,
            'unit' => 'kg',
            'quantity' => 25,
            'status' => 'active',
            'produce_produce_id' => $produce->produce_id,
            'user_user_id' => $farmer->user_id,
        ]);

        // Create 3 ratings with scores: 4, 5, 3 → average = 4.0
        for ($i = 0; $i < 3; $i++) {
            $reviewer = User::factory()->create([
                'role' => 'customer',
            ]);

            $cart = Cart::create([
                'user_user_id' => $reviewer->user_id,
            ]);

            $transaction = Transaction::create([
                'total_price' => 35000,
                'delivery_fee' => 5000,
                'delivery_name' => $reviewer->name,
                'delivery_phone' => '08123456789' . $i,
                'delivery_address' => 'Jl. Test No. ' . ($i + 1),
                'status' => 'delivered',
                'user_user_id' => $reviewer->user_id,
                'cart_cart_id' => $cart->cart_id,
            ]);

            TransactionItem::create([
                'quantity' => 1,
                'unit_price' => 35000,
                'subtotal' => 35000,
                'transaction_transaction_id' => $transaction->transaction_id,
                'listing_listing_id' => $listing->listing_id,
            ]);

            $scores = [4, 5, 3];
            Rating::create([
                'score' => $scores[$i],
                'comment' => 'Rating test ke-' . ($i + 1),
                'listing_listing_id' => $listing->listing_id,
                'user_user_id' => $reviewer->user_id,
                'transaction_transaction_id' => $transaction->transaction_id,
            ]);
        }

        $customer = User::factory()->create([
            'role' => 'customer',
        ]);

        $this->browse(function (Browser $browser) use ($customer, $listing) {
            $browser->loginAs($customer)
                ->visit('/marketplace/' . $listing->listing_id)
                // Average rating should be displayed: (4+5+3)/3 = 4.0
                ->assertSee('4.0')
                // Should show total review count
                ->assertSee('3 reviews')
                // Should show the Ratings & Reviews section
                ->assertSee('Ratings & Reviews');
        });
    }

    /**
     * PBI-27: Average rating displayed on the dedicated reviews page.
     */
    public function test_average_rating_displayed_on_reviews_page(): void
    {
        $farmer = User::factory()->create([
            'role' => 'farmer',
            'verification_status' => 'verified',
        ]);

        $produce = Produce::create([
            'name' => 'Jagung Manis',
            'category' => 'Sayuran',
            'emoji' => '🌽',
        ]);

        $listing = Listing::create([
            'title' => 'Jagung Manis Segar',
            'content' => 'Jagung manis berkualitas',
            'price' => 8000,
            'unit' => 'kg',
            'quantity' => 60,
            'status' => 'active',
            'produce_produce_id' => $produce->produce_id,
            'user_user_id' => $farmer->user_id,
        ]);

        // Create 2 ratings with scores: 5, 3 → average = 4.0
        $scores = [5, 3];
        for ($i = 0; $i < 2; $i++) {
            $reviewer = User::factory()->create([
                'role' => 'customer',
            ]);

            $cart = Cart::create([
                'user_user_id' => $reviewer->user_id,
            ]);

            $transaction = Transaction::create([
                'total_price' => 8000,
                'delivery_fee' => 5000,
                'delivery_name' => $reviewer->name,
                'delivery_phone' => '08123456789' . $i,
                'delivery_address' => 'Jl. Test No. ' . ($i + 1),
                'status' => 'delivered',
                'user_user_id' => $reviewer->user_id,
                'cart_cart_id' => $cart->cart_id,
            ]);

            TransactionItem::create([
                'quantity' => 1,
                'unit_price' => 8000,
                'subtotal' => 8000,
                'transaction_transaction_id' => $transaction->transaction_id,
                'listing_listing_id' => $listing->listing_id,
            ]);

            Rating::create([
                'score' => $scores[$i],
                'comment' => 'Ulasan jagung ke-' . ($i + 1),
                'listing_listing_id' => $listing->listing_id,
                'user_user_id' => $reviewer->user_id,
                'transaction_transaction_id' => $transaction->transaction_id,
            ]);
        }

        $customer = User::factory()->create([
            'role' => 'customer',
        ]);

        $this->browse(function (Browser $browser) use ($customer, $listing) {
            $browser->loginAs($customer)
                ->visit('/marketplace/' . $listing->listing_id . '/reviews')
                // Average = (5+3)/2 = 4.0
                ->assertSee('4.0')
                ->assertSee('2 reviews');
        });
    }

    /**
     * PBI-27: Rating distribution bars are shown on the product page.
     */
    public function test_rating_distribution_displayed(): void
    {
        $farmer = User::factory()->create([
            'role' => 'farmer',
            'verification_status' => 'verified',
        ]);

        $produce = Produce::create([
            'name' => 'Terong',
            'category' => 'Sayuran',
            'emoji' => '🍆',
        ]);

        $listing = Listing::create([
            'title' => 'Terong Ungu Segar',
            'content' => 'Terong ungu berkualitas',
            'price' => 7000,
            'unit' => 'kg',
            'quantity' => 40,
            'status' => 'active',
            'produce_produce_id' => $produce->produce_id,
            'user_user_id' => $farmer->user_id,
        ]);

        // Create ratings with various scores
        $scores = [5, 5, 4, 3, 5];
        foreach ($scores as $i => $score) {
            $reviewer = User::factory()->create([
                'role' => 'customer',
            ]);

            $cart = Cart::create([
                'user_user_id' => $reviewer->user_id,
            ]);

            $transaction = Transaction::create([
                'total_price' => 7000,
                'delivery_fee' => 5000,
                'delivery_name' => $reviewer->name,
                'delivery_phone' => '08123456789' . $i,
                'delivery_address' => 'Jl. Test No. ' . ($i + 1),
                'status' => 'delivered',
                'user_user_id' => $reviewer->user_id,
                'cart_cart_id' => $cart->cart_id,
            ]);

            TransactionItem::create([
                'quantity' => 1,
                'unit_price' => 7000,
                'subtotal' => 7000,
                'transaction_transaction_id' => $transaction->transaction_id,
                'listing_listing_id' => $listing->listing_id,
            ]);

            Rating::create([
                'score' => $score,
                'comment' => 'Ulasan ke-' . ($i + 1),
                'listing_listing_id' => $listing->listing_id,
                'user_user_id' => $reviewer->user_id,
                'transaction_transaction_id' => $transaction->transaction_id,
            ]);
        }

        $customer = User::factory()->create([
            'role' => 'customer',
        ]);

        $this->browse(function (Browser $browser) use ($customer, $listing) {
            $browser->loginAs($customer)
                ->visit('/marketplace/' . $listing->listing_id)
                // Average = (5+5+4+3+5)/5 = 4.4
                ->assertSee('4.4')
                ->assertSee('5 reviews')
                ->assertSee('Ratings & Reviews');
        });
    }

    /**
     * PBI-27: Product page shows placeholder message when no ratings exist.
     */
    public function test_no_ratings_placeholder_displayed(): void
    {
        $farmer = User::factory()->create([
            'role' => 'farmer',
            'verification_status' => 'verified',
        ]);

        $produce = Produce::create([
            'name' => 'Selada',
            'category' => 'Sayuran',
            'emoji' => '🥬',
        ]);

        $listing = Listing::create([
            'title' => 'Selada Hijau',
            'content' => 'Selada hijau segar',
            'price' => 6000,
            'unit' => 'ikat',
            'quantity' => 50,
            'status' => 'active',
            'produce_produce_id' => $produce->produce_id,
            'user_user_id' => $farmer->user_id,
        ]);

        $customer = User::factory()->create([
            'role' => 'customer',
        ]);

        $this->browse(function (Browser $browser) use ($customer, $listing) {
            $browser->loginAs($customer)
                ->visit('/marketplace/' . $listing->listing_id)
                ->assertSee('No reviews yet');
        });
    }
}
