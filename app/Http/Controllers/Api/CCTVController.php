<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cctv;
use App\Models\MapMarker;
use Illuminate\Http\Request;

class CCTVController extends Controller
{
    public function index()
    {
        $cctvs = Cctv::with('marker')->get();
        return response()->json(['data' => $cctvs]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'      => 'required|string|max:255',
            'status'    => 'required|in:online,offline',
            'stream_url'=> 'nullable|string',
            'latitude'  => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $cctv = Cctv::create([
            'nama'       => $request->nama,
            'status'     => $request->status,
            'stream_url' => $request->stream_url,
        ]);

        // Simpan marker
        MapMarker::create([
            'markable_type' => 'App\Models\Cctv',
            'markable_id'   => $cctv->id,
            'lat'           => $request->latitude,
            'lng'           => $request->longitude,
        ]);

        $cctv->load('marker');

        return response()->json([
            'message' => 'CCTV berhasil ditambahkan.',
            'data'    => $cctv,
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama'      => 'required|string|max:255',
            'status'    => 'required|in:online,offline',
            'stream_url'=> 'nullable|string',
            'latitude'  => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $cctv = Cctv::findOrFail($id);

        $cctv->update([
            'nama'       => $request->nama,
            'status'     => $request->status,
            'stream_url' => $request->stream_url,
        ]);

        // Update atau buat marker
        MapMarker::updateOrCreate(
            ['markable_type' => 'App\Models\Cctv', 'markable_id' => $cctv->id],
            ['lat' => $request->latitude, 'lng' => $request->longitude]
        );

        $cctv->load('marker');

        return response()->json([
            'message' => 'CCTV berhasil diperbarui.',
            'data'    => $cctv,
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate(['status' => 'required|in:online,offline']);
        $cctv = Cctv::findOrFail($id);
        $cctv->update(['status' => $request->status]);

        return response()->json(['message' => 'Status berhasil diperbarui.', 'data' => $cctv]);
    }

    public function destroy($id)
    {
        $cctv = Cctv::findOrFail($id);
        // Hapus marker terkait
        MapMarker::where('markable_type', 'App\Models\Cctv')
            ->where('markable_id', $cctv->id)
            ->delete();
        $cctv->delete();

        return response()->json(['message' => 'CCTV berhasil dihapus.']);
    }
}