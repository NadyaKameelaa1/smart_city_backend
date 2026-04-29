<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StatistikSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('statistik_kota')->insert([
            ['kategori' => 'penduduk', 'label' => 'Jumlah Penduduk', 'nilai' => '1.052.417', 'satuan' => 'jiwa', 'tahun' => 2025],
            ['kategori' => 'penduduk', 'label' => 'Kepadatan Penduduk', 'nilai' => '1.353', 'satuan' => 'jiwa/km²', 'tahun' => 2025],
            ['kategori' => 'geografi', 'label' => 'Jumlah Kecamatan', 'nilai' => '18', 'satuan' => 'kecamatan', 'tahun' => 2025],
            ['kategori' => 'geografi', 'label' => 'Jumlah Desa/Kelurahan', 'nilai' => '239', 'satuan' => 'desa', 'tahun' => 2025],
            ['kategori' => 'geografi', 'label' => 'Luas Wilayah', 'nilai' => '777,64', 'satuan' => 'km²', 'tahun' => 2025],
            ['kategori' => 'sosial', 'label' => 'IPM Purbalingga', 'nilai' => '71,90', 'satuan' => null, 'tahun' => 2025],
            ['kategori' => 'ekonomi', 'label' => 'PDRB Per Kapita', 'nilai' => '33,52', 'satuan' => 'juta Rp', 'tahun' => 2025],
            ['kategori' => 'penduduk', 'label' => 'Angkatan Kerja', 'nilai' => '558.120', 'satuan' => 'jiwa', 'tahun' => 2025],
        ]);

        // Seeder untuk statistik_grafik (Tren Pendidikan & Ekonomi 2025/2026)
        DB::table('statistik_grafik')->insert([
            [
                'judul' => 'Tingkat Pendidikan',
                'kategori' => 'sosial',
                'data_json' => json_encode([
                    ['label' => 'Tidak/Belum Sekolah', 'pct' => 11, 'color' => '#ef4444'],
                    ['label' => 'SD / Sederajat', 'pct' => 36, 'color' => '#f59e0b'],
                    ['label' => 'SMP / Sederajat', 'pct' => 21, 'color' => '#10b981'],
                    ['label' => 'SMA / SMK', 'pct' => 23, 'color' => '#3b82f6'],
                    ['label' => 'Diploma / Sarjana', 'pct' => 9, 'color' => '#8b5cf6'],
                ]),
                'tahun' => 2025
            ],
            [
                'judul' => 'Sektor Ekonomi Utama',
                'kategori' => 'ekonomi',
                'data_json' => json_encode([
                    ['label' => 'Industri Pengolahan', 'pct' => 35, 'color' => '#10b981'],
                    ['label' => 'Pertanian & Kehutanan', 'pct' => 23, 'color' => '#059669'],
                    ['label' => 'Perdagangan', 'pct' => 17, 'color' => '#3b82f6'],
                    ['label' => 'Konstruksi', 'pct' => 9, 'color' => '#f59e0b'],
                    ['label' => 'Lainnya', 'pct' => 16, 'color' => '#94a3b8'],
                ]),
                'tahun' => 2025
            ]
        ]);
    }
}
