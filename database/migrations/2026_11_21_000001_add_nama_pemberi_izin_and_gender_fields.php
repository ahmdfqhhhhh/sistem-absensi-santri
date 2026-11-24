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
        if (Schema::hasTable('perizinans') && ! Schema::hasColumn('perizinans', 'nama_pemberi_izin')) {
            Schema::table('perizinans', function (Blueprint $table) {
                $table->string('nama_pemberi_izin', 150)->nullable()->after('catatan_approval');
            });
        }

        if (Schema::hasTable('users') && ! Schema::hasColumn('users', 'gender')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('gender', 20)->nullable()->default('semua')->after('role');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('perizinans') && Schema::hasColumn('perizinans', 'nama_pemberi_izin')) {
            Schema::table('perizinans', function (Blueprint $table) {
                $table->dropColumn('nama_pemberi_izin');
            });
        }

        if (Schema::hasTable('users') && Schema::hasColumn('users', 'gender')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('gender');
            });
        }
    }
};
