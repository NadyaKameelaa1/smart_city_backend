<?php

namespace Database\Seeders;

use App\Models\Desa;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DesaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $catId = DB::table('building_categories')->where('nama', 'Desa')->value('id');
        $filePath = database_path('seeders/data/desa.csv');
        $file = fopen($filePath, 'r');
        $isHeader = true;

        while (($row = fgetcsv($file, 2000, ",")) !== FALSE) {
            if ($isHeader) { $isHeader = false; continue; }

            // 1. Buat data Desa
            $desa = \App\Models\Desa::create([
                'kecamatan_id' => $row[0],
                'nama'         => $row[1],
                'kode'         => $row[2],
            ]);

            // 2. Buat Markernya
            $desa->marker()->create([
                'category_id' => $catId,
                'lat'          => $row[3],
                'lng'          => $row[4],
            ]);
        }
        fclose($file);
    }
}
