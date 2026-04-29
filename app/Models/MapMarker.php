<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class MapMarker extends Model
{
    protected $table = 'map_markers';

    /**
     * Kolom di tabel map_markers (sesuai gambar DB):
     * id, category_id, markable_type, markable_id, lat, lng, created_at, updated_at
     *
     * TIDAK ADA: is_active, title, description, dsb.
     */
    protected $fillable = [
        'category_id',
        'markable_type',
        'markable_id',
        'lat',
        'lng',
    ];

    protected $casts = [
        'lat' => 'float',
        'lng' => 'float',
    ];

    /**
     * Relasi polymorphic — marker bisa milik Building, Kecamatan, Desa, CCTV, dll.
     */
    public function markable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Relasi ke BuildingCategory (opsional, untuk filter marker per kategori di peta).
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(BuildingCategory::class, 'category_id');
    }
}