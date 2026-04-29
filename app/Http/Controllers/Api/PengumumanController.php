<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pengumuman;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PengumumanController extends Controller
{
    // ─────────────────────────────────────────────────────────────
    // PUBLIC
    // ─────────────────────────────────────────────────────────────

    /**
     * GET /api/pengumuman
     * Semua pengumuman, diurutkan tanggal_mulai DESC
     */
    public function index()
    {
        $pengumuman = Pengumuman::orderBy('tanggal_mulai', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json(['success' => true, 'data' => $pengumuman]);
    }

    /**
     * GET /api/pengumuman/{slug}
     */
    public function show($slug)
    {
        $pengumuman = Pengumuman::where('slug', $slug)->firstOrFail();

        return response()->json(['success' => true, 'data' => $pengumuman]);
    }

    // ─────────────────────────────────────────────────────────────
    // SUPER ADMIN
    // ─────────────────────────────────────────────────────────────

    /**
     * GET /api/super-admin/pengumuman
     * Semua pengumuman untuk dashboard admin
     */
    public function adminIndex()
    {
        $pengumuman = Pengumuman::orderBy('created_at', 'desc')->get();

        return response()->json(['success' => true, 'data' => $pengumuman]);
    }

    /**
     * POST /api/super-admin/pengumuman
     * Tambah pengumuman baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'judul'            => 'required|string|max:300',
            'isi'              => 'required|string',
            'publisher'        => 'required|string|max:150',
            'prioritas'        => 'nullable|in:mendesak,sedang,umum',
            'penting'          => 'nullable|boolean',
            'tanggal_mulai'    => 'nullable|date',
            'tanggal_berakhir' => 'nullable|date|after_or_equal:tanggal_mulai',
            'attachment_url'   => 'nullable|url|max:500',
        ]);

        $slug = $this->makeUniqueSlug($request->judul);

        $pengumuman = Pengumuman::create([
            'judul'            => $request->judul,
            'slug'             => $slug,
            'isi'              => $request->isi,
            'publisher'        => $request->publisher,
            'prioritas'        => $request->input('prioritas', 'umum'),
            'penting'          => $request->boolean('penting', false) ? 1 : 0,
            'tanggal_mulai'    => $request->tanggal_mulai    ?: null,
            'tanggal_berakhir' => $request->tanggal_berakhir ?: null,
            'attachment_url'   => $request->attachment_url   ?: null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pengumuman berhasil ditambahkan.',
            'data'    => $pengumuman,
        ], 201);
    }

    /**
     * PUT /api/super-admin/pengumuman/{id}
     * Update pengumuman
     */
    public function update(Request $request, $id)
    {
        $pengumuman = Pengumuman::findOrFail($id);

        $request->validate([
            'judul'            => 'required|string|max:300',
            'isi'              => 'required|string',
            'publisher'        => 'required|string|max:150',
            'prioritas'        => 'nullable|in:mendesak,sedang,umum',
            'penting'          => 'nullable|boolean',
            'tanggal_mulai'    => 'nullable|date',
            'tanggal_berakhir' => 'nullable|date|after_or_equal:tanggal_mulai',
            'attachment_url'   => 'nullable|url|max:500',
        ]);

        // Regenerate slug hanya jika judul berubah
        $slug = ($pengumuman->judul !== $request->judul)
            ? $this->makeUniqueSlug($request->judul, $pengumuman->id)
            : $pengumuman->slug;

        $pengumuman->update([
            'judul'            => $request->judul,
            'slug'             => $slug,
            'isi'              => $request->isi,
            'publisher'        => $request->publisher,
            'prioritas'        => $request->input('prioritas', $pengumuman->prioritas),
            'penting'          => $request->boolean('penting', false) ? 1 : 0,
            'tanggal_mulai'    => $request->tanggal_mulai    ?: null,
            'tanggal_berakhir' => $request->tanggal_berakhir ?: null,
            'attachment_url'   => $request->attachment_url   ?: null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pengumuman berhasil diperbarui.',
            'data'    => $pengumuman->fresh(),
        ]);
    }

    /**
     * DELETE /api/super-admin/pengumuman/{id}
     */
    public function destroy($id)
    {
        $pengumuman = Pengumuman::findOrFail($id);
        $pengumuman->delete();

        return response()->json([
            'success' => true,
            'message' => 'Pengumuman berhasil dihapus.',
        ]);
    }

    /**
     * PATCH /api/super-admin/pengumuman/{id}/penting
     * Toggle field penting (tinyint 0/1) langsung dari tabel
     * Body: { "penting": true | false }
     */
    public function updatePenting(Request $request, $id)
    {
        $request->validate([
            'penting' => 'required|boolean',
        ]);

        $pengumuman = Pengumuman::findOrFail($id);
        $pengumuman->update(['penting' => $request->boolean('penting') ? 1 : 0]);

        return response()->json([
            'success' => true,
            'message' => 'Status penting berhasil diperbarui.',
            'data'    => ['id' => $pengumuman->id, 'penting' => $pengumuman->penting],
        ]);
    }

    /**
     * PATCH /api/super-admin/pengumuman/{id}/prioritas
     * Ubah prioritas langsung dari dropdown tabel
     * Body: { "prioritas": "mendesak" | "sedang" | "umum" }
     */
    public function updatePrioritas(Request $request, $id)
    {
        $request->validate([
            'prioritas' => 'required|in:mendesak,sedang,umum',
        ]);

        $pengumuman = Pengumuman::findOrFail($id);
        $pengumuman->update(['prioritas' => $request->prioritas]);

        return response()->json([
            'success' => true,
            'message' => 'Prioritas berhasil diperbarui.',
            'data'    => ['id' => $pengumuman->id, 'prioritas' => $pengumuman->prioritas],
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
            $query = Pengumuman::where('slug', $slug);
            if ($exceptId) $query->where('id', '!=', $exceptId);
            if (!$query->exists()) break;
            $slug = $base . '-' . $i++;
        }

        return $slug;
    }
}