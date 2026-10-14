<?php

namespace Tests\Feature;

use App\Models\Asrama;
use App\Models\Kelas;
use App\Models\Santri;
use App\Models\SesiAbsensi;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AbsensiWebControllerTest extends TestCase
{
    use DatabaseTransactions;

    #[Test]
    public function root_url_renders_absensi_scan_view_with_active_session(): void
    {
        $sesi = SesiAbsensi::factory()->create([
            'nama_sesi' => 'Sesi Uji Coba Pagi',
            'status' => 'dibuka',
            'tanggal' => today(),
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertViewIs('absensi.scan');
        $response->assertViewHas('sesiAktif');
        $response->assertViewHas('daftarSesi');
        $response->assertViewHas('stats');
        $response->assertViewHas('recentAbsensis');
        $response->assertSee('Sesi Uji Coba Pagi');
    }

    #[Test]
    public function scan_url_renders_absensi_scan_view_with_active_session(): void
    {
        $sesi = SesiAbsensi::factory()->create([
            'nama_sesi' => 'Sesi Uji Coba Scan URL',
            'status' => 'dibuka',
            'tanggal' => today(),
        ]);

        $response = $this->get('/scan');

        $response->assertStatus(200);
        $response->assertViewIs('absensi.scan');
        $response->assertViewHas('sesiAktif');
        $response->assertSee('Sesi Uji Coba Scan URL');
    }

    #[Test]
    public function scan_url_can_filter_by_specific_sesi_id(): void
    {
        $sesi1 = SesiAbsensi::factory()->create([
            'nama_sesi' => 'Sesi Pertama',
            'status' => 'dibuka',
            'tanggal' => today(),
        ]);

        $sesi2 = SesiAbsensi::factory()->create([
            'nama_sesi' => 'Sesi Kedua Tertarget',
            'status' => 'dibuka',
            'tanggal' => today(),
        ]);

        $response = $this->get('/scan?sesi_id='.$sesi2->id);

        $response->assertStatus(200);
        $response->assertViewIs('absensi.scan');
        $response->assertViewHas('sesiAktif', function ($viewSesi) use ($sesi2) {
            return $viewSesi && $viewSesi->id === $sesi2->id;
        });
        $response->assertSee('Sesi Kedua Tertarget');
    }

    #[Test]
    public function post_scan_handles_attendance_via_web_route(): void
    {
        $asrama = Asrama::factory()->create();
        $kelas = Kelas::factory()->create();

        $santri = Santri::factory()->create([
            'asrama_id' => $asrama->id,
            'kelas_id' => $kelas->id,
            'nis' => 'TESTNIS999',
            'status' => 'aktif',
        ]);

        $sesi = SesiAbsensi::factory()->create([
            'status' => 'dibuka',
            'tanggal' => today(),
            'batas_terlambat' => '23:59:59',
        ]);

        $response = $this->postJson('/scan', [
            'nis' => $santri->nis,
            'sesi_id' => $sesi->id,
        ]);

        $response->assertStatus(201);
        $response->assertJson([
            'success' => true,
            'data' => [
                'status' => 'hadir',
                'santri' => [
                    'nis' => 'TESTNIS999',
                ],
            ],
        ]);
    }
}
