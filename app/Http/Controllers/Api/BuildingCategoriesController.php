<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BuildingCategory;
// use Illuminate\Http\Request;

class BuildingCategoriesController extends Controller
{
    public function index()
    {
        // Ambil semua kategori kecuali yang masuk grup CCTV
        $categories = BuildingCategory::whereHas('group', function($query) {
            $query->where('nama', '!=', 'CCTV');
        })->get();

        return response()->json(['data' => $categories]);
    }
}
