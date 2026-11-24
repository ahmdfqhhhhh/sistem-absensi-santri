<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kamar extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'kamars';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'asrama_id',
        'nama_kamar',
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
     * Relasi ke data Asrama.
     *
     * @return BelongsTo<Asrama, $this>
     */
    public function asrama(): BelongsTo
    {
        return $this->belongsTo(Asrama::class, 'asrama_id');
    }

    /**
     * Relasi ke Santri yang menempati kamar ini.
     *
     * @return HasMany<Santri, $this>
     */
    public function santris(): HasMany
    {
        return $this->hasMany(Santri::class, 'asrama_id', 'asrama_id')
            ->where('kamar', $this->nama_kamar);
    }
}
