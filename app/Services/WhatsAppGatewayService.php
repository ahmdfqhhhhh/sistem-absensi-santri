<?php

namespace App\Services;

use App\Models\KamtibLog;
use App\Models\Perizinan;
use App\Models\Santri;

/**
 * Placeholder service untuk notifikasi WhatsApp ke wali santri.
 * Implementasi integrasi gateway WA dapat ditambahkan di sini.
 */
class WhatsAppGatewayService
{
    /**
     * Kirim notifikasi WhatsApp saat santri checkout / checkin di pos keamanan.
     */
    public function notifikasiKamtib(Santri $santri, KamtibLog $log, Perizinan $perizinan): void
    {
        // TODO: Implementasi pengiriman WhatsApp via gateway (Fonnte, WA Business API, dll.)
        // Contoh payload yang bisa dikirim:
        // - Nama santri, NIS, waktu checkout/checkin
        // - Nomor tujuan: $santri->no_wa_wali
        // Untuk saat ini, service ini hanya placeholder.
    }
}
