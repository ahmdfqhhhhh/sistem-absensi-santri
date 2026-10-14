<?php

namespace Database\Factories;

use App\Models\Asrama;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Asrama>
 */
class AsramaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $namaAsramaList = [
            'Asrama Al-Ghazali',
            'Asrama Ibnu Sina',
            'Asrama Al-Fatih',
            'Asrama Shalahuddin Al-Ayyubi',
            'Asrama Siti Khadijah',
            'Asrama Fatimah Az-Zahra',
            'Asrama Aisyah',
            'Asrama Imam Syafi\'i',
            'Asrama Abu Hanifah',
            'Asrama Imam Malik',
        ];

        $gender = fake()->randomElement(['putra', 'putri']);
        $prefix = $gender === 'putra' ? 'Ustadz ' : 'Ustadzah ';

        return [
            'nama_asrama' => fake()->randomElement($namaAsramaList).' '.fake()->randomElement(['A', 'B', 'C', '1', '2']),
            'gedung' => 'Gedung '.fake()->randomElement(['A', 'B', 'C', 'D', 'Komplek Barat', 'Komplek Timur']),
            'pembina' => $prefix.fake()->name($gender === 'putra' ? 'male' : 'female'),
            'gender' => $gender,
            'kapasitas' => fake()->numberBetween(30, 80),
            'keterangan' => fake()->optional()->sentence(),
        ];
    }

    /**
     * State untuk asrama santri putra.
     */
    public function putra(): static
    {
        return $this->state(fn (array $attributes) => [
            'gender' => 'putra',
            'pembina' => 'Ustadz '.fake()->name('male'),
        ]);
    }

    /**
     * State untuk asrama santri putri.
     */
    public function putri(): static
    {
        return $this->state(fn (array $attributes) => [
            'gender' => 'putri',
            'pembina' => 'Ustadzah '.fake()->name('female'),
        ]);
    }
}
