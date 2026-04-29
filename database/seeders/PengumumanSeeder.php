<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PengumumanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
            $data = [
            [
                'judul' => 'Pengumuman Seleksi Calon Komisaris Independen Dan DYMFK PT BPR Artha Perwira (Perseroda)',
                'slug' => 'pengumuman-seleksi-calon-komisaris-independen-dan-dymfk-pt-bpr-artha-perwira-perseroda',
                'isi' => 'Informasi pengumuman seleksi calon komisaris independen dan DYMFK PT BPR Artha Perwira (Perseroda) dapat dilihat pada lampiran dokumen.',
                'publisher' => 'Dinkominfo',
                'prioritas' => 'sedang',
                'tanggal_mulai' => '2026-03-09 00:00:00',
                'tanggal_berakhir' => null,
                'attachment_url' => 'https://www.purbalinggakab.go.id/wp-content/uploads/2026/03/PENGUMUMAN-SELEKSI-CALON-KOMISARIS-INDEPENDEN-DAN-DYMFK-PT-BPR-ARTHA-PERWIRA-PERSERODA.pdf',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'judul' => 'Pengumuman Pemilihan Penyedia Jasa Koneksi Internet IP Transit Dinas Komunikasi dan Informatika Kabupaten Purbalingga Tahun Anggaran 2026',
                'slug' => 'pengumuman-pemilihan-penyedia-jasa-koneksi-internet-ip-transit-dinas-komunikasi-dan-informatika-kabupaten-purbalingga-tahun-anggaran-2026',
                'isi' => 'Dinas Komunikasi dan Informatika Kabupaten Purbalingga mengumumkan pelaksanaan Pemilihan Penyedia Pengadaan Jasa Koneksi Internet IP Transit Tahun Anggaran 2026. Kegiatan pengadaan ini dilaksanakan untuk mendukung penyediaan layanan konektivitas internet yang andal bagi penyelenggaraan pemerintahan dan pelayanan publik di Kabupaten Purbalingga. Pemilihan penyedia jasa koneksi internet IP Transit dilaksanakan melalui metode mini kompetisi pada Sistem E-Katalog Versi 6 (INAPROC). Sehubungan dengan hal tersebut, kami mengundang para penyedia layanan internet untuk berpartisipasi dalam proses pengadaan ini. Pelaksanaan pemilihan penyedia dijadwalkan pada: 17 Desember 2025 pukul 08.00 WIB sampai dengan 19 Desember 2025 pukul 08.00 WIB. Informasi lengkap mengenai persyaratan, spesifikasi teknis, dan ketentuan pengadaan jasa koneksi internet IP Transit dapat diakses melalui tautan berikut: http://s.id/spektek-2026',
                'publisher' => 'Dinkominfo',
                'prioritas' => 'mendesak',
                'penting' => 1,
                'tanggal_mulai' => '2025-04-17 00:00:00',
                'tanggal_berakhir' => '2025-04-19 00:00:00',
                'attachment_url' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'judul' => 'Pengumuman Seleksi Administrasi Calon Anggota Direksi Perumda OWABONG Kabupaten Purbalingga Tahun 2025',
                'slug' => 'pengumuman-seleksi-administrasi-calon-anggota-direksi-perumda-owabong-kabupaten-purbalingga-tahun-2025',
                'isi' => 'Berdasarkan pemeriksaan berkas atau dokumen administrasi dan hasil Rapat Panitia Seleksi Calon Anggota Direksi Perumda Owabong Kabupaten Purbalingga Tahun 2025 pada Hari Senin, 23 Juni 2025 maka peserta yang dinyatakan memenuhi persyaratan administrasi dapat dilihat pada lampiran dokumen.',
                'publisher' => 'Dinkominfo',
                'prioritas' => 'mendesak',
                'tanggal_mulai' => '2025-06-23 00:00:00',
                'tanggal_berakhir' => null,
                'attachment_url' => 'https://www.purbalinggakab.go.id/wp-content/uploads/2025/06/PENGUMUMAN-SELEKSI-ADMINISTRASI-CALON-DIREKSI-OWABONG-2025.pdf',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'judul' => 'Pengumuman Perekrutan Calon Anggota Dewas Puspahastama Tahun 2023',
                'slug' => 'pengumuman-perekrutan-calon-anggota-dewas-puspahastama-tahun-2023',
                'isi' => 'Pengumuman Perekrutan Calon Anggota Dewan Pengawas Perumda Puspahastama Kabupaten Purbalingga Tahun 2023 dapat diunduh pada lampiran dibawah.',
                'publisher' => 'Dinkominfo',
                'prioritas' => 'mendesak',
                'tanggal_mulai' => '2023-01-20 00:00:00',
                'tanggal_berakhir' => null,
                'attachment_url' => 'https://www.purbalinggakab.go.id/wp-content/uploads/2023/01/Perumda-Puspahastama.pdf',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'judul' => 'Pengumuman Perekrutan Calon Anggota Dewas Perumda Air Minum Tirta Perwira Tahun 2023',
                'slug' => 'pengumuman-perekrutan-calon-anggota-dewas-perumda-air-minum-tirta-perwira-tahun-2023',
                'isi' => 'Pengumuman Perekrutan Calon Anggota Dewan Pengawas Perumda Air Minum Tirta Perwira Kabupaten Purbalingga Tahun 2023 dapat diunduh pada lampiran dibawah.',
                'publisher' => 'Dinkominfo',
                'prioritas' => 'mendesak',
                'tanggal_mulai' => '2023-01-20  00:00:00',
                'tanggal_berakhir' => null,
                'attachment_url' => 'https://www.purbalinggakab.go.id/wp-content/uploads/2023/01/Perumda-Air-Minum-Tirta-Perwira.pdf',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($data as $item) {
            \App\Models\Pengumuman::create($item);
        }
    }
}
