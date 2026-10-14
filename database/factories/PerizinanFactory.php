<?php

namespace Database\Factories;

use App\Models\Perizinan;
use App\Models\Santri;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Perizinan>
 */
class PerizinanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $mulai = fake()->dateTimeBetween('-10 days', '+5 days');
        $selesai = (clone $mulai)->modify('+'.rand(1, 4).' days');
        $status = fake()->randomElement(['pending', 'disetujui', 'ditolak', 'selesai']);

        return [
            'santri_id' => Santri::factory(),
            'nomor_surat' => 'IZN/'.date('Y').'/'.fake()->unique()->numerify('#####'),
            'jenis_izin' => fake()->randomElement([
                'pulang',
                'keluar_komplek',
                'sakit',
                'sambangan_keluarga',
                'tugas_pondok',
            ]),
            'keperluan' => fake()->randomElement([
                'Acara pernikahan saudara kandung di kampung halaman',
                'Pemeriksaan medis lanjutan ke rumah sakit rujukan',
                'Keperluan pengurusan berkas administrasi kependudukan',
                'Menghadiri khitanan adik kandung',
                'Kunjungan keluarga mendesak',
            ]),
            'tanggal_mulai' => $mulai,
            'tanggal_selesai' => $selesai,
            'tanggal_kembali_aktual' => $status === 'selesai' ? $selesai : null,
            'status' => $status,
            'approved_by' => in_array($status, ['disetujui', 'selesai', 'ditolak']) ? User::factory() : null,
            'catatan_approval' => in_array($status, ['disetujui', 'selesai'])
                ? 'Disetujui. Wajib kembali tepat waktu sebelum batas akhir perizinan.'
                : ($status === 'ditolak' ? 'Ditolak karena bertepatan dengan ujian pondok.' : null),
            'penjemput_nama' => fake()->name(),
            'penjemput_hubungan' => fake()->randomElement(['Ayah Kandung', 'Ibu Kandung', 'Paman', 'Kakak']),
            'bukti_dokumen_path' => 'perizinan_docs/'.fake()->uuid().'.pdf',
            'qr_code_path' => in_array($status, ['disetujui', 'selesai']) ? 'qrcodes/perizinan/'.fake()->uuid().'.svg' : null,
            'qr_token' => in_array($status, ['disetujui', 'selesai']) ? fake()->uuid() : null,
        ];
    }

    /**
     * State untuk izin berstatus pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
            'approved_by' => null,
            'catatan_approval' => null,
            'tanggal_kembali_aktual' => null,
        ]);
    }

    /**
     * State untuk izin berstatus disetujui.
     */
    public function disetujui(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'disetujui',
            'approved_by' => User::factory(),
            'catatan_approval' => 'Disetujui oleh pengasuhan santri.',
        ]);
    }

    /**
     * State untuk izin yang sudah selesai dan santri telah kembali.
     */
    public function selesai(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'selesai',
            'approved_by' => User::factory(),
            'tanggal_kembali_aktual' => now(),
        ]);
    }
}
