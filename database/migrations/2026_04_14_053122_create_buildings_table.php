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
        Schema::create('buildings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('building_categories')->onDelete('cascade');
            $table->foreignId('kecamatan_id')->constrained('kecamatan');
            $table->foreignId('desa_id')->nullable()->constrained('desa');
            
            $table->string('nama');
            $table->string('slug')->unique();
            $table->text('alamat')->nullable();
 
            $table->longText('deskripsi')->nullable();
            $table->string('kontak')->nullable();
            $table->string('website')->nullable();
            
            $table->json('metadata')->nullable()->comment('Menampung data dinamis per kategori');
            $table->string('thumbnail')->nullable();

            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('buildings');
    }
};
