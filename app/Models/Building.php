<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Building extends Model
{
    protected $fillable = [
        'category_id', 
        'kecamatan_id', 
        'desa_id',     
        'nama', 
        'slug', 
        'alamat', 
        'kontak', 
        'website', 
        'deskripsi', 
        'metadata', 
        'status',
        'thumbnail'
    ];

    // Otomatis casting JSON menjadi array PHP
    protected $casts = [
        'metadata' => 'array',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(BuildingCategory::class, 'category_id');
    }

    public function kecamatan(): BelongsTo
    {
        return $this->belongsTo(Kecamatan::class, 'kecamatan_id');
    }

    public function desa(): BelongsTo
    {
        return $this->belongsTo(Desa::class, 'desa_id');
    }

    /**
     * Relasi Polymorphic ke MapMarker.
     * Building "memiliki satu" marker di peta.
     */
    public function marker(): MorphOne
    {
        return $this->morphOne(MapMarker::class, 'markable');
    }
}
