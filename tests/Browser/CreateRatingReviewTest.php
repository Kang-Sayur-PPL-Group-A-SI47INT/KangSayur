<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\User;
use App\Models\Produce;
use App\Models\Listing;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Transaction;
use App\Models\TransactionItem;

class CreateRatingReviewTest extends DuskTestCase
{
    /**
     * Helper: set up a customer with a delivered order containing one listing.
     */
    private function createDeliveredOrder(): array
    {
        $customer = User::factory()->create([
            'role' => 'customer',
        ]);

        $farmer = User::factory()->create([
            'role' => 'farmer',
            'verification_status' => 'verified',
        ]);

        $produce = Produce::create([
            'name' => 'Tomat Segar',
            'category' => 'Sayuran',
            'emoji' => '🍅',
        ]);

        $listing = Listing::create([
            'title' => 'Tomat Segar Organik',
            'content' => 'Tomat segar berkualitas tinggi',
            'price' => 12000,
            'unit' => 'kg',
            'quantity' => 50,
            'status' => 'active',
            'produce_produce_id' => $produce->produce_id,
            'user_user_id' => $farmer->user_id,
        ]);

        $cart = Cart::create([
            'user_user_id' => $customer->user_id,
        ]);

        $transaction = Transaction::create([
            'total_price' => 12000,
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
            'unit_price' => 12000,
            'subtotal' => 12000,
            'transaction_transaction_id' => $transaction->transaction_id,
            'listing_listing_id' => $listing->listing_id,
        ]);

        return compact('customer', 'farmer', 'produce', 'listing', 'transaction');
    }

    /**
     * PBI-25: Customer can create a rating & review for a delivered order item.
     */
    public function test_customer_can_create_rating_and_review(): void
    {
        $data = $this->createDeliveredOrder();

        $this->browse(function (Browser $browser) use ($data) {
            $browser->loginAs($data['customer'])
                ->visit('/orders/' . $data['transaction']->transaction_id)
                ->assertSee('Rate Your Order')
                ->assertSee($data['listing']->title)
                // Click 4 stars
                ->click('@star-4-' . $data['listing']->listing_id)
                ->pause(300)
                // Type review comment
                ->type('comment', 'Tomat-nya sangat segar dan berkualitas!')
                // Submit the review
                ->press('@submit-review-' . $data['listing']->listing_id)
                ->pause(1000)
                ->assertSee('Thank you for your review! 🌟');
        });
    }

    /**
     * PBI-25: Customer can submit a rating without comment (comment is optional).
     */
    public function test_customer_can_create_rating_without_comment(): void
    {
        $data = $this->createDeliveredOrder();

        $this->browse(function (Browser $browser) use ($data) {
            $browser->loginAs($data['customer'])
                ->visit('/orders/' . $data['transaction']->transaction_id)
                ->assertSee('Rate Your Order')
                // Click 5 stars
                ->click('@star-5-' . $data['listing']->listing_id)
                ->pause(300)
                // Submit without comment
                ->press('@submit-review-' . $data['listing']->listing_id)
                ->pause(1000)
                ->assertSee('Thank you for your review! 🌟');
        });
    }

    /**
     * PBI-25: Cannot submit a review without selecting a star rating.
     * The submit button should be disabled when score is 0.
     */
    public function test_customer_cannot_submit_review_without_rating(): void
    {
        $data = $this->createDeliveredOrder();

        $this->browse(function (Browser $browser) use ($data) {
            $browser->loginAs($data['customer'])
                ->visit('/orders/' . $data['transaction']->transaction_id)
                ->assertSee('Rate Your Order')
                // The submit button should be disabled (score = 0)
                ->assertAttribute(
                    '@submit-review-' . $data['listing']->listing_id,
                    'disabled',
                    'true'
                );
        });
    }

    /**
     * PBI-25: Customer cannot review an order that is not delivered yet.
     * The "Rate Your Order" section should not appear for pending orders.
     */
    public function test_rate_section_not_shown_for_non_delivered_order(): void
    {
        $customer = User::factory()->create([
            'role' => 'customer',
        ]);

        $farmer = User::factory()->create([
            'role' => 'farmer',
            'verification_status' => 'verified',
        ]);

        $produce = Produce::create([
            'name' => 'Bayam Hijau',
            'category' => 'Sayuran',
            'emoji' => '🥬',
        ]);

        $listing = Listing::create([
            'title' => 'Bayam Hijau Segar',
            'content' => 'Bayam hijau segar organik',
            'price' => 8000,
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
            'total_price' => 8000,
            'delivery_fee' => 5000,
            'delivery_name' => $customer->name,
            'delivery_phone' => '081234567890',
            'delivery_address' => 'Jl. Test No. 2',
            'status' => 'paid',
            'user_user_id' => $customer->user_id,
            'cart_cart_id' => $cart->cart_id,
        ]);

        TransactionItem::create([
            'quantity' => 1,
            'unit_price' => 8000,
            'subtotal' => 8000,
            'transaction_transaction_id' => $transaction->transaction_id,
            'listing_listing_id' => $listing->listing_id,
        ]);

        $this->browse(function (Browser $browser) use ($customer, $transaction) {
            $browser->loginAs($customer)
                ->visit('/orders/' . $transaction->transaction_id)
                ->assertDontSee('Rate Your Order');
        });
    }
}
