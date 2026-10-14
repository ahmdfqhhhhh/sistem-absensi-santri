<?php

namespace Tests\Unit;

use App\Exceptions\AbsensiException;
use App\Http\Controllers\Api\KamtibController;
use App\Http\Requests\Api\ScanSuratIzinRequest;
use App\Models\Asrama;
use App\Models\KamtibLog;
use App\Models\Kelas;
use App\Models\Perizinan;
use App\Models\Santri;
use App\Models\User;
use App\Services\KamtibService;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class KamtibScanTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function scan_surat_izin_fails_when_qr_data_is_empty(): void
    {
        $service = Mockery::mock(KamtibService::class);
        $controller = new KamtibController($service);

        $request = ScanSuratIzinRequest::create('/api/kamtib/scan-surat-izin', 'POST', []);

        $response = $controller->scanSuratIzin($request);

        $this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $response->getStatusCode());
        $content = json_decode($response->getContent(), true);

        $this->assertFalse($content['success']);
        $this->assertArrayHasKey('qr_data', $content['errors']);
    }

    #[Test]
    public function scan_surat_izin_processes_checkout_successfully(): void
    {
        $santri = new Santri([
            'nis' => 'SAN-2026-0033',
            'nama' => 'Irfan Maulana',
        ]);
        $santri->id = 33;
        $santri->setRelation('asrama', new Asrama(['nama_asrama' => 'Asrama Al-Ghazali']));
        $santri->setRelation('kelas', new Kelas(['nama_kelas' => '10 IPA 1']));

        $perizinan = new Perizinan([
            'santri_id' => 33,
            'nomor_surat' => 'IZN/2026/10/0033',
            'status' => 'sedang_berlangsung',
            'qr_token' => 'IZN-CHECKOUT-TOKEN-123',
            'jenis_izin' => 'pulang',
            'tanggal_mulai' => now(),
            'tanggal_selesai' => now()->addDays(2),
        ]);
        $perizinan->id = 14;
        $perizinan->setRelation('santri', $santri);

        $kamtibLog = new KamtibLog([
            'santri_id' => 33,
            'perizinan_id' => 14,
            'tipe_log' => 'checkout',
            'waktu_log' => now(),
            'judul_pelanggaran' => 'Checkout Gerbang (Keluar Pondok): IZN/2026/10/0033',
            'kategori' => 'ringan',
            'poin_pelanggaran' => 0,
            'lokasi_kejadian' => 'Pos Gerbang Keamanan Utama',
            'status_penyelesaian' => 'selesai',
        ]);
        $kamtibLog->id = 55;
        $kamtibLog->setRelation('santri', $santri);
        $kamtibLog->setRelation('perizinan', $perizinan);

        $service = Mockery::mock(KamtibService::class);
        $service->shouldReceive('scanSuratIzin')
            ->once()
            ->with('IZN-CHECKOUT-TOKEN-123', 'auto', 1, null, null)
            ->andReturn([
                'action' => 'checkout',
                'is_terlambat' => false,
                'kamtib_log' => $kamtibLog,
                'perizinan' => $perizinan,
            ]);

        $controller = new KamtibController($service);

        $petugas = new User(['name' => 'Petugas Satpam Pos 1']);
        $petugas->id = 1;

        $request = ScanSuratIzinRequest::create('/api/kamtib/scan-surat-izin', 'POST', [
            'qr_data' => 'IZN-CHECKOUT-TOKEN-123',
            'petugas_id' => 1,
        ]);
        $request->setUserResolver(fn () => $petugas);

        $response = $controller->scanSuratIzin($request);

        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        $content = json_decode($response->getContent(), true);

        $this->assertTrue($content['success']);
        $this->assertEquals('checkout', $content['action']);
        $this->assertEquals('checkout', $content['data']['kamtib_log']['tipe_log']);
        $this->assertEquals('sedang_berlangsung', $content['data']['perizinan']['status']);
        $this->assertFalse($content['meta']['is_terlambat']);
    }

    #[Test]
    public function scan_surat_izin_processes_checkin_late_with_disciplinary_points(): void
    {
        $santri = new Santri([
            'nis' => 'SAN-2026-0044',
            'nama' => 'Rizky Ramadhan',
        ]);
        $santri->id = 44;
        $santri->setRelation('asrama', new Asrama(['nama_asrama' => 'Asrama Ibnu Sina']));
        $santri->setRelation('kelas', new Kelas(['nama_kelas' => '11 IPS 2']));

        $perizinan = new Perizinan([
            'santri_id' => 44,
            'nomor_surat' => 'IZN/2026/10/0044',
            'status' => 'terlambat_kembali',
            'qr_token' => 'IZN-LATE-TOKEN-44',
            'tanggal_mulai' => now()->subDays(3),
            'tanggal_selesai' => now()->subHours(4),
            'tanggal_kembali_aktual' => now(),
        ]);
        $perizinan->id = 20;
        $perizinan->setRelation('santri', $santri);

        $kamtibLog = new KamtibLog([
            'santri_id' => 44,
            'perizinan_id' => 20,
            'tipe_log' => 'checkin',
            'waktu_log' => now(),
            'judul_pelanggaran' => 'Terlambat Kembali ke Pondok: IZN/2026/10/0044 (Terlambat 240 menit)',
            'kategori' => 'berat',
            'poin_pelanggaran' => 25,
            'status_penyelesaian' => 'dalam_proses',
            'tindakan_sanksi' => 'Takzir kebersihan lingkungan pondok',
        ]);
        $kamtibLog->id = 77;
        $kamtibLog->setRelation('santri', $santri);
        $kamtibLog->setRelation('perizinan', $perizinan);

        $service = Mockery::mock(KamtibService::class);
        $service->shouldReceive('scanSuratIzin')
            ->once()
            ->with('IZN-LATE-TOKEN-44', 'checkin', null, 'Pos Barat', 'Santri dijemput orang tua')
            ->andReturn([
                'action' => 'checkin',
                'is_terlambat' => true,
                'kamtib_log' => $kamtibLog,
                'perizinan' => $perizinan,
            ]);

        $controller = new KamtibController($service);

        $request = ScanSuratIzinRequest::create('/api/kamtib/scan-surat-izin', 'POST', [
            'qr_data' => 'IZN-LATE-TOKEN-44',
            'action' => 'checkin',
            'lokasi' => 'Pos Barat',
            'catatan' => 'Santri dijemput orang tua',
        ]);

        $response = $controller->scanSuratIzin($request);

        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        $content = json_decode($response->getContent(), true);

        $this->assertTrue($content['success']);
        $this->assertEquals('checkin', $content['action']);
        $this->assertTrue($content['meta']['is_terlambat']);
        $this->assertEquals('terlambat_kembali', $content['data']['perizinan']['status']);
        $this->assertEquals(25, $content['data']['kamtib_log']['poin_pelanggaran']);
        $this->assertStringContainsString('TERLAMBAT', $content['message']);
    }

    #[Test]
    public function scan_surat_izin_returns_not_found_when_token_is_invalid(): void
    {
        $service = Mockery::mock(KamtibService::class);
        $service->shouldReceive('scanSuratIzin')
            ->once()
            ->with('TOKEN-INVALID', 'auto', null, null, null)
            ->andThrow(new AbsensiException('QR Code Surat Izin Digital tidak valid atau tidak terdaftar di sistem.', Response::HTTP_NOT_FOUND));

        $controller = new KamtibController($service);

        $request = ScanSuratIzinRequest::create('/api/kamtib/scan-surat-izin', 'POST', [
            'qr_data' => 'TOKEN-INVALID',
        ]);

        $response = $controller->scanSuratIzin($request);

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $content = json_decode($response->getContent(), true);

        $this->assertFalse($content['success']);
        $this->assertStringContainsString('tidak valid', $content['message']);
    }
}
