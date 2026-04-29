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
        Schema::create('event', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 200);
            $table->string('slug', 220)->unique();
            $table->enum('kategori', ['Budaya', 'Olahraga', 'Pemerintahan', 'Pariwisata', 'Pendidikan', 'Hiburan', 'Lainnya']);
            $table->string('penyelenggara', 150)->nullable();
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai')->nullable();
            $table->time('jam_mulai')->nullable();
            $table->time('jam_selesai')->nullable();
            $table->string('lokasi', 255)->nullable();
            $table->foreignId('kecamatan_id')->nullable()->constrained('kecamatan')
                  ->nullOnDelete()->cascadeOnUpdate();
            $table->string('thumbnail', 500)->nullable();
            $table->enum('status', ['draft','published','selesai'])
                  ->default('draft');
            $table->timestamps();

            $table->index('status');
            $table->index('tanggal_mulai');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event');
    }
};
