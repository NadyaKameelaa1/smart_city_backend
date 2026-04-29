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
        Schema::create('wisata', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 200);
            $table->string('slug', 220)->unique();
            $table->enum('kategori', ['Alam','Rekreasi','Budaya','Kuliner','Religi','Edukasi']);
            $table->longText('deskripsi')->nullable();;
            $table->foreignId('kecamatan_id')->nullable()->constrained('kecamatan')
                  ->nullOnDelete()->cascadeOnUpdate();
            $table->time('jam_buka')->nullable();
            $table->time('jam_tutup')->nullable();
            $table->string('fasilitas', 100)->nullable();

            $table->string('kontak', 100)->nullable();
            $table->decimal('rating', 3, 2)->default(0.00);
            $table->unsignedInteger('total_review')->default(0);
            $table->string('thumbnail', 500)->nullable();
            $table->enum('status', ['Aktif','Nonaktif','Draft'])->default('Draft');
            $table->timestamps();

            $table->index('status');
            $table->index('kategori');
            $table->index(['rating']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wisata');
    }
};
