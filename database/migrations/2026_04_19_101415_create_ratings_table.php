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
        Schema::create('ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('user_id')->constrained('users')->onDelete('restrict')->onUpdate('cascade');
            $table->foreignId('wisata_id')->constrained('wisata')->onDelete('cascade');
            
            $table->integer('rating'); // Cukup simpan angka 1 sampai 5
            $table->timestamps();

            // Biar satu user cuma bisa kasih 1 ulasan per tempat wisata
            $table->unique(['user_id', 'wisata_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ratings');
    }
};
