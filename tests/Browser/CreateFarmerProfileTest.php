<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\User;

class CreateFarmerProfileTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     */
    public function test_update_farmer_profile_success(): void
    {
        $user = User::factory()->create([
            'role' => 'farmer',
            'verification_status' => 'verified'
        ]);

        $this->browse(function ($browser) use ($user): void {
            $browser->loginAs($user)
                ->visit('/farmer/dashboard')
                ->pause(1000)
                ->assertPathIs('/farmer/dashboard')
                ->assertSee('Manage Produce')
                ->click('button.flex.items-center.p-1.rounded-full')
                ->pause(1000)
                ->clickLink('Edit Profile')
                ->pause(1000)
                ->assertPathIs('/farmer/profile')
                ->type('name', 'Pak Tani Sukses')
                ->type('farm_description', 'Kebun hidroponik modern dengan teknologi IoT.')
                ->type('city', 'Bandung')
                ->type('address', 'Jl. Kebun Hijau No. 45, Lembang')
                ->type('latitude', '-6.81148000')
                ->type('longitude', '107.61878000')
                ->press('Simpan Perubahan')
                ->assertPathIs('/farmer/profile')
                ->assertSee('Profil berhasil diperbarui.')
            ;
        });
    }

    public function test_update_farmer_profile_failed(): void
    {
        $user = User::factory()->create([
            'role' => 'farmer',
            'verification_status' => 'verified'
        ]);

        $this->browse(function ($browser) use ($user): void {
            $browser->loginAs($user)
                ->visit('/farmer/dashboard')
                ->pause(1000)
                ->assertPathIs('/farmer/dashboard')
                ->assertSee('Manage Produce')
                ->click('button.flex.items-center.p-1.rounded-full')
                ->pause(1000)
                ->clickLink('Edit Profile')
                ->pause(1000)
                ->assertPathIs('/farmer/profile')
                ->type('name', '')
                ->type('farm_description', 'Kebun hidroponik modern dengan teknologi IoT.')
                ->type('city', 'Bandung')
                ->type('address', 'Jl. Kebun Hijau No. 45, Lembang')
                ->type('latitude', '-6.81148000')
                ->type('longitude', '107.61878000')
                ->press('Simpan Perubahan')
                ->assertPathIs('/farmer/profile')
                ->assertDontSee('Profil berhasil diperbarui.')
            ;
        });
    }
}
