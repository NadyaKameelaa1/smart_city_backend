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
        Schema::create('building_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained('building_groups')->onDelete('cascade');
            $table->string('nama', 100);
            $table->string('icon_marker')->default('fa-building');
            $table->string('color_theme', 7)->default('#4f83bf');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('building_categories');
    }
};
