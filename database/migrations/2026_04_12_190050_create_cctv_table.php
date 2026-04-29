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
        Schema::create('cctv', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 150);
            // $table->string('lokasi', 255)->nullable();
            // $table->foreignId('kecamatan_id')->nullable()->constrained('kecamatan')
            //       ->nullOnDelete()->cascadeOnUpdate();
            $table->string('stream_url', 500)->comment('YouTube embed URL atau RTSP');
            $table->enum('status', ['online','offline'])->default('online');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cctv');
    }
};
