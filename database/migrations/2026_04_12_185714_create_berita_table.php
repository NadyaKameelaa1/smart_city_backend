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
        Schema::create('berita', function (Blueprint $table) {
            $table->id();
            $table->string('judul', 300);
            $table->string('slug', 320)->unique();
            $table->longText('konten');
            $table->enum('kategori', ['kecamatan','desa']);
            $table->string('publisher')->default('Tidak Ketahui');
            $table->string('thumbnail', 500)->nullable();
            $table->unsignedBigInteger('views')->default(0);
            $table->boolean('featured')->default(false);
            $table->enum('status', ['draft','published'])->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('kategori');
            $table->index(['published_at']);
            $table->index('featured');  
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('berita');
    }
};
