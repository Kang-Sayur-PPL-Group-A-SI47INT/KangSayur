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
            ['title' => 'Wortel Segar Organik', 'description' => 'Wortel segar langsung dari kebun organik Pak Budi di Lembang.', 'price' => 15000, 'unit' => 'kg', 'farmer_index' => 0, 'produce_index' => 0],
            ['title' => 'Tomat Merah Premium', 'description' => 'Tomat merah matang sempurna, manis dan berair.', 'price' => 12000, 'unit' => 'kg', 'farmer_index' => 1, 'produce_index' => 1],
            ['title' => 'Jagung Manis Muda', 'description' => 'Jagung manis baru dipetik pagi ini, sempurna untuk direbus.', 'price' => 8000, 'unit' => 'ikat', 'farmer_index' => 2, 'produce_index' => 2],
            ['title' => 'Sawi Hijau Segar', 'description' => 'Sawi hijau renyah dan segar untuk sayur bening atau tumis.', 'price' => 6000, 'unit' => 'ikat', 'farmer_index' => 0, 'produce_index' => 3],
            ['title' => 'Terong Ungu Jumbo', 'description' => 'Terong ungu ukuran besar, cocok untuk digoreng atau dibakar.', 'price' => 10000, 'unit' => 'kg', 'farmer_index' => 1, 'produce_index' => 4],
            ['title' => 'Paprika Hijau Crunchy', 'description' => 'Paprika hijau segar dengan rasa renyah, ideal untuk salad.', 'price' => 25000, 'unit' => 'kg', 'farmer_index' => 2, 'produce_index' => 5],
        ];

        $listings = [];
        foreach ($listingData as $data) {
            $listings[] = Listing::firstOrCreate(
                ['title' => $data['title']],
                [
                    'title' => $data['title'],
                    'description' => $data['description'],
                    'price' => $data['price'],
                    'unit' => $data['unit'],
                    'status' => 'active',
                    'user_user_id' => $farmers[$data['farmer_index']]->id,
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
            ]
        );

        $customer2 = User::firstOrCreate(
            ['email' => 'customer2@kangsayur.id'],
            [
                'name' => 'Dian Pembeli',
                'password' => bcrypt('password'),
            ]
        );

        foreach ($listings as $index => $listing) {
            // Not every listing gets a rating — skip index 3
            if ($index === 3) continue;

            Rating::firstOrCreate(
                [
                    'listing_listing_id' => $listing->listing_id,
                    'user_user_id' => $customer->id,
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
                        'user_user_id' => $customer2->id,
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
