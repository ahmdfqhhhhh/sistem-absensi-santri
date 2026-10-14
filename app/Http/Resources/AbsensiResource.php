<?php

namespace App\Http\Resources;

use App\Models\Absensi;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Absensi
 */
class AbsensiResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'santri' => [
                'id' => $this->santri?->id,
                'nis' => $this->santri?->nis,
                'nama' => $this->santri?->nama,
                'jenis_kelamin' => $this->santri?->jenis_kelamin,
                'kelas' => $this->santri?->kelas?->nama_kelas,
                'asrama' => $this->santri?->asrama?->nama_asrama,
                'no_wa_wali' => $this->santri?->no_wa_wali,
            ],
            'sesi' => [
                'id' => $this->sesiAbsensi?->id,
                'nama_sesi' => $this->sesiAbsensi?->nama_sesi,
                'kode_sesi' => $this->sesiAbsensi?->kode_sesi,
                'jenis' => $this->sesiAbsensi?->jenis,
                'tanggal' => $this->sesiAbsensi?->tanggal?->format('Y-m-d'),
                'jam_mulai' => $this->sesiAbsensi?->jam_mulai,
                'jam_selesai' => $this->sesiAbsensi?->jam_selesai,
            ],
            'waktu_absen' => $this->waktu_absen?->format('Y-m-d H:i:s'),
            'status' => $this->status,
            'status_label' => match ($this->status) {
                'hadir' => 'Hadir',
                'terlambat' => 'Terlambat',
                'izin' => 'Izin',
                'sakit' => 'Sakit',
                'alpa' => 'Alpa',
                default => ucfirst((string) $this->status),
            },
            'metode' => $this->metode,
            'keterangan' => $this->keterangan,
            'recorded_by' => $this->recordedBy?->name,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
