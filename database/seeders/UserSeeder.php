<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
        [
            'id' => '1e23d1b3-4e2e-4d6c-aba6-2d1889cfaccb',
            'username' => 'aizar124',
            'name' => 'aizar faruq nafiul umam',
            'email' => 'aizar.faruq11@gmail.com',
            'password' => Hash::make('aizar123'), // Sesuaikan passwordnya
            'avatar_url' => null,
            'kecamatan_id' => 1,
            'no_hp' => '08123456789',
            'tanggal_lahir' => '2005-01-01',
            'jenis_kelamin' => 'L',
            'role' => 'user',
            'wisata_id' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'id' => '6c879424-243b-4ab8-aac2-e1d5d514eb2b',
            'username' => 'Admin',
            'name' => 'Admin Purbalingga',
            'email' => 'admin@purbalingga.id',
            'password' => Hash::make('password_admin'),
            'avatar_url' => null,
            'kecamatan_id' => null,
            'no_hp' => null,
            'tanggal_lahir' => null,
            'jenis_kelamin' => null,
            'role' => 'superadmin',
            'wisata_id' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ],
    ]);
    }
}
