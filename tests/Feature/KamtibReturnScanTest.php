<?php

namespace Tests\Feature;

use App\Models\Asrama;
use App\Models\KamtibLog;
use App\Models\Kelas;
use App\Models\Perizinan;
use App\Models\Santri;
use App\Models\User;
use App\Services\KamtibService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class KamtibReturnScanTest extends TestCase
{
    use DatabaseTransactions;

    protected User $kamtibUser;

    protected Santri $santri;

    protected Perizinan $perizinan;

    protected function setUp(): void
    {
        parent::setUp();

        $this->kamtibUser = User::factory()->kamtib()->create();
        $this->actingAs($this->kamtibUser);

        $asrama = Asrama::factory()->create();
        $kelas = Kelas::factory()->create();

        $this->santri = Santri::factory()->create([
            'asrama_id' => $asrama->id,
            'kelas_id' => $kelas->id,
            'nama' => 'Muhammad Zidan Firdaus',
            'nis' => 'NIS-2026-999',
            'status' => 'aktif',
        ]);

        $this->perizinan = Perizinan::factory()->create([
            'santri_id' => $this->santri->id,
            'qr_token' => 'TOKEN-IZIN-PULANG-123456',
            'status' => 'sedang_berlangsung',
            'tanggal_mulai' => now()->subHours(5),
            'tanggal_selesai' => now()->addHours(2),
            'tanggal_kembali_aktual' => null,
            'approved_by' => $this->kamtibUser->id,
        ]);
    }

    #[Test]
    public function santri_who_returns_must_scan_qr_again_as_proof_of_return_via_qr_token(): void
    {
        $kamtibService = app(KamtibService::class);

        // Simulasi scan QR kembali di Pos Kamtib menggunakan QR token surat izin
        $result = $kamtibService->scanSuratIzin($this->perizinan->qr_token, 'auto', $this->kamtibUser->id);

        $this->assertEquals('checkin', $result['action']);
        $this->assertEquals('selesai', $result['perizinan']->status);
        $this->assertNotNull($result['perizinan']->tanggal_kembali_aktual);

        // Verifikasi database: status selesai & tanggal_kembali_aktual terisi
        $this->perizinan->refresh();
        $this->assertEquals('selesai', $this->perizinan->status);
        $this->assertNotNull($this->perizinan->tanggal_kembali_aktual);

        // Verifikasi log kamtib tersimpan sebagai bukti sah
        $log = KamtibLog::where('perizinan_id', $this->perizinan->id)
            ->where('tipe_log', 'checkin')
            ->first();

        $this->assertNotNull($log);
        $this->assertStringContainsString('bukti kepulangan sah', strtolower($log->catatan));

        // Verifikasi tampilan Surat Izin Digital menampilkan bukti sah
        $response = $this->get(route('perizinan.surat', $this->perizinan->id));
        $response->assertOk();
        $response->assertSee('BUKTI KEPULANGAN SAH TERVERIFIKASI');
        $response->assertSee('VERIFIED RETURN');
    }

    #[Test]
    public function santri_who_returns_can_scan_using_nis_santri_if_paper_is_missing(): void
    {
        $kamtibService = app(KamtibService::class);

        // Simulasi scan QR kembali di Pos Kamtib menggunakan NIS santri (Kartu Santri PVC)
        $result = $kamtibService->scanSuratIzin($this->santri->nis, 'auto', $this->kamtibUser->id);

        $this->assertEquals('checkin', $result['action']);
        $this->assertEquals('selesai', $result['perizinan']->status);

        $this->perizinan->refresh();
        $this->assertEquals('selesai', $this->perizinan->status);
        $this->assertNotNull($this->perizinan->tanggal_kembali_aktual);
    }

    #[Test]
    public function kamtib_web_scan_surat_endpoint_processes_return_scan_properly(): void
    {
        $response = $this->post(route('kamtib.scan.process'), [
            'qr_token' => $this->perizinan->qr_token,
            'action' => 'auto',
            'catatan' => 'Kembali dalam keadaan sehat wal afiat',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->perizinan->refresh();
        $this->assertEquals('selesai', $this->perizinan->status);
        $this->assertNotNull($this->perizinan->tanggal_kembali_aktual);
    }

    #[Test]
    public function kamtib_web_scan_records_manual_nama_petugas(): void
    {
        $response = $this->post(route('kamtib.scan.process'), [
            'qr_token' => $this->perizinan->qr_token,
            'action' => 'auto',
            'nama_petugas' => 'Pak Supri (Satpam Gerbang Timur)',
            'catatan' => 'Kembali sehat',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('kamtib_logs', [
            'perizinan_id' => $this->perizinan->id,
            'nama_petugas' => 'Pak Supri (Satpam Gerbang Timur)',
        ]);
    }
}
