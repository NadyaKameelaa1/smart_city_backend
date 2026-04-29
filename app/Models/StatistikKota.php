<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StatistikKota extends Model
{
    protected $table = 'statistik_kota';
    
    protected $fillable = ['kategori', 'label', 'nilai', 'satuan', 'tahun'];
}
