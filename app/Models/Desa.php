<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Desa extends Model
{
    protected $table = 'desa';

    protected $fillable = [
        'kecamatan_id',
        'nama',
        'kode',
    ];

    /**
     * Setiap Desa merujuk ke satu Kecamatan.
     */
    public function kecamatan(): BelongsTo
    {
        return $this->belongsTo(Kecamatan::class, 'kecamatan_id');
    }

    public function marker()
    {
        return $this->morphOne(MapMarker::class, 'markable');
    }
}