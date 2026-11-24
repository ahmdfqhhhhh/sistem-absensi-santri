<?php

namespace App\Models;

use Database\Factories\SantriFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

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
        'kamar',
        'kelas_id',
        'nama_wali',
        'no_wa_wali',
        'qr_code_path',
        'jenis_kelamin',
        'status',
        'tanggal_lahir',
        'alamat',
        'foto_path',
        'face_descriptor',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'foto_url',
        'has_face_descriptor',
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
            'face_descriptor' => 'array',
        ];
    }

    /**
     * Cek apakah santri sudah memiliki data vektor wajah terdaftar.
     */
    public function getHasFaceDescriptorAttribute(): bool
    {
        return !empty($this->face_descriptor);
    }

    /**
     * URL Foto Santri.
     */
    public function getFotoUrlAttribute(): ?string
    {
        if ($this->foto_path && Storage::disk('public')->exists($this->foto_path)) {
            return Storage::disk('public')->url($this->foto_path);
        }

        return null;
    }

    /**
     * Scope filter santri putra.
     */
    public function scopePutra(Builder $query): Builder
    {
        return $query->where('jenis_kelamin', 'L');
    }

    /**
     * Scope filter santri putri.
     */
    public function scopePutri(Builder $query): Builder
    {
        return $query->where('jenis_kelamin', 'P');
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
     * Relasi ke seluruh tagihan dan pembayaran santri.
     *
     * @return HasMany<Tagihan, $this>
     */
    public function tagihans(): HasMany
    {
        return $this->hasMany(Tagihan::class, 'santri_id');
    }

    /**
     * Hitung total nominal tunggakan santri yang belum dilunasi dan telah lewat jatuh tempo.
     */
    public function totalTunggakan(): int
    {
        return (int) $this->tagihans()
            ->where('status', 'belum_bayar')
            ->where('jatuh_tempo', '<', today())
            ->sum('nominal');
    }

    /**
     * Accessor total nominal tunggakan.
     */
    public function getTotalTunggakanAttribute(): int
    {
        return $this->totalTunggakan();
    }

    /**
     * Cek apakah santri memiliki tunggakan yang belum dilunasi.
     */
    public function hasTunggakan(): bool
    {
        return $this->tagihans()
            ->where('status', 'belum_bayar')
            ->where('jatuh_tempo', '<', today())
            ->exists();
    }

    /**
     * Accessor has_tunggakan / hasTunggakan untuk mencegah bentrok dynamic relationship Eloquent.
     */
    public function getHasTunggakanAttribute(): bool
    {
        return $this->hasTunggakan();
    }
}
