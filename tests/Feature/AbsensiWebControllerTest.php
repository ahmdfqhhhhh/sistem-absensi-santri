<?php

namespace Tests\Feature;

use App\Models\Asrama;
use App\Models\Kelas;
use App\Models\Santri;
use App\Models\SesiAbsensi;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AbsensiWebControllerTest extends TestCase
{
    use DatabaseTransactions;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->musyrif()->create();
        $this->actingAs($this->user);
    }

    #[Test]
    public function root_url_renders_absensi_scan_view_with_active_session(): void
    {
        $sesi = SesiAbsensi::factory()->create([
            'nama_sesi' => 'Sesi Uji Coba Pagi',
            'status' => 'dibuka',
            'tanggal' => today(),
        ]);

        $response = $this->get('/scan');

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

    #[Test]
    public function daily_prayer_and_school_sessions_are_automatically_available(): void
    {
        $response = $this->get('/scan');

        $response->assertOk();
        $response->assertSee('Sholat Subuh Berjamaah');
        $response->assertSee('Presensi Masuk Sekolah / KBM Madrasah');
        $response->assertSee('Sholat Dzuhur Berjamaah');
        $response->assertSee('Sholat Ashar Berjamaah');
        $response->assertSee('Sholat Maghrib Berjamaah');
        $response->assertSee('Sholat Isya Berjamaah');
        $sesiSekolah = SesiAbsensi::where('jenis', 'kegiatan_belajar')
            ->whereDate('tanggal', today())
            ->first();

        $this->assertNotNull($sesiSekolah);
        $this->assertEquals('07:00:00', $sesiSekolah->batas_terlambat);

        $responseSekolah = $this->get('/scan?sesi_id='.$sesiSekolah->id);
        $responseSekolah->assertSee('07:00');
    }

    #[Test]
    public function school_attendance_evaluates_late_when_scanned_after_07_00(): void
    {
        $santri = Santri::factory()->create(['status' => 'aktif']);
        $sesiSekolah = SesiAbsensi::ensureDailySessions()->firstWhere('jenis', 'kegiatan_belajar');

        // Simulasikan scan jam 07:15:00 (lewat batas 07:00:00)
        Carbon::setTestNow(today()->setTime(7, 15, 0));

        $response = $this->postJson('/scan', [
            'nis' => $santri->nis,
            'sesi_id' => $sesiSekolah->id,
        ]);

        $response->assertStatus(201);
        $response->assertJson([
            'success' => true,
            'data' => [
                'status' => 'terlambat',
            ],
        ]);

        // Simulasikan santri lain scan jam 06:45:00 (sebelum batas 07:00:00)
        Carbon::setTestNow(today()->setTime(6, 45, 0));
        $santri2 = Santri::factory()->create(['status' => 'aktif']);

        $response2 = $this->postJson('/scan', [
            'nis' => $santri2->nis,
            'sesi_id' => $sesiSekolah->id,
        ]);

        $response2->assertStatus(201);
        $response2->assertJson([
            'success' => true,
            'data' => [
                'status' => 'hadir',
            ],
        ]);

        Carbon::setTestNow(); // Reset clock
    }

    #[Test]
    public function batch_index_filters_santri_by_name_or_nis_search(): void
    {
        $asrama = Asrama::factory()->create();
        $kelas = Kelas::factory()->create();

        $santri1 = Santri::factory()->create([
            'asrama_id' => $asrama->id,
            'kelas_id' => $kelas->id,
            'nama' => 'Ahmad Sholihin',
            'nis' => 'NIS-AHMAD-01',
            'status' => 'aktif',
        ]);

        $santri2 = Santri::factory()->create([
            'asrama_id' => $asrama->id,
            'kelas_id' => $kelas->id,
            'nama' => 'Zaenal Muttaqin',
            'nis' => 'NIS-ZAENAL-02',
            'status' => 'aktif',
        ]);

        // Cari berdasarkan nama
        $response = $this->get('/absensi/batch?search=Sholihin');

        $response->assertOk();
        $response->assertViewIs('absensi.batch');
        $response->assertSee('Ahmad Sholihin');
        $response->assertDontSee('Zaenal Muttaqin');

        // Cari berdasarkan NIS
        $responseNis = $this->get('/absensi/batch?search=ZAENAL-02');

        $responseNis->assertOk();
        $responseNis->assertSee('Zaenal Muttaqin');
        $responseNis->assertDontSee('Ahmad Sholihin');
    }
}
