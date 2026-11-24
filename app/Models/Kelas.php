<?php

namespace App\Models;

use Database\Factories\KelasFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kelas extends Model
{
    /** @use HasFactory<KelasFactory> */
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'kelas';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nama_kelas',
        'tingkat',
        'jurusan',
        'wali_kelas',
        'tahun_ajaran',
    ];

    /**
     * Relasi ke data Santri yang terdaftar di kelas ini.
     *
     * @return HasMany<Santri, $this>
     */
    public function santris(): HasMany
    {
        return $this->hasMany(Santri::class, 'kelas_id');
    }

    /**
     * Mendapatkan kategori jenjang (SMP, SMA, SMK).
     */
    public function getJenjangAttribute(): string
    {
        $text = strtoupper($this->tingkat . ' ' . $this->nama_kelas);
        if (str_contains($text, 'SMK')) {
            return 'SMK';
        }
        if (str_contains($text, 'SMA')) {
            return 'SMA';
        }
        if (str_contains($text, 'SMP') || in_array($this->tingkat, ['7', '8', '9'])) {
            return 'SMP';
        }
        return 'Lainnya';
    }

    /**
     * Memastikan 9 rombel kelas sekolah umum (SMP, SMA, SMK) selalu tersedia.
     */
    public static function ensureStandardClassesExist(): void
    {
        $standards = [
            ['nama_kelas' => 'Kelas 7 SMP', 'tingkat' => '7 SMP'],
            ['nama_kelas' => 'Kelas 8 SMP', 'tingkat' => '8 SMP'],
            ['nama_kelas' => 'Kelas 9 SMP', 'tingkat' => '9 SMP'],
            ['nama_kelas' => 'Kelas 10 SMA', 'tingkat' => '10 SMA'],
            ['nama_kelas' => 'Kelas 11 SMA', 'tingkat' => '11 SMA'],
            ['nama_kelas' => 'Kelas 12 SMA', 'tingkat' => '12 SMA'],
            ['nama_kelas' => 'Kelas 10 SMK', 'tingkat' => '10 SMK'],
            ['nama_kelas' => 'Kelas 11 SMK', 'tingkat' => '11 SMK'],
            ['nama_kelas' => 'Kelas 12 SMK', 'tingkat' => '12 SMK'],
        ];

        foreach ($standards as $std) {
            self::firstOrCreate(
                ['nama_kelas' => $std['nama_kelas']],
                ['tingkat' => $std['tingkat']]
            );
        }
    }
}
