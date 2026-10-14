<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('perizinans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('santri_id')->constrained('santris')->cascadeOnDelete();
            $table->string('nomor_surat', 80)->nullable()->unique();
            $table->enum('jenis_izin', [
                'pulang',
                'keluar_komplek',
                'sakit',
                'sambangan_keluarga',
                'tugas_pondok',
            ])->default('pulang');
            $table->text('keperluan');
            $table->dateTime('tanggal_mulai');
            $table->dateTime('tanggal_selesai');
            $table->dateTime('tanggal_kembali_aktual')->nullable();
            $table->enum('status', [
                'pending',
                'disetujui',
                'ditolak',
                'sedang_berlangsung',
                'selesai',
                'terlambat_kembali',
            ])->default('pending');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('catatan_approval')->nullable();
            $table->string('penjemput_nama')->nullable();
            $table->string('penjemput_hubungan', 50)->nullable();
            $table->string('bukti_dokumen_path')->nullable();
            $table->string('qr_code_path')->nullable();
            $table->string('qr_token', 100)->nullable()->unique();
            $table->timestamps();

            $table->index('status');
            $table->index('created_at');
            $table->index(['tanggal_mulai', 'tanggal_selesai']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('perizinans');
    }
};
