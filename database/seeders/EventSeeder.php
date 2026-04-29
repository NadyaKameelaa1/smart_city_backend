<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'nama' => 'Melepas Penat 2026: Purbalingga',
                'slug' => 'melepas-penat-2026-purbalingga',
                'kategori' => 'Hiburan',
                'penyelenggara' => 'Shaolin Music',
                'tanggal_mulai' => '2026-06-27',
                'tanggal_selesai' => null,
                'jam_mulai' => '15:00',
                'jam_selesai' => '23:00',
                'lokasi' => 'GOR Goentoer Darjono',
                'kecamatan_id' => 5,
                'thumbnail' => 'melepas-penat-2026-purbalingga.jpeg',
                'status' => 'published',
            ]
        ];

        foreach ($data as $item) {
            \App\Models\Event::create($item);
        }


    }
}
