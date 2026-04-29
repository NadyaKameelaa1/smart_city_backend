<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketOrder extends Model
{
    use HasFactory;

    protected $table = 'ticket_orders';

    protected $fillable = [
        'kode_order',
        'user_id',
        'wisata_id',
        'tanggal_kunjungan',
        'jumlah_dewasa',
        'jumlah_anak',
        'total_harga',
        'status_tiket',       // enum: 'Aktif' | 'Digunakan'
        'metode_pembayaran',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'tanggal_kunjungan' => 'date:Y-m-d',
        'total_harga'       => 'integer',
        'jumlah_dewasa'     => 'integer',
        'jumlah_anak'       => 'integer',
    ];

    /**
     * Relasi: tiket ini milik user siapa
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relasi: tiket ini untuk wisata mana
     * Hanya ambil kolom yang dibutuhkan frontend
     */
    public function wisata()
    {
        return $this->belongsTo(Wisata::class, 'wisata_id');
    }
}