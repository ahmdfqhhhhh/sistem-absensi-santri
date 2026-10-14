<?php

namespace App\Http\Resources;

use App\Models\KamtibLog;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin KamtibLog
 */
class KamtibLogResource extends JsonResource
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
            'tipe_log' => $this->tipe_log,
            'waktu_log' => $this->waktu_log?->format('Y-m-d H:i:s'),
            'judul' => $this->judul_pelanggaran,
            'kategori' => $this->kategori,
            'poin_pelanggaran' => $this->poin_pelanggaran,
            'tanggal_kejadian' => $this->tanggal_kejadian?->format('Y-m-d'),
            'lokasi_kejadian' => $this->lokasi_kejadian,
            'tindakan_sanksi' => $this->tindakan_sanksi,
            'status_penyelesaian' => $this->status_penyelesaian,
            'catatan' => $this->catatan,
            'petugas' => $this->recordedBy?->name,
            'santri' => [
                'id' => $this->santri?->id,
                'nis' => $this->santri?->nis,
                'nama' => $this->santri?->nama,
                'kelas' => $this->santri?->kelas?->nama_kelas,
                'asrama' => $this->santri?->asrama?->nama_asrama,
            ],
            'perizinan' => $this->perizinan ? [
                'id' => $this->perizinan->id,
                'nomor_surat' => $this->perizinan->nomor_surat,
                'status' => $this->perizinan->status,
                'jenis_izin' => $this->perizinan->jenis_izin,
                'tanggal_mulai' => $this->perizinan->tanggal_mulai?->format('Y-m-d H:i:s'),
                'tanggal_selesai' => $this->perizinan->tanggal_selesai?->format('Y-m-d H:i:s'),
                'tanggal_kembali_aktual' => $this->perizinan->tanggal_kembali_aktual?->format('Y-m-d H:i:s'),
            ] : null,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
