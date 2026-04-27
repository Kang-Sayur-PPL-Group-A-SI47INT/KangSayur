<?php

namespace Database\Seeders;

use App\Models\Produce;
use Illuminate\Database\Seeder;

class ProduceSeeder extends Seeder
{
    /**
     * Seed dummy produce data.
     */
    public function run(): void
    {
        $produces = [
            // Sayuran
            ['name' => 'Wortel',        'category' => 'Sayuran',       'emoji' => '🥕'],
            ['name' => 'Tomat',         'category' => 'Sayuran',       'emoji' => '🍅'],
            ['name' => 'Jagung',        'category' => 'Sayuran',       'emoji' => '🌽'],
            ['name' => 'Terong',        'category' => 'Sayuran',       'emoji' => '🍆'],
            ['name' => 'Timun',         'category' => 'Sayuran',       'emoji' => '🥒'],
            ['name' => 'Paprika',       'category' => 'Sayuran',       'emoji' => '🫑'],
            ['name' => 'Kentang',       'category' => 'Sayuran',       'emoji' => '🥔'],
            ['name' => 'Labu Siam',     'category' => 'Sayuran',       'emoji' => '🎃'],
            ['name' => 'Kacang Panjang','category' => 'Sayuran',       'emoji' => '🫘'],

            // Sayuran Hijau
            ['name' => 'Sawi',          'category' => 'Sayuran Hijau', 'emoji' => '🥬'],
            ['name' => 'Bayam',         'category' => 'Sayuran Hijau', 'emoji' => '🥬'],
            ['name' => 'Kangkung',      'category' => 'Sayuran Hijau', 'emoji' => '🥬'],
            ['name' => 'Brokoli',       'category' => 'Sayuran Hijau', 'emoji' => '🥦'],
            ['name' => 'Selada',        'category' => 'Sayuran Hijau', 'emoji' => '🥗'],
            ['name' => 'Seledri',       'category' => 'Sayuran Hijau', 'emoji' => '🌿'],

            // Bumbu
            ['name' => 'Bawang Merah',  'category' => 'Bumbu',         'emoji' => '🧅'],
            ['name' => 'Bawang Putih',  'category' => 'Bumbu',         'emoji' => '🧄'],
            ['name' => 'Cabai Merah',   'category' => 'Bumbu',         'emoji' => '🌶️'],
            ['name' => 'Cabai Rawit',   'category' => 'Bumbu',         'emoji' => '🌶️'],
            ['name' => 'Jahe',          'category' => 'Bumbu',         'emoji' => '🫚'],
            ['name' => 'Kunyit',        'category' => 'Bumbu',         'emoji' => '🫚'],
            ['name' => 'Lengkuas',      'category' => 'Bumbu',         'emoji' => '🫚'],
            ['name' => 'Daun Bawang',   'category' => 'Bumbu',         'emoji' => '🌿'],

            // Buah
            ['name' => 'Pisang',        'category' => 'Buah',          'emoji' => '🍌'],
            ['name' => 'Mangga',        'category' => 'Buah',          'emoji' => '🥭'],
            ['name' => 'Jeruk',         'category' => 'Buah',          'emoji' => '🍊'],
            ['name' => 'Pepaya',        'category' => 'Buah',          'emoji' => '🍈'],
            ['name' => 'Semangka',      'category' => 'Buah',          'emoji' => '🍉'],
            ['name' => 'Alpukat',       'category' => 'Buah',          'emoji' => '🥑'],
            ['name' => 'Nanas',         'category' => 'Buah',          'emoji' => '🍍'],
            ['name' => 'Strawberry',    'category' => 'Buah',          'emoji' => '🍓'],

            // Umbi
            ['name' => 'Singkong',      'category' => 'Umbi',          'emoji' => '🥔'],
            ['name' => 'Ubi Jalar',     'category' => 'Umbi',          'emoji' => '🍠'],
            ['name' => 'Talas',         'category' => 'Umbi',          'emoji' => '🥔'],

            // Kacang-kacangan
            ['name' => 'Kedelai',       'category' => 'Kacang-kacangan', 'emoji' => '🫘'],
            ['name' => 'Kacang Tanah',  'category' => 'Kacang-kacangan', 'emoji' => '🥜'],
            ['name' => 'Kacang Hijau',  'category' => 'Kacang-kacangan', 'emoji' => '🫘'],
        ];

        foreach ($produces as $produce) {
            Produce::firstOrCreate(
                ['name' => $produce['name']],
                $produce
            );
        }
    }
}
