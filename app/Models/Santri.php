<?php

namespace App\Models;

use Database\Factories\SantriFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Santri extends Model
{
    /** @use HasFactory<SantriFactory> */
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'santris';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nis',
        'nama',
        'asrama_id',
        'kelas_id',
        'no_wa_wali',
        'qr_code_path',
        'jenis_kelamin',
        'status',
        'tanggal_lahir',
        'alamat',
        'foto_path',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tanggal_lahir' => 'date',
        ];
    }

    /**
     * Relasi ke data Asrama santri.
     *
     * @return BelongsTo<Asrama, $this>
     */
    public function asrama(): BelongsTo
    {
        return $this->belongsTo(Asrama::class, 'asrama_id');
    }

    /**
     * Relasi ke data Kelas santri.
     *
     * @return BelongsTo<Kelas, $this>
     */
    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    /**
     * Relasi ke semua rekaman Absensi santri.
     *
     * @return HasMany<Absensi, $this>
     */
    public function absensis(): HasMany
    {
        return $this->hasMany(Absensi::class, 'santri_id');
    }

    /**
     * Relasi ke semua riwayat Perizinan santri.
     *
     * @return HasMany<Perizinan, $this>
     */
    public function perizinans(): HasMany
    {
        return $this->hasMany(Perizinan::class, 'santri_id');
    }

    /**
     * Relasi ke semua catatan pelanggaran dan ketertiban (Kamtib) santri.
     *
     * @return HasMany<KamtibLog, $this>
     */
    public function kamtibLogs(): HasMany
    {
        return $this->hasMany(KamtibLog::class, 'santri_id');
    }

    /**
     * Scope untuk santri yang masih berstatus aktif.
     *
     * @param  Builder<$this>  $query
     * @return Builder<$this>
     */
    public function scopeAktif(Builder $query): Builder
    {
        return $query->where('status', 'aktif');
    }

    /**
     * Scope untuk santri putra.
     *
     * @param  Builder<$this>  $query
     * @return Builder<$this>
     */
    public function scopePutra(Builder $query): Builder
    {
        return $query->where('jenis_kelamin', 'L');
    }

    /**
     * Scope untuk santri putri.
     *
     * @param  Builder<$this>  $query
     * @return Builder<$this>
     */
    public function scopePutri(Builder $query): Builder
    {
        return $query->where('jenis_kelamin', 'P');
    }
}
