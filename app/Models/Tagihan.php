<?php

namespace App\Models;

use Database\Factories\TagihanFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Tagihan extends Model
{
    /** @use HasFactory<TagihanFactory> */
    use HasFactory;

    protected $table = 'tagihans';

    protected $fillable = [
        'santri_id',
        'kode_tagihan',
        'judul_tagihan',
        'kategori',
        'nominal',
        'nominal_dibayar',
        'nominal_transfer',
        'jatuh_tempo',
        'status',
        'metode_pembayaran',
        'tanggal_bayar',
        'bukti_transfer_path',
        'catatan_wali',
        'catatan_bendahara',
        'riwayat_cicilan',
        'dikonfirmasi_oleh',
        'dikonfirmasi_pada',
    ];

    protected function casts(): array
    {
        return [
            'jatuh_tempo' => 'date',
            'tanggal_bayar' => 'datetime',
            'dikonfirmasi_pada' => 'datetime',
            'nominal' => 'integer',
            'nominal_dibayar' => 'integer',
            'nominal_transfer' => 'integer',
            'riwayat_cicilan' => 'array',
        ];
    }

    /**
     * Relasi ke data santri penerima tagihan.
     *
     * @return BelongsTo<Santri, $this>
     */
    public function santri(): BelongsTo
    {
        return $this->belongsTo(Santri::class, 'santri_id');
    }

    /**
     * Relasi ke petugas bendahara yang mengonfirmasi pembayaran.
     *
     * @return BelongsTo<User, $this>
     */
    public function dikonfirmasiOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dikonfirmasi_oleh');
    }

    /**
     * Scope tagihan yang telah lunas.
     *
     * @param  Builder<$this>  $query
     * @return Builder<$this>
     */
    public function scopeLunas(Builder $query): Builder
    {
        return $query->where('status', 'lunas');
    }

    /**
     * Scope tagihan yang berstatus belum bayar.
     *
     * @param  Builder<$this>  $query
     * @return Builder<$this>
     */
    public function scopeBelumBayar(Builder $query): Builder
    {
        return $query->where('status', 'belum_bayar');
    }

    /**
     * Scope tagihan yang telah melewati jatuh tempo (nunggak).
     *
     * @param  Builder<$this>  $query
     * @return Builder<$this>
     */
    public function scopeNunggak(Builder $query): Builder
    {
        return $query->where('status', 'belum_bayar')
            ->where('jatuh_tempo', '<', today());
    }

    /**
     * Scope tagihan yang telah dibayar oleh wali santri dan menunggu verifikasi bendahara.
     *
     * @param  Builder<$this>  $query
     * @return Builder<$this>
     */
    public function scopeMenungguKonfirmasi(Builder $query): Builder
    {
        return $query->where('status', 'menunggu_konfirmasi');
    }

    /**
     * Cek apakah tagihan ini menunggak (lewat batas jatuh tempo).
     */
    public function getIsNunggakAttribute(): bool
    {
        return $this->status === 'belum_bayar' && $this->jatuh_tempo < today();
    }

    /**
     * Sisa tunggakan yang belum dilunasi.
     */
    public function getSisaTagihanAttribute(): int
    {
        return max(0, $this->nominal - $this->nominal_dibayar);
    }

    /**
     * Format nominal dalam rupiah.
     */
    public function getNominalRupiahAttribute(): string
    {
        return 'Rp ' . number_format($this->nominal, 0, ',', '.');
    }

    /**
     * Format sisa tagihan dalam rupiah.
     */
    public function getSisaTagihanRupiahAttribute(): string
    {
        return 'Rp ' . number_format($this->sisa_tagihan, 0, ',', '.');
    }

    /**
     * Format nominal yang sudah dibayar dalam rupiah.
     */
    public function getNominalDibayarRupiahAttribute(): string
    {
        return 'Rp ' . number_format($this->nominal_dibayar, 0, ',', '.');
    }

    /**
     * Label status bahasa Indonesia.
     */
    public function getStatusLabelAttribute(): string
    {
        if ($this->status === 'lunas') {
            return 'Lunas';
        }

        if ($this->status === 'menunggu_konfirmasi') {
            if ($this->nominal_transfer) {
                return 'Konfirmasi Cicilan (Rp ' . number_format($this->nominal_transfer, 0, ',', '.') . ')';
            }
            return 'Menunggu Konfirmasi';
        }

        if ($this->nominal_dibayar > 0) {
            return 'Dicicil (Sisa ' . $this->sisa_tagihan_rupiah . ')';
        }

        if ($this->is_nunggak) {
            return 'Nunggak (' . $this->jatuh_tempo->diffForHumans(today(), ['parts' => 1]) . ')';
        }

        if ($this->status === 'belum_bayar') {
            return 'Belum Lunas';
        }

        return ucfirst($this->status);
    }

    /**
     * CSS badge class sesuai status tagihan.
     */
    public function getStatusBadgeAttribute(): string
    {
        if ($this->status === 'lunas') {
            return 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800';
        }

        if ($this->status === 'menunggu_konfirmasi') {
            return 'bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300 border border-amber-200 dark:border-amber-800';
        }

        if ($this->nominal_dibayar > 0) {
            return 'bg-blue-100 text-blue-800 dark:bg-blue-950/60 dark:text-blue-300 border border-blue-200 dark:border-blue-800';
        }

        if ($this->is_nunggak) {
            return 'bg-rose-100 text-rose-800 dark:bg-rose-950/60 dark:text-rose-300 border border-rose-200 dark:border-rose-800';
        }

        return 'bg-slate-100 text-slate-800 dark:bg-slate-800 dark:text-slate-300 border border-slate-200 dark:border-slate-700';
    }

    /**
     * Catat pembayaran / cicilan ke dalam riwayat cicilan tagihan.
     */
    public function catatPembayaran(int $nominal, string $metode, ?string $catatan = null, ?int $userId = null): void
    {
        $riwayat = $this->riwayat_cicilan ?? [];
        $riwayat[] = [
            'tanggal' => now()->toDateTimeString(),
            'nominal' => $nominal,
            'metode'  => $metode,
            'catatan' => $catatan,
            'dikonfirmasi_oleh' => $userId ?? \Illuminate\Support\Facades\Auth::id(),
        ];

        $newNominalDibayar = min($this->nominal, $this->nominal_dibayar + $nominal);
        $status = ($newNominalDibayar >= $this->nominal) ? 'lunas' : 'belum_bayar';

        $this->update([
            'nominal_dibayar'     => $newNominalDibayar,
            'status'              => $status,
            'nominal_transfer'    => null,
            'bukti_transfer_path' => null,
            'metode_pembayaran'   => $metode,
            'tanggal_bayar'       => now(),
            'dikonfirmasi_oleh'   => $userId ?? \Illuminate\Support\Facades\Auth::id(),
            'dikonfirmasi_pada'   => now(),
            'riwayat_cicilan'     => $riwayat,
            'catatan_bendahara'   => $catatan ?? $this->catatan_bendahara,
        ]);
    }

    /**
     * Helper pembuat kode unik tagihan.
     */
    public static function generateKodeTagihan(): string
    {
        return 'TAG-' . date('Ym') . '-' . strtoupper(substr(uniqid(), -6));
    }
}
