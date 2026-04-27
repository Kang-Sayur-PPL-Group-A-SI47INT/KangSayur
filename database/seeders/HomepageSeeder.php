<?php

namespace Database\Seeders;

use App\Models\Listing;
use App\Models\Produce;
use App\Models\Rating;
use App\Models\User;
use Illuminate\Database\Seeder;

class HomepageSeeder extends Seeder
{
    /**
     * Seed demo data for the homepage featured produce section.
     */
    public function run(): void
    {
        // Create farmer users
        $farmers = [];
        $farmerData = [
            ['name' => 'Pak Budi', 'email' => 'budi@kangsayur.id'],
            ['name' => 'Ibu Sari', 'email' => 'sari@kangsayur.id'],
            ['name' => 'Pak Joko', 'email' => 'joko@kangsayur.id'],
        ];

        foreach ($farmerData as $data) {
            $farmers[] = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => bcrypt('password'),
                    'role' => 'farmer',
                ]
            );
        }

        // Create produce categories
        $produceData = [
            ['name' => 'Wortel', 'category' => 'Sayuran', 'emoji' => '🥕'],
            ['name' => 'Tomat', 'category' => 'Sayuran', 'emoji' => '🍅'],
            ['name' => 'Jagung', 'category' => 'Sayuran', 'emoji' => '🌽'],
            ['name' => 'Sawi', 'category' => 'Sayuran Hijau', 'emoji' => '🥬'],
            ['name' => 'Terong', 'category' => 'Sayuran', 'emoji' => '🍆'],
            ['name' => 'Paprika', 'category' => 'Sayuran', 'emoji' => '🫑'],
            ['name' => 'Bawang Merah', 'category' => 'Bumbu', 'emoji' => '🧅'],
            ['name' => 'Timun', 'category' => 'Sayuran', 'emoji' => '🥒'],
            ['name' => 'Cabai Merah', 'category' => 'Bumbu', 'emoji' => '🌶️'],
            ['name' => 'Brokoli', 'category' => 'Sayuran Hijau', 'emoji' => '🥦'],
        ];

        $produces = [];
        foreach ($produceData as $data) {
            $produces[] = Produce::firstOrCreate(
                ['name' => $data['name']],
                $data
            );
        }

        // Create listings
        $listingData = [
            ['title' => 'Wortel Segar Organik', 'content' => 'Wortel segar langsung dari kebun organik Pak Budi di Lembang.', 'price' => 15000, 'quantity' => 50, 'unit' => 'kg', 'farmer_index' => 0, 'produce_index' => 0],
            ['title' => 'Tomat Merah Premium', 'content' => 'Tomat merah matang sempurna, manis dan berair.', 'price' => 12000, 'quantity' => 8, 'unit' => 'kg', 'farmer_index' => 1, 'produce_index' => 1],
            ['title' => 'Jagung Manis Muda', 'content' => 'Jagung manis baru dipetik pagi ini, sempurna untuk direbus.', 'price' => 8000, 'quantity' => 30, 'unit' => 'ikat', 'farmer_index' => 2, 'produce_index' => 2],
            ['title' => 'Sawi Hijau Segar', 'content' => 'Sawi hijau renyah dan segar untuk sayur bening atau tumis.', 'price' => 6000, 'quantity' => 5, 'unit' => 'ikat', 'farmer_index' => 0, 'produce_index' => 3],
            ['title' => 'Terong Ungu Jumbo', 'content' => 'Terong ungu ukuran besar, cocok untuk digoreng atau dibakar.', 'price' => 10000, 'quantity' => 25, 'unit' => 'kg', 'farmer_index' => 1, 'produce_index' => 4],
            ['title' => 'Paprika Hijau Crunchy', 'content' => 'Paprika hijau segar dengan rasa renyah, ideal untuk salad.', 'price' => 25000, 'quantity' => 15, 'unit' => 'kg', 'farmer_index' => 2, 'produce_index' => 5],
        ];

        $listings = [];
        foreach ($listingData as $data) {
            $listings[] = Listing::firstOrCreate(
                ['title' => $data['title']],
                [
                    'title' => $data['title'],
                    'content' => $data['content'],
                    'price' => $data['price'],
                    'quantity' => $data['quantity'],
                    'unit' => $data['unit'],
                    'status' => 'active',
                    'user_user_id' => $farmers[$data['farmer_index']]->user_id,
                    'produce_produce_id' => $produces[$data['produce_index']]->produce_id,
                ]
            );
        }

        // Create some ratings for listings
        $customer = User::firstOrCreate(
            ['email' => 'customer@kangsayur.id'],
            [
                'name' => 'Rina Customer',
                'password' => bcrypt('password'),
                'role' => 'customer',
            ]
        );

        $customer2 = User::firstOrCreate(
            ['email' => 'customer2@kangsayur.id'],
            [
                'name' => 'Dian Pembeli',
                'password' => bcrypt('password'),
                'role' => 'customer',
            ]
        );

        foreach ($listings as $index => $listing) {
            // Not every listing gets a rating — skip index 3
            if ($index === 3) continue;

            Rating::firstOrCreate(
                [
                    'listing_listing_id' => $listing->listing_id,
                    'user_user_id' => $customer->user_id,
                ],
                [
                    'score' => rand(4, 5),
                    'comment' => 'Produk segar dan berkualitas!',
                ]
            );

            if ($index % 2 === 0) {
                Rating::firstOrCreate(
                    [
                        'listing_listing_id' => $listing->listing_id,
                        'user_user_id' => $customer2->user_id,
                    ],
                    [
                        'score' => rand(3, 5),
                        'comment' => 'Pengiriman cepat, recommended!',
                    ]
                );
            }
        }
    }
}
