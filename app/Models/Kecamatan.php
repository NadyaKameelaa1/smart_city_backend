<?php

namespace App\Models;

use App\Models\Cctv;
use App\Models\Desa;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kecamatan extends Model
{
    protected $table = 'kecamatan';

    protected $fillable = ['nama', 'kode'];

    /**
     * Satu Kecamatan memiliki banyak Desa.
     */
    public function desa(): HasMany
    {
        return $this->hasMany(Desa::class, 'kecamatan_id');
    }

    /**
     * Relasi ke CCTV yang ada di kecamatan tersebut.
     */
    public function cctv(): HasMany
    {
        return $this->hasMany(Cctv::class, 'kecamatan_id');
    }

    public function marker()
    {
        return $this->morphOne(MapMarker::class, 'markable');
    }

    public $timestamps = true;

}
