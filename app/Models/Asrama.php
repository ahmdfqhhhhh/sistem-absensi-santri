<?php

namespace App\Models;

use Database\Factories\AsramaFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Asrama extends Model
{
    /** @use HasFactory<AsramaFactory> */
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'asramas';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nama_asrama',
        'gedung',
        'pembina',
        'gender',
        'kapasitas',
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
            'kapasitas' => 'integer',
        ];
    }

    /**
     * Relasi ke data Santri yang menempati asrama ini.
     *
     * @return HasMany<Santri, $this>
     */
    public function santris(): HasMany
    {
        return $this->hasMany(Santri::class, 'asrama_id');
    }

    /**
     * Relasi ke daftar Kamar dalam asrama ini.
     *
     * @return HasMany<Kamar, $this>
     */
    public function kamars(): HasMany
    {
        return $this->hasMany(Kamar::class, 'asrama_id');
    }
}
