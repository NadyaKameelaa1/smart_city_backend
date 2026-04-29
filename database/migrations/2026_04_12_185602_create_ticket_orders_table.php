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
        Schema::create('ticket_orders', function (Blueprint $table) {
            $table->id();
            $table->string('kode_order', 30)->unique();
            $table->foreignUuid('user_id')->constrained('users')->onDelete('restrict')->onUpdate('cascade');
            $table->foreignId('wisata_id')->constrained('wisata')
                  ->restrictOnDelete()->cascadeOnUpdate();
            $table->date('tanggal_kunjungan');
            $table->unsignedTinyInteger('jumlah_dewasa')->default(0);
            $table->unsignedTinyInteger('jumlah_anak')->default(0);
            $table->unsignedBigInteger('total_harga');
            $table->enum('status_tiket', ['Aktif','Digunakan']);
            $table->string('metode_pembayaran', 50)->nullable();   
            $table->timestamps();

            $table->index('user_id');
            $table->index('status_tiket');
            $table->index('tanggal_kunjungan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_orders');
    }
};
