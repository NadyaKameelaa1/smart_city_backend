<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Rating;
use App\Models\Wisata;
use App\Models\TicketOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RatingController extends Controller
{
    /**
     * POST /api/wisata/{wisata_id}/rating
     * Submit atau update rating untuk sebuah wisata.
     *
     * Rules:
     *  - User harus sudah pernah berkunjung (ada ticket_order dengan status_tiket = 'Digunakan')
     *  - Satu user hanya bisa punya 1 rating per wisata (upsert)
     *  - Setelah simpan rating, update kolom rating & total_review di tabel wisata
     */
    public function store(Request $request, int $wisataId)
    {
        // Coba dari auth, fallback ke dev_user_id
        $user   = Auth::user();
        $userId = $user?->id ?? $request->query('dev_user_id');

        if (!$userId) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $request->validate(['rating' => 'required|integer|min:1|max:5']);

        $wisata = Wisata::findOrFail($wisataId);

        $tiketDigunakan = TicketOrder::where('user_id', $userId)
            ->where('wisata_id', $wisataId)
            ->where('status_tiket', 'Digunakan')
            ->count();

        $ratingCount = Rating::where('user_id', $userId)
            ->where('wisata_id', $wisataId)
            ->count();

        if ($ratingCount >= $tiketDigunakan) {
            return response()->json([
                'success' => false,
                'message' => 'Kamu sudah memberikan ulasan untuk semua kunjunganmu.',
            ], 403);
        }

        DB::transaction(function () use ($userId, $wisataId, $request, $wisata) {
            Rating::create([
                'user_id'   => $userId,
                'wisata_id' => $wisataId,
                'rating'    => $request->rating,
            ]);

            $avg   = Rating::where('wisata_id', $wisataId)->avg('rating');
            $count = Rating::where('wisata_id', $wisataId)->count();

            $wisata->update([
                'rating'       => round($avg, 2),
                'total_review' => $count,
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => 'Rating berhasil disimpan.',
            'data'    => [
                'wisata_id'    => $wisataId,
                'rating'       => $request->rating,
                'rating_baru'  => round(Rating::where('wisata_id', $wisataId)->avg('rating'), 2),
                'total_review' => Rating::where('wisata_id', $wisataId)->count(),
            ],
        ]);
    }

    public function status(int $wisataId)
{
    $user = Auth::user();
    $userId = $user?->id ?? request()->query('dev_user_id');

    if (!$userId) {
        return response()->json([
            'success'          => true,
            'tiket_digunakan'  => 0,
            'rating_diberikan' => 0,
            'bisa_review'      => false,
        ]);
    }

    $tiketDigunakan = TicketOrder::where('user_id', $userId)
        ->where('wisata_id', $wisataId)
        ->where('status_tiket', 'Digunakan')
        ->count();

    $ratingCount = Rating::where('user_id', $userId)
        ->where('wisata_id', $wisataId)
        ->count();

    return response()->json([
        'success'          => true,
        'tiket_digunakan'  => $tiketDigunakan,
        'rating_diberikan' => $ratingCount,
        'bisa_review'      => $tiketDigunakan > $ratingCount,
    ]);
}

    public function ratingStats(int $wisataId)
    {
        $perBintang = Rating::where('wisata_id', $wisataId)
            ->select('rating', DB::raw('count(*) as total'))
            ->groupBy('rating')
            ->pluck('total', 'rating')
            ->all();

        return response()->json([
            'success'    => true,
            'per_bintang' => $perBintang,
        ]);
    }

    /**
     * GET /api/wisata/{wisata_id}/rating/saya
     * Cek apakah user sudah mereview wisata ini, dan berapa ratingnya.
     */
    public function mySingleRating(int $wisataId)
    {
        $user = Auth::user();

        $rating = Rating::where('user_id', $user->id)
            ->where('wisata_id', $wisataId)
            ->first();

        return response()->json([
            'success'       => true,
            'sudah_direview' => !is_null($rating),
            'rating'        => $rating?->rating,
        ]);
    }
}