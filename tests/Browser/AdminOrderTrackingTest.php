<?php

namespace Tests\Browser;

use App\Models\User;
use App\Models\Produce;
use App\Models\Listing;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class PBI17AdminOrderTrackingTest extends DuskTestCase
{
    /**
     * Helper to create a full order setup: farmer + listing + customer + transaction.
     */
    private function createOrderSetup(string $status = 'paid', array $overrides = []): array
    {
        $farmer = User::factory()->create([
            'role' => 'farmer',
            'verification_status' => 'verified',
            'name' => 'Petani Order Test',
        ]);

        $produce = Produce::first() ?? Produce::create([
            'name' => 'Bayam',
            'category' => 'Sayuran',
            'emoji' => '🥬',
        ]);

        $listing = Listing::create([
            'title' => 'Bayam Segar Admin Order',
            'content' => 'Bayam segar untuk admin order tracking test',
            'price' => 10000,
            'quantity' => 100,
            'unit' => 'kg',
            'status' => 'active',
            'produce_produce_id' => $produce->produce_id,
            'user_user_id' => $farmer->user_id,
        ]);

        $customer = User::factory()->create([
            'role' => 'customer',
            'name' => 'Customer Order Test',
        ]);

        $transactionData = array_merge([
            'total_price' => 20000,
            'delivery_fee' => 5000,
            'delivery_name' => 'Customer Order Test',
            'delivery_phone' => '081234567890',
            'delivery_address' => 'Jl. Test No. 1',
            'status' => $status,
            'midtrans_order_id' => 'KS-ADMIN-' . uniqid(),
            'user_user_id' => $customer->user_id,
            'paid_at' => in_array($status, ['paid', 'shipping', 'shipped', 'delivered']) ? now() : null,
            'paid_status_at' => in_array($status, ['paid', 'shipping', 'shipped', 'delivered']) ? now() : null,
            'cart_cart_id' => 1,
        ], $overrides);

        $transaction = Transaction::create($transactionData);

        TransactionItem::create([
            'quantity' => 2,
            'unit_price' => 10000,
            'subtotal' => 20000,
            'transaction_transaction_id' => $transaction->transaction_id,
            'listing_listing_id' => $listing->listing_id,
        ]);

        return compact('farmer', 'customer', 'listing', 'transaction', 'produce');
    }

    // ==========================================
    // POSITIVE CASES
    // ==========================================

    /**
     * Test admin can access the order management page.
     */
    public function test_admin_can_view_order_list(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $setup = $this->createOrderSetup('paid');

        $this->browse(function (Browser $browser) use ($admin, $setup) {
            $browser->loginAs($admin)
                ->visit('/admin/transactions')
                ->assertPathIs('/admin/transactions')
                ->assertSee('Kelola Order 📦')
                ->assertSee($setup['transaction']->midtrans_order_id)
                ->assertSee('Customer Order Test')
                ->assertSee('Rp 20.000');
        });
    }

    /**
     * Test admin can see correct status badge for paid orders.
     */
    public function test_admin_can_see_paid_status_badge(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $setup = $this->createOrderSetup('paid');

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin)
                ->visit('/admin/transactions')
                ->assertSee('Paid');
        });
    }

    /**
     * Test admin can see correct status badge for shipping orders.
     */
    public function test_admin_can_see_shipping_status_badge(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $setup = $this->createOrderSetup('shipping');

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin)
                ->visit('/admin/transactions')
                ->assertSee('Shipping');
        });
    }

    /**
     * Test admin can see correct status badge for delivered orders.
     */
    public function test_admin_can_see_delivered_status_badge(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $setup = $this->createOrderSetup('delivered');

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin)
                ->visit('/admin/transactions')
                ->assertSee('Delivered');
        });
    }

    /**
     * Test admin can see correct status badge for cancelled orders.
     */
    public function test_admin_can_see_cancelled_status_badge(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $setup = $this->createOrderSetup('cancelled');

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin)
                ->visit('/admin/transactions')
                ->assertSee('Cancelled');
        });
    }

    /**
     * Test admin can filter orders by status using the status dropdown.
     */
    public function test_admin_can_filter_orders_by_status(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->createOrderSetup('paid');
        $this->createOrderSetup('delivered');

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin)
                ->visit('/admin/transactions')
                ->select('status', 'paid')
                ->press('Filter')
                ->pause(1000)
                ->assertQueryStringHas('status', 'paid')
                ->assertSee('Paid');
        });
    }

    /**
     * Test admin can filter orders to show only delivered status.
     */
    public function test_admin_can_filter_delivered_orders(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->createOrderSetup('delivered');

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin)
                ->visit('/admin/transactions')
                ->select('status', 'delivered')
                ->press('Filter')
                ->pause(1000)
                ->assertQueryStringHas('status', 'delivered')
                ->assertSee('Delivered');
        });
    }

    /**
     * Test admin can filter orders to show only cancelled status.
     */
    public function test_admin_can_filter_cancelled_orders(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->createOrderSetup('cancelled');

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin)
                ->visit('/admin/transactions')
                ->select('status', 'cancelled')
                ->press('Filter')
                ->pause(1000)
                ->assertQueryStringHas('status', 'cancelled')
                ->assertSee('Cancelled');
        });
    }

    /**
     * Test admin can update order status from paid → shipping (when shipping proof exists).
     */
    public function test_admin_can_update_status_paid_to_shipping(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $setup = $this->createOrderSetup('paid', [
            'shipping_proof' => 'proofs/test-proof.jpg',
            'shipping_proof_uploaded_at' => now(),
        ]);

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin)
                ->visit('/admin/transactions')
                ->assertSee('🚚 Konfirmasi Kirim')
                ->press('🚚 Konfirmasi Kirim')
                ->pause(1000)
                ->assertSee('Status order berhasil diubah');
        });
    }

    /**
     * Test admin can cancel a pending order.
     */
    public function test_admin_can_cancel_pending_order(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $setup = $this->createOrderSetup('pending');

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin)
                ->visit('/admin/transactions')
                ->assertSee('Pending')
                ->assertSee('❌ Batalkan')
                ->press('❌ Batalkan')
                ->acceptDialog()
                ->pause(1000)
                ->assertSee('Order berhasil dibatalkan.');
        });
    }

    /**
     * Test admin can cancel a paid order.
     */
    public function test_admin_can_cancel_paid_order(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $setup = $this->createOrderSetup('paid');

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin)
                ->visit('/admin/transactions')
                ->assertSee('Paid')
                ->assertSee('❌ Batalkan')
                ->press('❌ Batalkan')
                ->acceptDialog()
                ->pause(1000)
                ->assertSee('Order berhasil dibatalkan.');
        });
    }

    /**
     * Test admin can verify shipping proof and mark order as delivered.
     */
    public function test_admin_can_verify_shipping_proof(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $setup = $this->createOrderSetup('shipping', [
            'shipping_proof' => 'proofs/test-proof.jpg',
            'shipping_proof_uploaded_at' => now(),
        ]);

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin)
                ->visit('/admin/transactions')
                ->assertSee('Shipping')
                ->assertSee('✅ Verifikasi & Selesaikan')
                ->press('✅ Verifikasi & Selesaikan')
                ->pause(1000)
                ->assertSee('Bukti pengiriman terverifikasi');
        });
    }

    /**
     * Test admin can see the order creation date.
     */
    public function test_admin_can_see_order_date(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $setup = $this->createOrderSetup('paid');

        $this->browse(function (Browser $browser) use ($admin, $setup) {
            $browser->loginAs($admin)
                ->visit('/admin/transactions')
                ->assertSee($setup['transaction']->created_at->format('d M Y'));
        });
    }

    /**
     * Test admin sees empty state when no orders exist.
     */
    public function test_admin_sees_empty_order_state(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin)
                ->visit('/admin/transactions?status=nonexistent_status')
                ->assertSee('Tidak ada order.');
        });
    }

    /**
     * Test admin can see shipping proof button when proof is uploaded.
     */
    public function test_admin_can_see_shipping_proof_button(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $setup = $this->createOrderSetup('shipping', [
            'shipping_proof' => 'proofs/test-proof.jpg',
            'shipping_proof_uploaded_at' => now(),
        ]);

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin)
                ->visit('/admin/transactions')
                ->assertSee('📷 Lihat Bukti');
        });
    }

    /**
     * Test admin can see the page header and description.
     */
    public function test_admin_order_page_header(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin)
                ->visit('/admin/transactions')
                ->assertSee('Kelola Order 📦')
                ->assertSee('Kelola semua order dan verifikasi bukti pengiriman');
        });
    }

    /**
     * Test admin can see table column headers.
     */
    public function test_admin_order_table_headers(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin)
                ->visit('/admin/transactions')
                ->assertSee('ORDER ID')
                ->assertSee('Pelanggan')
                ->assertSee('Total')
                ->assertSee('Status')
                ->assertSee('Bukti Kirim')
                ->assertSee('Tanggal')
                ->assertSee('Aksi');
        });
    }

    // ==========================================
    // NEGATIVE CASES
    // ==========================================

    /**
     * Test customer cannot access admin order tracking page.
     */
    public function test_customer_cannot_access_admin_orders(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);

        $this->browse(function (Browser $browser) use ($customer) {
            $browser->loginAs($customer)
                ->visit('/admin/transactions')
                ->assertSee('403');
        });
    }

    /**
     * Test farmer cannot access admin order tracking page.
     */
    public function test_farmer_cannot_access_admin_orders(): void
    {
        $farmer = User::factory()->create([
            'role' => 'farmer',
            'verification_status' => 'verified',
        ]);

        $this->browse(function (Browser $browser) use ($farmer) {
            $browser->loginAs($farmer)
                ->visit('/admin/transactions')
                ->assertSee('403');
        });
    }

    /**
     * Test unauthenticated user cannot access admin order tracking page.
     */
    public function test_unauthenticated_cannot_access_admin_orders(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/admin/transactions')
                ->assertSee('403');
        });
    }

    /**
     * Test admin cannot cancel a shipping order (already in transit).
     */
    public function test_admin_cannot_cancel_shipping_order(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $setup = $this->createOrderSetup('shipping');

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin)
                ->visit('/admin/transactions')
                ->assertSee('Shipping')
                ->assertDontSee('❌ Batalkan');
        });
    }

    /**
     * Test admin cannot cancel a delivered order.
     */
    public function test_admin_cannot_cancel_delivered_order(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $setup = $this->createOrderSetup('delivered');

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin)
                ->visit('/admin/transactions')
                ->assertSee('Delivered')
                ->assertDontSee('❌ Batalkan');
        });
    }

    /**
     * Test admin cannot cancel an already cancelled order.
     */
    public function test_admin_cannot_cancel_already_cancelled_order(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $setup = $this->createOrderSetup('cancelled');

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin)
                ->visit('/admin/transactions')
                ->assertSee('Cancelled')
                ->assertDontSee('❌ Batalkan');
        });
    }

    /**
     * Test admin cannot advance status from paid → shipping when no shipping proof exists.
     * The "Konfirmasi Kirim" button should not appear without proof.
     */
    public function test_admin_cannot_confirm_shipping_without_proof(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $setup = $this->createOrderSetup('paid'); // No shipping_proof

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin)
                ->visit('/admin/transactions')
                ->assertSee('Paid')
                ->assertDontSee('🚚 Konfirmasi Kirim');
        });
    }

    /**
     * Test admin cannot verify shipping proof on a non-shipping order (e.g. paid).
     * The "Verifikasi & Selesaikan" button should not appear for paid orders.
     */
    public function test_admin_cannot_verify_proof_on_paid_order(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $setup = $this->createOrderSetup('paid', [
            'shipping_proof' => 'proofs/test-proof.jpg',
            'shipping_proof_uploaded_at' => now(),
        ]);

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin)
                ->visit('/admin/transactions')
                ->assertSee('Paid')
                ->assertDontSee('✅ Verifikasi & Selesaikan');
        });
    }

    /**
     * Test admin cannot verify shipping proof when no proof has been uploaded.
     * The "Verifikasi & Selesaikan" button should not appear for shipping orders without proof.
     */
    public function test_admin_cannot_verify_without_shipping_proof(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $setup = $this->createOrderSetup('shipping'); // No shipping_proof

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin)
                ->visit('/admin/transactions')
                ->assertSee('Shipping')
                ->assertDontSee('✅ Verifikasi & Selesaikan');
        });
    }

    /**
     * Test admin cannot directly submit an invalid status transition via POST.
     * Attempting paid → delivered (skipping shipping) should fail.
     */
    public function test_admin_invalid_status_transition_paid_to_delivered(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $setup = $this->createOrderSetup('paid');

        $this->browse(function (Browser $browser) use ($admin, $setup) {
            $txId = $setup['transaction']->transaction_id;

            $browser->loginAs($admin)
                ->visit("/admin/transactions/{$txId}/status?_method=POST&status=delivered")
                ->pause(500);

            // The transaction should still be paid (not delivered)
            $browser->visit('/admin/transactions')
                ->assertSee('Paid');
        });

        // Verify at database level the status was not changed
        $this->assertDatabaseHas('transactions', [
            'transaction_id' => $setup['transaction']->transaction_id,
            'status' => 'paid',
        ]);
    }

    /**
     * Test admin filter with invalid/non-existent status shows empty results.
     */
    public function test_admin_filter_with_invalid_status_shows_empty(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin)
                ->visit('/admin/transactions?status=invalid_status')
                ->assertSee('Tidak ada order.');
        });
    }

    /**
     * Test that no action buttons appear for delivered orders
     * (cannot cancel, cannot verify, cannot advance status).
     */
    public function test_admin_no_actions_for_delivered_order(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $setup = $this->createOrderSetup('delivered');

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin)
                ->visit('/admin/transactions')
                ->assertSee('Delivered')
                ->assertDontSee('❌ Batalkan')
                ->assertDontSee('🚚 Konfirmasi Kirim')
                ->assertDontSee('✅ Verifikasi & Selesaikan');
        });
    }

    /**
     * Test that no action buttons appear for cancelled orders
     * (cannot cancel again, cannot advance status).
     */
    public function test_admin_no_actions_for_cancelled_order(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $setup = $this->createOrderSetup('cancelled');

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin)
                ->visit('/admin/transactions')
                ->assertSee('Cancelled')
                ->assertDontSee('❌ Batalkan')
                ->assertDontSee('🚚 Konfirmasi Kirim')
                ->assertDontSee('✅ Verifikasi & Selesaikan');
        });
    }

    /**
     * Test admin can see cancel button only for cancellable orders (pending/paid).
     */
    public function test_admin_cancel_button_visible_for_pending(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $setup = $this->createOrderSetup('pending');

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin)
                ->visit('/admin/transactions')
                ->assertSee('Pending')
                ->assertSee('❌ Batalkan');
        });
    }

    /**
     * Test stock is restored when admin cancels a paid order.
     */
    public function test_admin_cancel_paid_order_restores_stock(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $setup = $this->createOrderSetup('paid');

        $originalQuantity = $setup['listing']->quantity;

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin)
                ->visit('/admin/transactions')
                ->press('❌ Batalkan')
                ->acceptDialog()
                ->pause(1000)
                ->assertSee('Order berhasil dibatalkan.');
        });

        // Verify stock was restored
        $setup['listing']->refresh();
        $this->assertEquals($originalQuantity + 2, $setup['listing']->quantity);
    }
}
