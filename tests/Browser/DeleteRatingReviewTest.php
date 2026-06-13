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
//vamosssss
class DeleteRatingReviewTest extends DuskTestCase
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
            'name' => 'Kentang',
            'category' => 'Sayuran',
            'emoji' => '🥔',
        ]);

        $listing = Listing::create([
            'title' => 'Kentang Segar',
            'content' => 'Kentang segar berkualitas',
            'price' => 15000,
            'unit' => 'kg',
            'quantity' => 30,
            'status' => 'active',
            'produce_produce_id' => $produce->produce_id,
            'user_user_id' => $farmer->user_id,
        ]);

        $cart = Cart::create([
            'user_user_id' => $customer->user_id,
        ]);

        $transaction = Transaction::create([
            'total_price' => 15000,
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
            'unit_price' => 15000,
            'subtotal' => 15000,
            'transaction_transaction_id' => $transaction->transaction_id,
            'listing_listing_id' => $listing->listing_id,
        ]);

        $rating = Rating::create([
            'score' => 4,
            'comment' => 'Kentang-nya bagus dan segar',
            'listing_listing_id' => $listing->listing_id,
            'user_user_id' => $customer->user_id,
            'transaction_transaction_id' => $transaction->transaction_id,
        ]);

        return compact('customer', 'farmer', 'produce', 'listing', 'transaction', 'rating');
    }

    /**
     * PBI-28: Customer can delete their own review from the order detail page.
     */
    public function test_customer_can_delete_review_from_order_detail(): void
    {
        $data = $this->createOrderWithRating();

        $this->browse(function (Browser $browser) use ($data) {
            $browser->loginAs($data['customer'])
                ->visit('/orders/' . $data['transaction']->transaction_id)
                // Should see the existing review
                ->assertSee('Kentang-nya bagus dan segar')
                // Click the delete button on the order detail page
                ->press('@delete-review-' . $data['listing']->listing_id)
                ->acceptDialog()
                ->pause(1000)
                ->assertSee('Your review has been deleted.');
        });
    }

    /**
     * PBI-28: Customer can delete their own review from the product page.
     */
    public function test_customer_can_delete_review_from_product_page(): void
    {
        $data = $this->createOrderWithRating();

        $this->browse(function (Browser $browser) use ($data) {
            $browser->loginAs($data['customer'])
                ->visit('/marketplace/' . $data['listing']->listing_id)
                // Should see their review highlighted
                ->assertSee('Your Review')
                ->assertSee('Kentang-nya bagus dan segar')
                // Click delete button (confirm dialog auto-accepted in Dusk)
                ->press('@delete-review-btn')
                ->acceptDialog()
                ->pause(1000)
                ->assertSee('Your review has been deleted.');
        });
    }

    /**
     * PBI-28: After deleting a review, the review form reappears on the order detail page.
     */
    public function test_review_form_reappears_after_deletion(): void
    {
        $data = $this->createOrderWithRating();

        $this->browse(function (Browser $browser) use ($data) {
            $browser->loginAs($data['customer'])
                ->visit('/orders/' . $data['transaction']->transaction_id)
                // Should see the existing review, not the form
                ->assertSee('Kentang-nya bagus dan segar')
                ->assertDontSee('Your Rating')
                // Delete the review
                ->press('@delete-review-' . $data['listing']->listing_id)
                ->acceptDialog()
                ->pause(1000)
                ->assertSee('Your review has been deleted.')
                // Revisit the order detail page — the form should be available again
                ->visit('/orders/' . $data['transaction']->transaction_id)
                ->assertSee('Rate Your Order')
                ->assertPresent('@star-1-' . $data['listing']->listing_id);
        });
    }
}
