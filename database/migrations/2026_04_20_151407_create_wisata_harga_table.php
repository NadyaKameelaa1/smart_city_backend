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
        Schema::create('wisata_harga', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wisata_id')->constrained('wisata')->onDelete('cascade');
            $table->enum('day_type', ['weekday', 'weekend', 'holiday'])->default('weekday');
            $table->unsignedInteger('harga_anak')->default(0);
            $table->unsignedInteger('harga_dewasa')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wisata_harga');
    }
};
