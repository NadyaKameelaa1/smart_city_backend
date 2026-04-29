<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Building;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class BuildingController extends Controller
{
    /**
     * GET /api/super-admin/buildings
     * Ambil semua bangunan beserta relasi untuk ditampilkan di tabel React.
     */
    public function index()
    {
        $buildings = Building::with(['category', 'category.group', 'kecamatan', 'desa', 'marker'])
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $buildings,
        ]);
    }

    /**
     * POST /api/super-admin/buildings
     * Tambah bangunan baru + marker peta.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'category_id'  => 'required|integer|exists:building_categories,id',
                'kecamatan_id' => 'required|integer|exists:kecamatan,id',
                'nama'         => 'required|string|max:255',
                'thumbnail'    => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
                'latitude'     => 'nullable|numeric',
                'longitude'    => 'nullable|numeric',
            ]);

            return DB::transaction(function () use ($request) {

                // 1. Upload thumbnail (opsional)
                $thumbnailPath = null;
                if ($request->hasFile('thumbnail')) {
                    $thumbnailPath = $request->file('thumbnail')->store('buildings', 'public');
                }

                // 2. Decode metadata (dikirim sebagai JSON string dari frontend)
                $metadata = null;
                if ($request->filled('metadata')) {
                    $decoded = json_decode($request->metadata, true);
                    // Simpan hanya jika metadata tidak kosong
                    $metadata = (is_array($decoded) && count($decoded) > 0) ? $decoded : null;
                }

                // 3. Buat slug unik (hindari duplikat)
                $baseSlug = Str::slug($request->nama);
                $slug     = $baseSlug;
                $counter  = 1;
                while (Building::where('slug', $slug)->exists()) {
                    $slug = $baseSlug . '-' . $counter++;
                }

                // 4. Simpan data Building
                // Pastikan semua field yang ada di $fillable Building model diisi
                $building = Building::create([
                    'category_id'  => (int) $request->category_id,
                    'kecamatan_id' => (int) $request->kecamatan_id,
                    'desa_id'      => $request->filled('desa_id') ? (int) $request->desa_id : null,
                    'nama'         => trim($request->nama),
                    'slug'         => $slug,
                    'alamat'       => $request->alamat,
                    'deskripsi'    => $request->deskripsi,
                    'kontak'       => $request->kontak,
                    'website'      => $request->website,
                    'metadata'     => $metadata,
                    'thumbnail'    => $thumbnailPath,
                    'status'       => $request->status ?? 'aktif',
                ]);

                // 5. Simpan Marker ke tabel map_markers
                // Kolom di tabel: id, category_id, markable_type, markable_id, lat, lng
                // TIDAK ADA kolom is_active — hapus field itu!
                if ($request->filled('latitude') && $request->filled('longitude')) {
                    $building->marker()->create([
                        'category_id' => (int) $request->category_id,
                        'lat'         => (float) $request->latitude,
                        'lng'         => (float) $request->longitude,
                        // markable_type & markable_id diisi otomatis oleh Laravel MorphOne
                    ]);
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Data bangunan berhasil ditambahkan!',
                    'data'    => $building->load(['category', 'kecamatan', 'marker']),
                ], 201);
            });

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors'  => $e->errors(),
            ], 422);

        } catch (\Exception $e) {
            Log::error('BuildingController@store error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan data bangunan.',
                'error'   => $e->getMessage(), // hapus di production
            ], 500);
        }
    }

    /**
     * PUT /api/super-admin/buildings/{id}
     * Update data bangunan + marker.
     */
    public function update(Request $request, $id)
    {
        try {
            $building = Building::findOrFail($id);

            $request->validate([
                'category_id'  => 'sometimes|integer|exists:building_categories,id',
                'kecamatan_id' => 'sometimes|integer|exists:kecamatan,id',
                'nama'         => 'sometimes|string|max:255',
                'thumbnail'    => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
                'latitude'     => 'nullable|numeric',
                'longitude'    => 'nullable|numeric',
            ]);

            return DB::transaction(function () use ($request, $building) {

                // Upload thumbnail baru (jika ada)
                $thumbnailPath = $building->thumbnail; // pertahankan yg lama jika tidak ada upload baru
                if ($request->hasFile('thumbnail')) {
                    // Hapus thumbnail lama dari storage jika ada
                    if ($building->thumbnail) {
                        \Illuminate\Support\Facades\Storage::disk('public')->delete($building->thumbnail);
                    }
                    $thumbnailPath = $request->file('thumbnail')->store('buildings', 'public');
                }

                // Decode metadata
                $metadata = $building->metadata; // pertahankan lama
                if ($request->filled('metadata')) {
                    $decoded  = json_decode($request->metadata, true);
                    $metadata = (is_array($decoded) && count($decoded) > 0) ? $decoded : null;
                }

                // Update slug hanya jika nama berubah
                $slug = $building->slug;
                if ($request->filled('nama') && $request->nama !== $building->nama) {
                    $baseSlug = Str::slug($request->nama);
                    $slug     = $baseSlug;
                    $counter  = 1;
                    while (Building::where('slug', $slug)->where('id', '!=', $building->id)->exists()) {
                        $slug = $baseSlug . '-' . $counter++;
                    }
                }

                // Update building
                $building->update([
                    'category_id'  => $request->filled('category_id')  ? (int) $request->category_id  : $building->category_id,
                    'kecamatan_id' => $request->filled('kecamatan_id') ? (int) $request->kecamatan_id : $building->kecamatan_id,
                    'desa_id'      => $request->filled('desa_id')      ? (int) $request->desa_id      : $building->desa_id,
                    'nama'         => $request->filled('nama')         ? trim($request->nama)         : $building->nama,
                    'slug'         => $slug,
                    'alamat'       => $request->input('alamat',    $building->alamat),
                    'deskripsi'    => $request->input('deskripsi', $building->deskripsi),
                    'kontak'       => $request->input('kontak',    $building->kontak),
                    'website'      => $request->input('website',   $building->website),
                    'metadata'     => $metadata,
                    'thumbnail'    => $thumbnailPath,
                    'status'       => $request->input('status', $building->status),
                ]);

                // Update atau buat marker
                if ($request->filled('latitude') && $request->filled('longitude')) {
                    $markerData = [
                        'category_id' => (int) ($request->category_id ?? $building->category_id),
                        'lat'         => (float) $request->latitude,
                        'lng'         => (float) $request->longitude,
                    ];

                    if ($building->marker) {
                        $building->marker->update($markerData);
                    } else {
                        $building->marker()->create($markerData);
                    }
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Data bangunan berhasil diperbarui!',
                    'data'    => $building->fresh(['category', 'kecamatan', 'marker']),
                ]);
            });

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['success' => false, 'message' => 'Bangunan tidak ditemukan.'], 404);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'message' => 'Validasi gagal.', 'errors' => $e->errors()], 422);

        } catch (\Exception $e) {
            Log::error('BuildingController@update error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal memperbarui data.', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * PATCH /api/super-admin/buildings/{id}/status
     * Toggle status aktif/nonaktif langsung dari tabel.
     */
    public function updateStatus(Request $request, $id)
    {
        try {
            $request->validate([
                'status' => 'required|in:aktif,nonaktif',
            ]);

            $building = Building::findOrFail($id);
            $building->update(['status' => $request->status]);

            return response()->json([
                'success' => true,
                'message' => 'Status berhasil diperbarui.',
                'data'    => ['id' => $building->id, 'status' => $building->status],
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal mengubah status.', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * DELETE /api/super-admin/buildings/{id}
     * Hapus bangunan beserta marker terkait.
     */
    public function destroy($id)
    {
        try {
            $building = Building::with('marker')->findOrFail($id);

            DB::transaction(function () use ($building) {
                // Hapus marker terlebih dahulu
                if ($building->marker) {
                    $building->marker->delete();
                }

                // Hapus thumbnail dari storage
                if ($building->thumbnail) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($building->thumbnail);
                }

                $building->delete();
            });

            return response()->json([
                'success' => true,
                'message' => 'Bangunan berhasil dihapus.',
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['success' => false, 'message' => 'Bangunan tidak ditemukan.'], 404);

        } catch (\Exception $e) {
            Log::error('BuildingController@destroy error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal menghapus bangunan.', 'error' => $e->getMessage()], 500);
        }
    }
}