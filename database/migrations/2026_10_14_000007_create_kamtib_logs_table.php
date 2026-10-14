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
        Schema::create('kamtib_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('santri_id')->constrained('santris')->cascadeOnDelete();
            $table->foreignId('perizinan_id')->nullable()->constrained('perizinans')->nullOnDelete();
            $table->enum('tipe_log', ['checkout', 'checkin', 'pelanggaran'])->default('pelanggaran');
            $table->dateTime('waktu_log')->nullable();
            $table->string('judul_pelanggaran');
            $table->enum('kategori', ['ringan', 'sedang', 'berat'])->default('ringan');
            $table->unsignedInteger('poin_pelanggaran')->default(5);
            $table->date('tanggal_kejadian');
            $table->string('lokasi_kejadian')->nullable();
            $table->text('tindakan_sanksi')->nullable();
            $table->enum('status_penyelesaian', ['belum_selesai', 'dalam_proses', 'selesai'])->default('belum_selesai');
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('catatan')->nullable();
            $table->string('bukti_foto_path')->nullable();
            $table->timestamps();

            $table->index('status_penyelesaian');
            $table->index('kategori');
            $table->index('created_at');
            $table->index('tanggal_kejadian');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kamtib_logs');
    }
};
