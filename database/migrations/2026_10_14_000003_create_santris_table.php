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
        Schema::create('santris', function (Blueprint $table) {
            $table->id();
            $table->string('nis', 50)->unique();
            $table->string('nama');
            $table->foreignId('asrama_id')->constrained('asramas')->cascadeOnDelete();
            $table->foreignId('kelas_id')->constrained('kelas')->cascadeOnDelete();
            $table->string('no_wa_wali', 25)->nullable();
            $table->string('qr_code_path')->nullable();
            $table->enum('jenis_kelamin', ['L', 'P'])->default('L');
            $table->enum('status', ['aktif', 'izin_pulang', 'skorsing', 'alumni', 'mutasi'])->default('aktif');
            $table->date('tanggal_lahir')->nullable();
            $table->text('alamat')->nullable();
            $table->string('foto_path')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('santris');
    }
};
