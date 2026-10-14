<?php

namespace App\Models;

use Database\Factories\KamtibLogFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KamtibLog extends Model
{
    /** @use HasFactory<KamtibLogFactory> */
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'kamtib_logs';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'santri_id',
        'perizinan_id',
        'tipe_log',
        'waktu_log',
        'judul_pelanggaran',
        'kategori',
        'poin_pelanggaran',
        'tanggal_kejadian',
        'lokasi_kejadian',
        'tindakan_sanksi',
        'status_penyelesaian',
        'recorded_by',
        'catatan',
        'bukti_foto_path',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tanggal_kejadian' => 'date',
            'waktu_log' => 'datetime',
            'poin_pelanggaran' => 'integer',
        ];
    }

    /**
     * Relasi ke Surat Perizinan santri terkait (jika ada).
     *
     * @return BelongsTo<Perizinan, $this>
     */
    public function perizinan(): BelongsTo
    {
        return $this->belongsTo(Perizinan::class, 'perizinan_id');
    }

    /**
     * Relasi ke Santri yang tercatat melanggar tata tertib / checkpoint gerbang.
     *
     * @return BelongsTo<Santri, $this>
     */
    public function santri(): BelongsTo
    {
        return $this->belongsTo(Santri::class, 'santri_id');
    }

    /**
     * Relasi ke Petugas Kamtib / Ustadz yang mencatat pelanggaran.
     *
     * @return BelongsTo<User, $this>
     */
    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    /**
     * Scope untuk pelanggaran yang belum terselesaikan.
     *
     * @param  Builder<$this>  $query
     * @return Builder<$this>
     */
    public function scopeBelumSelesai(Builder $query): Builder
    {
        return $query->where('status_penyelesaian', 'belum_selesai');
    }

    /**
     * Scope untuk pelanggaran berkategori berat.
     *
     * @param  Builder<$this>  $query
     * @return Builder<$this>
     */
    public function scopeBerat(Builder $query): Builder
    {
        return $query->where('kategori', 'berat');
    }

    /**
     * Scope berdasarkan kategori tertentu.
     *
     * @param  Builder<$this>  $query
     * @return Builder<$this>
     */
    public function scopeKategori(Builder $query, string $kategori): Builder
    {
        return $query->where('kategori', $kategori);
    }
}
