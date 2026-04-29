<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BuildingGroup extends Model
{
    protected $table = 'building_groups';

    protected $fillable = ['nama'];

    /**
     * Satu Group punya banyak kategori.
     */
    public function categories(): HasMany
    {
        return $this->hasMany(BuildingCategory::class, 'group_id');
    }
}
