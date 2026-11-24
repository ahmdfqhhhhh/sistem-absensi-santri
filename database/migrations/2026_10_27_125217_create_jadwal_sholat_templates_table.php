<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tabel ini menyimpan template jam sesi harian (sholat 5 waktu + KBM)
     * yang dapat dikonfigurasi oleh Super Admin tanpa perlu mengubah kode.
     */
    public function up(): void
    {
        Schema::create('jadwal_sholat_templates', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 20)->unique()->comment('Kode unik sesi: SUBUH, DZUHUR, ASHAR, MAGHRIB, ISYA, KBM');
            $table->string('nama_sesi', 100);
            $table->enum('jenis', [
                'sholat_berjamaah',
                'kegiatan_belajar',
                'tahfidz',
                'apel_kebersihan',
                'pengajian_kitab',
                'lainnya',
            ])->default('sholat_berjamaah');
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->time('batas_terlambat')->nullable()->comment('Batas waktu dihitung tepat waktu, setelah itu = terlambat');
            $table->text('deskripsi')->nullable();
            $table->boolean('aktif')->default(true)->comment('Jika false, sesi ini tidak akan di-generate harian');
            $table->unsignedInteger('urutan')->default(0)->comment('Urutan tampil di scanner (0=Subuh, 1=KBM, dst)');
            $table->timestamps();
        });

        // Seed default jadwal sholat 5 waktu + KBM
        $defaults = [
            [
                'kode' => 'SUBUH',
                'nama_sesi' => 'Sholat Subuh Berjamaah',
                'jenis' => 'sholat_berjamaah',
                'jam_mulai' => '04:15:00',
                'jam_selesai' => '05:30:00',
                'batas_terlambat' => '04:45:00',
                'deskripsi' => 'Presensi sholat Subuh berjamaah santri di Masjid Utama',
                'aktif' => true,
                'urutan' => 0,
            ],
            [
                'kode' => 'KBM',
                'nama_sesi' => 'Presensi Masuk Sekolah / KBM Madrasah',
                'jenis' => 'kegiatan_belajar',
                'jam_mulai' => '06:30:00',
                'jam_selesai' => '07:30:00',
                'batas_terlambat' => '07:00:00',
                'deskripsi' => 'Presensi masuk kelas KBM Madrasah pagi (Maksimal jam 07:00 WIB)',
                'aktif' => true,
                'urutan' => 1,
            ],
            [
                'kode' => 'DZUHUR',
                'nama_sesi' => 'Sholat Dzuhur Berjamaah',
                'jenis' => 'sholat_berjamaah',
                'jam_mulai' => '11:45:00',
                'jam_selesai' => '13:00:00',
                'batas_terlambat' => '12:15:00',
                'deskripsi' => 'Presensi sholat Dzuhur berjamaah santri dan asatidz',
                'aktif' => true,
                'urutan' => 2,
            ],
            [
                'kode' => 'ASHAR',
                'nama_sesi' => 'Sholat Ashar Berjamaah',
                'jenis' => 'sholat_berjamaah',
                'jam_mulai' => '15:00:00',
                'jam_selesai' => '16:15:00',
                'batas_terlambat' => '15:30:00',
                'deskripsi' => 'Presensi sholat Ashar berjamaah di Masjid Pesantren',
                'aktif' => true,
                'urutan' => 3,
            ],
            [
                'kode' => 'MAGHRIB',
                'nama_sesi' => 'Sholat Maghrib Berjamaah',
                'jenis' => 'sholat_berjamaah',
                'jam_mulai' => '17:45:00',
                'jam_selesai' => '19:00:00',
                'batas_terlambat' => '18:15:00',
                'deskripsi' => 'Presensi sholat Maghrib dan wirid berjamaah',
                'aktif' => true,
                'urutan' => 4,
            ],
            [
                'kode' => 'ISYA',
                'nama_sesi' => 'Sholat Isya Berjamaah',
                'jenis' => 'sholat_berjamaah',
                'jam_mulai' => '19:15:00',
                'jam_selesai' => '20:30:00',
                'batas_terlambat' => '19:45:00',
                'deskripsi' => 'Presensi sholat Isya berjamaah santri',
                'aktif' => true,
                'urutan' => 5,
            ],
        ];

        foreach ($defaults as $row) {
            DB::table('jadwal_sholat_templates')->insert(array_merge($row, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jadwal_sholat_templates');
    }
};
