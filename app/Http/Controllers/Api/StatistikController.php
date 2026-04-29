<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\StatistikGrafik;
use App\Models\StatistikKota;
use Illuminate\Http\JsonResponse;
// use Illuminate\Http\Request;

class StatistikController extends Controller
{
    public function index(): JsonResponse
    {
        $kota = StatistikKota::all();
        $grafik = StatistikGrafik::all();

        return response()->json([
            'success' => true,
            'message' => 'Data Statistik Purbalingga Berhasil Diambil',
            'data' => [
                'statistik_ringkasan' => $kota,
                'statistik_visual' => $grafik
            ]
        ]);
    }
}
