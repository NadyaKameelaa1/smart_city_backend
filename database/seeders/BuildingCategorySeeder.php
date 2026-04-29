<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BuildingCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil ID masing-masing group
        $fasumId = DB::table('building_groups')->where('nama', 'Fasilitas Umum')->value('id');
        $transId = DB::table('building_groups')->where('nama', 'Transportasi')->value('id');
        $sehatId = DB::table('building_groups')->where('nama', 'Fasilitas Kesehatan')->value('id');
        $wisataId = DB::table('building_groups')->where('nama', 'Destinasi Wisata')->value('id');

        $categories = [
            // Fasilitas Umum - Hijau (#2ecc71)
            ['group_id' => $fasumId, 'nama' => 'CCTV', 'icon' => 'fa-video', 'color' => '#2ecc71'],
            ['group_id' => $fasumId, 'nama' => 'Taman', 'icon' => 'fa-tree', 'color' => '#2ecc71'],
            ['group_id' => $fasumId, 'nama' => 'RPTRA', 'icon' => 'fa-child', 'color' => '#2ecc71'],
            ['group_id' => $fasumId, 'nama' => 'Wifi', 'icon' => 'fa-wifi', 'color' => '#2ecc71'],
            ['group_id' => $fasumId, 'nama' => 'RFH', 'icon' => 'fa-building', 'color' => '#2ecc71'],
            ['group_id' => $fasumId, 'nama' => 'Sarana Olahraga', 'icon' => 'fa-running', 'color' => '#2ecc71'],

            // Transportasi - Biru (#3498db)
            ['group_id' => $transId, 'nama' => 'Bus Stop', 'icon' => 'fa-bus', 'color' => '#3498db'],
            ['group_id' => $transId, 'nama' => 'Halte TJ', 'icon' => 'fa-bus-alt', 'color' => '#3498db'],
            ['group_id' => $transId, 'nama' => 'Stasiun MRT', 'icon' => 'fa-train', 'color' => '#3498db'],
            ['group_id' => $transId, 'nama' => 'Stasiun LRT', 'icon' => 'fa-subway', 'color' => '#3498db'],

            // Fasilitas Kesehatan - Merah (#e74c3c)
            ['group_id' => $sehatId, 'nama' => 'Rumah Sakit', 'icon' => 'fa-hospital', 'color' => '#e74c3c'],
            ['group_id' => $sehatId, 'nama' => 'Puskesmas', 'icon' => 'fa-clinic-medical', 'color' => '#e74c3c'],
            ['group_id' => $sehatId, 'nama' => 'Klinik', 'icon' => 'fa-stethoscope', 'color' => '#e74c3c'],
            ['group_id' => $sehatId, 'nama' => 'Lab Kesehatan', 'icon' => 'fa-flask', 'color' => '#e74c3c'],

            // Destinasi Wisata - Kuning (#f1c40f)
            ['group_id' => $wisataId, 'nama' => 'Wisata Alam', 'icon' => 'fa-leaf', 'color' => '#f1c40f'],
            ['group_id' => $wisataId, 'nama' => 'Kuliner', 'icon' => 'fa-utensils', 'color' => '#f1c40f'],
            ['group_id' => $wisataId, 'nama' => 'Belanja', 'icon' => 'fa-shopping-bag', 'color' => '#f1c40f'],
            ['group_id' => $wisataId, 'nama' => 'Kepulauan', 'icon' => 'fa-umbrella-beach', 'color' => '#f1c40f'],
            ['group_id' => $wisataId, 'nama' => 'Sejarah', 'icon' => 'fa-monument', 'color' => '#f1c40f'],
            ['group_id' => $wisataId, 'nama' => 'Kebudayaan dan Religi', 'icon' => 'fa-mosque', 'color' => '#f1c40f'],
        ];

        foreach ($categories as $cat) {
            DB::table('building_categories')->insert([
                'group_id' => $cat['group_id'],
                'nama' => $cat['nama'],
                'icon_marker' => $cat['icon'],
                'color_theme' => $cat['color'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
