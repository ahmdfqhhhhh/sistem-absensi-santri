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
        Schema::create('sesi_absensis', function (Blueprint $table) {
            $table->id();
            $table->string('nama_sesi');
            $table->string('kode_sesi', 60)->nullable()->unique();
            $table->enum('jenis', [
                'sholat_berjamaah',
                'kegiatan_belajar',
                'tahfidz',
                'apel_kebersihan',
                'pengajian_kitab',
                'lainnya',
            ])->default('sholat_berjamaah');
            $table->date('tanggal');
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->time('batas_terlambat')->nullable();
            $table->enum('status', ['dibuka', 'ditutup', 'dibatalkan'])->default('dibuka');
            $table->text('deskripsi')->nullable();
            $table->timestamps();

            $table->index('tanggal');
            $table->index('status');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sesi_absensis');
    }
};
