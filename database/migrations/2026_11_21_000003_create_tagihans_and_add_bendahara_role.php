<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Perbarui kolom role pada tabel users agar mendukung 'bendahara'
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('super_admin', 'musyrif', 'keamanan', 'wali_santri', 'bendahara') NOT NULL DEFAULT 'wali_santri'");
        }

        // 2. Buat tabel tagihans untuk sistem keuangan & pembayaran santri
        if (! Schema::hasTable('tagihans')) {
            Schema::create('tagihans', function (Blueprint $table) {
                $table->id();
                $table->foreignId('santri_id')->constrained('santris')->cascadeOnDelete();
                $table->string('kode_tagihan', 50)->unique();
                $table->string('judul_tagihan', 150);
                $table->string('kategori', 50)->default('spp'); // spp, uang_gedung, kitab, pendaftaran, kegiatan, lainnya
                $table->unsignedBigInteger('nominal');
                $table->unsignedBigInteger('nominal_dibayar')->default(0);
                $table->date('jatuh_tempo');
                $table->enum('status', ['belum_bayar', 'menunggu_konfirmasi', 'lunas', 'dibatalkan'])->default('belum_bayar');
                $table->string('metode_pembayaran', 50)->nullable(); // transfer_bank, qris, tunai_bendahara, manual
                $table->dateTime('tanggal_bayar')->nullable();
                $table->string('bukti_transfer_path')->nullable();
                $table->text('catatan_wali')->nullable();
                $table->text('catatan_bendahara')->nullable();
                $table->foreignId('dikonfirmasi_oleh')->nullable()->constrained('users')->nullOnDelete();
                $table->dateTime('dikonfirmasi_pada')->nullable();
                $table->timestamps();

                $table->index(['santri_id', 'status']);
                $table->index(['jatuh_tempo', 'status']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tagihans');

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('super_admin', 'musyrif', 'keamanan', 'wali_santri') NOT NULL DEFAULT 'wali_santri'");
        }
    }
};
