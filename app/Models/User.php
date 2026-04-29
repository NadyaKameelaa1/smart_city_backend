<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $keyType = 'string';
    public $incrementing = false;

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    /**
     * Nama kolom yang bisa diisi (Mass Assignable).
     */
    protected $fillable = [
        'id',
        'username',
        'name',
        'email',
        'password',
        'avatar_url',
        'kecamatan_id',
        'no_hp',
        'tanggal_lahir',
        'jenis_kelamin',
        'role',
        'wisata_id',
    ];

    protected $hidden = [
        'password',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'tanggal_lahir' => 'date:Y-m-d',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    // Relasi ke Kecamatan (Kota Asal)
    public function kecamatan(): BelongsTo
    {
        return $this->belongsTo(Kecamatan::class, 'kecamatan_id');
    }

    /**
     * Relasi ke Wisata.
     * User (Staff) memiliki satu tempat wisata yang dikelola.
     */
    public function wisata(): BelongsTo
    {
        // Hubungkan foreign key wisata_id ke tabel wisata
        return $this->belongsTo(Wisata::class, 'wisata_id');
    }
    
}