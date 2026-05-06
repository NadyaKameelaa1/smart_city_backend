<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TicketOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class TicketOrderController extends Controller
{

    public function store(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'wisata_id'          => ['required', 'exists:wisata,id'],
            'tanggal_kunjungan'   => ['required', 'date'],
            'jumlah_dewasa'      => ['required', 'integer', 'min:0'],
            'jumlah_anak'        => ['required', 'integer', 'min:0'],
            'total_harga'        => ['required', 'integer', 'min:0'],
            'metode_pembayaran'  => ['required', 'string', 'max:50'],
            'kode_order'         => ['nullable', 'string', 'max:30', 'unique:ticket_orders,kode_order'],
        ]);

        $order = TicketOrder::create([
            'kode_order'        => $data['kode_order'] ?? 'TKT-' . Str::upper(Str::random(10)),
            'user_id'           => $user->id,
            'wisata_id'         => $data['wisata_id'],
            'tanggal_kunjungan' => $data['tanggal_kunjungan'],
            'jumlah_dewasa'     => $data['jumlah_dewasa'],
            'jumlah_anak'       => $data['jumlah_anak'],
            'total_harga'       => $data['total_harga'],
            'status_tiket'      => 'Aktif',
            'metode_pembayaran' => $data['metode_pembayaran'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Tiket berhasil dibuat.',
            'data'    => $order,
        ], 201);
    }

    /**
     * GET /api/tiket-saya
     * Ambil semua ticket_orders milik user yang sedang login,
     * beserta relasi wisata (nama, thumbnail, kategori).
     */
    public function index(Request $request)
{
    $user = Auth::user();
 
    $query = TicketOrder::with(['wisata:id,nama,thumbnail,kategori,slug'])
        ->where('user_id', $user->id)
        ->orderByDesc('created_at');
 
    if ($request->filled('status')) {
        $query->where('status_tiket', $request->status);
    }
 
    $orders = $query->get();
 
    // Hitung jumlah rating yang sudah diberikan per wisata_id
    // Satu rating = satu kunjungan yang sudah direview
    $ratingCountPerWisata = \App\Models\Rating::where('user_id', $user->id)
        ->select('wisata_id', \Illuminate\Support\Facades\DB::raw('count(*) as total'))
        ->groupBy('wisata_id')
        ->pluck('total', 'wisata_id')
        ->all();
 
    // Hitung jumlah tiket "Digunakan" per wisata_id
    $usedCountPerWisata = TicketOrder::where('user_id', $user->id)
        ->where('status_tiket', 'Digunakan')
        ->select('wisata_id', \Illuminate\Support\Facades\DB::raw('count(*) as total'))
        ->groupBy('wisata_id')
        ->pluck('total', 'wisata_id')
        ->all();
 
    $orders = $orders->map(function ($order) use ($ratingCountPerWisata, $usedCountPerWisata) {
        $wisataId     = $order->wisata_id;
        $ratingDiberi = $ratingCountPerWisata[$wisataId] ?? 0;
        $tiketDigunakan = $usedCountPerWisata[$wisataId] ?? 0;
 
        // sudah_direview = true hanya jika jumlah rating >= jumlah kunjungan
        // Artinya masih ada "slot" review jika ratingDiberi < tiketDigunakan
        $order->sudah_direview = ($ratingDiberi >= $tiketDigunakan) && $tiketDigunakan > 0;
 
        return $order;
    });
 
    return response()->json(['success' => true, 'data' => $orders]);
}

    public function adminIndex(Request $request)
    {
        $user = $request->user();

        if ($user->role !== 'admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $tickets = TicketOrder::with(['user'])
            ->where('wisata_id', $user->wisata_id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json(['data' => $tickets]);
    }

    /**
     * GET /api/tiket-saya/{kode_order}
     * Detail satu tiket berdasarkan kode_order.
     * Hanya bisa diakses oleh pemilik tiket.
     */
    public function show(string $kodeOrder)
{
    $user = Auth::user();
 
    $order = TicketOrder::with(['wisata:id,nama,thumbnail,kategori,slug'])
        ->where('kode_order', $kodeOrder)
        ->where('user_id', $user->id)
        ->firstOrFail();
 
    // Cek slot review: berapa kali tiket digunakan vs berapa kali sudah review
    $tiketDigunakan = TicketOrder::where('user_id', $user->id)
        ->where('wisata_id', $order->wisata_id)
        ->where('status_tiket', 'Digunakan')
        ->count();
 
    $ratingDiberi = \App\Models\Rating::where('user_id', $user->id)
        ->where('wisata_id', $order->wisata_id)
        ->count();
 
    $order->sudah_direview = ($ratingDiberi >= $tiketDigunakan) && $tiketDigunakan > 0;
 
    return response()->json(['success' => true, 'data' => $order]);
}

    /**
 * Cek validitas tiket berdasarkan kode_order (sebelum gunakan)
 */
public function cekTiket(string $kodeOrder)
{
    $tiket = TicketOrder::with('wisata:id,nama,thumbnail,kategori')
        ->where('kode_order', $kodeOrder)
        ->first();

    if (!$tiket) {
        return response()->json([
            'valid'   => false,
            'message' => 'Tiket tidak ditemukan.',
        ], 404);
    }

    return response()->json([
        'valid'  => true,
        'data'   => $tiket,
        'status' => $tiket->status_tiket, // 'Aktif' atau 'Digunakan'
    ]);
}

/**
 * Ubah status tiket menjadi Digunakan
 */
public function gunakan(Request $request, $kode_order)
    {
        $user   = $request->user();
        $ticket = TicketOrder::where('kode_order', $kode_order)
            ->where('wisata_id', $user->wisata_id)
            ->firstOrFail();

        if ($ticket->status_tiket === 'Digunakan') {
            return response()->json(['message' => 'Tiket sudah digunakan.'], 422);
        }

        $ticket->update(['status_tiket' => 'Digunakan']);

        return response()->json([
            'message' => 'Tiket berhasil ditandai digunakan.',
            'data'    => $ticket,
        ]);
    }
}
