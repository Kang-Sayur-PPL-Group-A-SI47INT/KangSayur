<?php
namespace Database\Seeders;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
class AdminSeeder extends Seeder
{
    public function run(): void
    {
        if (User::where('email', 'admin@kangsayur.com')->exists()) {
            $this->command->info('Admin account already exists, skipping.');
            return;
        }
        User::create([
            'name' => 'Admin KangSayur',
            'email' => 'admin@kangsayur.com',
            'password' => Hash::make('admin12345'),
            'role' => 'admin',
            'verification_status' => 'verified',
        ]);
        $this->command->info('Admin account created: admin@kangsayur.com / admin12345');
    }
}