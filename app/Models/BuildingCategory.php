<?php

namespace App\Models;

use App\Models\MapMarker;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BuildingCategory extends Model
{
    protected $table = 'building_categories';

    protected $fillable = [
        'group_id', 'nama', 'icon_marker', 'color_theme'
    ];

    /**
     * Relasi ke Group (Fasilitas Umum, Pendidikan, dll).
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(BuildingGroup::class, 'group_id');
    }

    /**
     * Relasi ke MapMarkers. 
     * Satu kategori (misal: CCTV) punya banyak titik di peta.
     */
    public function markers(): HasMany
    {
        return $this->hasMany(MapMarker::class, 'category_id');
    }
}
