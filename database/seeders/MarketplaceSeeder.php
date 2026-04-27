<?php

namespace Database\Seeders;

use App\Models\Listing;
use App\Models\Produce;
use App\Models\Rating;
use App\Models\User;
use Illuminate\Database\Seeder;

class MarketplaceSeeder extends Seeder
{

    public function run(): void
    {
        /* ──────────────────────────────
         * 1. Farmer users
         * ────────────────────────────── */
        $farmerData = [
            ['name' => 'Pak Budi',      'email' => 'budi@kangsayur.id',      'city' => 'Lembang',    'farm_description' => 'Organic highland farm in Lembang, West Java.'],
            ['name' => 'Ibu Sari',      'email' => 'sari@kangsayur.id',      'city' => 'Malang',     'farm_description' => 'Family-run vegetable garden since 1998.'],
            ['name' => 'Pak Joko',      'email' => 'joko@kangsayur.id',      'city' => 'Batu',       'farm_description' => 'Specialising in premium peppers and chillies.'],
            ['name' => 'Pak Agus',      'email' => 'agus@kangsayur.id',      'city' => 'Cianjur',    'farm_description' => 'Rice-paddy and vegetable mixed farm.'],
            ['name' => 'Ibu Dewi',      'email' => 'dewi@kangsayur.id',      'city' => 'Garut',      'farm_description' => 'Hydroponic lettuce and herbs specialist.'],
            ['name' => 'Pak Hendra',    'email' => 'hendra@kangsayur.id',    'city' => 'Bandung',    'farm_description' => 'Urban rooftop farming pioneer.'],
        ];

        $farmers = [];
        foreach ($farmerData as $data) {
            $farmers[] = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name'             => $data['name'],
                    'password'         => bcrypt('password'),
                    'role'             => 'farmer',
                    'city'             => $data['city'],
                    'farm_description' => $data['farm_description'],
                ]
            );
        }

        /* ──────────────────────────────
         * 2. Customer users
         * ────────────────────────────── */
        $customer = User::firstOrCreate(
            ['email' => 'customer@kangsayur.id'],
            [
                'name'     => 'Rina Customer',
                'password' => bcrypt('password'),
                'role'     => 'customer',
                'city'     => 'Bandung',
            ]
        );

        $customer2 = User::firstOrCreate(
            ['email' => 'customer2@kangsayur.id'],
            [
                'name'     => 'Dian Pembeli',
                'password' => bcrypt('password'),
                'role'     => 'customer',
                'city'     => 'Jakarta',
            ]
        );

        $customer3 = User::firstOrCreate(
            ['email' => 'customer3@kangsayur.id'],
            [
                'name'     => 'Andi Pratama',
                'password' => bcrypt('password'),
                'role'     => 'customer',
                'city'     => 'Surabaya',
            ]
        );

        /* ──────────────────────────────
         * 3. Produce
         * ────────────────────────────── */
        $produceData = [
            // Sayuran
            ['name' => 'Wortel',        'category' => 'Sayuran',        'emoji' => '🥕'],
            ['name' => 'Tomat',         'category' => 'Sayuran',        'emoji' => '🍅'],
            ['name' => 'Jagung',        'category' => 'Sayuran',        'emoji' => '🌽'],
            ['name' => 'Terong',        'category' => 'Sayuran',        'emoji' => '🍆'],
            ['name' => 'Timun',         'category' => 'Sayuran',        'emoji' => '🥒'],
            ['name' => 'Kentang',       'category' => 'Sayuran',        'emoji' => '🥔'],
            // Sayuran Hijau
            ['name' => 'Sawi',          'category' => 'Sayuran Hijau',  'emoji' => '🥬'],
            ['name' => 'Brokoli',       'category' => 'Sayuran Hijau',  'emoji' => '🥦'],
            ['name' => 'Bayam',         'category' => 'Sayuran Hijau',  'emoji' => '🌿'],
            ['name' => 'Kangkung',      'category' => 'Sayuran Hijau',  'emoji' => '🌱'],
            // Bumbu
            ['name' => 'Bawang Merah',  'category' => 'Bumbu',          'emoji' => '🧅'],
            ['name' => 'Cabai Merah',   'category' => 'Bumbu',          'emoji' => '🌶️'],
            ['name' => 'Bawang Putih',  'category' => 'Bumbu',          'emoji' => '🧄'],
            ['name' => 'Jahe',          'category' => 'Bumbu',          'emoji' => '🫚'],
            // Buah
            ['name' => 'Paprika',       'category' => 'Buah',           'emoji' => '🫑'],
        ];

        $produces = [];
        foreach ($produceData as $data) {
            $produces[] = Produce::firstOrCreate(
                ['name' => $data['name']],
                $data
            );
        }

        /* ──────────────────────────────
         * 4. Listings (20 items)
         * ────────────────────────────── */
        $listingData = [
            // Wortel (produce index 0)
            ['title' => 'Wortel Segar Organik',        'content' => 'Wortel segar langsung dari kebun organik Pak Budi di Lembang. Ditanam tanpa pestisida, dipanen pagi hari untuk kesegaran maksimal. Cocok untuk jus, sup, atau masakan sehari-hari.',                                               'price' => 15000,  'quantity' => 50,  'unit' => 'kg',    'farmer_i' => 0, 'produce_i' => 0],
            ['title' => 'Wortel Baby Premium',          'content' => 'Wortel baby berukuran mini, manis dan renyah. Sempurna untuk snacking sehat atau salad premium.',                                                                                                                                  'price' => 22000,  'quantity' => 30,  'unit' => 'kg',    'farmer_i' => 3, 'produce_i' => 0],

            // Tomat (1)
            ['title' => 'Tomat Merah Premium',          'content' => 'Tomat merah matang sempurna, manis dan berair. Ideal untuk saus pasta, sambal, atau dimakan langsung.',                                                                                                                            'price' => 12000,  'quantity' => 80,  'unit' => 'kg',    'farmer_i' => 1, 'produce_i' => 1],
            ['title' => 'Tomat Cherry Organik',         'content' => 'Tomat cherry organik super manis. Cocok untuk salad, garnish, atau camilan sehat.',                                                                                                                                               'price' => 35000,  'quantity' => 25,  'unit' => 'kg',    'farmer_i' => 5, 'produce_i' => 1],

            // Jagung (2)
            ['title' => 'Jagung Manis Muda',            'content' => 'Jagung manis baru dipetik pagi ini, sempurna untuk direbus atau dibakar. Bulir besar dan penuh rasa.',                                                                                                                             'price' => 8000,   'quantity' => 100, 'unit' => 'ikat',  'farmer_i' => 2, 'produce_i' => 2],

            // Terong (3)
            ['title' => 'Terong Ungu Jumbo',            'content' => 'Terong ungu ukuran besar dari Malang, cocok untuk digoreng tepung, dibakar, atau dibuat terong balado.',                                                                                                                           'price' => 10000,  'quantity' => 60,  'unit' => 'kg',    'farmer_i' => 1, 'produce_i' => 3],

            // Timun (4)
            ['title' => 'Timun Segar Lokal',            'content' => 'Timun lokal segar dan renyah, ideal untuk lalapan, acar, atau infused water.',                                                                                                                                                    'price' => 7000,   'quantity' => 70,  'unit' => 'kg',    'farmer_i' => 3, 'produce_i' => 4],

            // Kentang (5)
            ['title' => 'Kentang Dieng Grade A',        'content' => 'Kentang dari dataran tinggi Dieng, tekstur lembut saat dimasak. Sempurna untuk kentang goreng, perkedel, atau sup.',                                                                                                               'price' => 18000,  'quantity' => 45,  'unit' => 'kg',    'farmer_i' => 0, 'produce_i' => 5],

            // Sawi (6)
            ['title' => 'Sawi Hijau Segar',             'content' => 'Sawi hijau renyah dan segar untuk sayur bening atau tumis. Dipetik langsung dari kebun.',                                                                                                                                         'price' => 6000,   'quantity' => 90,  'unit' => 'ikat',  'farmer_i' => 0, 'produce_i' => 6],

            // Brokoli (7)
            ['title' => 'Brokoli Organik Lembang',      'content' => 'Brokoli organik segar dari kebun dataran tinggi Lembang. Kaya nutrisi dan serat.',                                                                                                                                                'price' => 28000,  'quantity' => 35,  'unit' => 'kg',    'farmer_i' => 0, 'produce_i' => 7],

            // Bayam (8)
            ['title' => 'Bayam Hijau Segar',            'content' => 'Bayam hijau segar, daun tebal dan tidak layu. Cocok untuk sayur bayam, smoothie, atau tumisan.',                                                                                                                                   'price' => 5000,   'quantity' => 100, 'unit' => 'ikat',  'farmer_i' => 4, 'produce_i' => 8],

            // Kangkung (9)
            ['title' => 'Kangkung Hidroponik',          'content' => 'Kangkung hidroponik Ibu Dewi, lebih bersih dan tahan lama. Cocok untuk tumis kangkung atau pecel.',                                                                                                                                'price' => 6000,   'quantity' => 80,  'unit' => 'ikat',  'farmer_i' => 4, 'produce_i' => 9],

            // Bawang Merah (10)
            ['title' => 'Bawang Merah Brebes Super',    'content' => 'Bawang merah asli Brebes, aroma tajam dan rasa khas. Bumbu dapur wajib untuk masakan Indonesia.',                                                                                                                                  'price' => 32000,  'quantity' => 40,  'unit' => 'kg',    'farmer_i' => 2, 'produce_i' => 10],

            // Cabai Merah (11)
            ['title' => 'Cabai Merah Keriting',         'content' => 'Cabai merah keriting pedas mantap dari Batu, Malang. Warna merah cerah, cocok untuk sambal atau masakan pedas.',                                                                                                                    'price' => 45000,  'quantity' => 20,  'unit' => 'kg',    'farmer_i' => 2, 'produce_i' => 11],
            ['title' => 'Cabai Rawit Super Pedas',      'content' => 'Cabai rawit setan level pedas ekstrem. Hanya untuk pecinta pedas sejati!',                                                                                                                                                         'price' => 55000,  'quantity' => 15,  'unit' => 'kg',    'farmer_i' => 2, 'produce_i' => 11],

            // Bawang Putih (12)
            ['title' => 'Bawang Putih Lokal',           'content' => 'Bawang putih lokal premium, aroma kuat dan rasa gurih. Bumbu utama untuk berbagai masakan nusantara.',                                                                                                                              'price' => 38000,  'quantity' => 30,  'unit' => 'kg',    'farmer_i' => 3, 'produce_i' => 12],

            // Jahe (13)
            ['title' => 'Jahe Emprit Segar',            'content' => 'Jahe emprit segar dari Garut, tinggi minyak atsiri. Cocok untuk wedang jahe, jamu, atau bumbu masak.',                                                                                                                              'price' => 25000,  'quantity' => 40,  'unit' => 'kg',    'farmer_i' => 4, 'produce_i' => 13],

            // Paprika (14)
            ['title' => 'Paprika Hijau Crunchy',        'content' => 'Paprika hijau segar dengan rasa renyah, ideal untuk salad, tumisan, atau pizza topping.',                                                                                                                                           'price' => 25000,  'quantity' => 25,  'unit' => 'kg',    'farmer_i' => 5, 'produce_i' => 14],
            ['title' => 'Paprika Merah & Kuning Mix',   'content' => 'Paket paprika merah dan kuning, manis dan berwarna cerah. Perfect untuk stir-fry dan salad colorful.',                                                                                                                              'price' => 30000,  'quantity' => 20,  'unit' => 'kg',    'farmer_i' => 5, 'produce_i' => 14],
        ];

        $listings = [];
        foreach ($listingData as $data) {
            $listings[] = Listing::firstOrCreate(
                ['title' => $data['title']],
                [
                    'title'              => $data['title'],
                    'content'            => $data['content'],
                    'price'              => $data['price'],
                    'quantity'           => $data['quantity'],
                    'unit'               => $data['unit'],
                    'status'             => 'active',
                    'user_user_id'       => $farmers[$data['farmer_i']]->user_id,
                    'produce_produce_id' => $produces[$data['produce_i']]->produce_id,
                ]
            );
        }

        /* ──────────────────────────────
         * 5. Ratings
         * ────────────────────────────── */
        $reviewTexts = [
            'Produk segar dan berkualitas! Pasti pesan lagi.',
            'Pengiriman cepat, sayuran masih segar sampai rumah.',
            'Harga terjangkau untuk kualitas sebagus ini.',
            'Sangat memuaskan, recommended banget!',
            'Sayurannya bersih dan segar, top!',
            'Packaging rapi, produk sesuai deskripsi.',
            'Bumbu dapur wajib, selalu fresh.',
            'Anak-anak suka banget, sayurannya manis.',
        ];

        $customers = [$customer, $customer2, $customer3];

        foreach ($listings as $index => $listing) {
            // Each listing gets 1-3 ratings
            $numRatings = ($index % 3) + 1;

            for ($r = 0; $r < $numRatings; $r++) {
                if ($r >= count($customers)) break;

                Rating::firstOrCreate(
                    [
                        'listing_listing_id' => $listing->listing_id,
                        'user_user_id'       => $customers[$r]->user_id,
                    ],
                    [
                        'score'   => rand(3, 5),
                        'comment' => $reviewTexts[($index + $r) % count($reviewTexts)],
                    ]
                );
            }
        }
    }
}
