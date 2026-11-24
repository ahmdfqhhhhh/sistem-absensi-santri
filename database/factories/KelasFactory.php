<?php

namespace Database\Factories;

use App\Models\Kelas;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Kelas>
 */
class KelasFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $jenjang = fake()->randomElement(['SMP', 'SMA', 'SMK']);
        if ($jenjang === 'SMP') {
            $angka = fake()->randomElement(['7', '8', '9']);
            $tingkat = $angka . ' SMP';
            $jurusan = 'Umum';
            $nama = 'Kelas ' . $angka . fake()->randomElement(['A', 'B']) . ' SMP';
        } elseif ($jenjang === 'SMA') {
            $angka = fake()->randomElement(['10', '11', '12']);
            $tingkat = $angka . ' SMA';
            $jurusan = fake()->randomElement(['IPA', 'IPS', 'Keagamaan']);
            $nama = $angka . ' SMA ' . $jurusan;
        } else {
            $angka = fake()->randomElement(['10', '11', '12']);
            $tingkat = $angka . ' SMK';
            $jurusan = fake()->randomElement(['TKJ', 'Otomotif', 'Akuntansi']);
            $nama = $angka . ' SMK ' . $jurusan;
        }

        return [
            'nama_kelas' => $nama,
            'tingkat' => $tingkat,
            'jurusan' => $jurusan,
            'wali_kelas' => 'Ustadz ' . fake()->name(),
            'tahun_ajaran' => '2026/2027',
        ];
    }
}
