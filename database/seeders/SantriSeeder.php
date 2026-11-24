<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class SantriSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // 1. Ambil / Insert Asrama
        $asrama = DB::table('asramas')->first();
        $asramaId = $asrama ? $asrama->id : DB::table('asramas')->insertGetId([
            'nama_asrama' => 'Asrama Sunan Kalijaga',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 2. Ambil / Insert Kelas
        $kelas = DB::table('kelas')->first();
        $kelasId = $kelas ? $kelas->id : DB::table('kelas')->insertGetId([
            'nama_kelas' => 'Kelas 7A SMP',
            'tingkat' => '7 SMP',
            'jurusan' => 'Umum',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 3. Sesi Absensi (Lengkap dengan jam_mulai & jam_selesai)
        $sesiData = [
            'nama_sesi' => 'Shalat Subuh Berjamaah',
            'created_at' => now(),
            'updated_at' => now(),
        ];

        if (Schema::hasColumn('sesi_absensis', 'tanggal')) {
            $sesiData['tanggal'] = now()->toDateString();
        }
        if (Schema::hasColumn('sesi_absensis', 'jam_mulai')) {
            $sesiData['jam_mulai'] = '04:30:00';
        }
        if (Schema::hasColumn('sesi_absensis', 'jam_selesai')) {
            $sesiData['jam_selesai'] = '05:30:00';
        }
        if (Schema::hasColumn('sesi_absensis', 'waktu_mulai')) {
            $sesiData['waktu_mulai'] = '04:30:00';
        }
        if (Schema::hasColumn('sesi_absensis', 'waktu_selesai')) {
            $sesiData['waktu_selesai'] = '05:30:00';
        }

        DB::table('sesi_absensis')->updateOrInsert(
            ['id' => 1],
            $sesiData
        );

        // 4. Siapkan folder simpan QR Code
        Storage::disk('public')->makeDirectory('qr-santri');

        // 5. Insert Data Santri & Generate QR Code SVG
        $dataSantri = [
            ['nis' => '2026001', 'nama' => 'Ahmad Faqih', 'no_wa_wali' => '08123456789'],
            ['nis' => '2026002', 'nama' => 'Muhammad Rizqi', 'no_wa_wali' => '08129876543'],
        ];

        foreach ($dataSantri as $data) {
            $qrPath = 'qr-santri/'.$data['nis'].'.svg';

            QrCode::format('svg')
                ->size(300)
                ->margin(1)
                ->generate($data['nis'], storage_path('app/public/'.$qrPath));

            DB::table('santris')->updateOrInsert(
                ['nis' => $data['nis']],
                [
                    'nama' => $data['nama'],
                    'asrama_id' => $asramaId,
                    'kelas_id' => $kelasId,
                    'no_wa_wali' => $data['no_wa_wali'],
                    'qr_code_path' => $qrPath,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
