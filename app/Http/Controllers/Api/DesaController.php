<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Desa;
// use App\Models\Kecamatan;
// use Illuminate\Http\Request;

class DesaController extends Controller
{
    public function index(){
        $kecamatan = Desa::all();
        return response()->json(['data' => $kecamatan]);
    }
}
