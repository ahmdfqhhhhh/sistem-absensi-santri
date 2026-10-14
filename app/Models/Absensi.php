<?php

namespace App\Models;

use Database\Factories\AbsensiFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Absensi extends Model
{
    /** @use HasFactory<AbsensiFactory> */
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'absensis';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'santri_id',
        'sesi_absensi_id',
        'waktu_absen',
        'status',
        'metode',
        'recorded_by',
        'keterangan',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'waktu_absen' => 'datetime',
        ];
    }

    /**
     * Relasi ke Santri yang diabsen.
     *
     * @return BelongsTo<Santri, $this>
     */
    public function santri(): BelongsTo
    {
        return $this->belongsTo(Santri::class, 'santri_id');
    }

    /**
     * Relasi ke Sesi Absensi terkait.
     *
     * @return BelongsTo<SesiAbsensi, $this>
     */
    public function sesiAbsensi(): BelongsTo
    {
        return $this->belongsTo(SesiAbsensi::class, 'sesi_absensi_id');
    }

    /**
     * Relasi ke Pengguna/Ustadz yang mencatat presensi (jika manual atau scan via device ustadz).
     *
     * @return BelongsTo<User, $this>
     */
    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    /**
     * Scope absensi dengan status hadir.
     *
     * @param  Builder<$this>  $query
     * @return Builder<$this>
     */
    public function scopeHadir(Builder $query): Builder
    {
        return $query->where('status', 'hadir');
    }

    /**
     * Scope absensi dengan status terlambat.
     *
     * @param  Builder<$this>  $query
     * @return Builder<$this>
     */
    public function scopeTerlambat(Builder $query): Builder
    {
        return $query->where('status', 'terlambat');
    }

    /**
     * Scope absensi dengan status izin.
     *
     * @param  Builder<$this>  $query
     * @return Builder<$this>
     */
    public function scopeIzin(Builder $query): Builder
    {
        return $query->where('status', 'izin');
    }

    /**
     * Scope absensi dengan status sakit.
     *
     * @param  Builder<$this>  $query
     * @return Builder<$this>
     */
    public function scopeSakit(Builder $query): Builder
    {
        return $query->where('status', 'sakit');
    }

    /**
     * Scope absensi dengan status alpa.
     *
     * @param  Builder<$this>  $query
     * @return Builder<$this>
     */
    public function scopeAlpa(Builder $query): Builder
    {
        return $query->where('status', 'alpa');
    }
}
