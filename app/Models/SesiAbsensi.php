<?php

namespace App\Models;

use Database\Factories\SesiAbsensiFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SesiAbsensi extends Model
{
    /** @use HasFactory<SesiAbsensiFactory> */
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sesi_absensis';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nama_sesi',
        'kode_sesi',
        'jenis',
        'tanggal',
        'jam_mulai',
        'jam_selesai',
        'batas_terlambat',
        'status',
        'deskripsi',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
        ];
    }

    /**
     * Pastikan sesi harian (sholat 5 waktu & sekolah) otomatis dibuat untuk hari ini.
     *
     * @return \Illuminate\Database\Eloquent\Collection<int, static>
     */
    public static function ensureDailySessions(): \Illuminate\Database\Eloquent\Collection
    {
        $today = today();
        $defaults = [
            [
                'nama_sesi' => 'Sholat Subuh Berjamaah',
                'kode_sesi' => 'SBH-'.$today->format('Ymd'),
                'jenis' => 'sholat_berjamaah',
                'jam_mulai' => '04:15:00',
                'jam_selesai' => '05:30:00',
                'batas_terlambat' => '04:45:00',
            ],
            [
                'nama_sesi' => 'Presensi Masuk Sekolah / KBM Madrasah',
                'kode_sesi' => 'SCH-'.$today->format('Ymd'),
                'jenis' => 'kegiatan_belajar',
                'jam_mulai' => '06:30:00',
                'jam_selesai' => '07:30:00',
                'batas_terlambat' => '07:00:00',
            ],
            [
                'nama_sesi' => 'Sholat Dzuhur Berjamaah',
                'kode_sesi' => 'DZH-'.$today->format('Ymd'),
                'jenis' => 'sholat_berjamaah',
                'jam_mulai' => '11:45:00',
                'jam_selesai' => '13:00:00',
                'batas_terlambat' => '12:15:00',
            ],
            [
                'nama_sesi' => 'Sholat Ashar Berjamaah',
                'kode_sesi' => 'ASH-'.$today->format('Ymd'),
                'jenis' => 'sholat_berjamaah',
                'jam_mulai' => '15:00:00',
                'jam_selesai' => '16:15:00',
                'batas_terlambat' => '15:30:00',
            ],
            [
                'nama_sesi' => 'Sholat Maghrib Berjamaah',
                'kode_sesi' => 'MGH-'.$today->format('Ymd'),
                'jenis' => 'sholat_berjamaah',
                'jam_mulai' => '17:45:00',
                'jam_selesai' => '19:00:00',
                'batas_terlambat' => '18:15:00',
            ],
            [
                'nama_sesi' => 'Sholat Isya Berjamaah',
                'kode_sesi' => 'ISY-'.$today->format('Ymd'),
                'jenis' => 'sholat_berjamaah',
                'jam_mulai' => '19:15:00',
                'jam_selesai' => '20:30:00',
                'batas_terlambat' => '19:45:00',
            ],
        ];

        foreach ($defaults as $session) {
            static::firstOrCreate(
                [
                    'tanggal' => $today,
                    'kode_sesi' => $session['kode_sesi'],
                ],
                array_merge($session, [
                    'tanggal' => $today,
                    'status' => 'dibuka',
                ])
            );
        }

        return static::whereDate('tanggal', $today)->orderBy('jam_mulai')->get();
    }

    /**
     * Relasi ke seluruh presensi santri di sesi ini.
     *
     * @return HasMany<Absensi, $this>
     */
    public function absensis(): HasMany
    {
        return $this->hasMany(Absensi::class, 'sesi_absensi_id');
    }

    /**
     * Relasi banyak-ke-banyak dengan Santri melalui tabel absensis.
     *
     * @return BelongsToMany<Santri, $this>
     */
    public function santris(): BelongsToMany
    {
        return $this->belongsToMany(Santri::class, 'absensis', 'sesi_absensi_id', 'santri_id')
            ->withPivot(['waktu_absen', 'status', 'metode', 'keterangan', 'recorded_by'])
            ->withTimestamps();
    }

    /**
     * Scope untuk sesi absensi yang masih dibuka.
     *
     * @param  Builder<$this>  $query
     * @return Builder<$this>
     */
    public function scopeDibuka(Builder $query): Builder
    {
        return $query->where('status', 'dibuka');
    }

    /**
     * Scope untuk sesi absensi pada tanggal hari ini.
     *
     * @param  Builder<$this>  $query
     * @return Builder<$this>
     */
    public function scopeHariIni(Builder $query): Builder
    {
        return $query->whereDate('tanggal', today());
    }
}
