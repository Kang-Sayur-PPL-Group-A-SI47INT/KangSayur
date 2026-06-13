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

class UpdateRatingReviewTest extends DuskTestCase
{
    /**
     * Helper: create a customer with a delivered order and an existing rating.
     */
    private function createOrderWithRating(): array
    {
        $customer = User::factory()->create([
            'role' => 'customer',
        ]);

        $farmer = User::factory()->create([
            'role' => 'farmer',
            'verification_status' => 'verified',
        ]);

        $produce = Produce::create([
            'name' => 'Bawang Putih',
            'category' => 'Sayuran',
            'emoji' => '🧄',
        ]);

        $listing = Listing::create([
            'title' => 'Bawang Putih Segar',
            'content' => 'Bawang putih segar organik',
            'price' => 25000,
            'unit' => 'kg',
            'quantity' => 40,
            'status' => 'active',
            'produce_produce_id' => $produce->produce_id,
            'user_user_id' => $farmer->user_id,
        ]);

        $cart = Cart::create([
            'user_user_id' => $customer->user_id,
        ]);

        $transaction = Transaction::create([
            'total_price' => 25000,
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
            'unit_price' => 25000,
            'subtotal' => 25000,
            'transaction_transaction_id' => $transaction->transaction_id,
            'listing_listing_id' => $listing->listing_id,
        ]);

        $rating = Rating::create([
            'score' => 3,
            'comment' => 'Bawang-nya lumayan bagus',
            'listing_listing_id' => $listing->listing_id,
            'user_user_id' => $customer->user_id,
            'transaction_transaction_id' => $transaction->transaction_id,
        ]);

        return compact('customer', 'farmer', 'produce', 'listing', 'transaction', 'rating');
    }

    /**
     * PBI-29: Customer can update their review by deleting and re-submitting.
     * (The update flow is: delete existing review → submit new review)
     */
    public function test_customer_can_update_review(): void
    {
        $data = $this->createOrderWithRating();

        $this->browse(function (Browser $browser) use ($data) {
            $browser->loginAs($data['customer'])
                ->visit('/orders/' . $data['transaction']->transaction_id)
                // Should see existing review
                ->assertSee('Bawang-nya lumayan bagus')
                // Delete the existing review
                ->press('@delete-review-' . $data['listing']->listing_id)
                ->acceptDialog()
                ->pause(1000)
                ->assertSee('Your review has been deleted.')
                // Revisit the order detail page — the form should now be visible
                ->visit('/orders/' . $data['transaction']->transaction_id)
                ->assertSee('Rate Your Order')
                // Submit a new (updated) review with a higher score
                ->click('@star-5-' . $data['listing']->listing_id)
                ->pause(300)
                ->type('comment', 'Ternyata bawang-nya luar biasa segar! Recommended!')
                ->press('@submit-review-' . $data['listing']->listing_id)
                ->pause(1000)
                ->assertSee('Thank you for your review! 🌟');
        });
    }

    /**
     * PBI-29: The updated review (new score and comment) is displayed correctly.
     */
    public function test_updated_review_is_displayed_correctly(): void
    {
        $data = $this->createOrderWithRating();

        $this->browse(function (Browser $browser) use ($data) {
            $browser->loginAs($data['customer'])
                ->visit('/orders/' . $data['transaction']->transaction_id)
                // Delete existing review
                ->press('@delete-review-' . $data['listing']->listing_id)
                ->acceptDialog()
                ->pause(1000)
                // Revisit and submit updated review
                ->visit('/orders/' . $data['transaction']->transaction_id)
                ->click('@star-5-' . $data['listing']->listing_id)
                ->pause(300)
                ->type('comment', 'Review yang diperbarui - sangat puas!')
                ->press('@submit-review-' . $data['listing']->listing_id)
                ->pause(1000)
                ->assertSee('Thank you for your review! 🌟')
                // Verify the new review appears on the product page
                ->visit('/marketplace/' . $data['listing']->listing_id)
                ->assertSee('Review yang diperbarui - sangat puas!')
                ->assertSee('Your Review');
        });
    }

    /**
     * PBI-29: Updated review changes the average rating on the product page.
     */
    public function test_updated_review_changes_average_rating(): void
    {
        $data = $this->createOrderWithRating();

        // First, verify the current rating is reflected
        $this->browse(function (Browser $browser) use ($data) {
            $browser->loginAs($data['customer'])
                ->visit('/marketplace/' . $data['listing']->listing_id)
                // Initial average: 3.0 (only one review with score 3)
                ->assertSee('3.0')
                // Navigate to order detail and update the review
                ->visit('/orders/' . $data['transaction']->transaction_id)
                ->press('@delete-review-' . $data['listing']->listing_id)
                ->acceptDialog()
                ->pause(1000)
                ->visit('/orders/' . $data['transaction']->transaction_id)
                ->click('@star-5-' . $data['listing']->listing_id)
                ->pause(300)
                ->type('comment', 'Setelah dicoba lagi ternyata sangat bagus!')
                ->press('@submit-review-' . $data['listing']->listing_id)
                ->pause(1000)
                // Verify new average on product page: 5.0 (only one review with score 5)
                ->visit('/marketplace/' . $data['listing']->listing_id)
                ->assertSee('5.0');
        });
    }
}
