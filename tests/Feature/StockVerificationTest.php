<?php

namespace Tests\Feature;

use App\Models\Peminjaman;
use App\Models\PeminjamanItem;
use App\Models\Sarpras;
use App\Models\SarprasItem;
use App\Models\StatusPeminjaman;
use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\IntegrationTestCase; // Changed from TestCase

class StockVerificationTest extends IntegrationTestCase
{
    // use RefreshDatabase; // Use transaction instead since user might rely on specific DB engine or seeded data

    protected function setUp(): void
    {
        parent::setUp();
        DB::beginTransaction();
    }

    protected function tearDown(): void
    {
        DB::rollBack();
        parent::tearDown();
    }

    public function test_stock_deduction_flow()
    {
        // 1. Setup Data
        // Create Statuses (Use firstOrCreate to avoid duplicates if seeding ran)
        $statusTersedia = StatusPeminjaman::firstOrCreate(['nama' => 'tersedia']);
        $statusDipinjam = StatusPeminjaman::firstOrCreate(['nama' => 'dipinjam']);
        $statusDikembalikan = StatusPeminjaman::firstOrCreate(['nama' => 'dikembalikan']);
        
        // Create User & Role
        $roleInfo = Role::firstOrCreate(['nama' => 'user']);
        $roleAdmin = Role::firstOrCreate(['nama' => 'admin']);
        $user = User::factory()->create(['role_id' => $roleInfo->id]);
        $admin = User::factory()->create(['role_id' => $roleAdmin->id]);

        // Create Kategori
        $kategori = \App\Models\KategoriSarpras::firstOrCreate(['nama' => 'Elektronik']);

        // Create Sarpras
        $sarpras = Sarpras::forceCreate([
            'nama' => 'Laptop Test',
            'kategori_id' => $kategori->id,
        ]);

        // Create 5 Items (Available)
        $items = [];
        for ($i = 0; $i < 5; $i++) {
            $items[] = SarprasItem::forceCreate([
                'sarpras_id' => $sarpras->id,
                'kode' => 'LPT-'.$i,
                'status_peminjaman_id' => $statusTersedia->id 
            ]);
        }

        // 2. Check Initial Stock
        // Using scopeAvailable from SarprasItem logic
        $availableCount = SarprasItem::where('sarpras_id', $sarpras->id)->available()->count();
        $this->assertEquals(5, $availableCount, 'Initial stock should be 5');

        // 3. User Requests 2 Items
        // Simulate PeminjamanController::store logic
        $this->actingAs($user);
        
        $requestData = [
            'sarpras_id' => $sarpras->id,
            'jumlah' => 2,
            'tujuan' => 'Testing',
            'tanggal_pinjam' => now()->toDateString(),
            'tanggal_kembali_rencana' => now()->addDays(2)->toDateString(),
        ];

        // Call the store route
        $response = $this->post(route('user.peminjaman.store'), $requestData);
        
        // Assert Redirect (Success)
        if ($response->status() !== 302) {
            dump($response->getContent());
        }
        $response->assertStatus(302);
        $response->assertSessionHas('success');

        // 4. Verify Database State
        $peminjaman = Peminjaman::where('user_id', $user->id)->first();
        $this->assertNotNull($peminjaman);
        $this->assertEquals('menunggu', $peminjaman->status);
        
        // Verify Items Linked
        $this->assertEquals(2, $peminjaman->items()->count());

        // 5. Check Stock Availability (Should be 3)
        $availableCountAfterRequest = SarprasItem::where('sarpras_id', $sarpras->id)->available()->count();
        $this->assertEquals(3, $availableCountAfterRequest, 'Stock should be 3 after request pending');

        // 6. Admin Approves
        $this->actingAs($admin);
        $responseApprove = $this->put(route('admin.peminjaman.setujui', $peminjaman->id));
        $responseApprove->assertStatus(302);

        // 7. Verify Approval Effects
        $peminjaman->refresh();
        $this->assertEquals('disetujui', $peminjaman->status);
        
        // Verify Items Status Updated to 'dipinjam'
        foreach ($peminjaman->items as $pItem) {
            $pItem->refresh();
            $this->assertEquals($statusDipinjam->id, $pItem->sarprasItem->status_peminjaman_id);
        }

        // 8. Check Stock Availability (Should still be 3)
        $availableCountAfterApprove = SarprasItem::where('sarpras_id', $sarpras->id)->available()->count();
        $this->assertEquals(3, $availableCountAfterApprove, 'Stock should remaining 3 after approval');

        // 9. Return Items
        $responseReturn = $this->put(route('admin.peminjaman.kembalikan', $peminjaman->id));
        $responseReturn->assertStatus(302);

        // 10. Verify Return Effects
        $peminjaman->refresh();
        $this->assertEquals('dikembalikan', $peminjaman->status);
        
        // Verify Items Status Updated to 'dikembalikan' (or null, depending on controller logic, controller sets it to 'dikembalikan' status id)
        foreach ($peminjaman->items as $pItem) {
            $pItem->refresh();
            $this->assertEquals($statusDikembalikan->id, $pItem->sarprasItem->status_peminjaman_id);
        }

        // 11. Check Stock Availability (Should be 5)
        // scopeAvailable considers 'dikembalikan' as available
        $availableCountAfterReturn = SarprasItem::where('sarpras_id', $sarpras->id)->available()->count();
        $this->assertEquals(5, $availableCountAfterReturn, 'Stock should be restored to 5');
    }
}
