<?php

namespace Tests\Feature;

use App\Models\Asrama;
use App\Models\Kamar;
use App\Models\Kelas;
use App\Models\Santri;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SantriManagementTest extends TestCase
{
    use DatabaseTransactions;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->superAdmin()->create();
        $this->actingAs($this->user);
    }

    #[Test]
    public function santri_index_renders_successfully_with_data(): void
    {
        $asrama = Asrama::factory()->create();
        $kelas = Kelas::factory()->create();
        $santri = Santri::factory()->create([
            'asrama_id' => $asrama->id,
            'kelas_id' => $kelas->id,
            'kamar' => 'Kamar 101',
            'nama_wali' => 'Bapak Abdullah',
        ]);

        $response = $this->get(route('santri.index'));

        $response->assertOk();
        $response->assertViewIs('santri.index');
        $response->assertSee($santri->nama);
        $response->assertSee($santri->nis);
    }

    #[Test]
    public function santri_store_creates_record_and_redirects(): void
    {
        $asrama = Asrama::factory()->create();
        $kelas = Kelas::factory()->create();

        $payload = [
            'nis' => 'SAN-99999',
            'nama' => 'Santri Baru Uji Coba',
            'asrama_id' => $asrama->id,
            'kamar' => 'Kamar 204',
            'kelas_id' => $kelas->id,
            'nama_wali' => 'Ibu Fatimah',
            'no_wa_wali' => '081234567890',
            'jenis_kelamin' => 'L',
        ];

        $response = $this->post(route('santri.store'), $payload);

        $response->assertRedirect(route('santri.index'));
        $this->assertDatabaseHas('santris', [
            'nis' => 'SAN-99999',
            'nama' => 'Santri Baru Uji Coba',
            'kamar' => 'Kamar 204',
            'nama_wali' => 'Ibu Fatimah',
        ]);

        // Memastikan kamar otomatis tercatat pada tabel kamars
        $this->assertDatabaseHas('kamars', [
            'asrama_id' => $asrama->id,
            'nama_kamar' => 'Kamar 204',
        ]);
    }

    #[Test]
    public function santri_store_can_accept_string_tingkat_as_kelas(): void
    {
        $asrama = Asrama::factory()->create();

        $payload = [
            'nis' => 'SAN-88888',
            'nama' => 'Santri Tingkat Test',
            'asrama_id' => $asrama->id,
            'kamar' => 'Kamar 102',
            'kelas_id' => '10 SMA',
            'jenis_kelamin' => 'L',
        ];

        $response = $this->post(route('santri.store'), $payload);

        $response->assertRedirect(route('santri.index'));
        $santri = Santri::where('nis', 'SAN-88888')->first();
        $this->assertNotNull($santri);
        $this->assertEquals('10 SMA', $santri->kelas->tingkat);
    }

    #[Test]
    public function kamar_can_be_created_updated_and_deleted(): void
    {
        $asrama = Asrama::factory()->create();

        // 1. Tambah Kamar
        $response = $this->post(route('kamar.store'), [
            'asrama_id' => $asrama->id,
            'nama_kamar' => 'Kamar Bilal bin Rabah',
            'kapasitas' => 12,
            'keterangan' => 'Kamar Khusus Tahfidz',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('kamars', [
            'asrama_id' => $asrama->id,
            'nama_kamar' => 'Kamar Bilal bin Rabah',
            'kapasitas' => 12,
        ]);

        $kamar = Kamar::where('nama_kamar', 'Kamar Bilal bin Rabah')->first();

        // 2. Update Kamar
        $updateResp = $this->put(route('kamar.update', $kamar->id), [
            'asrama_id' => $asrama->id,
            'nama_kamar' => 'Kamar Bilal bin Rabah Utama',
            'kapasitas' => 15,
            'keterangan' => 'Lantai 1 Sayap Barat',
        ]);
        $updateResp->assertRedirect();
        $this->assertDatabaseHas('kamars', [
            'id' => $kamar->id,
            'nama_kamar' => 'Kamar Bilal bin Rabah Utama',
            'kapasitas' => 15,
        ]);

        // 3. Hapus Kamar
        $deleteResp = $this->delete(route('kamar.destroy', $kamar->id));
        $deleteResp->assertRedirect();
        $this->assertDatabaseMissing('kamars', [
            'id' => $kamar->id,
        ]);
    }

    #[Test]
    public function kamar_cannot_be_deleted_if_occupied(): void
    {
        $asrama = Asrama::factory()->create();
        $kamar = Kamar::create([
            'asrama_id' => $asrama->id,
            'nama_kamar' => 'Kamar A-01',
            'kapasitas' => 10,
        ]);
        $kelas = Kelas::factory()->create();

        Santri::factory()->create([
            'asrama_id' => $asrama->id,
            'kamar' => 'Kamar A-01',
            'kelas_id' => $kelas->id,
        ]);

        $response = $this->delete(route('kamar.destroy', $kamar->id));
        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('kamars', ['id' => $kamar->id]);
    }

    #[Test]
    public function asrama_can_be_created_updated_and_deleted(): void
    {
        // 1. Tambah Asrama
        $response = $this->post(route('asrama.store'), [
            'nama_asrama' => 'Asrama Utsman bin Affan',
            'gender' => 'putra',
            'gedung' => 'Gedung D Lt. 3',
            'pembina' => 'Ustadz Syamsul',
            'kapasitas' => 60,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('asramas', [
            'nama_asrama' => 'Asrama Utsman bin Affan',
            'gender' => 'putra',
        ]);

        $asrama = Asrama::where('nama_asrama', 'Asrama Utsman bin Affan')->first();

        // 2. Update Asrama
        $updateResp = $this->put(route('asrama.update', $asrama->id), [
            'nama_asrama' => 'Asrama Utsman bin Affan Updated',
            'gender' => 'putra',
            'gedung' => 'Gedung D Lt. 4',
            'pembina' => 'Ustadz Ahmad',
            'kapasitas' => 75,
        ]);
        $updateResp->assertRedirect();
        $this->assertDatabaseHas('asramas', [
            'id' => $asrama->id,
            'nama_asrama' => 'Asrama Utsman bin Affan Updated',
            'gedung' => 'Gedung D Lt. 4',
            'kapasitas' => 75,
        ]);

        // 3. Hapus asrama yang masih kosong
        $delResp = $this->delete(route('asrama.destroy', $asrama->id));
        $delResp->assertRedirect();
        $this->assertDatabaseMissing('asramas', ['id' => $asrama->id]);
    }

    #[Test]
    public function asrama_cannot_be_deleted_if_occupied_or_has_kamars(): void
    {
        $asrama = Asrama::factory()->create();
        Kamar::create([
            'asrama_id' => $asrama->id,
            'nama_kamar' => 'Kamar B-01',
            'kapasitas' => 10,
        ]);

        $response = $this->delete(route('asrama.destroy', $asrama->id));
        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('asramas', ['id' => $asrama->id]);
    }

    #[Test]
    public function kelas_can_be_created_updated_and_deleted(): void
    {
        // 1. Tambah Kelas
        $response = $this->post(route('kelas.store'), [
            'nama_kelas' => 'Kelas 7A SMP',
            'tingkat' => '7 SMP',
            'jurusan' => 'Umum',
            'wali_kelas' => 'Ustadz Hasan',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('kelas', [
            'nama_kelas' => 'Kelas 7A SMP',
            'tingkat' => '7 SMP',
            'jurusan' => 'Umum',
        ]);

        $kelas = Kelas::where('nama_kelas', 'Kelas 7A SMP')->first();

        // 2. Update Kelas
        $updateResp = $this->put(route('kelas.update', $kelas->id), [
            'nama_kelas' => 'Kelas 7A SMP Unggulan',
            'tingkat' => '7 SMP',
            'jurusan' => 'Umum',
            'wali_kelas' => 'Ustadz Hasan Al-Banna',
        ]);
        $updateResp->assertRedirect();
        $this->assertDatabaseHas('kelas', [
            'id' => $kelas->id,
            'nama_kelas' => 'Kelas 7A SMP Unggulan',
            'tingkat' => '7 SMP',
            'jurusan' => 'Umum',
            'wali_kelas' => 'Ustadz Hasan Al-Banna',
        ]);

        // 3. Hapus Kelas kosong
        $delResp = $this->delete(route('kelas.destroy', $kelas->id));
        $delResp->assertRedirect();
        $this->assertDatabaseMissing('kelas', ['id' => $kelas->id]);
    }

    #[Test]
    public function kelas_cannot_be_deleted_if_has_santris(): void
    {
        $asrama = Asrama::factory()->create();
        $kelas = Kelas::factory()->create();
        Santri::factory()->create([
            'asrama_id' => $asrama->id,
            'kelas_id' => $kelas->id,
        ]);

        $response = $this->delete(route('kelas.destroy', $kelas->id));
        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('kelas', ['id' => $kelas->id]);
    }

    #[Test]
    public function santri_update_modifies_record(): void
    {
        $santri = Santri::factory()->create([
            'nama' => 'Nama Lama',
            'kamar' => 'Kamar Lama',
        ]);

        $response = $this->put(route('santri.update', $santri->id), [
            'nama' => 'Nama Baru Diedit',
            'asrama_id' => $santri->asrama_id,
            'kamar' => 'Kamar Baru 303',
            'kelas_id' => $santri->kelas_id,
            'nama_wali' => 'Wali Baru',
            'no_wa_wali' => '089988776655',
            'status' => 'izin_pulang',
        ]);

        $response->assertRedirect(route('santri.index'));
        $this->assertDatabaseHas('santris', [
            'id' => $santri->id,
            'nama' => 'Nama Baru Diedit',
            'kamar' => 'Kamar Baru 303',
            'status' => 'izin_pulang',
        ]);
    }

    #[Test]
    public function santri_destroy_deletes_record(): void
    {
        $santri = Santri::factory()->create();

        $response = $this->delete(route('santri.destroy', $santri->id));

        $response->assertRedirect(route('santri.index'));
        $this->assertDatabaseMissing('santris', [
            'id' => $santri->id,
        ]);
    }

    #[Test]
    public function user_can_access_cetak_kartu_massal_and_filter(): void
    {
        $asrama = Asrama::factory()->create(['nama_asrama' => 'Asrama Khusus Kartu']);
        $kelas = Kelas::factory()->create(['nama_kelas' => 'Kelas Khusus Kartu']);

        $santriA = Santri::factory()->create([
            'asrama_id' => $asrama->id,
            'kelas_id' => $kelas->id,
            'nama' => 'Santri Kartu Pertama',
            'nis' => 'SAN-KARTU-001',
        ]);

        $santriB = Santri::factory()->create([
            'nama' => 'Santri Kartu Kedua',
            'nis' => 'SAN-KARTU-002',
        ]);

        // 1. Akses tanpa filter (semua santri)
        $response = $this->get(route('santri.cetak-kartu'));
        $response->assertOk();
        $response->assertViewIs('santri.cetak_kartu');
        $response->assertSee('Santri Kartu Pertama');
        $response->assertSee('Santri Kartu Kedua');

        // 2. Akses dengan filter kelas
        $filterKelasResp = $this->get(route('santri.cetak-kartu', ['kelas_id' => $kelas->id]));
        $filterKelasResp->assertOk();
        $filterKelasResp->assertSee('Santri Kartu Pertama');
        $filterKelasResp->assertDontSee('Santri Kartu Kedua');

        // 3. Akses dengan filter asrama
        $filterAsramaResp = $this->get(route('santri.cetak-kartu', ['asrama_id' => $asrama->id]));
        $filterAsramaResp->assertOk();
        $filterAsramaResp->assertSee('Santri Kartu Pertama');
        $filterAsramaResp->assertDontSee('Santri Kartu Kedua');
    }

    #[Test]
    public function user_can_download_single_qr_code_with_proper_filename(): void
    {
        $santri = Santri::factory()->create([
            'nama' => 'Ahmad Syarifudin',
            'nis' => 'SAN-2026-999',
        ]);

        $response = $this->get(route('santri.download-qr', $santri->id));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'image/svg+xml');
        $this->assertStringContainsString('attachment; filename="SAN-2026-999 - Ahmad Syarifudin.svg"', $response->headers->get('Content-Disposition') ?? '');
        $this->assertStringContainsString('<svg', $response->getContent() ?: '');
    }

    #[Test]
    public function user_can_download_bulk_qr_zip_with_filter(): void
    {
        $asrama = Asrama::factory()->create(['nama_asrama' => 'Asrama Khusus ZIP']);
        $santri = Santri::factory()->create([
            'asrama_id' => $asrama->id,
            'nama' => 'Santri Khusus ZIP',
            'nis' => 'SAN-ZIP-001',
        ]);

        $response = $this->get(route('santri.download-qr-zip', ['asrama_id' => $asrama->id]));

        $response->assertOk();
        $this->assertStringContainsString('application/zip', $response->headers->get('Content-Type') ?? '');
        $this->assertStringContainsString('QR_Code_Santri_', $response->headers->get('Content-Disposition') ?? '');
    }

    #[Test]
    public function user_can_bulk_delete_selected_santris(): void
    {
        $santri1 = Santri::factory()->create(['nama' => 'Santri Hapus 1']);
        $santri2 = Santri::factory()->create(['nama' => 'Santri Hapus 2']);
        $santri3 = Santri::factory()->create(['nama' => 'Santri Tetap Ada']);

        $response = $this->post(route('santri.bulk-delete'), [
            'ids' => [$santri1->id, $santri2->id],
        ]);

        $response->assertRedirect(route('santri.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('santris', ['id' => $santri1->id]);
        $this->assertDatabaseMissing('santris', ['id' => $santri2->id]);
        $this->assertDatabaseHas('santris', ['id' => $santri3->id]);
    }

    #[Test]
    public function bulk_delete_validates_required_ids(): void
    {
        $response = $this->post(route('santri.bulk-delete'), [
            'ids' => [],
        ]);

        $response->assertSessionHasErrors('ids');
    }
}
