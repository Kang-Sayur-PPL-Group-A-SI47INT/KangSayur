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

class ReadRatingReviewTest extends DuskTestCase
{
    /**
     * Helper: create a listing with ratings from multiple users.
     */
    private function createListingWithRatings(): array
    {
        $farmer = User::factory()->create([
            'role' => 'farmer',
            'verification_status' => 'verified',
        ]);

        $produce = Produce::create([
            'name' => 'Kangkung Segar',
            'category' => 'Sayuran',
            'emoji' => '🥬',
        ]);

        $listing = Listing::create([
            'title' => 'Kangkung Segar Organik',
            'content' => 'Kangkung segar berkualitas tinggi',
            'price' => 5000,
            'unit' => 'ikat',
            'quantity' => 100,
            'status' => 'active',
            'produce_produce_id' => $produce->produce_id,
            'user_user_id' => $farmer->user_id,
        ]);

        // Create reviewers with delivered transactions
        $reviewers = [];
        for ($i = 0; $i < 3; $i++) {
            $reviewer = User::factory()->create([
                'role' => 'customer',
            ]);

            $cart = Cart::create([
                'user_user_id' => $reviewer->user_id,
            ]);

            $transaction = Transaction::create([
                'total_price' => 5000,
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
                'unit_price' => 5000,
                'subtotal' => 5000,
                'transaction_transaction_id' => $transaction->transaction_id,
                'listing_listing_id' => $listing->listing_id,
            ]);

            Rating::create([
                'score' => $i + 3, // scores: 3, 4, 5
                'comment' => 'Review comment dari reviewer ke-' . ($i + 1),
                'listing_listing_id' => $listing->listing_id,
                'user_user_id' => $reviewer->user_id,
                'transaction_transaction_id' => $transaction->transaction_id,
            ]);

            $reviewers[] = $reviewer;
        }

        // Create a customer who will read the reviews
        $customer = User::factory()->create([
            'role' => 'customer',
        ]);

        return compact('customer', 'farmer', 'produce', 'listing', 'reviewers');
    }

    /**
     * PBI-26: Customer can read reviews on the product detail page.
     */
    public function test_customer_can_read_reviews_on_product_page(): void
    {
        $data = $this->createListingWithRatings();

        $this->browse(function (Browser $browser) use ($data) {
            $browser->loginAs($data['customer'])
                ->visit('/marketplace/' . $data['listing']->listing_id)
                ->assertSee('Customer Reviews')
                ->assertSee('Review comment dari reviewer ke-1')
                ->assertSee('★★★');
        });
    }

    /**
     * PBI-26: Customer can read reviews on the dedicated reviews page.
     */
    public function test_customer_can_read_reviews_on_reviews_page(): void
    {
        $data = $this->createListingWithRatings();

        $this->browse(function (Browser $browser) use ($data) {
            $browser->loginAs($data['customer'])
                ->visit('/marketplace/' . $data['listing']->listing_id . '/reviews')
                ->assertSee('ALL REVIEWS ')
                ->assertSee('Review comment dari reviewer ke-1')
                ->assertSee('Review comment dari reviewer ke-2')
                ->assertSee('Review comment dari reviewer ke-3');
        });
    }

    /**
     * PBI-26: Product page shows "No reviews yet" when there are no reviews.
     */
    public function test_no_reviews_message_shown_when_empty(): void
    {
        $farmer = User::factory()->create([
            'role' => 'farmer',
            'verification_status' => 'verified',
        ]);

        $produce = Produce::create([
            'name' => 'Brokoli',
            'category' => 'Sayuran',
            'emoji' => '🥦',
        ]);

        $listing = Listing::create([
            'title' => 'Brokoli Segar',
            'content' => 'Brokoli hijau segar',
            'price' => 15000,
            'unit' => 'kg',
            'quantity' => 20,
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

    /**
     * PBI-26: Customer can see their own review highlighted with "Your Review" badge.
     */
    public function test_customer_sees_own_review_highlighted(): void
    {
        $farmer = User::factory()->create([
            'role' => 'farmer',
            'verification_status' => 'verified',
        ]);

        $produce = Produce::create([
            'name' => 'Wortel',
            'category' => 'Sayuran',
            'emoji' => '🥕',
        ]);

        $listing = Listing::create([
            'title' => 'Wortel Segar',
            'content' => 'Wortel organik berkualitas',
            'price' => 10000,
            'unit' => 'kg',
            'quantity' => 40,
            'status' => 'active',
            'produce_produce_id' => $produce->produce_id,
            'user_user_id' => $farmer->user_id,
        ]);

        $customer = User::factory()->create([
            'role' => 'customer',
        ]);

        $cart = Cart::create([
            'user_user_id' => $customer->user_id,
        ]);

        $transaction = Transaction::create([
            'total_price' => 10000,
            'delivery_fee' => 5000,
            'delivery_name' => $customer->name,
            'delivery_phone' => '081234567890',
            'delivery_address' => 'Jl. Test No. 1',
            'status' => 'delivered',
            'user_user_id' => $customer->user_id,
            'cart_cart_id' => $cart->cart_id,
        ]);

        TransactionItem::create([
            'quantity' => 1,
            'unit_price' => 10000,
            'subtotal' => 10000,
            'transaction_transaction_id' => $transaction->transaction_id,
            'listing_listing_id' => $listing->listing_id,
        ]);

        Rating::create([
            'score' => 5,
            'comment' => 'Wortel-nya sangat segar!',
            'listing_listing_id' => $listing->listing_id,
            'user_user_id' => $customer->user_id,
            'transaction_transaction_id' => $transaction->transaction_id,
        ]);

        $this->browse(function (Browser $browser) use ($customer, $listing) {
            $browser->loginAs($customer)
                ->visit('/marketplace/' . $listing->listing_id)
                ->assertSee('Your Review')
                ->assertSee('Wortel-nya sangat segar!');
        });
    }
}
