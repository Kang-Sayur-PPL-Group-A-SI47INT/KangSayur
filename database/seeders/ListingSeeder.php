<?php

namespace Database\Seeders;

use App\Models\Listing;
use App\Models\Produce;
use App\Models\Rating;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ListingSeeder extends Seeder
{
    /**
     * Seed dummy listings with farmer users and ratings.
     */
    public function run(): void
    {
        // --- 1. Create Farmer Users ---
        $farmers = [
            [
                'name'             => 'Pak Budi',
                'email'            => 'budi@farmer.test',
                'password'         => Hash::make('password'),
                'role'             => 'farmer',
                'city'             => 'Bandung',
                'address'          => 'Jl. Cibaduyut No.12, Bandung',
                'latitude'         => -6.94970000,
                'longitude'        => 107.57630000,
                'farm_description' => 'Pertanian organik keluarga sejak 2005 di dataran tinggi Bandung.',
            ],
            [
                'name'             => 'Bu Siti',
                'email'            => 'siti@farmer.test',
                'password'         => Hash::make('password'),
                'role'             => 'farmer',
                'city'             => 'Malang',
                'address'          => 'Desa Punten, Kec. Bumiaji, Malang',
                'latitude'         => -7.87890000,
                'longitude'        => 112.52700000,
                'farm_description' => 'Spesialis sayuran hijau segar dari lereng Gunung Arjuno.',
            ],
            [
                'name'             => 'Pak Agus',
                'email'            => 'agus@farmer.test',
                'password'         => Hash::make('password'),
                'role'             => 'farmer',
                'city'             => 'Bogor',
                'address'          => 'Kp. Ciburial, Cisarua, Bogor',
                'latitude'         => -6.68590000,
                'longitude'        => 106.93070000,
                'farm_description' => 'Kebun buah tropis 2 hektar di kawasan Puncak, Bogor.',
            ],
            [
                'name'             => 'Bu Dewi',
                'email'            => 'dewi@farmer.test',
                'password'         => Hash::make('password'),
                'role'             => 'farmer',
                'city'             => 'Garut',
                'address'          => 'Desa Samarang, Garut',
                'latitude'         => -7.20620000,
                'longitude'        => 107.90870000,
                'farm_description' => 'Petani muda yang fokus pada pertanian berkelanjutan dan bumbu organik.',
            ],
            [
                'name'             => 'Pak Hendra',
                'email'            => 'hendra@farmer.test',
                'password'         => Hash::make('password'),
                'role'             => 'farmer',
                'city'             => 'Lembang',
                'address'          => 'Jl. Grand Hotel No.45, Lembang',
                'latitude'         => -6.81170000,
                'longitude'        => 107.61730000,
                'farm_description' => 'Greenhouse modern dengan sistem hidroponik untuk sayuran premium.',
            ],
        ];

        $farmerModels = [];
        foreach ($farmers as $data) {
            $farmerModels[] = User::firstOrCreate(
                ['email' => $data['email']],
                $data
            );
        }

        // --- 2. Create a Customer for ratings ---
        $customer = User::firstOrCreate(
            ['email' => 'customer@test.test'],
            [
                'name'     => 'Rina',
                'email'    => 'customer@test.test',
                'password' => Hash::make('password'),
                'role'     => 'customer',
                'city'     => 'Jakarta',
            ]
        );

        // --- 3. Ensure produces exist ---
        $this->callSilent(ProduceSeeder::class);
        $produces = Produce::all();

        if ($produces->isEmpty()) {
            $this->command->warn('No produces found — skipping listings.');
            return;
        }

        // --- 4. Listing templates ---
        $templates = [
            // Sayuran
            'Wortel'         => ['title' => 'Wortel Segar Organik',           'content' => 'Wortel organik manis dari kebun dataran tinggi. Dipanen pagi ini, cocok untuk jus dan masakan sehari-hari.',                        'price' => 12000,  'unit' => 'kg',    'qty' => 150],
            'Tomat'          => ['title' => 'Tomat Merah Matang Pohon',       'content' => 'Tomat merah ranum yang matang di pohon, rasa asam manis sempurna untuk sambal dan tumisan.',                                        'price' => 15000,  'unit' => 'kg',    'qty' => 200],
            'Jagung'         => ['title' => 'Jagung Manis Super',             'content' => 'Jagung manis varietas hibrida, bisa dimakan langsung atau diolah. Tekstur renyah, rasa manis alami.',                                'price' => 8000,   'unit' => 'kg',    'qty' => 300],
            'Terong'         => ['title' => 'Terong Ungu Segar',              'content' => 'Terong ungu muda dengan daging tebal, sempurna untuk terong balado atau terong bakar.',                                              'price' => 10000,  'unit' => 'kg',    'qty' => 120],
            'Timun'          => ['title' => 'Timun Segar Renyah',             'content' => 'Timun renyah dan segar, ideal untuk lalap, acar, dan campuran salad.',                                                               'price' => 7000,   'unit' => 'kg',    'qty' => 250],
            'Paprika'        => ['title' => 'Paprika Merah Hidroponik',       'content' => 'Paprika merah manis berkualitas premium dari greenhouse hidroponik. Cocok untuk tumisan dan salad.',                                  'price' => 45000,  'unit' => 'kg',    'qty' => 40],
            'Kentang'        => ['title' => 'Kentang Dieng Premium',          'content' => 'Kentang kuning pulen dari Dataran Tinggi Dieng. Cocok untuk sup, perkedel, atau french fries.',                                      'price' => 18000,  'unit' => 'kg',    'qty' => 500],
            'Labu Siam'      => ['title' => 'Labu Siam Muda',                 'content' => 'Labu siam muda pilihan yang empuk dan tidak pahit. Pas untuk sayur bening dan tumis.',                                               'price' => 6000,   'unit' => 'kg',    'qty' => 180],
            'Kacang Panjang'  => ['title' => 'Kacang Panjang Organik',        'content' => 'Kacang panjang segar tanpa pestisida, dipanen langsung dari kebun setiap pagi.',                                                     'price' => 10000,  'unit' => 'ikat',  'qty' => 100],

            // Sayuran Hijau
            'Sawi'           => ['title' => 'Sawi Hijau Segar',               'content' => 'Sawi hijau daun lebar yang renyah dan segar. Cocok untuk tumis, bakso, dan mie ayam.',                                               'price' => 8000,   'unit' => 'ikat',  'qty' => 200],
            'Bayam'          => ['title' => 'Bayam Organik Segar',            'content' => 'Bayam hijau organik yang baru dipetik. Kaya zat besi, sempurna untuk sayur bening dan smoothie hijau.',                               'price' => 5000,   'unit' => 'ikat',  'qty' => 300],
            'Kangkung'       => ['title' => 'Kangkung Darat Premium',         'content' => 'Kangkung darat segar dengan batang renyah. Favorit untuk cah kangkung dan plecing kangkung.',                                       'price' => 4000,   'unit' => 'ikat',  'qty' => 350],
            'Brokoli'        => ['title' => 'Brokoli Segar Organik',          'content' => 'Brokoli organik hijau tua dengan kuntum rapat. Kaya nutrisi, cocok dikukus atau ditumis.',                                           'price' => 25000,  'unit' => 'kg',    'qty' => 80],
            'Selada'         => ['title' => 'Selada Keriting Hidroponik',     'content' => 'Selada keriting segar dari greenhouse hidroponik. Bebas pestisida, siap makan untuk salad premium.',                                 'price' => 15000,  'unit' => 'pcs',   'qty' => 120],
            'Seledri'        => ['title' => 'Seledri Segar Wangi',            'content' => 'Seledri segar dengan aroma kuat, sempurna sebagai garnish sup dan soto.',                                                            'price' => 6000,   'unit' => 'ikat',  'qty' => 150],

            // Bumbu
            'Bawang Merah'   => ['title' => 'Bawang Merah Brebes',            'content' => 'Bawang merah Brebes pilihan, umbi besar dan aroma tajam. Bumbu dasar untuk segala masakan Indonesia.',                               'price' => 35000,  'unit' => 'kg',    'qty' => 400],
            'Bawang Putih'   => ['title' => 'Bawang Putih Lokal',             'content' => 'Bawang putih lokal dengan aroma lebih kuat dari impor. Siung besar, mudah dikupas.',                                                'price' => 40000,  'unit' => 'kg',    'qty' => 250],
            'Cabai Merah'    => ['title' => 'Cabai Merah Keriting',            'content' => 'Cabai merah keriting segar dengan tingkat kepedasan sedang. Warna merah cerah untuk sambal dan masakan.',                           'price' => 50000,  'unit' => 'kg',    'qty' => 100],
            'Cabai Rawit'    => ['title' => 'Cabai Rawit Super Pedas',         'content' => 'Cabai rawit hijau-merah dengan kepedasan tinggi. Panen langsung dari kebun untuk kesegaran maksimal.',                              'price' => 55000,  'unit' => 'kg',    'qty' => 80],
            'Jahe'           => ['title' => 'Jahe Gajah Segar',               'content' => 'Jahe gajah berkualitas tinggi, rimpang besar dan bersih. Cocok untuk wedang, jamu, dan bumbu masak.',                                'price' => 30000,  'unit' => 'kg',    'qty' => 150],
            'Kunyit'         => ['title' => 'Kunyit Segar Organik',           'content' => 'Kunyit organik dengan warna kuning pekat. Ideal untuk jamu kunyit asam dan bumbu kari.',                                            'price' => 20000,  'unit' => 'kg',    'qty' => 120],
            'Lengkuas'       => ['title' => 'Lengkuas Segar Wangi',           'content' => 'Lengkuas segar dengan aroma khas yang kuat. Bumbu wajib untuk rendang, sayur labu, dan opor.',                                      'price' => 15000,  'unit' => 'kg',    'qty' => 100],
            'Daun Bawang'    => ['title' => 'Daun Bawang Segar',              'content' => 'Daun bawang hijau segar untuk garnish dan pelengkap masakan. Aroma sedap.',                                                         'price' => 5000,   'unit' => 'ikat',  'qty' => 200],

            // Buah
            'Pisang'         => ['title' => 'Pisang Cavendish Lokal',          'content' => 'Pisang Cavendish manis dan lembut, panen matang pohon. Cocok untuk makan langsung atau olahan kue.',                                'price' => 20000,  'unit' => 'sisir', 'qty' => 200],
            'Mangga'         => ['title' => 'Mangga Harum Manis',              'content' => 'Mangga harum manis Indramayu, daging tebal kuning cerah. Rasa manis legit khas tropis.',                                            'price' => 25000,  'unit' => 'kg',    'qty' => 150],
            'Jeruk'          => ['title' => 'Jeruk Medan Manis',               'content' => 'Jeruk Medan dengan kulit tipis dan rasa manis segar. Sempurna untuk jus dan camilan sehat.',                                       'price' => 22000,  'unit' => 'kg',    'qty' => 180],
            'Pepaya'         => ['title' => 'Pepaya California',               'content' => 'Pepaya California matang sempurna, daging merah-oranye tebal. Kaya enzim papain untuk pencernaan.',                                'price' => 12000,  'unit' => 'kg',    'qty' => 100],
            'Semangka'       => ['title' => 'Semangka Merah Tanpa Biji',       'content' => 'Semangka merah manis tanpa biji, segar dan berair. Buah favorit untuk musim panas.',                                               'price' => 15000,  'unit' => 'kg',    'qty' => 80],
            'Alpukat'        => ['title' => 'Alpukat Mentega',                 'content' => 'Alpukat mentega premium, daging kuning lembut dan creamy. Cocok untuk jus alpukat dan salad.',                                      'price' => 30000,  'unit' => 'kg',    'qty' => 60],
            'Nanas'          => ['title' => 'Nanas Madu Subang',               'content' => 'Nanas madu Subang dengan rasa manis dominan tanpa getir. Daging kuning keemasan yang juicy.',                                      'price' => 18000,  'unit' => 'kg',    'qty' => 120],
            'Strawberry'     => ['title' => 'Strawberry Ciwidey Segar',        'content' => 'Strawberry merah segar dari perkebunan Ciwidey, Bandung. Rasa asam manis sempurna untuk dessert.',                                 'price' => 50000,  'unit' => 'kg',    'qty' => 30],

            // Umbi
            'Singkong'       => ['title' => 'Singkong Mentega',               'content' => 'Singkong mentega pulen dan manis alami. Cocok untuk singkong goreng, getuk, atau tape.',                                            'price' => 8000,   'unit' => 'kg',    'qty' => 300],
            'Ubi Jalar'      => ['title' => 'Ubi Jalar Cilembu',              'content' => 'Ubi Cilembu asli yang mengeluarkan madu saat dipanggang. Manis alami, lembut, dan legit.',                                           'price' => 20000,  'unit' => 'kg',    'qty' => 150],
            'Talas'          => ['title' => 'Talas Bogor Pilihan',            'content' => 'Talas Bogor bertekstur lembut dan gurih. Bahan utama untuk keripik talas, kolak, dan bubur.',                                        'price' => 15000,  'unit' => 'kg',    'qty' => 100],

            // Kacang-kacangan
            'Kedelai'        => ['title' => 'Kedelai Lokal Organik',          'content' => 'Kedelai organik lokal untuk tempe, tahu, dan susu kedelai. Butiran besar dan berkualitas.',                                          'price' => 18000,  'unit' => 'kg',    'qty' => 200],
            'Kacang Tanah'   => ['title' => 'Kacang Tanah Kulit',             'content' => 'Kacang tanah dengan kulit, cocok untuk kacang goreng, bumbu pecel, dan gado-gado.',                                                 'price' => 25000,  'unit' => 'kg',    'qty' => 150],
            'Kacang Hijau'   => ['title' => 'Kacang Hijau Premium',           'content' => 'Kacang hijau pilihan butiran seragam. Ideal untuk bubur kacang hijau, onde-onde, dan kecambah.',                                    'price' => 22000,  'unit' => 'kg',    'qty' => 180],
        ];

        // --- 5. Create Listings ---
        $createdListings = [];

        foreach ($produces as $produce) {
            $template = $templates[$produce->name] ?? null;
            if (!$template) {
                continue;
            }

            // Assign a random farmer
            $farmer = $farmerModels[array_rand($farmerModels)];

            // Vary dates so "New" badges appear on some
            $createdAt = now()->subDays(rand(0, 14))->subHours(rand(0, 12));

            $listing = Listing::firstOrCreate(
                [
                    'produce_produce_id' => $produce->produce_id,
                    'user_user_id'       => $farmer->user_id,
                    'title'              => $template['title'],
                ],
                [
                    'content'           => $template['content'],
                    'price'             => $template['price'] + rand(-2000, 3000),
                    'quantity'          => $template['qty'] + rand(-20, 50),
                    'unit'              => $template['unit'],
                    'status'            => 'active',
                    'availability_date' => now()->addDays(rand(1, 7)),
                    'created_at'        => $createdAt,
                    'updated_at'        => $createdAt,
                ]
            );

            $createdListings[] = $listing;
        }

        // --- 6. Seed some Ratings ---
        $comments = [
            'Kualitas sangat bagus, segar sekali!',
            'Pengiriman cepat, produk sesuai deskripsi.',
            'Harga terjangkau untuk kualitas segini.',
            'Sayurannya masih segar dan bersih.',
            'Puas banget, pasti pesan lagi!',
            'Rasa manis alami, anak-anak suka.',
            'Produk bagus tapi pengiriman agak lama.',
            'Fresh from the farm, recommended!',
            'Bumbu wangi dan berkualitas tinggi.',
            'Ukurannya besar-besar, sesuai ekspektasi.',
        ];

        foreach ($createdListings as $listing) {
            // ~60% chance of getting a rating
            if (rand(1, 10) <= 6) {
                Rating::firstOrCreate(
                    [
                        'listing_listing_id' => $listing->listing_id,
                        'user_user_id'       => $customer->user_id,
                    ],
                    [
                        'score'   => rand(3, 5),
                        'comment' => $comments[array_rand($comments)],
                    ]
                );
            }
        }

        $this->command->info('✅ Seeded ' . count($createdListings) . ' listings with ratings.');
    }
}
