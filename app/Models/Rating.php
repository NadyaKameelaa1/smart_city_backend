<?php

namespace App\Models;

use App\Models\User;
use App\Models\Wisata;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Rating extends Model
{
    use HasFactory;

    protected $table = 'ratings';

    // Kolom yang boleh diisi
    protected $fillable = [
        'user_id',
        'wisata_id',
        'rating',
    ];

    /**
     * Relasi: Review ini milik siapa? (User)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi: Review ini untuk wisata apa?
     */
    public function wisata(): BelongsTo
    {
        return $this->belongsTo(Wisata::class);
    }
}
