<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengumuman extends Model
{   
    protected $table = 'pengumuman';

    protected $fillable = [
        'judul', 'slug', 'isi', 'prioritas', 
        'tanggal_mulai', 'tanggal_berakhir', 
        'publisher', 'penting', 'attachment_url'
    ];

    public $timestamps = true;
}
