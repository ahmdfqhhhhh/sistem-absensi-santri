<?php

namespace Database\Factories;

use App\Models\Asrama;
use App\Models\Kelas;
use App\Models\Santri;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Santri>
 */
class SantriFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $gender = fake()->randomElement(['L', 'P']);

        return [
            'nis' => fake()->unique()->numerify('SAN-2026-#####'),
            'nama' => fake()->name($gender === 'L' ? 'male' : 'female'),
            'asrama_id' => Asrama::factory(),
            'kelas_id' => Kelas::factory(),
            'no_wa_wali' => '08'.fake()->numerify('##########'),
            'qr_code_path' => 'qrcodes/'.fake()->uuid().'.svg',
            'jenis_kelamin' => $gender,
            'status' => fake()->randomElement(['aktif', 'aktif', 'aktif', 'izin_pulang']),
            'tanggal_lahir' => fake()->dateTimeBetween('-18 years', '-12 years')->format('Y-m-d'),
            'alamat' => fake()->address(),
            'foto_path' => 'santri_photos/'.fake()->uuid().'.jpg',
        ];
    }

    /**
     * State untuk santri putra.
     */
    public function putra(): static
    {
        return $this->state(fn (array $attributes) => [
            'jenis_kelamin' => 'L',
            'nama' => fake()->name('male'),
            'asrama_id' => Asrama::factory()->putra(),
        ]);
    }

    /**
     * State untuk santri putri.
     */
    public function putri(): static
    {
        return $this->state(fn (array $attributes) => [
            'jenis_kelamin' => 'P',
            'nama' => fake()->name('female'),
            'asrama_id' => Asrama::factory()->putri(),
        ]);
    }

    /**
     * State untuk santri yang berstatus aktif.
     */
    public function aktif(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'aktif',
        ]);
    }

    /**
     * State untuk santri yang sedang izin pulang.
     */
    public function izinPulang(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'izin_pulang',
        ]);
    }
}
