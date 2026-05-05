<?php

namespace Database\Seeders;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Listing;
use App\Models\Produce;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class CartSeeder extends Seeder
{
    /**
     * Seed the cart with dummy data for testing.
     * Creates a customer user (if needed), some dummy listings, and adds them to cart.
     */
    public function run(): void
    {
        // Get or create a customer user
        $customer = User::where('role', 'customer')->first();
        if (!$customer) {
            $customer = User::create([
                'name' => 'Test Customer',
                'email' => 'customer@kangsayur.com',
                'password' => bcrypt('password'),
                'role' => 'customer',
                'address' => 'Jl. Merdeka No. 10, Bandung',
                'city' => 'Bandung',
                'latitude' => -6.9175,
                'longitude' => 107.6191,
            ]);
        }

        // Get or create a farmer user
        $farmer = User::where('role', 'farmer')->first();
        if (!$farmer) {
            $farmer = User::create([
                'name' => 'Pak Harto',
                'email' => 'farmer@kangsayur.com',
                'password' => bcrypt('password'),
                'role' => 'farmer',
                'address' => 'Desa Ciwidey, Bandung Selatan',
                'city' => 'Bandung',
                'latitude' => -7.0474,
                'longitude' => 107.4911,
                'farm_description' => 'Organic vegetable farm in Ciwidey highlands',
            ]);
        }

        // Create produce categories if they don't exist
        if (Schema::hasTable('produces')) {
            $vegetables = Produce::firstOrCreate(['name' => 'Vegetables'], [
                'name' => 'Vegetables',
            ]);
            $fruits = Produce::firstOrCreate(['name' => 'Fruits'], [
                'name' => 'Fruits',
            ]);
            $greens = Produce::firstOrCreate(['name' => 'Greens'], [
                'name' => 'Greens',
            ]);
        }

        // Create dummy listings if table exists
        if (Schema::hasTable('listings')) {
            $dummyListings = [
                [
                    'title' => 'Organic Red Spinach',
                    'content' => 'Freshly harvested organic red spinach from Ciwidey highlands.',
                    'price' => 15000,
                    'quantity' => 50,
                    'unit' => 'kg',
                    'status' => 'active',
                    'produce_produce_id' => $greens->produce_id ?? null,
                    'user_user_id' => $farmer->user_id,
                ],
                [
                    'title' => 'Sweet Carrots Bundle',
                    'content' => 'Premium quality carrots, naturally grown.',
                    'price' => 22000,
                    'quantity' => 30,
                    'unit' => 'kg',
                    'status' => 'active',
                    'produce_produce_id' => $vegetables->produce_id ?? null,
                    'user_user_id' => $farmer->user_id,
                ],
                [
                    'title' => 'Fresh Broccoli',
                    'content' => 'Crisp and green broccoli from the highlands.',
                    'price' => 28000,
                    'quantity' => 25,
                    'unit' => 'kg',
                    'status' => 'active',
                    'produce_produce_id' => $vegetables->produce_id ?? null,
                    'user_user_id' => $farmer->user_id,
                ],
            ];

            $listingIds = [];
            foreach ($dummyListings as $data) {
                $listing = Listing::create($data);
                $listingIds[] = $listing->listing_id;
            }

            // Create cart and add items
            $cart = $customer->getOrCreateCart();

            // Clear existing items
            $cart->items()->delete();

            // Add items with different quantities
            CartItem::create([
                'quantity' => 2,
                'cart_cart_id' => $cart->cart_id,
                'listing_listing_id' => $listingIds[0],
            ]);

            CartItem::create([
                'quantity' => 1,
                'cart_cart_id' => $cart->cart_id,
                'listing_listing_id' => $listingIds[1],
            ]);

            CartItem::create([
                'quantity' => 3,
                'cart_cart_id' => $cart->cart_id,
                'listing_listing_id' => $listingIds[2],
            ]);

            $this->command->info('Cart seeded with 3 dummy items for customer@kangsayur.com');
        } else {
            $this->command->warn('Listings table does not exist. Run migrations first.');
        }
    }
}
