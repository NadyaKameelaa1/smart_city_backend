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

        // Filter opsional: ?status=Aktif | Digunakan
        if ($request->filled('status')) {
            $query->where('status_tiket', $request->status);
        }

        $orders = $query->get();

        // Tambahkan flag sudah_direview untuk setiap order
        // (cek apakah user sudah rating wisata ini untuk kunjungan ini)
        $reviewedWisataIds = \App\Models\Rating::where('user_id', $user->id)
            ->pluck('wisata_id')
            ->toArray();

        $orders = $orders->map(function ($order) use ($reviewedWisataIds) {
            $order->sudah_direview = in_array($order->wisata_id, $reviewedWisataIds);
            return $order;
        });

        return response()->json([
            'success' => true,
            'data'    => $orders,
        ]);
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

        // Cek apakah user sudah mereview wisata ini
        $sudahDireview = \App\Models\Rating::where('user_id', $user->id)
            ->where('wisata_id', $order->wisata_id)
            ->exists();

        $order->sudah_direview = $sudahDireview;

        return response()->json([
            'success' => true,
            'data'    => $order,
        ]);
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
