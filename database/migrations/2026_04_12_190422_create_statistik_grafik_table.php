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
        Schema::create('statistik_grafik', function (Blueprint $table) {
            $table->id();
            $table->string('judul', 150);
            $table->string('kategori', 80);
            $table->json('data_json')->comment('misal: [{"label":"SD","pct":38,"color":"#hex"}]');
            $table->year('tahun');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('statistik_grafik');
    }
};
