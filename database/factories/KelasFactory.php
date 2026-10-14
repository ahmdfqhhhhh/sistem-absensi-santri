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
        $tingkat = fake()->randomElement(['7', '8', '9', '10', '11', '12']);
        $jurusan = in_array($tingkat, ['10', '11', '12'])
            ? fake()->randomElement(['Keagamaan', 'IPA', 'IPS', 'Tahfidz Al-Qur\'an', 'Bahasa'])
            : 'Madrasah Tsanawiyah';

        $abjad = fake()->randomElement(['A', 'B', 'C', '1', '2']);

        return [
            'nama_kelas' => $tingkat.' '.$jurusan.' '.$abjad,
            'tingkat' => (string) $tingkat,
            'jurusan' => $jurusan,
            'wali_kelas' => 'Ustadz '.fake()->name(),
            'tahun_ajaran' => '2026/2027',
        ];
    }
}
