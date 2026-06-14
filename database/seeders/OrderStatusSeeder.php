<?php

namespace Database\Seeders;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Listing;
use App\Models\Produce;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\User;
use Illuminate\Database\Seeder;

class OrderStatusSeeder extends Seeder
{
    /**
     * Seeds transactions with various statuses for Dusk testing.
     * Does NOT go through admin — directly sets status in DB.
     */
    public function run(): void
    {
        // Get or create a customer
        $customer = User::where('email', 'dusk_customer@kangsayur.com')->first()
            ?? User::create([
                'name'      => 'Dusk Customer',
                'email'     => 'dusk_customer@kangsayur.com',
                'password'  => bcrypt('password'),
                'role'      => 'customer',
                'address'   => 'Jl. Test No. 1, Bandung',
                'city'      => 'Bandung',
                'latitude'  => -6.9175,
                'longitude' => 107.6191,
            ]);

        // Get or create a farmer
        $farmer = User::where('email', 'dusk_farmer@kangsayur.com')->first()
            ?? User::create([
                'name'        => 'Dusk Farmer',
                'email'       => 'dusk_farmer@kangsayur.com',
                'password'    => bcrypt('password'),
                'role'        => 'farmer',
                'address'     => 'Desa Test, Bandung',
                'city'        => 'Bandung',
                'latitude'    => -7.0474,
                'longitude'   => 107.4911,
            ]);

        // Get or create a produce category
        $produce = Produce::firstOrCreate(['name' => 'Vegetables']);

        // Get or create a listing owned by the farmer
        $listing = Listing::firstOrCreate(
            ['title' => 'Dusk Test Veggie', 'user_user_id' => $farmer->user_id],
            [
                'content'             => 'Test listing for Dusk',
                'price'               => 20000,
                'quantity'            => 100,
                'unit'                => 'kg',
                'status'              => 'active',
                'produce_produce_id'  => $produce->produce_id,
                'user_user_id'        => $farmer->user_id,
            ]
        );

        // Statuses to seed
        $statuses = ['pending', 'paid', 'shipping', 'delivered', 'cancelled'];

        foreach ($statuses as $status) {
            // Create a dedicated cart for each transaction
            $cart = Cart::create(['user_user_id' => $customer->user_id]);

            CartItem::create([
                'quantity'           => 2,
                'cart_cart_id'       => $cart->cart_id,
                'listing_listing_id' => $listing->listing_id,
            ]);

            // Create transaction directly with status 
            $transaction = Transaction::create([
                'total_price'        => 40000,
                'delivery_fee'       => 5000,
                'delivery_name'      => 'Dusk Receiver',
                'delivery_phone'     => '081234567890',
                'delivery_address'   => 'Jl. Test No. 1, Bandung',
                'status'             => $status,   // <-- set directly
                'midtrans_order_id'  => 'DUSK-' . strtoupper($status) . '-001',
                'paid_at'            => in_array($status, ['paid', 'shipping', 'delivered']) ? now() : null,
                'user_user_id'       => $customer->user_id,
                'cart_cart_id'       => $cart->cart_id,
            ]);

            TransactionItem::create([
                'quantity'                    => 2,
                'unit_price'                  => 20000,
                'subtotal'                    => 40000,
                'transaction_transaction_id'  => $transaction->transaction_id,
                'listing_listing_id'          => $listing->listing_id,
            ]);

            $this->command->info("Created transaction with status: {$status} (ID: {$transaction->transaction_id})");
        }
    }
}
