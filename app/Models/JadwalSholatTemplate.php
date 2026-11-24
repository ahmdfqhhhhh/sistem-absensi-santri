<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class JadwalSholatTemplate extends Model
{
    protected $table = 'jadwal_sholat_templates';

    protected $fillable = [
        'kode',
        'nama_sesi',
        'jenis',
        'jam_mulai',
        'jam_selesai',
        'batas_terlambat',
        'deskripsi',
        'aktif',
        'urutan',
    ];

    protected function casts(): array
    {
        return [
            'aktif' => 'boolean',
            'urutan' => 'integer',
        ];
    }

    /**
     * Scope: hanya template yang aktif, diurutkan sesuai urutan sholat.
     *
     * @param  Builder<$this>  $query
     * @return Builder<$this>
     */
    public function scopeAktif($query)
    {
        return $query->where('aktif', true)->orderBy('urutan');
    }

    /**
     * Label kode yang mudah dibaca.
     */
    public function getLabelKodeAttribute(): string
    {
        return match ($this->kode) {
            'SUBUH' => '🌅 Subuh',
            'KBM' => '🎓 KBM',
            'DZUHUR' => '☀️ Dzuhur',
            'ASHAR' => '🌤 Ashar',
            'MAGHRIB' => '🌆 Maghrib',
            'ISYA' => '🌙 Isya',
            default => $this->kode,
        };
    }
}
