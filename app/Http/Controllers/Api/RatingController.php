<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Rating;
use App\Models\Wisata;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RatingController extends Controller
{
    /**
     * POST /api/wisata/{wisata_id}/rating
     * Kirim rating baru. User hanya boleh rating sekali per wisata.
     * Butuh auth:sanctum.
     */
    public function store(Request $request, $wisata_id)
    {
        $user = Auth::user();

        // Validasi wisata ada
        $wisata = Wisata::find($wisata_id);
        if (!$wisata) {
            return response()->json(['message' => 'Wisata tidak ditemukan.'], 404);
        }

        // Cek apakah user sudah pernah rating wisata ini
        $existing = Rating::where('user_id', $user->id)
            ->where('wisata_id', $wisata_id)
            ->first();

        if ($existing) {
            return response()->json([
                'message' => 'Kamu sudah memberikan rating untuk wisata ini.',
            ], 422);
        }

        $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
        ]);

        // Simpan rating baru
        Rating::create([
            'user_id'   => $user->id,
            'wisata_id' => $wisata_id,
            'rating'    => $request->rating,
        ]);

        // Update kolom rating & total_review di tabel wisata
        $avg   = Rating::where('wisata_id', $wisata_id)->avg('rating');
        $count = Rating::where('wisata_id', $wisata_id)->count();

        $wisata->update([
            'rating'       => round($avg, 2),
            'total_review' => $count,
        ]);

        return response()->json([
            'success'      => true,
            'message'      => 'Rating berhasil dikirim.',
            'rating_avg'   => round($avg, 2),
            'total_review' => $count,
        ]);
    }

    /**
     * GET /api/wisata/{wisata_id}/rating/saya
     * Ambil rating milik user yang sedang login untuk wisata tertentu.
     * Butuh auth:sanctum.
     */
    public function mySingleRating($wisata_id)
    {
        $user = Auth::user();

        $rating = Rating::where('user_id', $user->id)
            ->where('wisata_id', $wisata_id)
            ->first();

        return response()->json([
            'sudah_direview' => (bool) $rating,
            'rating'         => $rating?->rating,
        ]);
    }

    /**
     * GET /api/wisata/{wisata_id}/rating-stats
     * Statistik rating publik (tidak butuh login).
     */
    public function ratingStats($wisata_id)
    {
        $avg   = Rating::where('wisata_id', $wisata_id)->avg('rating');
        $count = Rating::where('wisata_id', $wisata_id)->count();

        // Distribusi per bintang
        $dist = [];
        for ($i = 1; $i <= 5; $i++) {
            $dist[$i] = Rating::where('wisata_id', $wisata_id)->where('rating', $i)->count();
        }

        return response()->json([
            'rating_avg'   => $avg ? round($avg, 2) : 0,
            'total_review' => $count,
            'distribution' => $dist,
        ]);
    }

    /**
     * GET /api/wisata/{wisata_id}/rating/status
     * Cek apakah user yang login sudah review wisata ini.
     * Tidak crash jika user belum login (kembalikan false).
     */
    public function status(Request $request, $wisata_id)
    {
        $user = Auth::guard('sanctum')->user();

        if (!$user) {
            return response()->json(['sudah_direview' => false]);
        }

        $exists = Rating::where('user_id', $user->id)
            ->where('wisata_id', $wisata_id)
            ->exists();

        return response()->json(['sudah_direview' => $exists]);
    }
}