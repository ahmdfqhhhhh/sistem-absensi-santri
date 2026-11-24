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
        Schema::table('tagihans', function (Blueprint $table) {
            $table->unsignedBigInteger('nominal_transfer')->nullable()->after('nominal_dibayar')
                ->comment('Nominal pembayaran/cicilan yang diajukan oleh wali santri');
            $table->json('riwayat_cicilan')->nullable()->after('catatan_bendahara')
                ->comment('Log riwayat pembayaran bertahap/cicilan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tagihans', function (Blueprint $table) {
            $table->dropColumn(['nominal_transfer', 'riwayat_cicilan']);
        });
    }
};
