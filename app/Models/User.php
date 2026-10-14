<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Relasi ke presensi yang dicatat oleh user ini.
     *
     * @return HasMany<Absensi, $this>
     */
    public function absensisRecorded(): HasMany
    {
        return $this->hasMany(Absensi::class, 'recorded_by');
    }

    /**
     * Relasi ke perizinan santri yang disetujui oleh user ini.
     *
     * @return HasMany<Perizinan, $this>
     */
    public function perizinansApproved(): HasMany
    {
        return $this->hasMany(Perizinan::class, 'approved_by');
    }

    /**
     * Relasi ke log kamtib yang dicatat oleh user ini.
     *
     * @return HasMany<KamtibLog, $this>
     */
    public function kamtibLogsRecorded(): HasMany
    {
        return $this->hasMany(KamtibLog::class, 'recorded_by');
    }
}
