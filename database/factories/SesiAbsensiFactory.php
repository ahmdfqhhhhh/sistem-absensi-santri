<?php

namespace Database\Factories;

use App\Models\SesiAbsensi;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SesiAbsensi>
 */
class SesiAbsensiFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $sesiList = [
            ['nama' => 'Sholat Subuh Berjamaah', 'jenis' => 'sholat_berjamaah', 'mulai' => '04:30:00', 'selesai' => '05:30:00', 'toleransi' => '04:45:00'],
            ['nama' => 'Kajian Kitab Kuning Pagi', 'jenis' => 'pengajian_kitab', 'mulai' => '05:45:00', 'selesai' => '06:45:00', 'toleransi' => '06:00:00'],
            ['nama' => 'Apel Kedisiplinan Pagi', 'jenis' => 'apel_kebersihan', 'mulai' => '07:00:00', 'selesai' => '07:30:00', 'toleransi' => '07:15:00'],
            ['nama' => 'KBM Madrasah Sesi 1', 'jenis' => 'kegiatan_belajar', 'mulai' => '07:30:00', 'selesai' => '12:00:00', 'toleransi' => '07:45:00'],
            ['nama' => 'Sholat Ashar Berjamaah', 'jenis' => 'sholat_berjamaah', 'mulai' => '15:15:00', 'selesai' => '16:00:00', 'toleransi' => '15:30:00'],
            ['nama' => 'Sholat Maghrib & Wirid Bersama', 'jenis' => 'sholat_berjamaah', 'mulai' => '17:50:00', 'selesai' => '18:45:00', 'toleransi' => '18:05:00'],
            ['nama' => 'Kajian Kitab Ba\'da Maghrib', 'jenis' => 'pengajian_kitab', 'mulai' => '18:45:00', 'selesai' => '19:15:00', 'toleransi' => '18:55:00'],
            ['nama' => 'Sholat Isya Berjamaah', 'jenis' => 'sholat_berjamaah', 'mulai' => '19:20:00', 'selesai' => '20:00:00', 'toleransi' => '19:35:00'],
            ['nama' => 'Halaqah Tahfidz Malam', 'jenis' => 'tahfidz', 'mulai' => '20:15:00', 'selesai' => '21:30:00', 'toleransi' => '20:30:00'],
        ];

        $sesi = fake()->randomElement($sesiList);
        $tanggal = fake()->dateTimeBetween('-5 days', '+3 days')->format('Y-m-d');

        return [
            'nama_sesi' => $sesi['nama'],
            'kode_sesi' => strtoupper(fake()->unique()->bothify('SESI-####-????')),
            'jenis' => $sesi['jenis'],
            'tanggal' => $tanggal,
            'jam_mulai' => $sesi['mulai'],
            'jam_selesai' => $sesi['selesai'],
            'batas_terlambat' => $sesi['toleransi'],
            'status' => fake()->randomElement(['dibuka', 'dibuka', 'ditutup']),
            'deskripsi' => 'Presensi kegiatan santri: '.$sesi['nama'],
        ];
    }

    /**
     * State untuk sesi dengan status dibuka.
     */
    public function dibuka(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'dibuka',
        ]);
    }

    /**
     * State untuk sesi dengan status ditutup.
     */
    public function ditutup(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'ditutup',
        ]);
    }

    /**
     * State untuk sesi pada hari ini.
     */
    public function hariIni(): static
    {
        return $this->state(fn (array $attributes) => [
            'tanggal' => today()->toDateString(),
        ]);
    }
}
