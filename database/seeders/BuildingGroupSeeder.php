<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BuildingGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $groups = [
            ['nama' => 'Fasilitas Umum'],
            ['nama' => 'Transportasi'],
            ['nama' => 'Fasilitas Kesehatan'],
            ['nama' => 'Destinasi Wisata'],
        ];

        foreach ($groups as $group) {
            DB::table('building_groups')->insert([
                'nama' => $group['nama'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
