<?php

namespace Database\Seeders;

// use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $this->call([
            KecamatanSeeder::class, // Harus pertama
            DesaSeeder::class,      // Harus kedua
            BuildingGroupSeeder::class, // Folder Utama
            BuildingCategorySeeder::class, // Isi Folder (Kategori)
            WisataSeeder::class,     // Wisata + Markernya (sekaligus di file seeder ini)
            CctvSeeder::class,       // CCTV saja
            MapMarkerSeeder::class,  // Baru kemudian tempelkan Marker ke CCTV
            UserSeeder::class,
            BeritaSeeder::class,
            PengumumanSeeder::class,
            PelayananSeeder::class,
            EventSeeder::class,
            StatistikSeeder::class,
            // Seeder lainnya...
    ]);
    }
}
