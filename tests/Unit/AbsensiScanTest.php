<?php

namespace Tests\Unit;

use App\Exceptions\AbsensiException;
use App\Http\Controllers\Api\AbsensiController;
use App\Models\Absensi;
use App\Models\Asrama;
use App\Models\Kelas;
use App\Models\Perizinan;
use App\Models\Santri;
use App\Models\SesiAbsensi;
use App\Services\AbsensiService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class AbsensiScanTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function scan_qr_fails_when_parameters_are_missing(): void
    {
        $service = Mockery::mock(AbsensiService::class);
        $controller = new AbsensiController($service);

        $request = Request::create('/api/absensi/scan-qr', 'POST', []);

        $response = $controller->scanQr($request);

        $this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);
        $this->assertFalse($content['success']);
        $this->assertArrayHasKey('errors', $content);
        $this->assertArrayHasKey('nis', $content['errors']);
        $this->assertArrayHasKey('sesi_id', $content['errors']);
    }

    #[Test]
    public function scan_qr_returns_success_with_attendance_resource_and_performance_meta(): void
    {
        $santri = new Santri([
            'nis' => 'SAN-2026-0001',
            'nama' => 'Ahmad Santri',
            'jenis_kelamin' => 'L',
        ]);
        $santri->id = 1;
        $santri->setRelation('asrama', new Asrama(['nama_asrama' => 'Asrama Al-Ghazali']));
        $santri->setRelation('kelas', new Kelas(['nama_kelas' => '10 Keagamaan A']));

        $sesi = new SesiAbsensi([
            'nama_sesi' => 'Sholat Subuh Berjamaah',
            'kode_sesi' => 'SBUH-20261014',
            'jenis' => 'sholat_berjamaah',
            'tanggal' => '2026-10-14',
            'jam_mulai' => '04:30:00',
            'jam_selesai' => '05:30:00',
            'batas_terlambat' => '04:45:00',
            'status' => 'dibuka',
        ]);
        $sesi->id = 10;

        $absensi = new Absensi([
            'santri_id' => 1,
            'sesi_absensi_id' => 10,
            'waktu_absen' => Carbon::parse('2026-10-14 04:35:00'),
            'status' => 'hadir',
            'metode' => 'qr_code',
            'keterangan' => 'Hadir tepat waktu melalui pemindaian QR',
        ]);
        $absensi->id = 99;
        $absensi->setRelation('santri', $santri);
        $absensi->setRelation('sesiAbsensi', $sesi);

        $service = Mockery::mock(AbsensiService::class);
        $service->shouldReceive('scanQr')
            ->once()
            ->with('SAN-2026-0001', 10, null)
            ->andReturn([
                'absensi' => $absensi,
                'is_izin_aktif' => false,
                'perizinan_aktif' => null,
                'processing_time_ms' => 12.34,
            ]);

        $controller = new AbsensiController($service);

        $request = Request::create('/api/absensi/scan-qr', 'POST', [
            'nis' => 'SAN-2026-0001',
            'sesi_id' => 10,
        ]);

        $response = $controller->scanQr($request);

        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);
        $this->assertTrue($content['success']);
        $this->assertEquals('hadir', $content['data']['status']);
        $this->assertEquals('Hadir', $content['data']['status_label']);
        $this->assertEquals('SAN-2026-0001', $content['data']['santri']['nis']);
        $this->assertLessThan(500, $content['meta']['waktu_eksekusi_ms']);
    }

    #[Test]
    public function scan_qr_automatically_marks_status_when_active_permission_exists(): void
    {
        $santri = new Santri([
            'nis' => 'SAN-2026-0002',
            'nama' => 'Fathur Rahman',
            'jenis_kelamin' => 'L',
        ]);
        $santri->id = 2;
        $santri->setRelation('asrama', new Asrama(['nama_asrama' => 'Asrama Ibnu Sina']));
        $santri->setRelation('kelas', new Kelas(['nama_kelas' => '11 IPA 1']));

        $sesi = new SesiAbsensi([
            'nama_sesi' => 'Sholat Ashar Berjamaah',
            'kode_sesi' => 'ASR-20261014',
            'status' => 'dibuka',
        ]);
        $sesi->id = 15;

        $activeIzin = new Perizinan([
            'santri_id' => 2,
            'nomor_surat' => 'IZN/2026/10/0045',
            'jenis_izin' => 'sakit',
            'keperluan' => 'Demam tinggi istirahat di poskestren',
            'status' => 'disetujui',
        ]);
        $activeIzin->id = 5;

        $absensi = new Absensi([
            'santri_id' => 2,
            'sesi_absensi_id' => 15,
            'waktu_absen' => now(),
            'status' => 'sakit',
            'metode' => 'qr_code',
            'keterangan' => 'Otomatis dari Perizinan Aktif: Sakit (IZN/2026/10/0045) - Demam tinggi istirahat di poskestren',
        ]);
        $absensi->id = 101;
        $absensi->setRelation('santri', $santri);
        $absensi->setRelation('sesiAbsensi', $sesi);

        $service = Mockery::mock(AbsensiService::class);
        $service->shouldReceive('scanQr')
            ->once()
            ->with('SAN-2026-0002', 15, null)
            ->andReturn([
                'absensi' => $absensi,
                'is_izin_aktif' => true,
                'perizinan_aktif' => $activeIzin,
                'processing_time_ms' => 18.25,
            ]);

        $controller = new AbsensiController($service);

        $request = Request::create('/api/absensi/scan-qr', 'POST', [
            'nis' => 'SAN-2026-0002',
            'sesi_id' => 15,
        ]);

        $response = $controller->scanQr($request);

        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);
        $this->assertTrue($content['success']);
        $this->assertTrue($content['meta']['status_otomatis_izin']);
        $this->assertEquals('sakit', $content['data']['status']);
        $this->assertEquals('IZN/2026/10/0045', $content['meta']['perizinan_aktif']['nomor_surat']);
        $this->assertStringContainsString('Presensi otomatis dialihkan menjadi status', $content['message']);
        $this->assertLessThan(500, $content['meta']['waktu_eksekusi_ms']);
    }

    #[Test]
    public function scan_qr_returns_not_found_when_santri_does_not_exist(): void
    {
        $service = Mockery::mock(AbsensiService::class);
        $service->shouldReceive('scanQr')
            ->once()
            ->with('NON-EXISTENT', 1, null)
            ->andThrow(new AbsensiException("Santri dengan NIS 'NON-EXISTENT' tidak ditemukan.", Response::HTTP_NOT_FOUND));

        $controller = new AbsensiController($service);

        $request = Request::create('/api/absensi/scan-qr', 'POST', [
            'nis' => 'NON-EXISTENT',
            'sesi_id' => 1,
        ]);

        $response = $controller->scanQr($request);

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);
        $this->assertFalse($content['success']);
        $this->assertStringContainsString('tidak ditemukan', $content['message']);
        $this->assertLessThan(500, $content['meta']['waktu_eksekusi_ms']);
    }

    #[Test]
    public function scan_qr_returns_conflict_when_double_scan(): void
    {
        $service = Mockery::mock(AbsensiService::class);
        $service->shouldReceive('scanQr')
            ->once()
            ->with('SAN-2026-0001', 1, null)
            ->andThrow(new AbsensiException('Santri sudah tercatat presensi pada sesi ini.', Response::HTTP_CONFLICT));

        $controller = new AbsensiController($service);

        $request = Request::create('/api/absensi/scan-qr', 'POST', [
            'nis' => 'SAN-2026-0001',
            'sesi_id' => 1,
        ]);

        $response = $controller->scanQr($request);

        $this->assertEquals(Response::HTTP_CONFLICT, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);
        $this->assertFalse($content['success']);
        $this->assertStringContainsString('sudah tercatat presensi', $content['message']);
    }
}
