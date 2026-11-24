<?php

namespace Tests\Feature;

use App\Models\Perizinan;
use App\Models\Santri;
use App\Models\SesiAbsensi;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DashboardAndModulesTest extends TestCase
{
    use DatabaseTransactions;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->superAdmin()->create();
        $this->actingAs($this->admin);
    }

    #[Test]
    public function dashboard_page_renders_with_metrics_and_sessions(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertViewIs('dashboard');
        $response->assertSee('E-Absensi');
        $response->assertSee('Total Santri Aktif');
    }

    #[Test]
    public function batch_attendance_page_renders_with_santri_list(): void
    {
        $sesi = SesiAbsensi::factory()->create(['status' => 'dibuka']);
        $santri = Santri::factory()->create(['status' => 'aktif']);

        $response = $this->get(route('absensi.batch', ['sesi_id' => $sesi->id]));

        $response->assertOk();
        $response->assertViewIs('absensi.batch');
        $response->assertSee($santri->nama);
        $response->assertSee($sesi->nama_sesi);
    }

    #[Test]
    public function batch_attendance_can_be_stored_in_bulk(): void
    {
        $sesi = SesiAbsensi::factory()->create(['status' => 'dibuka']);
        $santri1 = Santri::factory()->create(['status' => 'aktif']);
        $santri2 = Santri::factory()->create(['status' => 'aktif']);

        $payload = [
            'sesi_id' => $sesi->id,
            'statuses' => [
                $santri1->id => 'hadir',
                $santri2->id => 'izin',
            ],
        ];

        $response = $this->post(route('absensi.batch.process'), $payload);

        $response->assertRedirect();
        $this->assertDatabaseHas('absensis', [
            'santri_id' => $santri1->id,
            'sesi_absensi_id' => $sesi->id,
            'status' => 'hadir',
        ]);
        $this->assertDatabaseHas('absensis', [
            'santri_id' => $santri2->id,
            'sesi_absensi_id' => $sesi->id,
            'status' => 'izin',
        ]);
    }

    #[Test]
    public function perizinan_web_index_renders_and_form_submits(): void
    {
        $santri = Santri::factory()->create(['status' => 'aktif']);

        $response = $this->get(route('perizinan.index'));
        $response->assertOk();
        $response->assertViewIs('perizinan.index');

        $payload = [
            'santri_id' => $santri->id,
            'jenis_izin' => 'pulang',
            'keperluan' => 'Menghadiri walimatul ursy kakak kandung',
            'tanggal_mulai' => now()->format('Y-m-d H:i:s'),
            'tanggal_selesai' => now()->addDays(3)->format('Y-m-d H:i:s'),
            'penjemput_nama' => 'Bapak Rahmat',
            'penjemput_hubungan' => 'Ayah Kandung',
        ];

        $postResponse = $this->post(route('perizinan.store'), $payload);
        $postResponse->assertRedirect(route('perizinan.index'));

        $this->assertDatabaseHas('perizinans', [
            'santri_id' => $santri->id,
            'jenis_izin' => 'pulang',
            'status' => 'pending',
        ]);
    }

    #[Test]
    public function perizinan_can_be_approved_and_digital_letter_is_viewable(): void
    {
        $santri = Santri::factory()->create(['status' => 'aktif']);
        $perizinan = Perizinan::factory()->create([
            'santri_id' => $santri->id,
            'status' => 'pending',
        ]);

        $response = $this->post(route('perizinan.approve', $perizinan->id), [
            'catatan' => 'Disetujui untuk pulang',
        ]);

        $response->assertRedirect(route('perizinan.index'));
        $this->assertDatabaseHas('perizinans', [
            'id' => $perizinan->id,
            'status' => 'disetujui',
        ]);

        $suratResponse = $this->get(route('perizinan.surat', $perizinan->id));
        $suratResponse->assertOk();
        $suratResponse->assertViewIs('perizinan.surat');
        $suratResponse->assertSee($santri->nama);
    }

    #[Test]
    public function kamtib_web_index_renders_and_handles_verification(): void
    {
        $santri = Santri::factory()->create(['status' => 'aktif']);
        $perizinan = Perizinan::factory()->create([
            'santri_id' => $santri->id,
            'status' => 'disetujui',
            'qr_token' => 'TOKEN-TEST-KAMTIB-12345',
        ]);

        $indexResponse = $this->get(route('kamtib.index'));
        $indexResponse->assertOk();
        $indexResponse->assertViewIs('kamtib.index');

        // Check-out santri at the gate
        $scanResponse = $this->post(route('kamtib.scan.process'), [
            'qr_token' => 'TOKEN-TEST-KAMTIB-12345',
            'action' => 'checkout',
            'catatan' => 'Keluar dijemput wali',
        ]);

        $scanResponse->assertRedirect(route('kamtib.index'));
        $this->assertDatabaseHas('kamtib_logs', [
            'santri_id' => $santri->id,
            'tipe_log' => 'checkout',
        ]);
    }

    #[Test]
    public function wali_santri_portal_renders_ananda_profile_and_stats(): void
    {
        $wali = User::factory()->waliSantri()->create();
        $santri = Santri::factory()->create([
            'no_wa_wali' => $wali->no_hp,
            'status' => 'aktif',
        ]);

        $response = $this->actingAs($wali)->get(route('wali.portal'));

        $response->assertOk();
        $response->assertViewIs('wali.portal');
        $response->assertSee($santri->nama);
    }

    #[Test]
    public function role_middleware_restricts_unauthorized_access(): void
    {
        $kamtib = User::factory()->keamanan()->create();

        // Petugas Kamtib mencoba membuka manajemen data santri -> ditolak dan diarahkan ke kamtib.index
        $response = $this->actingAs($kamtib)->get(route('santri.index'));

        $response->assertRedirect(route('kamtib.index'));
        $response->assertSessionHas('error');
    }

    #[Test]
    public function quick_login_allows_instant_role_switching(): void
    {
        $response = $this->get(route('quick.login', 'musyrif'));

        $response->assertRedirect(route('home'));
        $this->assertAuthenticated();
        $this->assertEquals(User::ROLE_MUSYRIF, auth()->user()->role);
    }

    #[Test]
    public function kamtib_user_accessing_home_is_redirected_to_kamtib_pos(): void
    {
        $kamtib = User::factory()->keamanan()->create();

        $response = $this->actingAs($kamtib)->get(route('home'));

        $response->assertRedirect(route('kamtib.index'));
    }

    #[Test]
    public function wali_santri_cannot_view_or_submit_permits_for_other_santri(): void
    {
        $wali = User::factory()->waliSantri()->create(['no_hp' => '081234567890']);
        $ownSantri = Santri::factory()->create(['no_wa_wali' => '081234567890', 'status' => 'aktif']);
        $otherSantri = Santri::factory()->create(['no_wa_wali' => '089876543210', 'status' => 'aktif']);

        $ownPerizinan = Perizinan::factory()->create([
            'santri_id' => $ownSantri->id,
            'status' => 'disetujui',
            'approved_by' => $this->admin->id,
        ]);
        $otherPerizinan = Perizinan::factory()->create([
            'santri_id' => $otherSantri->id,
            'status' => 'disetujui',
            'approved_by' => $this->admin->id,
        ]);

        // Wali can view own santri's surat
        $ownSuratResponse = $this->actingAs($wali)->get(route('perizinan.surat', $ownPerizinan->id));
        $ownSuratResponse->assertOk();
        $ownSuratResponse->assertSee($ownSantri->nama);
        $ownSuratResponse->assertSee($this->admin->name); // Disetujui Oleh

        // Wali is blocked from viewing other santri's surat (strictly no demo fallback)
        $otherSuratResponse = $this->actingAs($wali)->get(route('perizinan.surat', $otherPerizinan->id));
        $otherSuratResponse->assertRedirect(route('wali.portal'));
        $otherSuratResponse->assertSessionHas('error');

        // Wali cannot submit permit for other santri
        $submitOtherResponse = $this->actingAs($wali)->post(route('perizinan.store'), [
            'santri_id' => $otherSantri->id,
            'jenis_izin' => 'pulang',
            'keperluan' => 'Mencoba izin santri lain',
            'tanggal_mulai' => now()->format('Y-m-d H:i:s'),
            'tanggal_selesai' => now()->addDays(2)->format('Y-m-d H:i:s'),
        ]);
        $submitOtherResponse->assertRedirect();
        $submitOtherResponse->assertSessionHas('error');

        // Wali with no linked santri sees clean empty state in portal without demo fallback
        $unlinkedWali = User::factory()->waliSantri()->create(['no_hp' => '080000000000']);
        $portalResponse = $this->actingAs($unlinkedWali)->get(route('wali.portal'));
        $portalResponse->assertOk();
        $portalResponse->assertSee('Data Santri Belum Terhubung');
        $portalResponse->assertDontSee($otherSantri->nama);
    }

    #[Test]
    public function register_wali_page_does_not_leak_santri_names(): void
    {
        auth()->logout();

        $santri = Santri::factory()->create([
            'nama' => 'Rahasia Santri Pribadi',
            'nis' => 'SAN-SECRET-001',
            'status' => 'aktif',
        ]);

        $response = $this->get(route('register.wali'));
        $response->assertOk();
        $response->assertDontSee('Rahasia Santri Pribadi');
        $response->assertDontSee('SAN-SECRET-001');
        $response->assertSee('Nomor Induk Santri (NIS Ananda)');
    }

    #[Test]
    public function wali_can_register_with_valid_nis(): void
    {
        auth()->logout();

        $santri = Santri::factory()->create([
            'nama' => 'Muhammad Alif',
            'nis' => 'SAN-2026-999',
            'status' => 'aktif',
        ]);

        $response = $this->post(route('register.wali.process'), [
            'name' => 'Bapak Usman',
            'email' => 'usman.wali@example.com',
            'no_hp' => '081233445566',
            'nis_santri' => 'SAN-2026-999',
            'hubungan' => 'Ayah Kandung',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('wali.portal'));
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'usman.wali@example.com',
            'role' => User::ROLE_WALI_SANTRI,
        ]);
        $this->assertDatabaseHas('santris', [
            'id' => $santri->id,
            'no_wa_wali' => '081233445566',
            'nama_wali' => 'Bapak Usman',
        ]);
    }

    #[Test]
    public function wali_registration_fails_with_invalid_nis(): void
    {
        auth()->logout();

        $response = $this->post(route('register.wali.process'), [
            'name' => 'Bapak Palsu',
            'email' => 'palsu@example.com',
            'no_hp' => '081299998888',
            'nis_santri' => 'SAN-TIDAK-ADA',
            'hubungan' => 'Ayah Kandung',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors('nis_santri');
        $this->assertGuest();
    }

    #[Test]
    public function can_save_and_delete_face_descriptor_for_santri(): void
    {
        $santri = Santri::factory()->create(['status' => 'aktif']);
        $fakeDescriptor = array_fill(0, 128, 0.12345);

        // Simpan descriptor wajah
        $response = $this->postJson(route('santri.face-descriptor.save', $santri->id), [
            'face_descriptor' => $fakeDescriptor,
        ]);

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'santri' => [
                    'id' => $santri->id,
                    'has_face_descriptor' => true,
                ],
            ]);

        $santri->refresh();
        $this->assertNotNull($santri->face_descriptor);
        $this->assertCount(128, $santri->face_descriptor);
        $this->assertTrue($santri->has_face_descriptor);

        // Hapus descriptor wajah
        $deleteResponse = $this->deleteJson(route('santri.face-descriptor.delete', $santri->id));
        $deleteResponse->assertOk()->assertJson(['success' => true]);

        $santri->refresh();
        $this->assertNull($santri->face_descriptor);
        $this->assertFalse($santri->has_face_descriptor);
    }

    #[Test]
    public function can_fetch_face_descriptors_for_absensi_ai(): void
    {
        $santriWithFace = Santri::factory()->create([
            'status' => 'aktif',
            'face_descriptor' => array_fill(0, 128, 0.54321),
        ]);

        $santriWithoutFace = Santri::factory()->create([
            'status' => 'aktif',
            'face_descriptor' => null,
        ]);

        $response = $this->getJson(route('absensi.face-descriptors'));

        $response->assertOk()
            ->assertJson(['success' => true])
            ->assertJsonFragment(['nis' => $santriWithFace->nis]);

        // Pastikan santri tanpa wajah tidak masuk dalam database face descriptors
        $data = $response->json('data');
        $this->assertTrue(collect($data)->contains('nis', $santriWithFace->nis));
        $this->assertFalse(collect($data)->contains('nis', $santriWithoutFace->nis));
    }

    #[Test]
    public function absensi_scan_page_renders_face_ai_and_qr_modes(): void
    {
        $sesi = SesiAbsensi::factory()->create(['status' => 'dibuka']);

        $response = $this->get(route('absensi.scan', ['sesi_id' => $sesi->id]));

        $response->assertOk();
        $response->assertSee('Deteksi Wajah (Face AI)');
        $response->assertSee('Pemindai QR Code');
        $response->assertSee('face-stream-video');
        $response->assertSee('qr-reader');
    }
}
