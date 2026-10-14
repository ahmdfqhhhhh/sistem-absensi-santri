<?php

namespace App\Models;

use Database\Factories\PerizinanFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Perizinan extends Model
{
    /** @use HasFactory<PerizinanFactory> */
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'perizinans';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'santri_id',
        'nomor_surat',
        'jenis_izin',
        'keperluan',
        'tanggal_mulai',
        'tanggal_selesai',
        'tanggal_kembali_aktual',
        'status',
        'approved_by',
        'catatan_approval',
        'penjemput_nama',
        'penjemput_hubungan',
        'bukti_dokumen_path',
        'qr_code_path',
        'qr_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tanggal_mulai' => 'datetime',
            'tanggal_selesai' => 'datetime',
            'tanggal_kembali_aktual' => 'datetime',
        ];
    }

    /**
     * Relasi ke Santri yang mengajukan/diberikan izin.
     *
     * @return BelongsTo<Santri, $this>
     */
    public function santri(): BelongsTo
    {
        return $this->belongsTo(Santri::class, 'santri_id');
    }

    /**
     * Relasi ke Pengguna/Ustadz yang menyetujui izin.
     *
     * @return BelongsTo<User, $this>
     */
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Relasi ke riwayat log keamanan/ketertiban (checkout/checkin gerbang).
     *
     * @return HasMany<KamtibLog, $this>
     */
    public function kamtibLogs(): HasMany
    {
        return $this->hasMany(KamtibLog::class, 'perizinan_id');
    }

    /**
     * Scope untuk perizinan yang menunggu persetujuan (pending).
     *
     * @param  Builder<$this>  $query
     * @return Builder<$this>
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope untuk perizinan yang sudah disetujui.
     *
     * @param  Builder<$this>  $query
     * @return Builder<$this>
     */
    public function scopeDisetujui(Builder $query): Builder
    {
        return $query->where('status', 'disetujui');
    }

    /**
     * Scope untuk perizinan yang sedang berlangsung saat ini.
     *
     * @param  Builder<$this>  $query
     * @return Builder<$this>
     */
    public function scopeSedangBerlangsung(Builder $query): Builder
    {
        $now = now();

        return $query->where('status', 'disetujui')
            ->where('tanggal_mulai', '<=', $now)
            ->where('tanggal_selesai', '>=', $now);
    }
}
