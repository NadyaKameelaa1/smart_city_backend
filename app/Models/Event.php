<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Event extends Model
{
    protected $table = 'event';

    protected $fillable = [
        'nama',
        'slug',
        'kategori',
        'penyelenggara',
        'tanggal_mulai',
        'tanggal_selesai',
        'jam_mulai',
        'jam_selesai',
        'lokasi',
        'kecamatan_id',
        'thumbnail',
        'status',
    ];

    public function kecamatan(): BelongsTo
    {
        return $this->belongsTo(Kecamatan::class, 'kecamatan_id');
    }
}
