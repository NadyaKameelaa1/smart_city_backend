<?php

namespace Database\Seeders;

// use App\Models\Cctv;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MapMarkerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categoryId = DB::table('building_categories')->where('nama', 'CCTV')->value('id');
        $allCctv = \App\Models\Cctv::all();

        // Data koordinat manual karena di tabel cctv sudah tidak ada kolom lat/lng
        $coords = [
            ['lat' => -7.387654, 'lng' => 109.367890], // Alun-alun
            ['lat' => -7.391234, 'lng' => 109.354321], // Terminal
            ['lat' => -7.380000, 'lng' => 109.360000], // Bancar
            ['lat' => -7.385000, 'lng' => 109.370000], // Segamas
        ];

        foreach ($allCctv as $index => $cctv) {
            $cctv->marker()->create([
                'category_id' => $categoryId,
                'lat' => $coords[$index]['lat'],
                'lng' => $coords[$index]['lng'],
            ]);
        }
    }
}
