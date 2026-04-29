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
        Schema::create('map_markers', function (Blueprint $table) {
            // agar kita tidak perlu buat banyak tabel marker untuk masing2 tempat, karna disini ada berbagai marker yang berbeda. marker wisata, marker cctv, dan marker bangunan biasa yang bermacam macam.
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained('building_categories')->onDelete('set null');
            
            /**
             * SAKTI (Polymorphic): Kolom ini adalah "Kunci" agar tabel ini bisa ditempelkan ke tabel manapun. analogi : stiker yg menunjukkan perbedaan di tiap barang
             * $table->morphs('markable') akan otomatis menciptakan dua kolom:
             * 1. markable_id   => Menyimpan ID data asli (Contoh: 10)
             * 2. markable_type => Menyimpan Nama Modelnya (Contoh: 'App\Models\Cctv' atau 'App\Models\Wisata')
             * * MANFAATNYA: Kita tidak perlu buat banyak tabel marker (wisata_markers, cctv_markers, bulding_markers).
             * Cukup SATU tabel ini untuk SEMUA titik di peta Purbalingga.
             */
            $table->morphs('markable'); 

            $table->decimal('lat', 10, 8);
            $table->decimal('lng', 11, 8);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('map_markers');
    }
};
