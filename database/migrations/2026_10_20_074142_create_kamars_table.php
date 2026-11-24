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
        Schema::create('kamars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asrama_id')->constrained('asramas')->cascadeOnDelete();
            $table->string('nama_kamar', 100);
            $table->unsignedSmallInteger('kapasitas')->default(10);
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->unique(['asrama_id', 'nama_kamar']);
            $table->index('asrama_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kamars');
    }
};
