<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StatistikGrafik extends Model
{
    protected $table = 'statistik_grafik';
    protected $fillable = ['judul', 'kategori', 'data_json', 'tahun'];

    protected $casts = [
        'data_json' => 'array',
    ];
}
