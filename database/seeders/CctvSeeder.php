<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CctvSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cctvs = [
            [
                'nama' => 'Alun-alun Purbalingga',
                'stream_url' => 'https://www.youtube.com/embed/n7FLPsRLboI', // Dummy live
            ],
            [
                'nama' => 'Simpang Empat Terminal',
                'stream_url' => 'https://www.youtube.com/embed/JJ3MWNYVCU4',
            ],
            [
                'nama' => 'Simpang Empat Bancar',
                'stream_url' => 'https://www.youtube.com/embed/PZFDeJ4Wf1Y',
            ],
            [
                'nama' => 'Pasar Segamas',
                'stream_url' => 'https://www.youtube.com/embed/XhmQkH-8_qw',
            ],
        ];

        foreach ($cctvs as $cctv) {
            DB::table('cctv')->insert(array_merge($cctv, [
                'status' => 'online',
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
