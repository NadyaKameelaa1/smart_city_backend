<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WisataPrice extends Model
{
    use HasFactory;

    // Nama tabel harus sesuai dengan migration
    protected $table = 'wisata_harga';

    protected $fillable = [
        'wisata_id',
        'day_type',
        'harga_anak',
        'harga_dewasa',
    ];

    // Casting agar harga otomatis jadi integer saat dikirim ke React
    protected $casts = [
        'harga_anak' => 'integer',
        'harga_dewasa' => 'integer',
    ];

    /**
     * Relasi balik ke Wisata
     */
    public function wisata()
    {
        return $this->belongsTo(Wisata::class);
    }
}