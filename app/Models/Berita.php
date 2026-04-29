<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Berita extends Model
{
    protected $table = 'berita';
    protected $fillable = [
        'judul', 'slug', 'konten', 'kategori', 
        'kecamatan_id', 'publisher', 'thumbnail', 
        'views', 'featured', 'status', 'published_at'
    ];

    // Relasi ke kecamatan jika diperlukan
    public function kecamatan() {
        return $this->belongsTo(Kecamatan::class);
    }
}
