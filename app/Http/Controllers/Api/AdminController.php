<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminController extends Controller
{
    //LOGIKA LOGIN :
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // Cari user berdasarkan username atau email
        $user = User::where('username', $request->username)
                    ->orWhere('email', $request->username)
                    ->first();

        // Cek user exist & password cocok
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Username atau password salah.'
            ], 401);
        }

        // Cek role: hanya 'admin' yang boleh masuk panel ini
        if ($user->role !== 'admin') {
            return response()->json([
                'message' => 'Akses ditolak. Akun ini bukan admin wisata.'
            ], 403);
        }

        // Hapus token lama (opsional, biar tidak numpuk)
        $user->tokens()->where('name', 'admin_token')->delete();

        // Buat token baru
        $token = $user->createToken('admin_token')->plainTextToken;

        return response()->json([
            'message' => 'Login berhasil.',
            'token'   => $token,
            'user'    => [
                'id'         => $user->id,
                'name'       => $user->name,
                'username'   => $user->username,
                'email'      => $user->email,
                'role'       => $user->role,
                'wisata_id'  => $user->wisata_id,
                'avatar_url' => $user->avatar_url,
            ],
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logout berhasil.']);
    }

    public function me(Request $request)
    {
        $user = $request->user();

        if ($user->role !== 'admin') {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        return response()->json(['user' => $user]);
    }

    public function index()
    {
        // Daftar akun admin (untuk super-admin)
        $admins = User::where('role', 'admin')->get();
        return response()->json(['data' => $admins]);
    }

    public function adminAkun()
    {
        $users = User::with('wisata')
            ->where('role', 'admin')
            ->get();
        
        return response()->json(['data' => $users]);
    }

    public function adminStore(Request $request)
    {
        $request->validate([
            'email'     => 'required|email|unique:users,email',
            'username'  => 'required|string|unique:users,username',
            'name'      => 'required|string',
            'password'  => 'required|string|min:8',
            'wisata_id' => 'required|exists:wisata,id',
        ], [
            'email.unique'    => 'Email sudah digunakan.',
            'username.unique' => 'Username sudah digunakan.',
            'password.min'    => 'Password minimal 8 karakter.',
            'wisata_id.exists'=> 'Destinasi wisata tidak valid.',
        ]);

        $user = User::create([
            'id'        => (string) \Illuminate\Support\Str::uuid(),
            'email'     => $request->email,
            'username'  => $request->username,
            'name'      => $request->name,
            'password'  => \Illuminate\Support\Facades\Hash::make($request->password),
            'role'      => 'admin',
            'wisata_id' => $request->wisata_id,
        ]);

        // Load relasi wisata untuk dikembalikan ke frontend
        $user->load('wisata');

        return response()->json([
            'message' => 'Akun admin berhasil dibuat.',
            'data'    => $user,
        ], 201);
    }

    public function adminUpdate(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'email'    => 'required|email|unique:users,email,' . $id,
            'username' => 'required|string|unique:users,username,' . $id,
            'name'     => 'required|string',
            'password' => 'nullable|string|min:8',
            'wisata_id'=> 'required|exists:wisata,id',
        ], [
            'password.min' => 'Password minimal 8 karakter.',
        ]);

        $data = [
            'email'     => $request->email,
            'username'  => $request->username,
            'name'      => $request->name,
            'wisata_id' => $request->wisata_id,
            'role'      => 'admin',
        ];

        if ($request->filled('password')) {
            $data['password'] = \Illuminate\Support\Facades\Hash::make($request->password);
        }

        $user->update($data);
        $user->load('wisata');

        return response()->json([
            'message' => 'Akun berhasil diperbarui.',
            'data'    => $user,
        ]);
    }

    public function adminDestroy($id)
    {
        $user = User::findOrFail($id);
        $user->tokens()->delete(); // hapus semua token sanctum
        $user->delete();

        return response()->json(['message' => 'Akun berhasil dihapus.']);
    }
}