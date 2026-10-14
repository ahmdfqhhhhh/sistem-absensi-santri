<?php

namespace Database\Factories;

use App\Models\Absensi;
use App\Models\Santri;
use App\Models\SesiAbsensi;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Absensi>
 */
class AbsensiFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $status = fake()->randomElement(['hadir', 'hadir', 'hadir', 'terlambat', 'izin', 'sakit', 'alpa']);

        return [
            'santri_id' => Santri::factory(),
            'sesi_absensi_id' => SesiAbsensi::factory(),
            'waktu_absen' => $status === 'alpa' ? null : fake()->dateTimeThisMonth(),
            'status' => $status,
            'metode' => fake()->randomElement(['qr_code', 'qr_code', 'manual', 'rfid']),
            'recorded_by' => User::factory(),
            'keterangan' => in_array($status, ['izin', 'sakit']) ? fake()->sentence() : null,
        ];
    }

    /**
     * State untuk kehadiran tepat waktu (hadir).
     */
    public function hadir(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'hadir',
            'waktu_absen' => now(),
            'metode' => 'qr_code',
        ]);
    }

    /**
     * State untuk kehadiran terlambat.
     */
    public function terlambat(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'terlambat',
            'waktu_absen' => now()->addMinutes(15),
            'keterangan' => 'Terlambat masuk sesi',
        ]);
    }

    /**
     * State untuk izin.
     */
    public function izin(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'izin',
            'waktu_absen' => null,
            'metode' => 'manual',
            'keterangan' => 'Izin keperluan keluarga',
        ]);
    }

    /**
     * State untuk sakit.
     */
    public function sakit(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'sakit',
            'waktu_absen' => null,
            'metode' => 'manual',
            'keterangan' => 'Istirahat di poskestren',
        ]);
    }

    /**
     * State untuk alpa (tanpa keterangan).
     */
    public function alpa(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'alpa',
            'waktu_absen' => null,
            'metode' => 'manual',
            'keterangan' => 'Tidak hadir tanpa keterangan',
        ]);
    }
}
