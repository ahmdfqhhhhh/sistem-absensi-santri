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

#[Fillable(['name', 'email', 'password', 'role', 'no_hp', 'gender'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Konstan role yang tersedia dalam sistem sesuai PRD.
     */
    public const ROLE_SUPER_ADMIN = 'super_admin';

    public const ROLE_MUSYRIF = 'musyrif';

    public const ROLE_KEAMANAN = 'keamanan';

    public const ROLE_WALI_SANTRI = 'wali_santri';

    public const ROLE_BENDAHARA = 'bendahara';

    public function isSuperAdmin(): bool
    {
        return $this->role === self::ROLE_SUPER_ADMIN;
    }

    public function isMusyrif(): bool
    {
        return $this->role === self::ROLE_MUSYRIF;
    }

    public function isKeamanan(): bool
    {
        return $this->role === self::ROLE_KEAMANAN;
    }

    public function isWali(): bool
    {
        return $this->role === self::ROLE_WALI_SANTRI;
    }

    public function isBendahara(): bool
    {
        return $this->role === self::ROLE_BENDAHARA;
    }

    public function isPutra(): bool
    {
        return $this->gender === 'putra';
    }

    public function isPutri(): bool
    {
        return $this->gender === 'putri';
    }

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
