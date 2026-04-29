<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pelayanan;

class PelayananController extends Controller
{
    public function index()
    {
        $pelayanan = Pelayanan::orderBy('id')->get();
        return response()->json([
            'success' => true,
            'data'    => $pelayanan
        ]);
    }
}
