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
        Schema::create('pengumuman', function (Blueprint $table) {
            $table->id();
            $table->string('judul', 300);
            $table->string('slug', 320)->unique();
            $table->longText('isi');
            $table->string('publisher', 150)->nullable();
            // $table->string('kategori', 80)->nullable();
            $table->enum('prioritas', ['mendesak','sedang','umum'])->default('umum');
            $table->boolean('penting')->default(false);
            
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_berakhir')->nullable();
            $table->string('attachment_url', 500)->nullable();
            $table->timestamps();

            $table->index('prioritas');
            $table->index('penting');
            $table->index(['tanggal_mulai', 'tanggal_berakhir']);
        });
    }   

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengumuman');
    }
};
