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
        if (Schema::hasTable('kamtib_logs') && ! Schema::hasColumn('kamtib_logs', 'nama_petugas')) {
            Schema::table('kamtib_logs', function (Blueprint $table) {
                $table->string('nama_petugas', 150)->nullable()->after('recorded_by');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('kamtib_logs') && Schema::hasColumn('kamtib_logs', 'nama_petugas')) {
            Schema::table('kamtib_logs', function (Blueprint $table) {
                $table->dropColumn('nama_petugas');
            });
        }
    }
};
