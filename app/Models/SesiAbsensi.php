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
