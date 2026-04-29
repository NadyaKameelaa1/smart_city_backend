<?php

namespace Database\Seeders;

use App\Models\Pelayanan;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PelayananSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'nama'       => 'LPSE',
                'deskripsi'  => 'Layanan Pengadaan Secara Elektronik',
                'icon'       => 'fa-file-contract',
                'url'        => 'https://lpse.purbalinggakab.go.id',
                'kategori'   => 'Pengadaan',
            ],
            [
                'nama'       => 'Open Data',
                'deskripsi'  => 'Portal Data Terbuka Purbalingga',
                'icon'       => 'fa-database',
                'url'        => 'https://data.purbalinggakab.go.id',
                'kategori'   => 'Informasi',
            ],
            [
                'nama'       => 'PPID',
                'deskripsi'  => 'Pejabat Pengelola Informasi & Dokumentasi',
                'icon'       => 'fa-folder-open',
                'url'        => 'https://ppid.purbalinggakab.go.id',
                'kategori'   => 'Informasi',
            ],
            [
                'nama'       => 'Lapor Mas Bupati',
                'deskripsi'  => 'Kanal Pengaduan & Aspirasi Masyarakat',
                'icon'       => 'fa-comment-dots',
                'url'        => 'https://lapor.go.id',
                'kategori'   => 'Pengaduan',
            ],
            [
                'nama'       => 'JDIH',
                'deskripsi'  => 'Jaringan Dokumentasi & Informasi Hukum',
                'icon'       => 'fa-balance-scale',
                'url'        => 'https://jdih.purbalinggakab.go.id',
                'kategori'   => 'Hukum',
            ],
            [
                'nama'       => 'SIPD RI',
                'deskripsi'  => 'Sistem Informasi Pemerintahan Daerah',
                'icon'       => 'fa-landmark',
                'url'        => 'https://sipd.kemendagri.go.id',
                'kategori'   => 'Pemerintahan',
            ],
            [
                'nama'       => 'SiCantik',
                'deskripsi'  => 'Sistem Cerdas Perizinan Terintegrasi',
                'icon'       => 'fa-id-card-alt',
                'url'        => 'https://sicantik.go.id',
                'kategori'   => 'Perizinan',
            ],
            [
                'nama'       => 'OSS Indonesia',
                'deskripsi'  => 'Online Single Submission Perizinan Usaha',
                'icon'       => 'fa-briefcase',
                'url'        => 'https://oss.go.id',
                'kategori'   => 'Perizinan',
            ],
            [
                'nama'       => 'IMB / PBG',
                'deskripsi'  => 'Izin Mendirikan Bangunan / Persetujuan Bangunan',
                'icon'       => 'fa-building',
                'url'        => 'https://simbg.pu.go.id',
                'kategori'   => 'Perizinan',
            ],
            [
                'nama'       => 'Pajak Daerah',
                'deskripsi'  => 'Pembayaran & informasi pajak daerah Purbalingga',
                'icon'       => 'fa-coins',
                'url'        => 'https://bapenda.purbalinggakab.go.id',
                'kategori'   => 'Keuangan',
            ],
            [
                'nama'       => 'BPJS Kesehatan',
                'deskripsi'  => 'Informasi & pendaftaran BPJS Kesehatan',
                'icon'       => 'fa-heartbeat',
                'url'        => 'https://bpjs-kesehatan.go.id',
                'kategori'   => 'Kesehatan',
            ],
            [
                'nama'       => 'PKH / Bansos',
                'deskripsi'  => 'Informasi & pendaftaran bantuan sosial daerah',
                'icon'       => 'fa-hand-holding-heart',
                'url'        => 'https://cekbansos.kemensos.go.id',
                'kategori'   => 'Sosial',
            ],
        ];

        foreach ($data as $item) {
            Pelayanan::create($item);
        }
    }
}
