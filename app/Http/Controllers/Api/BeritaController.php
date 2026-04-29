<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Berita;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BeritaController extends Controller
{
    // ─────────────────────────────────────────────────────────────
    // PUBLIC
    // ─────────────────────────────────────────────────────────────

    /**
     * GET /api/berita
     * Hanya berita published, untuk halaman publik
     */
    public function index()
    {
        $berita = Berita::where('status', 'published')
            ->orderBy('published_at', 'desc')
            ->get();

        return response()->json(['success' => true, 'data' => $berita]);
    }

    // app/Http/Controllers/Api/BeritaController.php

    /**
     * GET /api/berita/{slug}
     * Detail + increment views
     */
    public function show($slug)
    {
        $berita = Berita::where('slug', $slug)->firstOrFail();
        $berita->increment('views');
        $berita->refresh();

        return response()->json(['success' => true, 'data' => $berita]);
    }

    // ─────────────────────────────────────────────────────────────
    // SUPER ADMIN
    // ─────────────────────────────────────────────────────────────

    /**
     * GET /api/super-admin/berita
     * Semua berita (draft + published), untuk dashboard admin
     */
    public function adminIndex()
    {
        $berita = Berita::orderBy('created_at', 'desc')->get();

        // Tambahkan full URL thumbnail agar frontend bisa langsung pakai
        $berita->transform(function ($b) {
            $b->thumbnail_url = $b->thumbnail
                ? asset('storage/' . $b->thumbnail)
                : null;
            return $b;
        });

        return response()->json(['success' => true, 'data' => $berita]);
    }

    /**
     * POST /api/super-admin/berita
     * Tambah berita baru dengan upload thumbnail
     */
    public function store(Request $request)
    {
        $request->validate([
            'judul'     => 'required|string|max:300',
            'konten'    => 'required|string',
            'kategori'  => 'required|in:kecamatan,desa',
            'publisher' => 'nullable|string|max:255',
            'status'    => 'nullable|in:draft,published',
            'featured'  => 'nullable|boolean',
            'thumbnail' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $slug = $this->makeUniqueSlug($request->judul);

        $thumbnailPath = null;
        if ($request->hasFile('thumbnail')) {
            $thumbnailPath = $request->file('thumbnail')
                ->store('berita', 'public');
        }

        $status      = $request->input('status', 'draft');
        $publishedAt = ($status === 'published') ? now() : null;

        $berita = Berita::create([
            'judul'        => $request->judul,
            'slug'         => $slug,
            'konten'       => $request->konten,
            'kategori'     => $request->kategori,
            'publisher'    => $request->input('publisher', 'Tidak Ketahui'),
            'thumbnail'    => $thumbnailPath,
            'status'       => $status,
            'featured'     => $request->boolean('featured', false),
            'views'        => 0,
            'published_at' => $publishedAt,
        ]);

        $berita->thumbnail_url = $thumbnailPath
            ? asset('storage/' . $thumbnailPath)
            : null;

        return response()->json([
            'success' => true,
            'message' => 'Berita berhasil ditambahkan.',
            'data'    => $berita,
        ], 201);
    }

    /**
     * PUT /api/super-admin/berita/{id}
     * Update berita (gunakan POST + _method=PUT dari FormData)
     */
    public function update(Request $request, $id)
    {
        $berita = Berita::findOrFail($id);

        $request->validate([
            'judul'     => 'required|string|max:300',
            'konten'    => 'required|string',
            'kategori'  => 'required|in:kecamatan,desa',
            'publisher' => 'nullable|string|max:255',
            'status'    => 'nullable|in:draft,published',
            'featured'  => 'nullable|boolean',
            'thumbnail' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        // Regenerate slug hanya jika judul berubah
        $slug = ($berita->judul !== $request->judul)
            ? $this->makeUniqueSlug($request->judul, $berita->id)
            : $berita->slug;

        // Handle thumbnail upload
        $thumbnailPath = $berita->thumbnail;
        if ($request->hasFile('thumbnail')) {
            // Hapus thumbnail lama
            if ($berita->thumbnail) {
                Storage::disk('public')->delete($berita->thumbnail);
            }
            $thumbnailPath = $request->file('thumbnail')
                ->store('berita', 'public');
        }

        // Jika status berubah ke published, set published_at
        $statusBaru  = $request->input('status', $berita->status);
        $publishedAt = $berita->published_at;
        if ($statusBaru === 'published' && $berita->status !== 'published') {
            $publishedAt = now();
        }

        $berita->update([
            'judul'        => $request->judul,
            'slug'         => $slug,
            'konten'       => $request->konten,
            'kategori'     => $request->kategori,
            'publisher'    => $request->input('publisher', $berita->publisher),
            'thumbnail'    => $thumbnailPath,
            'status'       => $statusBaru,
            'featured'     => $request->boolean('featured', $berita->featured),
            'published_at' => $publishedAt,
        ]);

        $berita->thumbnail_url = $thumbnailPath
            ? asset('storage/' . $thumbnailPath)
            : null;

        return response()->json([
            'success' => true,
            'message' => 'Berita berhasil diperbarui.',
            'data'    => $berita,
        ]);
    }

    /**
     * DELETE /api/super-admin/berita/{id}
     */
    public function destroy($id)
    {
        $berita = Berita::findOrFail($id);

        if ($berita->thumbnail) {
            Storage::disk('public')->delete($berita->thumbnail);
        }

        $berita->delete();

        return response()->json([
            'success' => true,
            'message' => 'Berita berhasil dihapus.',
        ]);
    }

    /**
     * PATCH /api/super-admin/berita/{id}/status
     * Ganti status draft ↔ published langsung dari dropdown tabel
     * Body: { "status": "published" | "draft" }
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:draft,published',
        ]);

        $berita = Berita::findOrFail($id);

        $publishedAt = $berita->published_at;
        if ($request->status === 'published' && $berita->status !== 'published') {
            $publishedAt = now();
        }

        $berita->update([
            'status'       => $request->status,
            'published_at' => $publishedAt,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Status berita berhasil diperbarui.',
            'data'    => ['id' => $berita->id, 'status' => $berita->status, 'published_at' => $berita->published_at],
        ]);
    }

    /**
     * PATCH /api/super-admin/berita/{id}/featured
     * Toggle featured langsung dari toggle di tabel
     * Body: { "featured": true | false }
     */
    public function updateFeatured(Request $request, $id)
    {
        $request->validate([
            'featured' => 'required|boolean',
        ]);

        $berita = Berita::findOrFail($id);
        $berita->update(['featured' => $request->boolean('featured')]);

        return response()->json([
            'success' => true,
            'message' => 'Featured berita berhasil diperbarui.',
            'data'    => ['id' => $berita->id, 'featured' => $berita->featured],
        ]);
    }

    // ─────────────────────────────────────────────────────────────
    // Helper
    // ─────────────────────────────────────────────────────────────

    private function makeUniqueSlug(string $judul, $exceptId = null): string
    {
        $base = Str::slug($judul);
        $slug = $base;
        $i    = 1;

        while (true) {
            $query = Berita::where('slug', $slug);
            if ($exceptId) $query->where('id', '!=', $exceptId);
            if (!$query->exists()) break;
            $slug = $base . '-' . $i++;
        }

        return $slug;
    }
}