<?php

namespace Tests\Browser;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class PBI31AdminVerifyDocumentTest extends DuskTestCase
{
    /**
     * Test admin can access the verification list page.
     */
    public function test_admin_verification(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        // Create a farmer with pending verification
        $farmer = User::factory()->create([
            'role' => 'farmer',
            'verification_status' => 'pending',
            'doc_skp' => 'farmer-documents/test-skp.jpg',
            'doc_nib' => 'farmer-documents/test-nib.jpg',
            'doc_ktp' => 'farmer-documents/test-ktp.jpg',
            'doc_skt' => 'farmer-documents/test-skt.jpg',
            'doc_land_cert' => 'farmer-documents/test-land.jpg',
        ]);

        $this->browse(function (Browser $browser) use ($admin, $farmer) {
            $browser->loginAs($admin)
                ->visit('/admin/verifications')
                ->assertPathIs('/admin/verifications')
                ->assertSee('Verifikasi Petani 📋')
                ->assertSee('Kelola verifikasi dokumen petani')
                ->assertSee($farmer->name)
                ->assertSee($farmer->email)
                ->assertSee('Pending')
                ->assertSee('5/5')  // All documents uploaded
                // Click the pending filter tab
                ->visit("/admin/verifications?status=pending")
                ->pause(1000)
                ->assertQueryStringHas('status', 'pending')
                ->assertSee('Petani Incomplete Docs')
                ->visit("/admin/verifications/{$farmer->user_id}")
                ->assertSee('Setujui Verifikasi')
                ->press('✅ Setujui Verifikasi')
                ->pause(1000)
                ->assertPathIs('/admin/verifications')
                ->assertSee('berhasil diverifikasi')
                ->visit("/admin/verifications/{$farmer->user_id}")
                ->assertSee('Verified')
                ->assertSee('Petani ini sudah terverifikasi');
        });
    }


    /**
     * Test admin can reject a farmer's verification.
     */
    public function test_admin_can_reject_farmer_verification(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $farmer = User::factory()->create([
            'role' => 'farmer',
            'verification_status' => 'pending',
            'name' => 'Petani Reject Test',
            'doc_skp' => 'farmer-documents/reject-skp.jpg',
            'doc_nib' => 'farmer-documents/reject-nib.jpg',
            'doc_ktp' => 'farmer-documents/reject-ktp.jpg',
            'doc_skt' => 'farmer-documents/reject-skt.jpg',
            'doc_land_cert' => 'farmer-documents/reject-land.jpg',
        ]);

        $this->browse(function (Browser $browser) use ($admin, $farmer) {
            $browser->loginAs($admin)
                ->visit("/admin/verifications/{$farmer->user_id}")
                ->assertSee('Tolak Verifikasi')
                ->press('❌ Tolak Verifikasi')
                ->pause(500)
                ->type('rejection_note', 'Dokumen KTP tidak jelas, harap upload ulang.')
                ->press('Konfirmasi Penolakan')
                ->pause(2000)
                ->visit("/admin/verifications/{$farmer->user_id}")
                ->assertPathIs("/admin/verifications/{$farmer->user_id}")
                ->assertSee('Rejected')
                ->assertSee('Dokumen KTP tidak jelas, harap upload ulang.');
                
        });

    }

    /**
     * Test customer cannot access admin verifications page.
     */
    public function test_customer_cannot_access_admin_verifications(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);

        $this->browse(function (Browser $browser) use ($customer) {
            $browser->loginAs($customer)
                ->visit('/admin/verifications')
                ->assertSee('403');
        });
    }

    /**
     * Test farmer cannot access admin verifications page.
     */
    public function test_farmer_cannot_access_admin_verifications(): void
    {
        $farmer = User::factory()->create([
            'role' => 'farmer',
            'verification_status' => 'verified',
        ]);

        $this->browse(function (Browser $browser) use ($farmer) {
            $browser->loginAs($farmer)
                ->visit('/admin/verifications')
                ->assertSee('403');
        });
    }

    /**
     * Test admin cannot verify nonexistent user.
     */
    public function test_admin_cannot_verify_nonexistent_user(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin)
                ->visit('/admin/verifications/99999')
                ->assertSee('404');
        });
    }

    /**
     * Test admin views customer as verification target returns 404.
     */
    public function test_admin_views_customer_as_verification_target(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $customer = User::factory()->create(['role' => 'customer']);

        $this->browse(function (Browser $browser) use ($admin, $customer) {
            $browser->loginAs($admin)
                ->visit("/admin/verifications/{$customer->user_id}")
                ->assertSee('404');
        });
    }
}
