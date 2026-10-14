<?php

namespace Database\Seeders;

use App\Models\Absensi;
use App\Models\Asrama;
use App\Models\KamtibLog;
use App\Models\Kelas;
use App\Models\Perizinan;
use App\Models\Santri;
use App\Models\SesiAbsensi;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Buat Akun Admin & Petugas Kamtib/Pengasuhan
        $admin = User::factory()->create([
            'name' => 'Ustadz Administrator',
            'email' => 'admin@pesantren.test',
        ]);

        $petugasKamtib = User::factory()->create([
            'name' => 'Ustadz Keamanan & Ketertiban',
            'email' => 'kamtib@pesantren.test',
        ]);

        // 2. Buat Asrama Putra & Putri
        $asramaPutra1 = Asrama::factory()->putra()->create(['nama_asrama' => 'Asrama Al-Ghazali (Putra)']);
        $asramaPutra2 = Asrama::factory()->putra()->create(['nama_asrama' => 'Asrama Ibnu Sina (Putra)']);
        $asramaPutri1 = Asrama::factory()->putri()->create(['nama_asrama' => 'Asrama Siti Khadijah (Putri)']);
        $asramaPutri2 = Asrama::factory()->putri()->create(['nama_asrama' => 'Asrama Fatimah Az-Zahra (Putri)']);

        $asramas = collect([$asramaPutra1, $asramaPutra2, $asramaPutri1, $asramaPutri2]);

        // 3. Buat Rombongan Belajar / Kelas
        $kelas1 = Kelas::factory()->create(['nama_kelas' => '10 Keagamaan A', 'tingkat' => '10', 'jurusan' => 'Keagamaan']);
        $kelas2 = Kelas::factory()->create(['nama_kelas' => '11 Keagamaan A', 'tingkat' => '11', 'jurusan' => 'Keagamaan']);
        $kelas3 = Kelas::factory()->create(['nama_kelas' => '12 Tahfidz A', 'tingkat' => '12', 'jurusan' => 'Tahfidz Al-Qur\'an']);
        $kelas4 = Kelas::factory()->create(['nama_kelas' => '7 MTs Unggulan', 'tingkat' => '7', 'jurusan' => 'Madrasah Tsanawiyah']);

        $kelasList = collect([$kelas1, $kelas2, $kelas3, $kelas4]);

        // 4. Buat Santri
        $santriList = collect();

        foreach (range(1, 25) as $i) {
            $isPutra = $i % 2 === 1;
            $asrama = $isPutra ? ($i % 4 === 1 ? $asramaPutra1 : $asramaPutra2) : ($i % 4 === 2 ? $asramaPutri1 : $asramaPutri2);
            $kelas = $kelasList->random();

            $santri = Santri::factory()->create([
                'asrama_id' => $asrama->id,
                'kelas_id' => $kelas->id,
                'jenis_kelamin' => $isPutra ? 'L' : 'P',
                'status' => 'aktif',
            ]);

            $santriList->push($santri);
        }

        // 5. Buat Sesi Absensi
        $sesiSubuh = SesiAbsensi::factory()->create([
            'nama_sesi' => 'Sholat Subuh Berjamaah',
            'kode_sesi' => 'SBUH-'.date('Ymd'),
            'jenis' => 'sholat_berjamaah',
            'tanggal' => today()->toDateString(),
            'jam_mulai' => '04:30:00',
            'jam_selesai' => '05:30:00',
            'batas_terlambat' => '04:45:00',
            'status' => 'dibuka',
        ]);

        $sesiKajian = SesiAbsensi::factory()->create([
            'nama_sesi' => 'Kajian Kitab Kuning Ba\'da Maghrib',
            'kode_sesi' => 'KJ-MGB-'.date('Ymd'),
            'jenis' => 'pengajian_kitab',
            'tanggal' => today()->toDateString(),
            'jam_mulai' => '18:45:00',
            'jam_selesai' => '19:15:00',
            'batas_terlambat' => '18:55:00',
            'status' => 'dibuka',
        ]);

        // 6. Buat Record Absensi untuk Sesi Subuh
        foreach ($santriList as $santri) {
            $statusAbsen = fake()->randomElement(['hadir', 'hadir', 'hadir', 'hadir', 'terlambat', 'izin', 'sakit']);

            Absensi::factory()->create([
                'santri_id' => $santri->id,
                'sesi_absensi_id' => $sesiSubuh->id,
                'status' => $statusAbsen,
                'waktu_absen' => in_array($statusAbsen, ['hadir', 'terlambat']) ? now()->subMinutes(rand(10, 50)) : null,
                'metode' => in_array($statusAbsen, ['hadir', 'terlambat']) ? 'qr_code' : 'manual',
                'recorded_by' => $admin->id,
            ]);
        }

        // 7. Buat Riwayat Perizinan
        foreach ($santriList->take(6) as $santri) {
            Perizinan::factory()->create([
                'santri_id' => $santri->id,
                'approved_by' => $admin->id,
            ]);
        }

        // 8. Buat Log Kamtib (Keamanan & Ketertiban)
        foreach ($santriList->take(4) as $santri) {
            KamtibLog::factory()->create([
                'santri_id' => $santri->id,
                'recorded_by' => $petugasKamtib->id,
            ]);
        }
    }
}
