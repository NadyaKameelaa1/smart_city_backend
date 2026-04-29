<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BuildingGroup;
// use Illuminate\Http\Request;

class BuildingGroupsController extends Controller
{
    public function index()
    {
        $groups = BuildingGroup::all();
        return response()->json([
            'success' => true,
            'data' => $groups
        ]);
    }
}
