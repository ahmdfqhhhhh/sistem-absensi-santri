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
        if (! Schema::hasTable('kamars')) {
            Schema::create('kamars', function (Blueprint $table) {
                $table->id();
                $table->foreignId('asrama_id')->constrained('asramas')->cascadeOnDelete();
                $table->string('nama_kamar', 100);
                $table->unsignedSmallInteger('kapasitas')->default(10);
                $table->string('keterangan')->nullable();
                $table->timestamps();

                $table->index('asrama_id');
            });
        }

        if (Schema::hasTable('santris')) {
            Schema::table('santris', function (Blueprint $table) {
                if (! Schema::hasColumn('santris', 'kamar')) {
                    $table->string('kamar', 50)->nullable()->after('asrama_id');
                }
                if (! Schema::hasColumn('santris', 'nama_wali')) {
                    $table->string('nama_wali', 100)->nullable()->after('kelas_id');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('santris')) {
            Schema::table('santris', function (Blueprint $table) {
                if (Schema::hasColumn('santris', 'kamar')) {
                    $table->dropColumn('kamar');
                }
                if (Schema::hasColumn('santris', 'nama_wali')) {
                    $table->dropColumn('nama_wali');
                }
            });
        }

        Schema::dropIfExists('kamars');
    }
};
