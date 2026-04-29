<?php

namespace Database\Seeders;

// use App\Models\MapMarker;
use App\Models\Wisata;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class WisataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Ambil ID kategori Building (untuk Marker)
        $catId = DB::table('building_categories')->where('nama', 'Wisata')->value('id');

        // 2. Ambil ID Kecamatan (agar tidak manual ID-nya, cari berdasarkan nama)
        $bojongsari = DB::table('kecamatan')->where('nama', 'Bojongsari')->value('id');
        $karangreja = DB::table('kecamatan')->where('nama', 'Karangreja')->value('id');

        // 3. Data Lengkap Wisata
        $dataWisata = [
            [
                'nama'          => 'Owabong Water Park',
                'kategori'      => 'Rekreasi',
                'deskripsi'     => 'Owabong (Obyek Wisata Air Bojongsari) adalah taman air terbesar dan terpopuler di Kabupaten Purbalingga. Dengan luas area lebih dari 5 hektar, Owabong menawarkan berbagai wahana air yang seru dan aman untuk seluruh keluarga.',
                'kecamatan_id'  => $bojongsari,
                'jam_buka'      => '07:00:00',
                'jam_tutup'     => '18:00:00',
                'fasilitas'     => 'Parkir Luas, Mushola, Food Court, Ruang Ganti',
                'thumbnail'     => 'wisata/Wisata-Owabong.png', // Path di storage/app/public/wisata
                'lat'           => -7.351234,
                'lng'           => 109.345678,
                'wisata_harga' => [
                    ['day_type' => 'weekday', 'harga_anak' => 0, 'harga_dewasa' => 25000],
                    ['day_type' => 'weekend', 'harga_anak' => 0, 'harga_dewasa' => 35000],
                ]
            ],
            [
                'nama'          => 'Goa Lawa Purbalingga',
                'kategori'      => 'Alam',
                'deskripsi'     => 'Pesona goa kelelawar yang eksotis di bawah aliran lava purba.',
                'kecamatan_id'  => $karangreja,
                'jam_buka'      => '08:00:00',
                'jam_tutup'     => '16:00:00',
                'fasilitas'     => 'Lampu Hias, Cafe, Jalur Tracking',
                'thumbnail'     => 'wisata/Wisata-Golaga.jpg',
                'lat'           => -7.212345,
                'lng'           => 109.312345,
                'wisata_harga' => [
                    ['day_type' => 'weekday', 'harga_anak' => 20000, 'harga_dewasa' => 20000],
                    ['day_type' => 'weekend', 'harga_anak' => 25000, 'harga_dewasa' => 25000],
                ]
            ]
        ];

        foreach ($dataWisata as $item) {
            // Simpan ke tabel wisata
            $wisata = Wisata::create([
                'nama'          => $item['nama'],
                'slug'          => Str::slug($item['nama']),
                'kategori'      => $item['kategori'],
                'deskripsi'     => $item['deskripsi'],
                'kecamatan_id'  => $item['kecamatan_id'],
                'jam_buka'      => $item['jam_buka'],
                'jam_tutup'     => $item['jam_tutup'],
                'fasilitas'     => $item['fasilitas'],
                'thumbnail'     => $item['thumbnail'],
                'status'        => 'Aktif',
            ]);

            foreach ($item['wisata_harga'] as $price) {
                $wisata->prices()->create($price);
            }

            // Simpan ke tabel map_markers (Polymorphic)
            $wisata->marker()->create([
                'category_id' => $catId,
                'lat'         => $item['lat'],
                'lng'         => $item['lng'],
            ]);
        }
    }
}