<?php

namespace Tests\Feature;

use App\Models\Kelas;
use App\Models\Santri;
use App\Models\Tagihan;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BendaharaKeuanganTest extends TestCase
{
    use DatabaseTransactions;

    // ================================================================
    // AUTH & REDIRECT TESTS
    // ================================================================

    /** @test */
    public function test_bendahara_redirects_to_dashboard_on_login(): void
    {
        $bendahara = $this->createBendahara();

        $response = $this->post('/login', [
            'email'    => $bendahara->email,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('bendahara.index'));
    }

    /** @test */
    public function test_guest_cannot_access_bendahara_routes(): void
    {
        $response = $this->get('/bendahara');

        $response->assertRedirect('/login');
    }

    /** @test */
    public function test_musyrif_cannot_access_bendahara_routes(): void
    {
        $musyrif = User::factory()->musyrif()->create();

        $response = $this->actingAs($musyrif)->get('/bendahara');

        // Middleware redirects non-bendahara to their own dashboard
        $response->assertRedirect();
    }

    /** @test */
    public function test_wali_cannot_access_bendahara_routes(): void
    {
        $wali = User::factory()->waliSantri()->create();

        $response = $this->actingAs($wali)->get('/bendahara');

        $response->assertRedirect();
    }

    // ================================================================
    // BENDAHARA DASHBOARD TESTS
    // ================================================================

    /** @test */
    public function test_bendahara_can_view_dashboard(): void
    {
        $bendahara = $this->createBendahara();

        $response = $this->actingAs($bendahara)->get('/bendahara');

        $response->assertStatus(200);
    }

    /** @test */
    public function test_bendahara_can_view_rekap(): void
    {
        $bendahara = $this->createBendahara();

        $response = $this->actingAs($bendahara)->get('/bendahara/rekap');

        $response->assertStatus(200);
    }

    /** @test */
    public function test_bendahara_can_view_tagihan_index(): void
    {
        $bendahara = $this->createBendahara();

        $response = $this->actingAs($bendahara)->get('/bendahara/tagihan');

        $response->assertStatus(200);
    }

    /** @test */
    public function test_bendahara_can_view_verifikasi(): void
    {
        $bendahara = $this->createBendahara();

        $response = $this->actingAs($bendahara)->get('/bendahara/verifikasi');

        $response->assertStatus(200);
    }

    // ================================================================
    // TAGIHAN MANAGEMENT TESTS
    // ================================================================

    /** @test */
    public function test_bendahara_can_create_tagihan_for_single_santri(): void
    {
        $bendahara = $this->createBendahara();
        $santri    = Santri::factory()->aktif()->create();

        $payload = [
            'target'        => 'single',
            'santri_id'     => $santri->id,
            'judul_tagihan' => 'SPP Januari 2025',
            'kategori'      => 'spp',
            'nominal'       => 350000,
            'jatuh_tempo'   => now()->addDays(14)->format('Y-m-d'),
        ];

        $response = $this->actingAs($bendahara)->post('/bendahara/tagihan', $payload);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('tagihans', [
            'santri_id'     => $santri->id,
            'judul_tagihan' => 'SPP Januari 2025',
            'nominal'       => 350000,
            'status'        => 'belum_bayar',
        ]);
    }

    /** @test */
    public function test_bendahara_can_create_tagihan_bulk_all(): void
    {
        $bendahara = $this->createBendahara();
        $santris   = Santri::factory()->aktif()->count(3)->create();

        $payload = [
            'target'        => 'semua',
            'judul_tagihan' => 'SPP Februari 2025',
            'kategori'      => 'spp',
            'nominal'       => 300000,
            'jatuh_tempo'   => now()->addDays(14)->format('Y-m-d'),
        ];

        $response = $this->actingAs($bendahara)->post('/bendahara/tagihan', $payload);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        foreach ($santris as $santri) {
            $this->assertDatabaseHas('tagihans', [
                'santri_id'     => $santri->id,
                'judul_tagihan' => 'SPP Februari 2025',
            ]);
        }
    }

    /** @test */
    public function test_bendahara_can_create_tagihan_bulk_per_kelas(): void
    {
        $bendahara = $this->createBendahara();
        $kelas     = Kelas::factory()->create();
        $santris   = Santri::factory()->aktif()->count(2)->create(['kelas_id' => $kelas->id]);
        // Santri di kelas lain (tidak boleh dapat tagihan)
        $santriLain = Santri::factory()->aktif()->create();

        $payload = [
            'target'        => 'kelas',
            'kelas_id'      => $kelas->id,
            'judul_tagihan' => 'SPP Maret 2025',
            'kategori'      => 'spp',
            'nominal'       => 350000,
            'jatuh_tempo'   => now()->addDays(14)->format('Y-m-d'),
        ];

        $response = $this->actingAs($bendahara)->post('/bendahara/tagihan', $payload);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        foreach ($santris as $santri) {
            $this->assertDatabaseHas('tagihans', [
                'santri_id'     => $santri->id,
                'judul_tagihan' => 'SPP Maret 2025',
            ]);
        }

        // Santri di luar kelas tidak mendapat tagihan ini
        $this->assertDatabaseMissing('tagihans', [
            'santri_id'     => $santriLain->id,
            'judul_tagihan' => 'SPP Maret 2025',
        ]);
    }

    /** @test */
    public function test_bendahara_tagihan_store_validates_required_fields(): void
    {
        $bendahara = $this->createBendahara();

        $response = $this->actingAs($bendahara)->post('/bendahara/tagihan', []);

        $response->assertSessionHasErrors(['target', 'judul_tagihan', 'kategori', 'nominal', 'jatuh_tempo']);
    }

    // ================================================================
    // VERIFIKASI TESTS
    // ================================================================

    /** @test */
    public function test_bendahara_can_konfirmasi_transfer(): void
    {
        $bendahara = $this->createBendahara();
        $tagihan   = $this->createSantriWithTagihan('menungguKonfirmasi');

        $response = $this->actingAs($bendahara)->post("/bendahara/verifikasi/{$tagihan->id}", [
            'action'            => 'konfirmasi',
            'catatan_bendahara' => 'Pembayaran valid.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('tagihans', [
            'id'     => $tagihan->id,
            'status' => 'lunas',
        ]);
    }

    /** @test */
    public function test_bendahara_can_tolak_transfer(): void
    {
        $bendahara = $this->createBendahara();
        $tagihan   = $this->createSantriWithTagihan('menungguKonfirmasi');

        $response = $this->actingAs($bendahara)->post("/bendahara/verifikasi/{$tagihan->id}", [
            'action' => 'tolak',
            'alasan' => 'Bukti transfer tidak terbaca.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('tagihans', [
            'id'     => $tagihan->id,
            'status' => 'belum_bayar',
        ]);
    }

    /** @test */
    public function test_bendahara_can_catat_bayar_tunai(): void
    {
        $bendahara = $this->createBendahara();
        $tagihan   = $this->createSantriWithTagihan(); // belum_bayar

        $response = $this->actingAs($bendahara)->post("/bendahara/tagihan/{$tagihan->id}/tunai", [
            'catatan_bendahara' => 'Bayar tunai di loket.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('tagihans', [
            'id'                => $tagihan->id,
            'status'            => 'lunas',
            'metode_pembayaran' => 'tunai',
        ]);
    }

    // ================================================================
    // KUITANSI TESTS
    // ================================================================

    /** @test */
    public function test_bendahara_can_view_kuitansi_lunas(): void
    {
        $bendahara = $this->createBendahara();
        $tagihan   = $this->createSantriWithTagihan('lunas');

        $response = $this->actingAs($bendahara)->get("/bendahara/kuitansi/{$tagihan->id}");

        $response->assertStatus(200);
    }

    /** @test */
    public function test_bendahara_cannot_view_kuitansi_belum_bayar(): void
    {
        $bendahara = $this->createBendahara();
        $tagihan   = $this->createSantriWithTagihan(); // belum_bayar

        $response = $this->actingAs($bendahara)->get("/bendahara/kuitansi/{$tagihan->id}");

        // Controller returns back()->with('error', ...) when not lunas
        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    // ================================================================
    // SUPER ADMIN TESTS
    // ================================================================

    /** @test */
    public function test_super_admin_can_view_tunggakan(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();

        $response = $this->actingAs($superAdmin)->get('/keuangan/tunggakan');

        $response->assertStatus(200);
    }

    /** @test */
    public function test_bendahara_cannot_access_super_admin_tunggakan(): void
    {
        $bendahara = $this->createBendahara();

        $response = $this->actingAs($bendahara)->get('/keuangan/tunggakan');

        // EnsureUserRole redirects bendahara to bendahara.index
        $response->assertRedirect();
    }

    // ================================================================
    // WALI PEMBAYARAN TESTS
    // ================================================================

    /** @test */
    public function test_wali_can_view_pembayaran(): void
    {
        [$wali] = $this->createWaliWithSantri();

        $response = $this->actingAs($wali)->get('/wali/pembayaran');

        $response->assertStatus(200);
    }

    /** @test */
    public function test_wali_can_submit_bukti_transfer(): void
    {
        Storage::fake('public');

        [$wali, $santri] = $this->createWaliWithSantri();
        $tagihan = Tagihan::factory()->for($santri)->create(['status' => 'belum_bayar']);

        $file = UploadedFile::fake()->image('bukti.jpg', 400, 300);

        $response = $this->actingAs($wali)->post("/wali/pembayaran/{$tagihan->id}", [
            'bukti_transfer'    => $file,
            'metode_pembayaran' => 'transfer_bank',
            'catatan_wali'      => 'Sudah transfer via m-banking.',
        ]);

        $response->assertRedirect(route('wali.pembayaran'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('tagihans', [
            'id'     => $tagihan->id,
            'status' => 'menunggu_konfirmasi',
        ]);
    }

    /** @test */
    public function test_wali_cannot_pay_other_santri_tagihan(): void
    {
        Storage::fake('public');

        // Wali A dengan santri A
        [$waliA] = $this->createWaliWithSantri();

        // Santri B milik wali B (no_wa_wali berbeda)
        $waliB   = User::factory()->waliSantri()->create(['no_hp' => '081234567890']);
        $santriB = Santri::factory()->aktif()->create(['no_wa_wali' => '081234567890']);
        $tagihanB = Tagihan::factory()->for($santriB)->create(['status' => 'belum_bayar']);

        $file = UploadedFile::fake()->image('bukti.jpg', 400, 300);

        $response = $this->actingAs($waliA)->post("/wali/pembayaran/{$tagihanB->id}", [
            'bukti_transfer'    => $file,
            'metode_pembayaran' => 'transfer_bank',
            'catatan_wali'      => 'Bayar tagihan orang lain.',
        ]);

        $response->assertStatus(403);
    }

    // ================================================================
    // CICILAN & AKUMULASI TAGIHAN TESTS
    // ================================================================

    /** @test */
    public function test_bendahara_accumulates_nominal_when_santri_has_unpaid_tagihan(): void
    {
        $bendahara = $this->createBendahara();
        $santri    = Santri::factory()->aktif()->create();

        // Tagihan pertama
        $existing = Tagihan::factory()->for($santri)->create([
            'judul_tagihan' => 'Syahriah Januari',
            'nominal'       => 500000,
            'status'        => 'belum_bayar',
        ]);

        // Terbitkan tagihan kedua untuk santri yang sama
        $response = $this->actingAs($bendahara)->post('/bendahara/tagihan', [
            'target'        => 'single',
            'santri_id'     => $santri->id,
            'judul_tagihan' => 'Syahriah Februari',
            'kategori'      => 'spp',
            'nominal'       => 500000,
            'jatuh_tempo'   => now()->addDays(14)->format('Y-m-d'),
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Tidak boleh membuat row baru, nominal diakumulasikan
        $this->assertEquals(1, Tagihan::where('santri_id', $santri->id)->where('status', 'belum_bayar')->count());
        $existing->refresh();
        $this->assertEquals(1000000, $existing->nominal);
        $this->assertStringContainsString('Syahriah Januari', $existing->judul_tagihan);
        $this->assertStringContainsString('Syahriah Februari', $existing->judul_tagihan);
    }

    /** @test */
    public function test_bendahara_can_catat_bayar_tunai_sebagian_cicilan(): void
    {
        $bendahara = $this->createBendahara();
        $tagihan   = $this->createSantriWithTagihan();
        $tagihan->update(['nominal' => 1000000, 'nominal_dibayar' => 0]);

        $response = $this->actingAs($bendahara)->post("/bendahara/tagihan/{$tagihan->id}/tunai", [
            'nominal_bayar'     => 300000,
            'catatan_bendahara' => 'Cicilan ke-1 tunai',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $tagihan->refresh();
        $this->assertEquals(300000, $tagihan->nominal_dibayar);
        $this->assertEquals(700000, $tagihan->sisa_tagihan);
        $this->assertEquals('belum_bayar', $tagihan->status);
        $this->assertNotEmpty($tagihan->riwayat_cicilan);
    }

    /** @test */
    public function test_wali_can_submit_bukti_transfer_cicilan(): void
    {
        [$wali, $santri] = $this->createWaliWithSantri();
        $tagihan = Tagihan::factory()->for($santri)->create([
            'nominal' => 1000000,
            'nominal_dibayar' => 0,
            'status' => 'belum_bayar',
        ]);

        $file = \Illuminate\Http\UploadedFile::fake()->image('bukti_cicil.jpg');

        $response = $this->actingAs($wali)->post("/wali/pembayaran/{$tagihan->id}", [
            'bukti_transfer'    => $file,
            'metode_pembayaran' => 'transfer_bsi',
            'nominal_bayar'     => 400000,
            'catatan_wali'      => 'Cicil dulu 400rb',
        ]);

        $response->assertRedirect('/wali/pembayaran');
        $tagihan->refresh();
        $this->assertEquals('menunggu_konfirmasi', $tagihan->status);
        $this->assertEquals(400000, $tagihan->nominal_transfer);
    }

    /** @test */
    public function test_bendahara_can_konfirmasi_cicilan_dan_tagihan_tetap_aktif(): void
    {
        $bendahara = $this->createBendahara();
        $tagihan   = $this->createSantriWithTagihan('menungguKonfirmasi');
        $tagihan->update([
            'nominal'          => 1000000,
            'nominal_dibayar'  => 0,
            'nominal_transfer' => 400000,
        ]);

        $response = $this->actingAs($bendahara)->post("/bendahara/verifikasi/{$tagihan->id}", [
            'action'             => 'konfirmasi',
            'nominal_konfirmasi' => 400000,
        ]);

        $response->assertRedirect();
        $tagihan->refresh();
        $this->assertEquals(400000, $tagihan->nominal_dibayar);
        $this->assertEquals(600000, $tagihan->sisa_tagihan);
        $this->assertEquals('belum_bayar', $tagihan->status);
    }

    // ================================================================
    // PRIVATE HELPER METHODS
    // ================================================================

    /**
     * Buat user dengan role bendahara.
     */
    private function createBendahara(): User
    {
        return User::factory()->bendahara()->create();
    }

    /**
     * Buat user wali_santri beserta santri yang dikaitkan via no_wa_wali = no_hp.
     *
     * @return array{0: User, 1: Santri}
     */
    private function createWaliWithSantri(): array
    {
        $noHp  = '0812' . fake()->numerify('########');
        $wali  = User::factory()->waliSantri()->create(['no_hp' => $noHp]);
        $santri = Santri::factory()->aktif()->create(['no_wa_wali' => $noHp]);

        return [$wali, $santri];
    }

    /**
     * Buat santri beserta satu tagihan.
     * State: null = belum_bayar, 'lunas', 'menungguKonfirmasi', 'nunggak'
     */
    private function createSantriWithTagihan(?string $state = null): Tagihan
    {
        $santri = Santri::factory()->aktif()->create();

        $factory = Tagihan::factory()->for($santri);

        if ($state) {
            $factory = $factory->{$state}();
        }

        return $factory->create();
    }
}
