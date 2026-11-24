<?php

namespace Tests\Feature;

use App\Models\JadwalSholatTemplate;
use App\Models\SesiAbsensi;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class JadwalSholatTest extends TestCase
{
    use DatabaseTransactions;

    protected User $superAdmin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->superAdmin = User::factory()->superAdmin()->create();
    }

    #[Test]
    public function super_admin_can_access_jadwal_sholat_settings_page(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('jadwal.index'));

        $response->assertOk();
        $response->assertViewIs('jadwal.index');
        $response->assertSee('Pengaturan Jadwal Sholat');
        $response->assertSee('Sholat Subuh Berjamaah');
    }

    #[Test]
    public function guest_cannot_access_jadwal_sholat_settings(): void
    {
        $response = $this->get(route('jadwal.index'));

        $response->assertRedirect(route('login'));
    }

    #[Test]
    public function wali_santri_cannot_access_jadwal_sholat_settings(): void
    {
        $wali = User::factory()->waliSantri()->create();

        $response = $this->actingAs($wali)->get(route('jadwal.index'));

        $response->assertRedirect(route('wali.portal'));
        $response->assertSessionHas('error');
    }

    #[Test]
    public function super_admin_can_update_prayer_times_and_sync_with_today_sessions(): void
    {
        // Pastikan sesi hari ini digenerate terlebih dahulu
        SesiAbsensi::ensureDailySessions();

        $subuhTemplate = JadwalSholatTemplate::where('kode', 'SUBUH')->firstOrFail();
        $kbmTemplate = JadwalSholatTemplate::where('kode', 'KBM')->firstOrFail();
        $dzuhurTemplate = JadwalSholatTemplate::where('kode', 'DZUHUR')->firstOrFail();
        $asharTemplate = JadwalSholatTemplate::where('kode', 'ASHAR')->firstOrFail();
        $maghribTemplate = JadwalSholatTemplate::where('kode', 'MAGHRIB')->firstOrFail();
        $isyaTemplate = JadwalSholatTemplate::where('kode', 'ISYA')->firstOrFail();

        $payload = [
            'apply_to_today' => 1,
            'jadwal' => [
                [
                    'id' => $subuhTemplate->id,
                    'nama_sesi' => 'Sholat Subuh Berjamaah Baru',
                    'jam_mulai' => '04:30',
                    'jam_selesai' => '05:45',
                    'batas_terlambat' => '05:00',
                    'aktif' => 1,
                ],
                [
                    'id' => $kbmTemplate->id,
                    'nama_sesi' => $kbmTemplate->nama_sesi,
                    'jam_mulai' => '06:45',
                    'jam_selesai' => '07:45',
                    'batas_terlambat' => '07:15',
                    'aktif' => 1,
                ],
                [
                    'id' => $dzuhurTemplate->id,
                    'nama_sesi' => $dzuhurTemplate->nama_sesi,
                    'jam_mulai' => '12:00',
                    'jam_selesai' => '13:15',
                    'batas_terlambat' => '12:30',
                    'aktif' => 1,
                ],
                [
                    'id' => $asharTemplate->id,
                    'nama_sesi' => $asharTemplate->nama_sesi,
                    'jam_mulai' => '15:15',
                    'jam_selesai' => '16:30',
                    'batas_terlambat' => '15:45',
                    'aktif' => 1,
                ],
                [
                    'id' => $maghribTemplate->id,
                    'nama_sesi' => $maghribTemplate->nama_sesi,
                    'jam_mulai' => '18:00',
                    'jam_selesai' => '19:15',
                    'batas_terlambat' => '18:30',
                    'aktif' => 1,
                ],
                [
                    'id' => $isyaTemplate->id,
                    'nama_sesi' => $isyaTemplate->nama_sesi,
                    'jam_mulai' => '19:30',
                    'jam_selesai' => '20:45',
                    'batas_terlambat' => '20:00',
                    'aktif' => 1,
                ],
            ],
        ];

        $response = $this->actingAs($this->superAdmin)->put(route('jadwal.update'), $payload);

        $response->assertRedirect(route('jadwal.index'));
        $response->assertSessionHas('success');

        // Pastikan template di DB berubah
        $subuhTemplate->refresh();
        $this->assertEquals('04:30:00', $subuhTemplate->jam_mulai);
        $this->assertEquals('05:00:00', $subuhTemplate->batas_terlambat);
        $this->assertEquals('Sholat Subuh Berjamaah Baru', $subuhTemplate->nama_sesi);

        // Pastikan sesi hari ini tersinkronisasi
        $dateCode = str_replace('-', '', today()->toDateString());
        $subuhHariIni = SesiAbsensi::where('tanggal', today()->toDateString())
            ->where('kode_sesi', 'SBH-'.$dateCode)
            ->first();

        $this->assertNotNull($subuhHariIni);
        $this->assertEquals('04:30:00', $subuhHariIni->jam_mulai);
        $this->assertEquals('05:00:00', $subuhHariIni->batas_terlambat);
        $this->assertEquals('Sholat Subuh Berjamaah Baru', $subuhHariIni->nama_sesi);
    }

    #[Test]
    public function super_admin_can_reset_prayer_times_to_defaults(): void
    {
        // Ubah jam Subuh terlebih dahulu
        JadwalSholatTemplate::where('kode', 'SUBUH')->update([
            'jam_mulai' => '05:00:00',
            'jam_selesai' => '06:00:00',
        ]);

        $response = $this->actingAs($this->superAdmin)->post(route('jadwal.reset'));

        $response->assertRedirect(route('jadwal.index'));
        $response->assertSessionHas('success');

        $subuh = JadwalSholatTemplate::where('kode', 'SUBUH')->firstOrFail();
        $this->assertEquals('04:15:00', $subuh->jam_mulai);
        $this->assertEquals('05:30:00', $subuh->jam_selesai);
    }
}
