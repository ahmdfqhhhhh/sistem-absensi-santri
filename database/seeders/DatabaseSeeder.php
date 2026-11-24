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
        // 1. Buat Akun Pengguna Sesuai 4 Peran Pengguna di PRD
        $admin = User::factory()->superAdmin()->create([
            'name' => 'Ustadz Super Admin (Pengurus Pusat)',
            'email' => 'admin@pesantren.test',
        ]);

        $musyrif = User::factory()->musyrif()->create([
            'name' => 'Ustadz Ahmad Musyrif',
            'email' => 'musyrif@pesantren.test',
        ]);

        $petugasKamtib = User::factory()->keamanan()->create([
            'name' => 'Ustadz Hasan (Pengurus Keamanan/Kamtib)',
            'email' => 'kamtib@pesantren.test',
        ]);

        $waliSantri = User::factory()->waliSantri()->create([
            'name' => 'Bapak Rahmat (Wali Santri)',
            'email' => 'wali@pesantren.test',
        ]);

        // 2. Buat Asrama Putra & Putri
        $asramaPutra1 = Asrama::factory()->putra()->create(['nama_asrama' => 'Asrama Al-Ghazali (Putra)']);
        $asramaPutra2 = Asrama::factory()->putra()->create(['nama_asrama' => 'Asrama Ibnu Sina (Putra)']);
        $asramaPutri1 = Asrama::factory()->putri()->create(['nama_asrama' => 'Asrama Siti Khadijah (Putri)']);
        $asramaPutri2 = Asrama::factory()->putri()->create(['nama_asrama' => 'Asrama Fatimah Az-Zahra (Putri)']);

        $asramas = collect([$asramaPutra1, $asramaPutra2, $asramaPutri1, $asramaPutri2]);

        // 3. Buat Rombongan Belajar / Kelas (SMP, SMA, SMK dipisahkan)
        $kelas1 = Kelas::factory()->create(['nama_kelas' => 'Kelas 7A SMP', 'tingkat' => '7 SMP', 'jurusan' => 'Umum']);
        $kelas2 = Kelas::factory()->create(['nama_kelas' => 'Kelas 8A SMP', 'tingkat' => '8 SMP', 'jurusan' => 'Umum']);
        $kelas3 = Kelas::factory()->create(['nama_kelas' => 'Kelas 9A SMP', 'tingkat' => '9 SMP', 'jurusan' => 'Umum']);
        $kelas4 = Kelas::factory()->create(['nama_kelas' => '10 SMA IPA', 'tingkat' => '10 SMA', 'jurusan' => 'IPA']);
        $kelas5 = Kelas::factory()->create(['nama_kelas' => '11 SMA IPS', 'tingkat' => '11 SMA', 'jurusan' => 'IPS']);
        $kelas6 = Kelas::factory()->create(['nama_kelas' => '10 SMK TKJ', 'tingkat' => '10 SMK', 'jurusan' => 'Teknik Komputer & Jaringan']);
        $kelas7 = Kelas::factory()->create(['nama_kelas' => '12 SMK RPL', 'tingkat' => '12 SMK', 'jurusan' => 'Rekayasa Perangkat Lunak']);

        $kelasList = collect([$kelas1, $kelas2, $kelas3, $kelas4, $kelas5, $kelas6, $kelas7]);

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

        // 5. Buat Sesi Absensi Rutin Harian (Sholat 5 Waktu & KBM Sekolah Pagi Max 07:00)
        $dailySessions = SesiAbsensi::ensureDailySessions();
        $sesiSubuh = $dailySessions->firstWhere('nama_sesi', 'Sholat Subuh Berjamaah');
        $sesiSekolah = $dailySessions->firstWhere('nama_sesi', 'Presensi Masuk Sekolah / KBM Madrasah');

        // 6. Buat Record Absensi Realistis untuk Sesi Subuh & KBM Sekolah
        foreach ($santriList as $santri) {
            $statusAbsen = fake()->randomElement(['hadir', 'hadir', 'hadir', 'hadir', 'terlambat', 'izin', 'sakit']);

            if ($sesiSubuh) {
                Absensi::factory()->create([
                    'santri_id' => $santri->id,
                    'sesi_absensi_id' => $sesiSubuh->id,
                    'status' => $statusAbsen,
                    'waktu_absen' => in_array($statusAbsen, ['hadir', 'terlambat']) ? now()->subMinutes(rand(10, 50)) : null,
                    'metode' => in_array($statusAbsen, ['hadir', 'terlambat']) ? 'qr_code' : 'manual',
                    'recorded_by' => $admin->id,
                ]);
            }

            if ($sesiSekolah) {
                $statusSekolah = fake()->randomElement(['hadir', 'hadir', 'hadir', 'terlambat', 'izin']);
                Absensi::factory()->create([
                    'santri_id' => $santri->id,
                    'sesi_absensi_id' => $sesiSekolah->id,
                    'status' => $statusSekolah,
                    'waktu_absen' => in_array($statusSekolah, ['hadir', 'terlambat']) ? now()->subMinutes(rand(5, 30)) : null,
                    'metode' => 'qr_code',
                    'recorded_by' => $admin->id,
                    'keterangan' => $statusSekolah === 'terlambat' ? 'Lewat batas waktu 07:00 WIB' : 'Tepat waktu sebelum 07:00 WIB',
                ]);
            }
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
