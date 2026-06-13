<?php

namespace Tests\Browser;

use App\Models\User;
use App\Models\Produce;
use App\Models\Listing;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class PBI17FarmerOrderTrackingTest extends DuskTestCase
{
    /**
     * Helper to create a farmer with a listing, a customer, and a transaction.
     */
    private function createFarmerOrderSetup(string $status = 'paid'): array
    {
        $farmer = User::factory()->create([
            'role' => 'farmer',
            'verification_status' => 'verified',
        ]);

        $produce = Produce::first() ?? Produce::create([
            'name' => 'Bayam',
            'category' => 'Sayuran',
            'emoji' => '🥬',
        ]);

        $listing = Listing::create([
            'title' => 'Bayam Segar Order Test',
            'content' => 'Bayam segar untuk test order tracking',
            'price' => 10000,
            'quantity' => 100,
            'unit' => 'kg',
            'status' => 'active',
            'produce_produce_id' => $produce->produce_id,
            'user_user_id' => $farmer->user_id,
            
        ]); 

        $customer = User::factory()->create(['role' => 'customer']);

        $transaction = Transaction::create([
            'total_price' => 20000,
            'delivery_fee' => 5000,
            'delivery_name' => 'Test Customer',
            'delivery_phone' => '081234567890',
            'delivery_address' => 'Jl. Test No. 1',
            'status' => $status,
            'midtrans_order_id' => 'KS-TEST-' . uniqid(),
            'user_user_id' => $customer->user_id,
            'paid_at' => $status !== 'pending' ? now() : null,
            'paid_status_at' => $status !== 'pending' ? now() : null,
            'cart_cart_id' => 1
        ]);

        TransactionItem::create([
            'quantity' => 2,
            'unit_price' => 10000,
            'subtotal' => 20000,
            'transaction_transaction_id' => $transaction->transaction_id,
            'listing_listing_id' => $listing->listing_id,
        ]);

        return compact('farmer', 'customer', 'listing', 'transaction', 'produce');
    }

    /**
     * Test farmer can view their order list.
     */
    public function test_farmer_can_view_order_list(): void
    {
        $setup = $this->createFarmerOrderSetup('paid');

        $this->browse(function (Browser $browser) use ($setup) {
            $browser->loginAs($setup['farmer'])
                ->visit('/farmer/orders')
                ->assertPathIs('/farmer/orders')
                ->assertSee('Order Management 📦')
                ->assertSee($setup['transaction']->midtrans_order_id)
                ->assertSee('Paid')
                ->assertSee('Bayam Segar Order Test');
        });
    }

    /**
     * Test farmer can see order details including items and total.
     */
    public function test_farmer_can_see_order_details(): void
    {
        $setup = $this->createFarmerOrderSetup('paid');

        $this->browse(function (Browser $browser) use ($setup) {
            $browser->loginAs($setup['farmer'])
                ->visit('/farmer/orders')
                ->assertSee($setup['transaction']->midtrans_order_id)
                ->assertSee('Bayam Segar Order Test')
                ->assertSee('2 ×')  // quantity
                ->assertSee('Rp 20.000');  // subtotal
        });
    }

    /**
     * Test farmer sees empty state when no orders exist.
     */
    public function test_farmer_sees_empty_orders(): void
    {
        $farmer = User::factory()->create([
            'role' => 'farmer',
            'verification_status' => 'verified',
        ]);

        $this->browse(function (Browser $browser) use ($farmer) {
            $browser->loginAs($farmer)
                ->visit('/farmer/orders')
                ->assertPathIs('/farmer/orders')
                ->assertSee('No Orders Yet')
                ->assertSee('Orders for your produce will appear here.');
        });
    }

    /**
     * Test farmer can see orders with different statuses.
     */
    public function test_farmer_can_see_different_order_statuses(): void
    {
        $setup = $this->createFarmerOrderSetup('shipping');

        $this->browse(function (Browser $browser) use ($setup) {
            $browser->loginAs($setup['farmer'])
                ->visit('/farmer/orders')
                ->assertSee('Shipping');
        });
    }

    /**
     * Test farmer can delete cancelled order.
     */
    public function test_farmer_can_delete_cancelled_order(): void
    {
        $setup = $this->createFarmerOrderSetup('cancelled');

        $this->browse(function (Browser $browser) use ($setup) {
            $browser->loginAs($setup['farmer'])
                ->visit('/farmer/orders')
                ->assertSee('Cancelled')
                ->assertPresent("@delete-order-{$setup['transaction']->transaction_id}")
                ->press("@delete-order-{$setup['transaction']->transaction_id}")
                ->acceptDialog()
                ->pause(1000)
                ->assertSee('deleted successfully');
        });
    }

    /**
     * Test farmer can delete delivered order.
     */
    public function test_farmer_can_delete_delivered_order(): void
    {
        $setup = $this->createFarmerOrderSetup('delivered');

        $this->browse(function (Browser $browser) use ($setup) {
            $browser->loginAs($setup['farmer'])
                ->visit('/farmer/orders')
                ->assertSee('Delivered')
                ->assertPresent("@delete-order-{$setup['transaction']->transaction_id}")
                ->press("@delete-order-{$setup['transaction']->transaction_id}")
                ->acceptDialog()
                ->pause(1000)
                ->assertSee('deleted successfully');
        });
    }

    /**
     * Test farmer cannot see delete button for paid orders.
     */
    public function test_farmer_cannot_delete_paid_order(): void
    {
        $setup = $this->createFarmerOrderSetup('paid');

        $this->browse(function (Browser $browser) use ($setup) {
            $browser->loginAs($setup['farmer'])
                ->visit('/farmer/orders')
                ->assertSee('Paid')
                ->assertMissing("@delete-order-{$setup['transaction']->transaction_id}");
        });
    }

    /**
     * Test farmer order page shows customer name.
     */
    public function test_farmer_order_shows_customer_name(): void
    {
        $setup = $this->createFarmerOrderSetup('paid');

        $this->browse(function (Browser $browser) use ($setup) {
            $browser->loginAs($setup['farmer'])
                ->visit('/farmer/orders')
                ->assertSee($setup['customer']->name);
        });
    }

    /**
     * Test customer cannot access farmer orders page.
     */
    public function test_customer_cannot_access_farmer_orders(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);

        $this->browse(function (Browser $browser) use ($customer) {
            $browser->loginAs($customer)
                ->visit('/farmer/orders')
                ->assertSee('403');
        });
    }

    /**
     * Test unverified farmer cannot access orders page.
     */
    public function test_unverified_farmer_cannot_access_orders(): void
    {
        $farmer = User::factory()->create([
            'role' => 'farmer',
            'verification_status' => 'unverified',
        ]);

        $this->browse(function (Browser $browser) use ($farmer) {
            $browser->loginAs($farmer)
                ->visit('/farmer/orders')
                ->pause(1000)
                ->assertPathIs('/farmer/profile');
        });
    }

    /**
     * Test farmer cannot delete shipping order.
     */
    public function test_farmer_cannot_delete_shipping_order(): void
    {
        $setup = $this->createFarmerOrderSetup('shipping');

        $this->browse(function (Browser $browser) use ($setup) {
            $browser->loginAs($setup['farmer'])
                ->visit('/farmer/orders')
                ->assertSee('Shipping')
                ->assertMissing("@delete-order-{$setup['transaction']->transaction_id}");
        });
    }

    /**
     * Test unauthenticated user cannot access farmer orders.
     */
    public function test_unauthenticated_cannot_access_farmer_orders(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/farmer/orders')
                ->assertPathIs('/login');
        });
    }
}
