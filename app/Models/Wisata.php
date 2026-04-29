<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Wisata extends Model
{
    use HasFactory;

    protected $table = 'wisata';

    protected $fillable = [
        'nama', 'slug', 'kategori', 'deskripsi',
        'kecamatan_id',
        'jam_buka', 'jam_tutup',
        'harga_anak', 'harga_dewasa',
        'fasilitas', 'kontak',
        'rating', 'total_review',
        'thumbnail', 'status',
    ];

    protected $casts = [
        'rating' => 'float',
    ];

    // app/Models/Wisata.php

    public function kecamatan()
    {
        return $this->belongsTo(Kecamatan::class, 'kecamatan_id');
    }

    // Relasi ke tabel ratings
    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    // app/Models/Wisata.php
    public function prices()
    {
        return $this->hasMany(WisataPrice::class, 'wisata_id');
    }

    /**
     * Aksesor untuk mendapatkan rata-rata rating
     * Cara panggil di Controller/Resource: $wisata->average_rating
     */
    public function getAverageRatingAttribute()
    {
        // Mengambil rata-rata kolom 'rating' dan bulatkan 1 desimal
        return round($this->ratings()->avg('rating'), 1) ?: 0;
    }

    /**
     * Aksesor untuk mendapatkan jumlah ulasan
     * Cara panggil: $wisata->total_reviews
     */
    public function getTotalReviewsAttribute()
    {
        return $this->ratings()->count();
    }

    protected $appends = ['alamat_lengkap'];

    public function getAlamatLengkapAttribute() {
        return "Kecamatan " . ($this->kecamatan?->nama ?? '-') . ", Purbalingga";
    }

    // Tambahkan fungsi ini di Model Wisata DAN Model CCTV
    public function marker()
    {
        return $this->morphOne(MapMarker::class, 'markable');
    }

}