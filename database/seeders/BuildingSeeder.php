<?php

namespace Database\Seeders;

use App\Models\Building;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BuildingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $building = Building::create([
        'category_id' => 1, // Pastikan ID kategori ini ada (misal: RS)
        'kecamatan_id' => 1, 
        'nama' => 'RSUD dr. R. Goeteng Taroenadibrata',
        'slug' => 'rsud-goeteng',
        'alamat' => 'Jl. Tentara Pelajar No.22, Purbalingga',
        'metadata' => [
            'tipe_rs' => 'Tipe B',
            'jumlah_bed' => 200,
            'igd_24_jam' => 'Ya'
        ],
        'status' => 'active'
    ]);

    $building->marker()->create([
        'category_id' => 1,
        'lat' => -7.3883,
        'lng' => 109.3639,
        'is_active' => true
    ]);
     }
}
