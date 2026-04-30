<?php

use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BeritaController;
use App\Http\Controllers\Api\BuildingCategoriesController;
use App\Http\Controllers\Api\BuildingController;
use App\Http\Controllers\Api\BuildingGroupsController;
use App\Http\Controllers\Api\CCTVController;
use App\Http\Controllers\Api\DesaController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\KecamatanController;
use App\Http\Controllers\Api\PelayananController;
use App\Http\Controllers\Api\PengumumanController;
use App\Http\Controllers\Api\RatingController;
use App\Http\Controllers\Api\StatistikController;
use App\Http\Controllers\Api\TicketOrderController;
use App\Http\Controllers\Api\WisataController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Models\Wisata;

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login',    [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me',      [AuthController::class, 'me']);
    });
});

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/tiket',       [TicketOrderController::class, 'store']);
        Route::get('/tiket',        [TicketOrderController::class, 'index']);
        Route::get('/tiket/{kodeOrder}', [TicketOrderController::class, 'show']);
        
        Route::post('/wisata/{wisata_id}/rating',       [RatingController::class, 'store']);
        Route::get('/wisata/{wisata_id}/rating/saya',   [RatingController::class, 'mySingleRating']);
        });
        
    Route::get('/wisata/{wisata_id}/rating-stats', [RatingController::class, 'ratingStats']);
    Route::get('/wisata/{wisata_id}/rating/status', [RatingController::class, 'status']);

    Route::get('/wisata', [WisataController::class, 'index']);
    Route::get('/wisata/{slug}', [WisataController::class, 'show']);
    
    Route::get('/berita', [BeritaController::class, 'index']);
    Route::get('/berita/{slug}', [BeritaController::class, 'show']);

    Route::get('/pengumuman', [PengumumanController::class, 'index']);
    Route::get('/pengumuman/{slug}', [PengumumanController::class, 'show']);

    Route::get('/pelayanan', [PelayananController::class, 'index']);

    Route::get('/events', [EventController::class, 'index']);

    Route::get('/statistik-purbalingga', [StatistikController::class, 'index']);

    Route::get('/building-categories', [BuildingCategoriesController::class, 'index']);

    Route::get('/building-groups', [BuildingGroupsController::class, 'index']);

    Route::get('/kecamatan', [KecamatanController::class, 'index']);

    Route::prefix('admin')->group(function () {
        Route::post('/login',  [AdminController::class, 'login']);

        Route::middleware('auth:sanctum')->group(function () {
            Route::post('/logout', [AdminController::class, 'logout']);
            Route::get('/me',      [AdminController::class, 'me']);
            Route::get('/wisata', function (Request $request) {
                $user = $request->user();
                if ($user->role !== 'admin') {
                    return response()->json(['message' => 'Forbidden'], 403);
                }

                // Tambahkan with prices dan kecamatan di sini
                $wisata = Wisata::with(['prices', 'kecamatan'])
                    ->find($user->wisata_id);

                if (!$wisata) {
                    return response()->json(['message' => 'Wisata tidak ditemukan'], 404);
                }

                $wisata->thumbnail_url = $wisata->thumbnail
                    ? asset('storage/' . $wisata->thumbnail)
                    : null;

                $wisata->append(['alamat_lengkap']);

                return response()->json(['data' => $wisata]);
            });

            Route::get('/tiket', [TicketOrderController::class, 'adminIndex']);
    
            Route::patch('/tiket/{kode_order}/gunakan', [TicketOrderController::class, 'gunakan']);
            Route::get('/tiket/{kode_order}', [TicketOrderController::class, 'cekTiket']);

        });
    });
    
    Route::prefix('super-admin')->group(function () {
        Route::get('/akun', [AdminController::class, 'adminAkun']);
        Route::post('/akun', [AdminController::class, 'adminStore']);
        Route::post('/akun/{id}', [AdminController::class, 'adminUpdate']);
        Route::delete('/akun/{id}', [AdminController::class, 'adminDestroy']);

        Route::get('/wisata', [WisataController::class, 'adminIndex']);
        
        Route::post('/wisata', [WisataController::class, 'store']);
        Route::post('/wisata/{id}', [WisataController::class, 'update']);
        Route::delete('/wisata/{id}', [WisataController::class, 'destroy']);
        Route::patch('/wisata/{id}/status', [WisataController::class, 'updateStatus']);

        Route::get('/kecamatan', [KecamatanController::class, 'index']);
        Route::get('/desa', [DesaController::class, 'index']);

        Route::get('/pengumuman', [PengumumanController::class, 'index']);
        Route::get('/pengumuman', [PengumumanController::class, 'adminIndex']);
        Route::get('/pengumuman/{slug}', [PengumumanController::class, 'show']);
        Route::post('/pengumuman',                [PengumumanController::class, 'store']);
        Route::post('/pengumuman/{id}',           [PengumumanController::class, 'update']);
        Route::delete('/pengumuman/{id}',         [PengumumanController::class, 'destroy']);
        Route::patch('/pengumuman/{id}/status',   [PengumumanController::class, 'updatePenting']);
        Route::patch('/pengumuman/{id}/featured', [PengumumanController::class, 'updatePrioritas']);
        // Route::get('/pengumuman/{judul}/{exceptId}', [PengumumanController::class, 'index']);
        

        Route::get('/berita', [BeritaController::class, 'index']);
        Route::get('/berita', [BeritaController::class, 'adminIndex']);
        Route::post('/berita',                [BeritaController::class, 'store']);
        Route::post('/berita/{id}',           [BeritaController::class, 'update']);
        Route::delete('/berita/{id}',         [BeritaController::class, 'destroy']);
        Route::patch('/berita/{id}/status',   [BeritaController::class, 'updateStatus']);
        Route::patch('/berita/{id}/featured', [BeritaController::class, 'updateFeatured']);

        Route::get('/event', [EventController::class, 'index']);
        Route::get('/event', [EventController::class, 'adminIndex']);
        Route::post('/event', [EventController::class, 'store']);
        Route::post('/event/{id}', [EventController::class, 'update']);
        Route::delete('/event/{id}', [EventController::class, 'destroy']);
        Route::patch('/event/{id}/status', [EventController::class, 'updateStatus']);

        Route::get('/cctv',              [CCTVController::class, 'index']);
        Route::post('/cctv',             [CCTVController::class, 'store']);
        Route::post('/cctv/{id}',        [CCTVController::class, 'update']);
        Route::patch('/cctv/{id}/status',[CCTVController::class, 'updateStatus']);
        Route::delete('/cctv/{id}',      [CCTVController::class, 'destroy']);
        

        Route::get('/buildings', [BuildingController::class, 'index']);
        Route::post('/buildings', [BuildingController::class, 'store']);
        Route::post('/buildings/{id}', [BuildingController::class, 'update']);
        Route::patch('/buildings/{id}/status', [BuildingController::class, 'updateStatus']);
        Route::delete('/buildings/{id}', [BuildingController::class, 'destroy']);

        Route::get('/building-categories', [BuildingCategoriesController::class, 'index']);
        Route::get('/building-groups', [BuildingGroupsController::class, 'index']);
    });


Route::get('/dev/tiket/{user_id}', function ($user_id) {
    try {
        // Tanpa select kolom dulu — ambil semua kolom wisata
        $orders = \App\Models\TicketOrder::with(['wisata:id,nama,thumbnail,kategori,slug,kecamatan_id'])
        ->where('user_id', $user_id)
        ->orderByDesc('created_at')
        ->get();

        $reviewedIds = [];
        try {
            $reviewedIds = \App\Models\Rating::where('user_id', $user_id)
                ->pluck('wisata_id')->toArray();
        } catch (\Throwable $e) {}

        $orders = $orders->map(function ($o) use ($reviewedIds) {
            $o->sudah_direview = in_array($o->wisata_id, $reviewedIds);
            return $o;
        });

        return response()->json(['success' => true, 'data' => $orders]);

    } catch (\Throwable $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage(),
            'file'    => basename($e->getFile()),
            'line'    => $e->getLine(),
        ], 500);
    }
});

Route::post('/dev/rating/{user_id}/{wisata_id}', function ($user_id, $wisata_id, \Illuminate\Http\Request $request) {
    try {
        // Cek apakah sudah pernah rating
        $existing = \App\Models\Rating::where('user_id', $user_id)
            ->where('wisata_id', $wisata_id)->first();

        if ($existing) {
            return response()->json(['message' => 'Sudah pernah memberi rating.'], 422);
        }

        \App\Models\Rating::create([
            'user_id'   => $user_id,
            'wisata_id' => $wisata_id,
            'rating'    => $request->rating,
        ]);

        // Update kolom rating & total_review di tabel wisata
        $avg   = \App\Models\Rating::where('wisata_id', $wisata_id)->avg('rating');
        $count = \App\Models\Rating::where('wisata_id', $wisata_id)->count();

        \App\Models\Wisata::where('id', $wisata_id)->update([
            'rating'       => round($avg, 2),
            'total_review' => $count,
        ]);

        return response()->json(['success' => true]);

    } catch (\Throwable $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
    }
});
