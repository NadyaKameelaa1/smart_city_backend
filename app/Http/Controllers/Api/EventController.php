<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EventController extends Controller
{
    public function index() {
        $events = Event::orderBy('tanggal_mulai', 'asc')->get();
        return response()->json([
            'success' => true,
            'data'    => $events
        ]);
    }

    /** GET /api/super-admin/event */
    public function adminIndex()
    {
        $events = Event::orderBy('created_at', 'desc')->get();
        return response()->json(['success' => true, 'data' => $events]);
    }

    /** POST /api/super-admin/event */
    public function store(Request $request)
    {
        $request->validate([
            'nama'             => 'required|string|max:200',
            'kategori'         => 'required|string|max:100',
            'penyelenggara'    => 'required|string|max:150',
            'tanggal_mulai'    => 'required|date',
            'tanggal_selesai'  => 'nullable|date|after_or_equal:tanggal_mulai',
            'jam_mulai'        => 'nullable|date_format:H:i',
            'jam_selesai'      => 'nullable|date_format:H:i',
            'lokasi'           => 'nullable|string|max:255',
            'kecamatan_id'     => 'required|exists:kecamatan,id',
            'status'           => 'nullable|in:draft,published,selesai',
            'thumbnail'        => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $slug = $this->makeUniqueEventSlug($request->nama);

        $thumbnailPath = null;
        if ($request->hasFile('thumbnail')) {
            $thumbnailPath = $request->file('thumbnail')->store('event', 'public');
        }

        $event = Event::create([
            'nama'             => $request->nama,
            'slug'             => $slug,
            'kategori'         => $request->kategori,
            'penyelenggara'    => $request->penyelenggara,
            'tanggal_mulai'    => $request->tanggal_mulai,
            'tanggal_selesai'  => $request->tanggal_selesai,
            'jam_mulai'        => $request->jam_mulai,
            'jam_selesai'      => $request->jam_selesai,
            'lokasi'           => $request->lokasi,
            'kecamatan_id'     => $request->kecamatan_id,
            'status'           => $request->input('status', 'draft'),
            'thumbnail'        => $thumbnailPath,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Event berhasil ditambahkan.',
            'data'    => $event,
        ], 201);
    }

    /** POST /api/super-admin/event/{id} (dengan _method PUT) */
    public function update(Request $request, $id)
    {
        $event = Event::findOrFail($id);

        $request->validate([
            'nama'             => 'required|string|max:200',
            'kategori'         => 'required|string|max:100',
            'penyelenggara'    => 'required|string|max:150',
            'tanggal_mulai'    => 'required|date',
            'tanggal_selesai'  => 'nullable|date|after_or_equal:tanggal_mulai',
            'jam_mulai'        => 'nullable|date_format:H:i',
            'jam_selesai'      => 'nullable|date_format:H:i',
            'lokasi'           => 'nullable|string|max:255',
            'kecamatan_id'     => 'required|exists:kecamatan,id',
            'status'           => 'nullable|in:draft,published,selesai',
            'thumbnail'        => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        // Regenerasi slug hanya jika nama berubah
        if ($event->nama !== $request->nama) {
            $slug = $this->makeUniqueEventSlug($request->nama, $event->id);
        } else {
            $slug = $event->slug;
        }

        $data = [
            'nama'             => $request->nama,
            'slug'             => $slug,
            'kategori'         => $request->kategori,
            'penyelenggara'    => $request->penyelenggara,
            'tanggal_mulai'    => $request->tanggal_mulai,
            'tanggal_selesai'  => $request->tanggal_selesai,
            'jam_mulai'        => $request->jam_mulai,
            'jam_selesai'      => $request->jam_selesai,
            'lokasi'           => $request->lokasi,
            'kecamatan_id'     => $request->kecamatan_id,
            'status'           => $request->input('status', $event->status),
        ];

        // Thumbnail baru jika diunggah
        if ($request->hasFile('thumbnail')) {
            // Hapus file lama
            if ($event->thumbnail) {
                Storage::disk('public')->delete($event->thumbnail);
            }
            $data['thumbnail'] = $request->file('thumbnail')->store('event', 'public');
        }

        $event->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Event berhasil diperbarui.',
            'data'    => $event->fresh(),
        ]);
    }

    /** DELETE /api/super-admin/event/{id} */
    public function destroy($id)
    {
        $event = Event::findOrFail($id);
        // Hapus thumbnail jika ada
        if ($event->thumbnail) {
            Storage::disk('public')->delete($event->thumbnail);
        }
        $event->delete();

        return response()->json([
            'success' => true,
            'message' => 'Event berhasil dihapus.',
        ]);
    }

    /** PATCH /api/super-admin/event/{id}/status */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:draft,published,selesai',
        ]);

        $event = Event::findOrFail($id);
        $event->update(['status' => $request->status]);

        return response()->json([
            'success' => true,
            'message' => 'Status event berhasil diperbarui.',
            'data'    => ['id' => $event->id, 'status' => $event->status],
        ]);
    }

    // ─────────────────────────────────────────────────────────
    // Helper: slug unik untuk event
    // ─────────────────────────────────────────────────────────
    private function makeUniqueEventSlug(string $nama, $exceptId = null): string
    {
        $base = Str::slug($nama);
        $slug = $base;
        $i = 1;

        while (true) {
            $query = Event::where('slug', $slug);
            if ($exceptId) {
                $query->where('id', '!=', $exceptId);
            }
            if (!$query->exists()) {
                break;
            }
            $slug = $base . '-' . $i++;
        }

        return $slug;
    }
}
