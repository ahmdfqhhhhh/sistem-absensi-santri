<?php

namespace Database\Factories;

use App\Models\Santri;
use App\Models\Tagihan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Tagihan>
 */
class TagihanFactory extends Factory
{
    protected $model = Tagihan::class;

    public function definition(): array
    {
        $nominal = fake()->randomElement([250000, 300000, 350000, 500000]);

        return [
            'santri_id' => Santri::factory(),
            'kode_tagihan' => Tagihan::generateKodeTagihan(),
            'judul_tagihan' => 'SPP ' . fake()->monthName() . ' ' . date('Y'),
            'kategori' => 'spp',
            'nominal' => $nominal,
            'nominal_dibayar' => 0,
            'jatuh_tempo' => now()->addDays(10),
            'status' => 'belum_bayar',
            'metode_pembayaran' => null,
            'tanggal_bayar' => null,
            'bukti_transfer_path' => null,
            'catatan_wali' => null,
            'catatan_bendahara' => null,
            'dikonfirmasi_oleh' => null,
            'dikonfirmasi_pada' => null,
        ];
    }

    public function lunas(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'lunas',
            'nominal_dibayar' => $attributes['nominal'],
            'tanggal_bayar' => now()->subDays(2),
            'metode_pembayaran' => 'transfer_bank',
            'dikonfirmasi_pada' => now()->subDays(2),
            'catatan_bendahara' => 'Pembayaran lunas via transfer bank terverifikasi.',
        ]);
    }

    public function nunggak(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'belum_bayar',
            'nominal_dibayar' => 0,
            'jatuh_tempo' => now()->subDays(15),
        ]);
    }

    public function menungguKonfirmasi(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'menunggu_konfirmasi',
            'nominal_dibayar' => $attributes['nominal'],
            'tanggal_bayar' => now(),
            'metode_pembayaran' => 'transfer_bank',
            'bukti_transfer_path' => 'bukti-transfer/sample.jpg',
            'catatan_wali' => 'Sudah transfer melalui m-banking BSI',
        ]);
    }
}
