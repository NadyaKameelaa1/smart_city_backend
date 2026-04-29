<?php

namespace Database\Seeders;

use App\Models\Kecamatan;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KecamatanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $catId = DB::table('building_categories')->where('nama', 'Kecamatan')->value('id');
        $filePath = database_path('seeders/data/kecamatan.csv');
        
        if (!file_exists($filePath)) {
            $this->command->error("File CSV tidak ditemukan di: $filePath");
            return;
        }

        $file = fopen($filePath, 'r');
        $isHeader = true;

        while (($row = fgetcsv($file, 1000, ",")) !== FALSE) {
            if ($isHeader) { 
                $isHeader = false; 
                continue; 
            }

            // Validasi: Pastikan baris memiliki data yang cukup (minimal kolom 0 dan 1)
            // isset($row[1]) mencegah error "Undefined array key 1"
            if (!isset($row[0]) || !isset($row[1]) || empty($row[0])) {
                continue; 
            }

            // 1. Buat data Kecamatan
            $kec = \App\Models\Kecamatan::create([
                'nama' => $row[0],
                'kode' => $row[1],
            ]);

            // 2. Buat Markernya di tabel map_markers
            $kec->marker()->create([
                'category_id' => $catId,
                'lat'  => $row[2] ?? 0, // Pake ?? 0 supaya kalau CSV-nya kosong gak error
                'lng'  => $row[3] ?? 0,
            ]);
        }
        fclose($file);
    }
}
