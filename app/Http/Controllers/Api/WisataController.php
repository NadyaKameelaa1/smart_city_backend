<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Wisata;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class WisataController extends Controller
{
    // ── Publik: hanya yang aktif ────────────────────────────────────
    public function index()
    {
        $wisata = Wisata::with(['marker', 'prices'])
            ->whereIn('status', ['aktif', 'Aktif'])
            ->get();

        return response()->json(['success' => true, 'data' => $wisata]);
    }

    // ── Super-admin: semua status ────────────────────────────────────
    public function adminIndex()
    {
        $wisata = Wisata::with(['marker', 'kecamatan', 'prices'])
            ->get()
            ->each(function ($item) {
                $item->thumbnail_url = $item->thumbnail
                    ? asset('storage/' . $item->thumbnail)
                    : null;
            });

        return response()->json(['success' => true, 'data' => $wisata]);
    }

    // ── Store ────────────────────────────────────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'nama'         => 'required|string|max:255',
            'kategori'     => 'required|string|max:100',
            'deskripsi'    => 'nullable|string',
            'kecamatan_id' => 'required|exists:kecamatan,id',
            'jam_buka'     => 'nullable|string',
            'jam_tutup'    => 'nullable|string',
            'fasilitas'    => 'nullable|string',
            'kontak'       => 'nullable|string',
            'status'       => 'required|in:aktif,nonaktif,draft,Aktif,Nonaktif,Draft',
            'thumbnail'    => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'latitude'     => 'nullable|numeric',
            'longitude'    => 'nullable|numeric',
            'prices'       => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $wisata = new Wisata();
            $wisata->fill($request->only([
                'nama', 'kategori', 'deskripsi', 'kecamatan_id',
                'jam_buka', 'jam_tutup', 'fasilitas', 'kontak', 'status',
            ]));
            $wisata->slug = Str::slug($request->nama);

            if ($request->hasFile('thumbnail')) {
                $wisata->thumbnail = $request->file('thumbnail')->store('wisata', 'public');
            }

            $wisata->save();

            // Marker
            if ($request->filled('latitude') && $request->filled('longitude')) {
                $wisata->marker()->create([
                    'lat'         => (float) $request->latitude,
                    'lng'         => (float) $request->longitude,
                    'category_id' => null,
                ]);
            }

            // Harga
            $this->syncPrices($wisata, $request->input('prices'));

            DB::commit();

            $wisata->load('kecamatan', 'marker', 'prices');
            $wisata->thumbnail_url = $wisata->thumbnail ? asset('storage/' . $wisata->thumbnail) : null;

            return response()->json([
                'success' => true,
                'message' => 'Wisata berhasil ditambahkan.',
                'data'    => $wisata,
            ], 201);

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('WisataController::store error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan: ' . $e->getMessage(),
            ], 500);
        }
    }

    // ── Update ───────────────────────────────────────────────────────
    // PENTING: Route harus menerima POST (bukan PUT/PATCH) agar Laravel
    // bisa membaca file upload dari FormData.
    // Di routes/api.php gunakan:
    //   Route::post('/super-admin/wisata/{id}', [WisataController::class, 'update']);
    // Dan di frontend kirim dengan:
    //   fd.append('_method', 'PUT');
    //   api.post(`/super-admin/wisata/${id}`, fd)
    public function update(Request $request, $id)
    {
        $wisata = Wisata::findOrFail($id);

        $request->validate([
            'nama'         => 'sometimes|string|max:255',
            'kategori'     => 'sometimes|string|max:100',
            'deskripsi'    => 'nullable|string',
            'kecamatan_id' => 'sometimes|exists:kecamatan,id',
            'jam_buka'     => 'nullable|string',
            'jam_tutup'    => 'nullable|string',
            'fasilitas'    => 'nullable|string',
            'kontak'       => 'nullable|string',
            'status'       => 'sometimes|in:aktif,nonaktif,draft,Aktif,Nonaktif,Draft',
            'thumbnail'    => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'latitude'     => 'nullable|numeric',
            'longitude'    => 'nullable|numeric',
            'prices'       => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // ── 1. Update field wisata ─────────────────────────────
            $nullableFields = ['jam_buka', 'jam_tutup', 'fasilitas', 'kontak', 'deskripsi'];
            $fillable = ['nama', 'kategori', 'deskripsi', 'kecamatan_id',
                         'jam_buka', 'jam_tutup', 'fasilitas', 'kontak', 'status'];

            foreach ($fillable as $field) {
                if ($request->has($field)) {
                    $val = $request->input($field);
                    $wisata->$field = ($val === '' && in_array($field, $nullableFields))
                        ? null
                        : $val;
                }
            }

            if ($request->has('nama') && $request->nama) {
                $wisata->slug = Str::slug($request->nama);
            }

            // ── 2. Thumbnail ───────────────────────────────────────
            if ($request->hasFile('thumbnail')) {
                if ($wisata->thumbnail) {
                    Storage::disk('public')->delete($wisata->thumbnail);
                }
                $wisata->thumbnail = $request->file('thumbnail')->store('wisata', 'public');
            }

            $wisata->save();

            // ── 3. Marker (lat / lng) ──────────────────────────────
            // FIX: Jangan taruh markable_type/markable_id di kondisi updateOrCreate
            // karena morphOne sudah handle scope itu otomatis.
            // Pakai update/create manual agar tidak duplikat.
            $lat = $request->input('latitude');
            $lng = $request->input('longitude');

            if ($lat !== null && $lng !== null && $lat !== '' && $lng !== '') {
                $existingMarker = $wisata->marker;
                if ($existingMarker) {
                    // Update marker yang sudah ada
                    $existingMarker->update([
                        'lat' => (float) $lat,
                        'lng' => (float) $lng,
                    ]);
                } else {
                    // Buat marker baru
                    $wisata->marker()->create([
                        'lat'         => (float) $lat,
                        'lng'         => (float) $lng,
                        'category_id' => null,
                    ]);
                }
            }

            // ── 4. Harga ───────────────────────────────────────────
            $pricesRaw = $request->input('prices');
            $this->syncPrices($wisata, ($pricesRaw !== '' && $pricesRaw !== null) ? $pricesRaw : null);

            DB::commit();

            $wisata->load('kecamatan', 'marker', 'prices');
            $wisata->thumbnail_url = $wisata->thumbnail
                ? asset('storage/' . $wisata->thumbnail)
                : null;

            return response()->json([
                'success' => true,
                'message' => 'Wisata berhasil diperbarui.',
                'data'    => $wisata,
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('WisataController::update error', [
                'id'    => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui: ' . $e->getMessage(),
            ], 500);
        }
    }

    // ── Helper sync harga ────────────────────────────────────────────
    private function syncPrices(Wisata $wisata, ?string $pricesJson): void
    {
        if ($pricesJson === null || $pricesJson === '') return;

        $prices = json_decode($pricesJson, true);
        if (!is_array($prices) || empty($prices)) return;

        // Hapus semua harga lama milik wisata ini
        $wisata->prices()->delete();

        foreach ($prices as $price) {
            $dayType = $price['day_type'] ?? null;
            if (!$dayType) continue;

            $wisata->prices()->create([
                'day_type'     => $dayType,
                'harga_anak'   => (int) ($price['harga_anak']   ?? 0),
                'harga_dewasa' => (int) ($price['harga_dewasa'] ?? 0),
            ]);
        }
    }

    // ── Delete ───────────────────────────────────────────────────────
    public function destroy($id)
    {
        $wisata = Wisata::findOrFail($id);

        if ($wisata->thumbnail) {
            Storage::disk('public')->delete($wisata->thumbnail);
        }

        $wisata->delete();

        return response()->json(['success' => true, 'message' => 'Wisata berhasil dihapus.']);
    }

    // ── Update status ────────────────────────────────────────────────
    public function updateStatus(Request $request, $id)
    {
        $request->validate(['status' => 'required|in:Aktif,Nonaktif,Draft']);

        $wisata = Wisata::findOrFail($id);
        $wisata->status = $request->status;
        $wisata->save();

        return response()->json([
            'success' => true,
            'message' => 'Status wisata diperbarui.',
            'data'    => $wisata,
        ]);
    }

    // ── Show (publik) ────────────────────────────────────────────────
    public function show($slug)
    {
        $wisata = Wisata::with(['marker', 'kecamatan', 'prices'])
            ->where('slug', $slug)
            ->whereIn('status', ['aktif', 'Aktif'])
            ->firstOrFail();

        $wisata->thumbnail_url = $wisata->thumbnail
            ? asset('storage/' . $wisata->thumbnail)
            : null;

        return response()->json(['success' => true, 'data' => $wisata]);
    }
}