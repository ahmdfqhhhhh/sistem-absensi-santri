<?php

namespace App\Http\Resources;

use App\Models\Perizinan;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

/**
 * @mixin Perizinan
 */
class PerizinanResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $now = now();
        $isActiveNow = in_array($this->status, ['disetujui', 'sedang_berlangsung'])
            && $this->tanggal_mulai <= $now
            && $this->tanggal_selesai >= $now;

        $qrCodeUrl = $this->qr_code_path
            ? (Storage::disk('public')->exists($this->qr_code_path)
                ? Storage::disk('public')->url($this->qr_code_path)
                : url('storage/'.$this->qr_code_path))
            : null;

        return [
            'id' => $this->id,
            'nomor_surat' => $this->nomor_surat,
            'santri' => [
                'id' => $this->santri?->id,
                'nis' => $this->santri?->nis,
                'nama' => $this->santri?->nama,
                'jenis_kelamin' => $this->santri?->jenis_kelamin,
                'kelas' => $this->santri?->kelas?->nama_kelas,
                'asrama' => $this->santri?->asrama?->nama_asrama,
                'no_wa_wali' => $this->santri?->no_wa_wali,
            ],
            'jenis_izin' => $this->jenis_izin,
            'jenis_izin_label' => match ($this->jenis_izin) {
                'pulang' => 'Izin Pulang ke Rumah',
                'keluar_komplek' => 'Keluar Komplek Pesantren',
                'sakit' => 'Izin Sakit / Pengobatan',
                'sambangan_keluarga' => 'Sambangan Keluarga',
                'tugas_pondok' => 'Tugas Luar Pondok',
                default => ucfirst(str_replace('_', ' ', (string) $this->jenis_izin)),
            },
            'keperluan' => $this->keperluan,
            'tanggal_mulai' => $this->tanggal_mulai?->format('Y-m-d H:i:s'),
            'tanggal_selesai' => $this->tanggal_selesai?->format('Y-m-d H:i:s'),
            'tanggal_kembali_aktual' => $this->tanggal_kembali_aktual?->format('Y-m-d H:i:s'),
            'status' => $this->status,
            'status_label' => match ($this->status) {
                'pending' => 'Menunggu Persetujuan',
                'disetujui' => 'Disetujui',
                'ditolak' => 'Ditolak',
                'sedang_berlangsung' => 'Sedang Berlangsung',
                'selesai' => 'Selesai (Telah Kembali)',
                'terlambat_kembali' => 'Terlambat Kembali',
                default => ucfirst((string) $this->status),
            },
            'approved_by' => $this->approvedBy?->name,
            'catatan_approval' => $this->catatan_approval,
            'penjemput' => [
                'nama' => $this->penjemput_nama,
                'hubungan' => $this->penjemput_hubungan,
            ],
            'qr_token' => $this->qr_token,
            'qr_code_url' => $qrCodeUrl,
            'is_active_now' => $isActiveNow,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
