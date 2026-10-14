<?php

namespace Database\Factories;

use App\Models\KamtibLog;
use App\Models\Santri;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<KamtibLog>
 */
class KamtibLogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $pelanggaranList = [
            ['judul' => 'Terlambat masuk kamar asrama saat jam malam', 'kategori' => 'ringan', 'poin' => 5, 'sanksi' => 'Teguran lisan dan membaca istighfar 100 kali'],
            ['judul' => 'Tidak hadir sholat berjamaah tanpa uzur syar\'i', 'kategori' => 'ringan', 'poin' => 10, 'sanksi' => 'Takzir membaca surat Al-Mulk di selasar masjid'],
            ['judul' => 'Meninggalkan piket kebersihan lingkungan asrama', 'kategori' => 'ringan', 'poin' => 5, 'sanksi' => 'Membersihkan teras asrama selama 2 hari berturut-turut'],
            ['judul' => 'Membawa gawai atau smartphone tanpa izin pengasuhan', 'kategori' => 'sedang', 'poin' => 25, 'sanksi' => 'Penyitaan perangkat dan surat peringatan pertama'],
            ['judul' => 'Keluar komplek pondok pesantren tanpa izin resmi', 'kategori' => 'sedang', 'poin' => 30, 'sanksi' => 'Skorsing tidak boleh keluar asrama selama 1 pekan'],
            ['judul' => 'Merokok di area tersembunyi asrama', 'kategori' => 'sedang', 'poin' => 35, 'sanksi' => 'Kerja bakti sosial dan pemanggilan wali santri'],
            ['judul' => 'Berkelahi dengan sesama santri', 'kategori' => 'berat', 'poin' => 50, 'sanksi' => 'Surat Peringatan ke-2 dan sidang majelis kamtib pondok'],
            ['judul' => 'Kabur dari pondok pesantren', 'kategori' => 'berat', 'poin' => 75, 'sanksi' => 'Surat Peringatan Terakhir dan konseling mendalam'],
        ];

        $pelanggaran = fake()->randomElement($pelanggaranList);

        return [
            'santri_id' => Santri::factory(),
            'judul_pelanggaran' => $pelanggaran['judul'],
            'kategori' => $pelanggaran['kategori'],
            'poin_pelanggaran' => $pelanggaran['poin'],
            'tanggal_kejadian' => fake()->dateTimeBetween('-30 days', 'now')->format('Y-m-d'),
            'lokasi_kejadian' => fake()->randomElement([
                'Kamar Asrama Putra Lt. 2',
                'Halaman Depan Masjid',
                'Kantin Belakang Pondok',
                'Gerbang Pos Satpam Barat',
                'Lorong Kelas Madrasah',
            ]),
            'tindakan_sanksi' => $pelanggaran['sanksi'],
            'status_penyelesaian' => fake()->randomElement(['belum_selesai', 'dalam_proses', 'selesai']),
            'recorded_by' => User::factory(),
            'catatan' => 'Pencatatan pelanggaran oleh bagian ketertiban dan keamanan santri.',
            'bukti_foto_path' => 'kamtib_evidence/'.fake()->uuid().'.jpg',
        ];
    }

    /**
     * State untuk pelanggaran ringan.
     */
    public function ringan(): static
    {
        return $this->state(fn (array $attributes) => [
            'kategori' => 'ringan',
            'poin_pelanggaran' => 5,
        ]);
    }

    /**
     * State untuk pelanggaran sedang.
     */
    public function sedang(): static
    {
        return $this->state(fn (array $attributes) => [
            'kategori' => 'sedang',
            'poin_pelanggaran' => 25,
        ]);
    }

    /**
     * State untuk pelanggaran berat.
     */
    public function berat(): static
    {
        return $this->state(fn (array $attributes) => [
            'kategori' => 'berat',
            'poin_pelanggaran' => 50,
        ]);
    }

    /**
     * State untuk catatan yang belum diselesaikan santri.
     */
    public function belumSelesai(): static
    {
        return $this->state(fn (array $attributes) => [
            'status_penyelesaian' => 'belum_selesai',
        ]);
    }

    /**
     * State untuk catatan yang telah diselesaikan.
     */
    public function selesai(): static
    {
        return $this->state(fn (array $attributes) => [
            'status_penyelesaian' => 'selesai',
        ]);
    }
}
