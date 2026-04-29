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
        Schema::create('statistik_kota', function (Blueprint $table) {
            $table->id();
            $table->string('kategori', 80)->comment('misal: penduduk, ekonomi, pendidikan');
            $table->string('label', 150)->comment('misal: Jumlah Penduduk');
            $table->string('nilai', 50)->comment('disimpan string: 921.543 atau 77,65 km²');
            $table->string('satuan', 50)->nullable()->comment('misal:jiwa, km², %');
            $table->year('tahun');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('statistik_kota');
    }
};
